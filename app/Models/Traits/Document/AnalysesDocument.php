<?php

namespace App\Models\Traits\Document;

use App\Models\Bookmark;
use App\Models\Feed;
use Illuminate\Support\Facades\Http;
use SimplePie;
use Storage;

trait AnalysesDocument
{
    /**
     * Provides temporary access to response to analyzers.
     *
     * @var \Illuminate\Http\Client\Response
     */
    protected $response;
    // -------------------------------------------------------------------------
    // ----| Properties |-------------------------------------------------------
    // -------------------------------------------------------------------------

    /**
     * Provides temporary access to document's body to analyzers.
     *
     * @var string
     */
    private $body;

    // -------------------------------------------------------------------------
    // ----| Methods |----------------------------------------------------------
    // -------------------------------------------------------------------------

    /**
     * Begin document analysis.
     */
    public function analyze()
    {
        // Don't bother if document isn't bookmarked anymore
        if ($this->isOrphan()) {
            if ($this->wasOrphanFor(config('cyca.maxOrphanAge.document'))) {
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
     * Copy content of resource at document's URL in "local" storage.
     */
    protected function fetchContent()
    {
        $storageRoot      = $this->getStoragePath();
        $bodyFilename     = $storageRoot.'/body';
        $responseFilename = $storageRoot.'/response.json';
        $debugFilename    = $storageRoot.'/debug';

        Storage::put($debugFilename, null);

        $debugStream = fopen(storage_path('app/'.$debugFilename), 'w');

        try {
            $this->response = Http::withOptions(array_merge([
                'debug' => env('APP_DEBUG') ? $debugStream : false,
            ], config('http_client')))->timeout(30)->get($this->url);

            $this->body = $this->response->body();
        } catch (\Exception $ex) {
            report($ex);
        } finally {
            fclose($debugStream);
        }

        if (!$this->response) {
            return;
        }

        $psrResponse = $this->response->toPsrResponse();

        $responseData = [
            'headers'          => $this->response->headers(),
            'protocol_version' => $psrResponse->getProtocolVersion(),
            'response'         => $this->response,
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
     * corresponding home page URL.
     *
     * @return bool|string Return feed's home page if really a feed, false otherwise
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
     * document.
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
     * @return bool
     */
    protected function existingDocumentsMerged()
    {
        $realUrl = urldecode((string) $this->response->effectiveUri());

        if ($realUrl !== $this->url) {
            $document = self::where('url', $realUrl)->first();

            if ($document) {
                Bookmark::where('document_id', $this->id)->update(['document_id' => $document->id]);

                $allBookmarks = Bookmark::where('document_id', $this->id)->get()->groupBy('folder_id');

                foreach ($allBookmarks as $folderId => $bookmarks) {
                    if ($bookmarks->count() > 1) {
                        array_shift($bookmarks);

                        foreach ($bookmarks as $bookmark) {
                            $bookmark->delete();
                        }
                    }
                }

                return true;
            }

            $this->url = $realUrl;
        }

        return false;
    }

    /**
     * Select analyzers for this particular document then run them.
     */
    protected function runAnalyzers()
    {
        if (array_key_exists($this->mimetype, config('analyzers'))) {
            $this->launchAnalyzerFor($this->mimetype);
        } else {
            // In doubt, launch HtmlAnalyzer
            $this->launchAnalyzerFor('text/html');
        }
    }

    protected function launchAnalyzerFor($mimetype)
    {
        $className = config(sprintf('analyzers.%s', $mimetype));
        $instance  = new $className();

        $instance->setDocument($this)->setBody($this->body)->setResponse($this->response)->analyze();
    }
}
