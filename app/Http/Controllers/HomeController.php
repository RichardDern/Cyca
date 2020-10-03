<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Folder;
use App\Models\Document;
use App\Models\Feed;
use App\Models\IgnoredFeed;
use App\Facades\ThemeManager;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }

    /**
     * Show user's account page
     */
    public function account()
    {
        return view('account');
    }

    /**
     * Theme selection page
     */
    public function theme() {
        $availableThemes = ThemeManager::listAvailableThemes();

        return view('account.themes')->with(['availableThemes' => $availableThemes]);
    }

    public function getThemes() {
        return ThemeManager::listAvailableThemes();
    }

    /**
     * Save theme to user's profile
     */
    public function setTheme(Request $request) {
        $availableThemes = ThemeManager::listAvailableThemes();

        if(!array_key_exists($request->input('theme'), $availableThemes)) {
            abort(422);
        }

        $request->user()->theme = $request->input('theme');
        $request->user()->save();

        return redirect()->back();
    }

    /**
     * Export user's data
     */
    public function export(Request $request) {
        $root = $request->user()->folders()->ofType('root')->first();
        $rootArray = [
            'documents' => [],
            'folders' => $this->exportTree($root->children()->get())
        ];

        foreach($root->documents()->get() as $document) {
            $documentArray = [
                'url' => $document->url,
                'feeds' => []
            ];

            foreach($document->feeds()->get() as $feed) {
                $documentArray['feeds'] = [
                    'url' => $feed->url,
                    'is_ignored' => $feed->is_ignored
                ];
            }

            $rootArray['documents'][] = $documentArray;
        }

        return response()->streamDownload(function ()use ($rootArray) {
            echo json_encode($rootArray);
        }, sprintf('%s - Export.json', $request->user()->name), [
            'Content-Type' => 'application/x-json'
        ]);
    }

    /**
     * Export a single tree branch
     */
    protected function exportTree($folders) {
        $array = [];

        foreach($folders as $folder) {
            $folderArray = [
                'title' => $folder->title,
                'documents' => [],
                'folders' => []
            ];

            foreach($folder->documents()->get() as $document) {
                $documentArray = [
                    'url' => $document->url,
                    'feeds' => []
                ];

                foreach($document->feeds()->get() as $feed) {
                    $documentArray['feeds'][] = [
                        'url' => $feed->url,
                        'is_ignored' => $feed->is_ignored
                    ];
                }

                $folderArray['documents'][] = $documentArray;
            }

            $folderArray['folders'] = $this->exportTree(($folder->children()->get()));

            $array[] = $folderArray;
        }

        return $array;
    }

    /**
     * Show the import form
     */
    public function showImportForm() {
        return view('import');
    }

    /**
     * Import a file
     */
    //TODO: Handle different input file types, such as OPML and Netscape bookmarks
    public function import(Request $request) {
        if(!$request->hasFile('file')) {
            abort(422, "A file is required");
        }

        if (!$request->file('file')->isValid()) {
            abort(422, "Invalid file");
        }

        $contents = file_get_contents((string)$request->file('file'));
        $data = [];

        try {
            $data = json_decode($contents, true);
        } catch(\Exception $ex) {

        }

        $this->importData($data);

        return redirect()->route('import');
    }

    protected function importData($data) {
        $user = request()->user();
        $root = $user->folders()->ofType('root')->first();

        $this->importDocuments($root, $data['documents']);
        $this->importFolders($root, $data['folders']);
    }

    protected function importFolders($folder, $foldersData) {
        $user = request()->user();

        foreach($foldersData as $folderData) {
            $children = $user->folders()->save(new Folder([
                'title'     => $folderData['title'],
                'parent_id' => $folder->id,
            ]));

            $this->importDocuments($children, $folderData['documents']);
            $this->importFolders($children, $folderData['folders']);
        }
    }

    protected function importDocuments($folder, $documentsData) {
        foreach($documentsData as $docData) {
            if(empty($docData['url'])) {
                continue;
            }

            $url = urldecode($docData['url']);
            $document = Document::firstOrCreate(['url' => $url]);

            $this->importFeeds($document, $docData['feeds']);

            $folder->documents()->save($document, [
                'initial_url' => $url,
            ]);
        }
    }

    protected function importFeeds($document, $feedsData) {
        $user = request()->user();
        $feedsToAttach = $document->feeds()->get()->pluck('id')->all();

        foreach($feedsData as $feedData) {
            if(empty($feedData['url'])) {
                continue;
            }

            $feedUrl = urldecode($feedData['url']);

            $feed = Feed::firstOrCreate(['url' => $feedUrl]);

            $feedsToAttach[] = $feed->id;

            if($feedData['is_ignored']) {
                $ignoredFeed = IgnoredFeed::where('user_id', $user->id)->where('feed_id', $feed->id)->first();

                if(!$ignoredFeed) {
                    $ignoredFeed = new IgnoredFeed();

                    $ignoredFeed->user()->associate($user);
                    $ignoredFeed->feed()->associate($feed);

                    $ignoredFeed->save();
                }
            }
        }

        $document->feeds()->sync($feedsToAttach);
    }
}
