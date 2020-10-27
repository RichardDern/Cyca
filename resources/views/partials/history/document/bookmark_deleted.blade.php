@lang(':user removed the bookmark to :document from :folder', [
    'user' => (string) view('partials.user', ['user' => $user]),
    'document' => (string) view('partials.document', ['document' => $document]),
    'folder' => (string) view('partials.folder', ['folder' => $folder])
])