<a href="mailto:{{ $user['email'] }}"><svg
        fill="currentColor"
        width="16"
        height="16"
        class="favicon folder-account inline"
    >
        <use xlink:href="{{ $iconsFileUrl }}#account" />
    </svg>{{ $user['name'] }}</a>