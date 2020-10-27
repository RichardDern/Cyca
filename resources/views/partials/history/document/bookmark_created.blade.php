@lang(':user created a bookmark to :document in :folder', [
    'user' => (string) view('partials.user', ['user' => $user]),
    'document' => (string) view('partials.document', ['document' => $document]),
    'folder' => (string) view('partials.folder', ['folder' => $folder])
])