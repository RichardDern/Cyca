@if($breadcrumbs)
@lang(':user deleted folder :folder from :breadcrumbs', [
    'user' => (string) view('partials.user', ['user' => $user]),
    'folder' => (string) view('partials.folder', ['folder' => $folder]),
    'breadcrumbs' => $breadcrumbs
])
@else
@lang(':user deleted folder :folder', [
    'user' => (string) view('partials.user', ['user' => $user]),
    'folder' => (string) view('partials.folder', ['folder' => $folder])
])
@endif