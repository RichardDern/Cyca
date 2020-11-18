@lang(':user removed the bookmark to :document from :breadcrumbs', [
'user' => (string) view('partials.user', ['user' => $user]),
'document' => (string) view('partials.document', ['document' => $document]),
'breadcrumbs' => $breadcrumbs
])
