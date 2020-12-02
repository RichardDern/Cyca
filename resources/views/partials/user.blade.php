<a href="mailto:{{ $user['email'] }}" class="inline-block"><div class="flex items-center space-x-1"><svg
        fill="currentColor"
        width="16"
        height="16"
        class="text-green-500"
    >
        <use xlink:href="{{ asset('images/icons.svg') }}#account" />
    </svg>{{ $user['name'] }}</div></a>