<?php

namespace App\Models\Traits\Feed;

use App\Models\FeedItem;
use App\Models\FeedItemState;
use App\Notifications\UnreadItemsChanged;
use DomDocument;
use DOMXPath;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use SimplePie;

trait AnalysesFeed
{
    // --------------------------------------------------------------------------
    // ----| Properties |--------------------------------------------------------
    // --------------------------------------------------------------------------

    /**
     * SimplePie client.
     *
     * @var SimplePie
     */
    private $client;

    // --------------------------------------------------------------------------
    // ----| Methods |-----------------------------------------------------------
    // --------------------------------------------------------------------------

    /**
     * Begin feed analysis.
     */
    public function analyze()
    {
        // Don't bother if feed isn't attached to any document anymore
        if ($this->isOrphan()) {
            if ($this->wasOrphanFor(config('cyca.maxOrphanAge.feed'))) {
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

        $this->title       = \App\Helpers\Cleaner::cleanupString($this->client->get_title(), true, true);
        $this->description = \App\Helpers\Cleaner::cleanupString($this->client->get_description());
        $this->checked_at  = now();

        $this->save();

        $this->createItems($this->client->get_items());
    }

    /**
     * Prepare the client.
     */
    protected function prepareClient()
    {
        $this->client = new SimplePie();

        Storage::makeDirectory($this->getStoragePath().'/cache');

        $this->client->force_feed(true);
        $this->client->set_cache_location(storage_path('app/'.$this->getStoragePath().'/cache'));
        $this->client->set_feed_url($this->url);
    }

    /**
     * Store feed items in database.
     *
     * @param array $items
     */
    protected function createItems($items)
    {
        $toSync   = $this->feedItems()->pluck('feed_items.id')->all();
        $newItems = [];

        foreach ($items as $item) {
            $feedItem = FeedItem::where('hash', $item->get_id(true))->first();

            if (!$feedItem) {
                $feedItem = new FeedItem();

                $feedItem->hash         = $item->get_id(true);
                $feedItem->title        = \App\Helpers\Cleaner::cleanupString($item->get_title(), true, true);
                $feedItem->url          = $item->get_permalink();
                $feedItem->description  = $this->formatText($item->get_description(true));
                $feedItem->content      = $this->formatText($item->get_content(true));
                $feedItem->published_at = $item->get_gmdate();

                if ($feedItem->published_at->addDays(config('cyca.maxOrphanAge.feeditems'))->lt(now())) {
                    continue;
                }

                $feedItem->save();

                $data = collect($item->data)->except([
                    'data',
                    'child',
                ]);

                Storage::put($feedItem->getStoragePath().'/data.json', $data->toJson());
            }

            if (!in_array($feedItem->id, $toSync)) {
                $toSync[] = $feedItem->id;
            }

            $newItems[] = $feedItem;
        }

        $this->feedItems()->sync($toSync);
        $this->createUnreadItems($newItems);
    }

    /**
     * Apply various transformations to specified text.
     *
     * @param string $text
     *
     * @return string
     */
    protected function formatText($text)
    {
        if (empty($text)) {
            return;
        }

        $text = mb_convert_encoding($text, 'HTML-ENTITIES', 'UTF-8');

        if (empty($text)) {
            return;
        }

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

        return \App\Helpers\Cleaner::cleanupString($text);
    }

    protected function createUnreadItems($feedItems)
    {
        $ignoredByUsers   = $this->ignored()->pluck('user_id')->all();
        $documentsChanged = [];
        $foldersChanged   = [];
        $usersToNotify    = [];

        foreach ($this->documents()->get() as $document) {
            $folders = $document->folders()->get();

            foreach ($folders as $folder) {
                if (!array_key_exists($folder->id, $foldersChanged)) {
                    $foldersChanged[$folder->id] = $folder;
                }

                $users = $folder->group->activeUsers()->whereNotIn('users.id', $ignoredByUsers)->get();

                foreach ($users as $user) {
                    if (!array_key_exists($user->id, $usersToNotify)) {
                        $usersToNotify[$user->id] = $user;
                    }

                    foreach ($feedItems as $feedItem) {
                        $feedItemStateData = [
                            'document_id'  => $document->id,
                            'feed_id'      => $this->id,
                            'user_id'      => $user->id,
                            'feed_item_id' => $feedItem->id,
                        ];

                        $feedItemState = FeedItemState::where('user_id', $user->id)
                            ->where('feed_item_id', $feedItem->id)
                            ->first();

                        if (!$feedItemState) {
                            FeedItemState::create($feedItemStateData);

                            if (!in_array($document->id, $documentsChanged)) {
                                $documentsChanged[] = $document->id;
                            }
                        }
                    }
                }
            }
        }

        Notification::send($usersToNotify, new UnreadItemsChanged(['folders' => $foldersChanged, 'documents' => $documentsChanged]));
    }
}
