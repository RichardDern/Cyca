<?php

namespace App\Models\Traits\Document;

use App\Models\Bookmark;
use App\Models\Feed;
use DomDocument;
use DOMElement;
use DOMXPath;
use Elphin\IcoFileLoader\IcoFileService;
use Illuminate\Support\Facades\Http;
use Imagick;
use League\Uri\Http as UriHttp;
use League\Uri\UriResolver;
use SimplePie;
use Storage;
use Str;
use \ForceUTF8\Encoding as UTF8;

trait AnalysesDocument
{
    # --------------------------------------------------------------------------
    # ----| Properties |--------------------------------------------------------
    # --------------------------------------------------------------------------

    /**
     * Provides temporary access to document's body to analyzers
     *
     * @var string
     */
    private $body = null;

    /**
     * Provides temporary access to response to analyzers
     *
     * @var Illuminate\Http\Client\Response
     */
    private $response = null;

    /**
     * Provides temporary access to DOM document to analyzers
     *
     * @var DOMDocument
     */
    private $domDocument = null;

    /**
     * Provides temporary access to <meta> tags to analyzers
     *
     * @var array
     */
    private $metaTags = [];

    /**
     * Provides temporary access to <link> tags to analyzers
     *
     * @var array
     */
    private $linkTags = [];

    # --------------------------------------------------------------------------
    # ----| Methods |-----------------------------------------------------------
    # --------------------------------------------------------------------------

