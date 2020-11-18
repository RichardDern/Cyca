@lang(':user created a bookmark to :document in :breadcrumbs', [
'user' => (string) view('partials.user', ['user' => $user]),
'document' => (string) view('partials.document', ['document' => $document]),
'breadcrumbs' => $breadcrumbs
])
