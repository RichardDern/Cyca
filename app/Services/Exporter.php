<?php

namespace App\Services;

use App\Models\Folder;
use App\Models\User;

/**
 * Exports data from Cyca
 */
class Exporter
{
    /**
     * Should we include highlights in the export ?
     * @var boolean
     */
    protected $withHighlights = true;

    /**
     * Should we include bookmarks in the export ?
     * @var boolean
     */
    protected $withBookmarks = true;

    /**
     * User to import data for
     * @var \App\Models\User
     */
    protected $forUser = null;

    /**
     * Folder to export bookmarks and feeds from
     * @var \App\Models\Folder
     */
    protected $fromFolder = null;

    /**
     * Should we ignore highlights during export ? By default, highlights will
     * be exported as well
     *
     * @return self
     */
    public function withoutHighlights()
    {
        $this->withHighlights = false;

        return $this;
    }

    /**
     * Should we ignore bookmarks during export ? By default, bookmarks will
     * be exported as well
     *
     * @return self
     */
    public function withoutBookmarks()
    {
        $this->withoutBookmarks = false;

        return $this;
    }

    /**
     * Defines which user to export data for. If not defined, user will be
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
     * Defines from which folder data will be exported. If not defined, root
     * folder attached to specified user will be used.
     *
     * @param \App\Models\Folder $folder
     * @return self
     */
    public function fromFolder(Folder $folder)
    {
        $this->fromFolder = $folder;

        return $this;
    }

    /**
     * Export specified user's data into a PHP array
     *
     * @return array
     */
    public function export()
    {
        if (empty($this->fromFolder)) {
            $this->fromFolder = $this->forUser->folders()->ofType('root')->first();
        }

        $rootArray = [
            'documents' => [],
            'folders'   => $this->exportTree($this->fromFolder->children()->get()),
        ];

        foreach ($this->fromFolder->documents()->get() as $document) {
            $documentArray = [
                'url'   => $document->url,
                'feeds' => [],
            ];

            foreach ($document->feeds()->get() as $feed) {
                $documentArray['feeds'] = [
                    'url'        => $feed->url,
                    'is_ignored' => $feed->is_ignored,
                ];
            }

            $rootArray['documents'][] = $documentArray;
        }

        return [
            'highlights' => $this->forUser->highlights()->select(['expression', 'color'])->get(),
            'bookmarks'  => $rootArray,
        ];
    }

    /**
     * Export a single tree branch
     */
    protected function exportTree($folders)
    {
        $array = [];

        foreach ($folders as $folder) {
            $folderArray = [
                'title'     => $folder->title,
                'documents' => [],
                'folders'   => [],
            ];

            foreach ($folder->documents()->get() as $document) {
                $documentArray = [
                    'url'   => $document->url,
                    'feeds' => [],
                ];

                foreach ($document->feeds()->get() as $feed) {
                    $documentArray['feeds'][] = [
                        'url'        => $feed->url,
                        'is_ignored' => $feed->is_ignored,
                    ];
                }

                $folderArray['documents'][] = $documentArray;
            }

            $folderArray['folders'] = $this->exportTree(($folder->children()->get()));

            $array[] = $folderArray;
        }

        return $array;
    }
}