    /**
     * Ensures string doesn't contain any "undesirable" characters, such as
     * extra-spaces or line-breaks. This is not a purifying method. Only basic
     * cleanup is done here.
     *
     * @param string $string
     * @return string
     */
    protected function cleanupString($string, $stripTags = true)
    {
        if (empty($string)) {
            return null;
        }

        $string = UTF8::toUTF8($string, UTF8::ICONV_TRANSLIT);
        $string = preg_replace('#[\s\t\r\n]+#', ' ', $string);
        $string = html_entity_decode($string, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $string = str_replace('&apos;', "'", $string);

        if ($stripTags) {
            $string = strip_tags(trim($string));
        }

        return trim($string);
    }

    /**
     * Begin document analysis
     */
    public function analyze()
    {
        // Don't bother if document isn't bookmarked anymore
        if ($this->folders()->count() === 0) {
            if (!empty($this->checked_at) && $this->checked_at->addDays(config('cyca.maxOrphanAge.document'))->lt(now())) {
                $this->delete();
            }

            return;
        }

        $this->fetchContent();

        if (empty($this->response)) {
            $this->checked_at = now();

            $this->save();

            return;
        }

        if ($homepage = $this->isFeed()) {
            $this->convertToFeed($homepage);
            return;
        }

        if ($this->existingDocumentsMerged()) {
            return;
        }

        $this->runAnalyzers();

        $this->checked_at = now();

        $this->save();
    }

    /**
     * Copy content of resource at document's URL in "local" storage
     */
    protected function fetchContent()
    {
        $storageRoot      = $this->getStoragePath();
        $bodyFilename     = $storageRoot . '/body';
        $responseFilename = $storageRoot . '/response.json';

        try {
            $this->response = Http::timeout(10)->get($this->url);

            $this->body = $this->response->body();
        } catch (\Exception $ex) {
            report($ex);

            return;
        }

        $psrResponse = $this->response->toPsrResponse();

        $responseData = [
            'headers'          => $this->response->headers(),
            'protocol_version' => $psrResponse->getProtocolVersion()
        ];

        Storage::put($responseFilename, json_encode($responseData));

        if ($this->response->ok()) {
            Storage::put($bodyFilename, $this->body);

            $this->mimetype = Storage::mimetype($bodyFilename);
        }

        $this->http_status_code = $this->response->status();
        $this->http_status_text = $this->response->getReasonPhrase();
    }

    /**
     * Quickly determine if document is, in fact, a feed, and return
     * corresponding home page URL
     *
     * @return boolean|string Return feed's home page if really a feed, false otherwise
     */
    protected function isFeed()
    {
        $client = new SimplePie();

        $client->enable_cache(false);
        $client->set_raw_data($this->body);

        if ($client->init()) {
            return urldecode($client->get_permalink());
        }

        return false;
    }

    /**
     * Transform this document into a feed, by creating or using an existing
     * document with provided homepage URL, creating or using an existing feed
     * with current document's URL, linking both, updating any references to
     * this document to point to the new document, and finally deleting this
     * document
     *
     * @param string $homepage
     */
    protected function convertToFeed($homepage)
    {
        $document = self::firstOrCreate(['url' => $homepage]);
        $feed     = Feed::firstOrCreate(['url' => $this->url]);

        if (!$document->feeds()->find($feed->id)) {
            $document->feeds()->attach($feed);
        }

        Bookmark::where('document_id', $this->id)->update(['document_id' => $document->id, 'initial_url' => $homepage]);

        $this->delete();
    }

    /**
     * Find another document having the same real URL. If one is found, we will
     * update all bookmarks to use the oldest document and delete this one.
     *
     * Returns true if documents have been merged.
     *
     * @return boolean
     */
    protected function existingDocumentsMerged()
    {
        $realUrl = urldecode((string) $this->response->effectiveUri());

        if ($realUrl !== $this->url) {
            $document = self::where('url', $realUrl)->first();

            if ($document) {
                Bookmark::where('document_id', $this->id)->update(['document_id' => $document->id]);
                //TODO: At this point, we should ensure that there is only one
                // bookmark pointing to this document in a single folder
                //$this->delete();
                return true;
            } else {
                $this->url = $realUrl;
            }
        }

        return false;
    }

    /**
     * Select analyzers for this particular document then run them
     */
    protected function runAnalyzers()
    {
        switch ($this->mimetype) {
            // Handle everything not HTML here before "default"
            default:
                $this->runHtmlAnalyzer();
                break;
        }
    }

    /**
     * Perform HTML analysis
     */
    protected function runHtmlAnalyzer()
    {
        if (empty($this->body)) {
            return;
        }

        $this->createDomDocument();
        $this->findTitle();
        $this->findMetaTags();
        $this->findLinkTags();
        $this->findBestFavicon();
        $this->discoverFeeds();
    }

    /**
     * Create a DOM document from document's body
     */
    protected function createDomDocument()
    {
        $this->body = mb_convert_encoding($this->body, 'HTML-ENTITIES', 'UTF-8');

        libxml_use_internal_errors(true);

        $this->domDocument = new DomDocument('1.0', 'UTF-8');

        $this->domDocument->loadHtml($this->body);

        libxml_clear_errors();
    }

    /**
     * Find nodes corresponding to specified XPath query
     *
     * @param string $xpathQuery
     * @return DomNodeList
     */
    protected function findNodes($xpathQuery)
    {
        $xpath = new DOMXPath($this->domDocument);
        return $xpath->query($xpathQuery);
    }

    /**
     * Find first node corresponding to specified XPath query
     *
     * @param string $xpathQuery
     * @return DomNode
     */
    protected function findFirstNode($xpathQuery)
    {
        $xpath = new DOMXPath($this->domDocument);
        $nodes = $xpath->query($xpathQuery);

        if ($nodes->length === 0) {
            return null;
        }

        return $nodes->item(0);
    }

    /**
     * Place in an array all attributes of a specific DOMElement
     *
     * @param DOMElement $node
     * @return array
     */
    private function domElementToArray(DOMElement $node)
    {
        $data = [];

        foreach ($node->attributes as $attribute) {
            $key   = Str::slug($attribute->localName);
            $value = $this->cleanupString($attribute->nodeValue);

            $data[$key] = $value;
        }

        return $data;
    }

    /**
     * Ensure specified url is absolute by using a base URL defined earlier
     *
     * @param string $source
     * @return string
     */
    protected function makeUrlAbsolute($source)
    {
        $baseUri     = UriHttp::createFromString((string) $this->response->effectiveUri());
        $relativeUri = UriHttp::createFromString($source);
        $newUri      = UriResolver::resolve($relativeUri, $baseUri);

        return (string) $newUri;
    }

    /**
     * Find document's title
     */
    private function findTitle()
    {
        $node = $this->findFirstNode('//head/title');

        if (empty($node)) {
            return null;
        }

        $this->title = $this->cleanupString($node->nodeValue);
    }

    /**
     * Find and parse meta tags
     */
    private function findMetaTags()
    {
        $nodes = $this->findNodes('//head/meta');

        $this->metaTags = [];

        foreach ($nodes as $node) {
            $this->parseMetaTag($node);
        }

        $this->metaTags = collect($this->metaTags)->sortKeys()->all();

        //TODO: Format description
        $this->description = data_get($this->metaTags, 'meta.Description.content');
    }

    /**
     * Parse a meta tag and return a formated array
     *
     * @param DOMElement $node
     */
    private function parseMetaTag(DOMElement $node)
    {
        $data = $this->domElementToArray($node);

        if (empty($data)) {
            return;
        }

        $group = 'nonStandard';
        $name  = null;

        if (!empty($data['charset'])) {
            $group = 'charset';
            $name  = 'charset';
        } elseif (!empty($data['name'])) {
            $group = 'meta';
            $name  = $data['name'];

            $data['originalName'] = $data['name'];

            unset($data['name']);
        } elseif (!empty($data['property'])) {
            $group = 'properties';
            $name  = $data['property'];

            $data['originalName'] = $data['property'];

            unset($data['property']);
        } elseif (!empty($data['http-equiv'])) {
            $group = 'pragma';
            $name  = $data['http-equiv'];

            $data['originalName'] = $data['http-equiv'];

            unset($data['http-equiv']);
        }

        $name = Str::studly(str_replace(':', '_', $name));

        if (!empty($name)) {
            switch ($name) {
                // Handle specific meta tag formatting here
                default:
                    $this->metaTags[$group][$name] = $data;
                    break;
            }
        } else {
            $this->metaTags[$group][] = $data;
        }
    }

    /**
     * Find and parse link tags
     */
    private function findLinkTags()
    {
        $nodes = $this->findNodes('//head/link');

        $this->linkTags = [];

        foreach ($nodes as $node) {
            $this->parseLinkTag($node);
        }

        $this->linkTags = collect($this->linkTags)->sortKeys()->all();
    }

    /**
     * Parse a link tag and return a formated array
     *
     * @param DOMElement $node
     * @return array
     */
    private function parseLinkTag(DOMElement $node)
    {
        $data = $this->domElementToArray($node);

        if (empty($data)) {
            return;
        }

        $group = 'Others';

        if (!empty($data['rel'])) {
            $group = Str::studly($data['rel']);
        }

        $this->linkTags[$group][] = $data;
    }

    /**
     * Fetch all link tags marked as being a favicon, then determine which one
     * is best suited to be the one.
     */
    private function findBestFavicon()
    {
        $defaultFaviconUrl = $this->makeUrlAbsolute('/favicon.ico');
        $potentialIcons    = [];

        $links = $this->linkTags;

        foreach ($links as $group => $tags) {
            foreach ($tags as $tag) {
                if (!empty($tag['rel']) && in_array($tag['rel'], config('cyca.faviconRels'))) {
                    $potentialIcons[] = $tag['href'];
                }
            }
        }

        $potentialIcons[] = $defaultFaviconUrl;

        $topWidth     = 0;
        $selectedIcon = null;

        foreach ($potentialIcons as $potentialIcon) {
            $url = $this->makeUrlAbsolute($potentialIcon);

            try {
                $response = Http::timeout(10)->get($url);
            } catch (\Exception $ex) {
                report($ex);

                continue;
            }

            if (!$response->ok()) {
                continue;
            }

            $body     = $response->body();
            $filePath = sprintf('%s/favicon_%s', $this->getStoragePath(), md5($body));

            Storage::put($filePath, $body);

            $mimetype = Storage::mimetype($filePath);

            if (!$this->isValidFavicon($body, $mimetype)) {
                Storage::delete($filePath);
                continue;
            }

            $width = $this->getImageWidth($body, $mimetype);

            if ($width >= $topWidth) {
                $topWidth     = $width;
                $selectedIcon = $filePath;
            } else {
                Storage::delete($filePath);
            }
        }

        if (!empty($selectedIcon)) {
            $this->favicon_path = $selectedIcon;
        }
    }

    /**
     * Determine if favicon has a valid mime type
     *
     * @param string $mimetype
     * @return boolean
     */
    private function isValidFavicon($body, $mimetype)
    {
        if (!in_array($mimetype, config('cyca.faviconTypes'))) {
            return false;
        }

        return $this->isValidImage($body, $mimetype);
    }

    /**
     * Determine if favicon is a valid image. Mime type is used to adjust tests
     *
     * @param string $mimeType
     * @return boolean
     */
    private function isValidImage($body, $mimeType)
    {
        switch ($mimeType) {
            case 'image/x-icon':
            case 'image/vnd.microsoft.icon':
                $loader = new IcoFileService;

                try {
                    $loader->extractIcon($body, 16, 16);
                } catch (\Exception $ex) {
                    return false;
                }

                return true;
            case 'image/svg':
            case 'image/svg+xml':
                $im = new Imagick();

                try {
                    $im->readImageBlob($body);
                } catch (\Exception $ex) {
                    $im->destroy();

                    report($ex);

                    return false;
                }

                return true;
            default:
                $res = @imagecreatefromstring($body);

                if (!$res) {
                    return false;
                }

                return true;
        }
    }

    /**
     * Obtain width of image
     *
     * @param string $mimeType
     * @return int
     */
    private function getImageWidth($body, $mimeType)
    {
        switch ($mimeType) {
            case 'image/x-icon':
            case 'image/vnd.microsoft.icon':
                return 16;
            case 'image/svg':
            case 'image/svg+xml':
                return 1024;
            default:
                $infos = @getimagesizefromstring($body);

                if (!$infos) {
                    return 0;
                }

                return $infos[0];
        }
    }

    /**
     * Discover feeds for this document, store them and link them
     */
    protected function discoverFeeds()
    {
        $toSync = $this->feeds()->get()->pluck('id')->all();

        $alternateLinks = data_get($this->linkTags, 'Alternate', []);

        // Hard guessing some paths (.rss, ./rss. ./.rss for instance)
        $potentialNames = ['feed', 'rss', 'atom'];

        foreach ($potentialNames as $potentialName) {
            $alternateLinks[] = [
                'type' => 'application/xml',
                'href' => sprintf('.%s', $potentialName),
            ];

            $alternateLinks[] = [
                'type' => 'application/xml',
                'href' => sprintf('./%s', $potentialName),
            ];

            $alternateLinks[] = [
                'type' => 'application/xml',
                'href' => sprintf('./.%s', $potentialName),
            ];
        }

        foreach ($alternateLinks as $alternateLink) {
            if (empty($alternateLink['type']) || !in_array($alternateLink['type'], config('cyca.feedTypes'))) {
                continue;
            }

            $url = $this->makeUrlAbsolute($alternateLink['href']);

            $client = new SimplePie();

            $client->force_feed(true);
            $client->set_feed_url($url);
            $client->set_stupidly_fast(true);
            $client->enable_cache(false);

            if (!$client->init()) {
                continue;
            }

            $feed = Feed::firstOrCreate(['url' => $url]);

            if (!in_array($feed->id, $toSync)) {
                $toSync[] = $feed->id;
            }
        }

        $this->feeds()->sync($toSync);
    }
}
