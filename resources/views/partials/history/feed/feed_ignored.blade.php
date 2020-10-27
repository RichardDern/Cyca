@lang(':user ignores feed :feed', [
    'user' => (string) view('partials.user', ['user' => $user]),
    'feed' => (string) view('partials.feed', ['feed' => $feed])
])