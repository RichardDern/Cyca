@lang(':user created the group :group', [
'user' => (string) view('partials.user', ['user' => $user]),
'group' => (string) view('partials.group', ['group' => $group])
])
