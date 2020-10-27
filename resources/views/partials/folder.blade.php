<span class="inline-folder"><svg
        fill="currentColor"
        width="16"
        height="16"
        class="favicon inline {{ $folder['iconColor'] }}"
    >
        <use xlink:href="{{ $iconsFileUrl }}#{{ $folder['icon'] }}" />
    </svg>{{ (!empty($folder['type']) && $folder['type'] !== 'folder') ? __($folder['title']) : $folder['title'] }}</span>