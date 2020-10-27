@if($breadcrumbs)
@lang(':user created the folder :folder in :breadcrumbs', [
    'user' => (string) view('partials.user', ['user' => $user]),
    'folder' => (string) view('partials.folder', ['folder' => $folder]),
    'breadcrumbs' => $breadcrumbs
])
@else
@lang(':user created the folder :folder', [
    'user' => (string) view('partials.user', ['user' => $user]),
    'folder' => (string) view('partials.folder', ['folder' => $folder])
])
@endif