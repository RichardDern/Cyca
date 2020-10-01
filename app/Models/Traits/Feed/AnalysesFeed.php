<?php

namespace App\Models\Traits\Feed;

use App\Models\FeedItem;
use App\Models\FeedItemState;
use App\Models\IgnoredFeed;
use App\Notifications\UnreadItemsChanged;
use DomDocument;
use DOMXPath;
use Illuminate\Support\Facades\Storage;
use SimplePie;
use \ForceUTF8\Encoding as UTF8;
use Illuminate\Support\Facades\Notification;

trait AnalysesFeed
{
    # --------------------------------------------------------------------------
    # ----| Properties |--------------------------------------------------------
    # --------------------------------------------------------------------------

    /**
     * SimplePie client
     *
     * @var SimplePie
     */
    private $client = null;

    # --------------------------------------------------------------------------
    # ----| Methods |-----------------------------------------------------------
    # --------------------------------------------------------------------------

    /**
     * Begin feed analysis
     */
    public function analyze()
    {
        // Don't bother if feed isn't attached to any document anymore
        if($this->documents()->count() === 0) {
            if($this->checked_at->addDays(config('cyca.maxOrphanAge.feed'))->lt(now())) {
                $this->delete();
            }

            return;
        }

        $this->prepareClient();

        if (!$this->client->init()) {
            $this->error = $this->client->error();
            return;
        }

        if ($this->client->subscribe_url() !== $this->url) {
            $this->url = $this->client->subscribe_url();
        }

        $this->title       = $this->cleanupString($this->client->get_title(), true, true);
        $this->description = $this->cleanupString($this->client->get_description());
        $this->checked_at  = now();

        $this->save();

        $this->createItems($this->client->get_items());
    }

    /**
     * Prepare the client
     */
    protected function prepareClient()
    {
        $this->client = new SimplePie();

        Storage::makeDirectory($this->getStoragePath() . '/cache');

        $this->client->force_feed(true);
        $this->client->set_cache_location(storage_path('app/' . $this->getStoragePath() . '/cache'));
        $this->client->set_feed_url($this->url);
    }

    /**
     * Store feed items in database
     *
     * @param array $items
     */
    protected function createItems($items)
    {
        $toSync   = $this->feedItems()->pluck('feed_items.id')->all();
        $newItems = [];

        foreach ($items as $item) {
            $feedItem = FeedItem::where('hash', $item->get_id(true))->first();

            if ($feedItem) {
                continue;
            }

            $feedItem = new FeedItem();

            $feedItem->hash         = $item->get_id(true);
            $feedItem->title        = $this->cleanupString($item->get_title(), true, true);
            $feedItem->url          = $item->get_permalink();
            $feedItem->description  = $this->formatText($item->get_description(true));
            $feedItem->content      = $this->formatText($item->get_content(true));
            $feedItem->published_at = $item->get_gmdate();

            if($feedItem->published_at->addDays(config('cyca.maxOrphanAge.feeditems'))->lt(now())) {
                continue;
            }

            $feedItem->save();

            if (!in_array($feedItem->id, $toSync)) {
                $toSync[] = $feedItem->id;
            }

            $newItems[] = $feedItem;
        }

        $this->feedItems()->sync($toSync);
        $this->createUnreadItems($newItems);
    }

    /**
     * Perform various formatting on specified string
     *
     * @param string $string
     * @param boolean $stripTags If true, HTML tags will be suppressed
     * @param boolean $removeExtraSpaces If true, a regex will be applied to remove unnecessary white-spaces
     * @return string
     */
    protected function cleanupString($string, $stripTags = false, $removeExtraSpaces = false)
    {
        $string = UTF8::toUTF8($string, UTF8::ICONV_TRANSLIT);
        $string = html_entity_decode($string, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        if($removeExtraSpaces) {
            $string = preg_replace('#[\s\t\r\n]+#', ' ', $string);
        }

        if ($stripTags) {
            $string = strip_tags(trim($string));
        }

        $string = trim($string);

        return $string;
    }

    /**
     * Apply various transformations to specified text
     *
     * @param string $text
     * @return string
     */
    protected function formatText($text)
    {
        if (empty($text)) {
            return;
        }

        $text = mb_convert_encoding($text, 'HTML-ENTITIES', 'UTF-8');

        libxml_use_internal_errors(true);

        $domDocument = new DomDocument('1.0', 'UTF-8');

        $domDocument->loadHtml($text, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        libxml_clear_errors();

        $xpath = new DOMXPath($domDocument);

        $anchors = $xpath->query('//a');

        foreach ($anchors as $anchor) {
            $anchor->setAttribute('rel', 'noopener noreferrer');
            $anchor->setAttribute('href', urldecode($anchor->getAttribute('href')));
        }

        $text = $domDocument->saveHTML();
        $text = $this->cleanupString($text);

        return $text;
    }

    protected function createUnreadItems($feedItems)
    {
        $ignoredFeeds = [];
        $usersToNotify = [];

        foreach ($feedItems as $feedItem) {
            $feedItemId = $feedItem->id;

            foreach ($feedItem->feeds()->get() as $feed) {
                $feedId = $feed->id;

                $ignoredFeeds[$feedId] = IgnoredFeed::where('feed_id', $feedId)->get()->pluck('user_id')->all();

                foreach ($feed->documents()->get() as $document) {
                    $documentId = $document->id;

                    foreach ($document->folders()->get() as $folder) {
                        $folderId = $folder->id;
                        $userId   = $folder->user_id;

                        if (in_array($userId, $ignoredFeeds[$feedId])) {
                            continue;
                        }

                        $feedItemState = FeedItemState::where('user_id', $userId)
                            ->where('feed_item_id', $feedItemId)
                            ->first();

                        if (!$feedItemState) {
                            FeedItemState::create([
                                'user_id'      => $userId,
                                'folder_id'    => $folderId,
                                'document_id'  => $documentId,
                                'feed_id'      => $feedId,
                                'feed_item_id' => $feedItemId,
                            ]);

                            $usersToNotify[] = $folder->user;
                        }
                    }
                }
            }
        }

        $this->notifyUsers($usersToNotify);
    }

    protected function notifyUsers($users) {
        Notification::send($users, new UnreadItemsChanged());
    }
}
