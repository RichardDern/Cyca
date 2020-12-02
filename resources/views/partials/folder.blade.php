<div class="inline-block rounded dark:bg-gray-800 px-2 py-1">
    <div class="flex items-center space-x-1">
        <svg fill="currentColor" width="16" height="16" class="{{ $folder['iconColor'] }}">
            <use xlink:href="{{ asset('images/icons.svg') }}#{{ $folder['icon'] }}" />
        </svg>
        <span>{{ (!empty($folder['type']) && $folder['type'] !== 'folder') ? __($folder['title']) : $folder['title'] }}</span>
    </div>
</div>