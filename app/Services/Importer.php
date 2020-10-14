<?php

namespace App\Services;

use App\Contracts\ImportAdapter;
use App\Models\Document;
use App\Models\Feed;
use App\Models\Folder;
use App\Models\Highlight;
use App\Models\IgnoredFeed;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Imports data in Cyca
 */
class Importer
{
    /**
     * Adapter used to import data
     * @var \App\Contracts\ImportAdapter
     */
    protected $importAdapter = null;

    /**
     * User to import data for
     * @var \App\Models\User
     */
    protected $forUser = null;

    /**
     * Folder to import bookmarks and feeds to
     * @var \App\Models\Folder
     */
    protected $inFolder = null;

    /**
     * Should we import highlights as well ?
     * @var boolean
     */
    protected $withHighlights = true;

    /**
     * Data to be imported as an array
     * @var array
     */
    protected $dataArray = [];

    /**
     * Indicates which adapter to use for importation
     *
     * @param string $adapterName
     * @return self
     */
    public function using($adapterName)
    {
        $className = config(sprintf('importers.adapters.%s.adapter', $adapterName));

        if (empty($className)) {
            abort(422, sprintf('Unknown import adapter %s', $className));
        }

        $this->importAdapter = new $className();

        return $this;
    }

    /**
     * Defines which user to import data for. If not defined, user will be
     * extracted from request.
     *
     * @param \App\Models\User $user
     * @return self
     */
    public function forUser(User $user)
    {
        $this->forUser = $user;

        return $this;
    }

    /**
     * Defines in which folder data will be imported to. If not defined, root
     * folder attached to specified user will be used.
     *
     * @param \App\Models\Folder $folder
     * @return self
     */
    public function inFolder(Folder $folder)
    {
        $this->inFolder = $folder;

        return $this;
    }

    /**
     * Should we ignore highlights during import ? By default, highlights will
     * be imported as well
     *
     * @return self
     */
    public function withoutHighlights()
    {
        $this->withHighlights = false;

        return $this;
    }

    /**
     * Import data from specified file. Must be a valid json file, valid from
     * Cyca's architecture point of view.
     *
     * @param string $path Full path to file to import
     * @return self
     */
    public function fromFile($path)
    {
        if (empty($this->forUser)) {
            abort(422, "Target user not specified");
        }

        $contents = file_get_contents($path);

        if (empty($contents)) {
            abort(422, "File does not exists");
        }

        $this->dataArray = json_decode($contents, true);

        return $this;
    }

    /**
     * Import data using current request informations
     *
     * @param \Illuminate\Http\Request $request
     * @return self
     */
    public function fromRequest(Request $request)
    {
        if (empty($this->importAdapter)) {
            $this->using($request->input('importer'));
        }

        if (empty($this->importAdapter)) {
            abort(422, "An import adapter must be specified");
        }

        if (empty($this->forUser)) {
            $this->forUser = $request->user();
        }

        $this->dataArray = $this->importAdapter->importFromRequest($request);

        return $this;
    }

    /**
     * Perform the import
     */
    public function import()
    {
        if (empty($this->inFolder)) {
            $this->inFolder = $this->forUser->folders()->ofType('root')->first();
        }

        if ($this->withHighlights && !empty($this->dataArray['highlights'])) {
            $this->importHighlights($this->dataArray['highlights']);
        }

        if (!empty($this->dataArray['bookmarks'])) {
            $this->importBookmarks($this->dataArray['bookmarks']);
        }
    }

    /**
     * Import highlights from specified array
     *
     * @param array $highlights
     */
    protected function importHighlights($highlights)
    {
        foreach ($highlights as $highlightData) {
            $highlight = Highlight::where('user_id', $this->forUser->id)->where('expression', $highlightData['expression'])->first();

            if (!$highlight) {
                $highlight = new Highlight();

                $highlight->user_id    = $this->forUser->id;
                $highlight->expression = $highlightData['expression'];
                $highlight->color      = $highlightData['color'];

                $highlight->save();
            }
        }
    }

    /**
     * Import bookmarks from specified array
     *
     * @param array $bookmarks
     */
    protected function importBookmarks($bookmarks)
    {
        $this->importDocuments($this->inFolder, $bookmarks['documents'] ?: []);
        $this->importFolders($this->inFolder, $bookmarks['folders'] ?: []);
    }

    /**
     * Import folders
     *
     * @param \App\Models\Folder Destination folder
     * @param array $foldersData Array of sub-folders definitions
     */
    protected function importFolders($folder, $foldersData)
    {
        foreach ($foldersData as $folderData) {
            $children = $this->forUser->folders()->save(new Folder([
                'title'     => $folderData['title'],
                'parent_id' => $folder->id,
            ]));

            $this->importDocuments($children, $folderData['documents']);
            $this->importFolders($children, $folderData['folders']);
        }
    }

    /**
     * Import documents
     *
     * @param \App\Models\Folder Destination folder
     * @param array $documentsData Array of documents definitions
     */
    protected function importDocuments($folder, $documentsData)
    {
        foreach ($documentsData as $docData) {
            if (empty($docData['url'])) {
                continue;
            }

            $url      = urldecode($docData['url']);
            $document = Document::firstOrCreate(['url' => $url]);

            $this->importFeeds($document, $docData['feeds']);

            $folder->documents()->save($document, [
                'initial_url' => $url,
            ]);
        }
    }

    /**
     * Import feeds
     *
     * @param \App\Models\Document $document Destination document
     * @param array $feedsData Array of feeds definitions
     */
    protected function importFeeds($document, $feedsData)
    {
        $feedsToAttach = $document->feeds()->get()->pluck('id')->all();

        foreach ($feedsData as $feedData) {
            if (empty($feedData['url'])) {
                continue;
            }

            $feedUrl = urldecode($feedData['url']);

            $feed = Feed::firstOrCreate(['url' => $feedUrl]);

            $feedsToAttach[] = $feed->id;

            if ($feedData['is_ignored']) {
                $ignoredFeed = IgnoredFeed::where('user_id', $this->forUser->id)->where('feed_id', $feed->id)->first();

                if (!$ignoredFeed) {
                    $ignoredFeed = new IgnoredFeed();

                    $ignoredFeed->user()->associate($this->forUser);
                    $ignoredFeed->feed()->associate($feed);

                    $ignoredFeed->save();
                }
            }
        }

        $document->feeds()->sync($feedsToAttach);
    }
}
