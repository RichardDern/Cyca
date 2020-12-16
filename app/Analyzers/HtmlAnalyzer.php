<?php

namespace App\Analyzers;

use App\Models\Feed;
use DomDocument;
use DOMElement;
use DOMXPath;
use Elphin\IcoFileLoader\IcoFileService;
use Illuminate\Support\Facades\Http;
use SimplePie;
use Storage;
use Str;

/**
 * Extract information from a HTML file.
 */
class HtmlAnalyzer extends Analyzer
{
    /**
     * Provides temporary access to DOM document to analyzers.
     *
     * @var DOMDocument
     */
    private $domDocument;

    /**
     * Provides temporary access to <meta> tags to analyzers.
     *
     * @var array
     */
    private $metaTags = [];

    /**
     * Provides temporary access to <link> tags to analyzers.
     *
     * @var array
     */
    private $linkTags = [];

    /**
     * Analyzes document.
     */
    public function analyze()
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
     * Create a DOM document from document's body.
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
     * Find nodes corresponding to specified XPath query.
     *
     * @param string $xpathQuery
     *
     * @return DomNodeList
     */
    protected function findNodes($xpathQuery)
    {
        $xpath = new DOMXPath($this->domDocument);

        return $xpath->query($xpathQuery);
    }

    /**
     * Find first node corresponding to specified XPath query.
     *
     * @param string $xpathQuery
     *
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
     * Discover feeds for this document, store them and link them.
     */
    protected function discoverFeeds()
    {
        $toSync = $this->document->feeds()->get()->pluck('id')->all();

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

            try {
                $url = \App\Helpers\Url::makeUrlAbsolute($this->response->effectiveUri(), $alternateLink['href']);
            } catch (\Exception $ex) {
                // Malformed URL
                continue;
            }

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

        $this->document->feeds()->sync($toSync);
    }

    /**
     * Place in an array all attributes of a specific DOMElement.
     *
     * @return array
     */
    private function domElementToArray(DOMElement $node)
    {
        $data = [];

        foreach ($node->attributes as $attribute) {
            $key   = Str::slug($attribute->localName);
            $value = \App\Helpers\Cleaner::cleanupString($attribute->nodeValue);

            $data[$key] = $value;
        }

        return $data;
    }

    /**
     * Find document's title.
     */
    private function findTitle()
    {
        $node = $this->findFirstNode('//head/title');

        if (empty($node)) {
            return null;
        }

        $this->document->title = \App\Helpers\Cleaner::cleanupString($node->nodeValue, true, true);
    }

    /**
     * Find and parse meta tags.
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
        $this->document->description = data_get($this->metaTags, 'meta.Description.content');
    }

    /**
     * Parse a meta tag and return a formated array.
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
     * Find and parse link tags.
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
     * Parse a link tag and return a formated array.
     *
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
        $defaultFaviconUrl = \App\Helpers\Url::makeUrlAbsolute($this->response->effectiveUri(), '/favicon.ico');
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
            $url = \App\Helpers\Url::makeUrlAbsolute($this->response->effectiveUri(), $potentialIcon);

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
            $filePath = sprintf('%s/favicon_%s', $this->document->getStoragePath(), md5($body));

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
            $this->document->favicon_path = $selectedIcon;
        }
    }

    /**
     * Determine if favicon has a valid mime type.
     *
     * @param string $mimetype
     * @param mixed  $body
     *
     * @return bool
     */
    private function isValidFavicon($body, $mimetype)
    {
        if (!in_array($mimetype, config('cyca.faviconTypes'))) {
            return false;
        }

        return $this->isValidImage($body, $mimetype);
    }

    /**
     * Determine if favicon is a valid image. Mime type is used to adjust tests.
     *
     * @param string $mimeType
     * @param mixed  $body
     *
     * @return bool
     */
    private function isValidImage($body, $mimeType)
    {
        switch ($mimeType) {
            case 'image/x-icon':
            case 'image/vnd.microsoft.icon':
                $loader = new IcoFileService();

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
     * Obtain width of image.
     *
     * @param string $mimeType
     * @param mixed  $body
     *
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
}
