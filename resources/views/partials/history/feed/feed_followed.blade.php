@lang(':user follows feed :feed', [
    'user' => (string) view('partials.user', ['user' => $user]),
    'feed' => (string) view('partials.feed', ['feed' => $feed])
])