<div class="w-full h-96">
    <embed src="{{ $url }}" type="application/pdf" height="100%" width="100%" />
</div>

<details>
    <summary>{{ __("Details") }}</summary>
    <div class="vertical list striped items-rounded compact">
        @foreach(collect($details)->sortKeys() as $key => $value)
        <div class="list-item">
            <div class="list-item-title">{{ $key }}</div>
            <div class="list-item-value">
                @if(is_array($value))
                <ul>
                    @foreach($value as $_value)
                    <li>{{ $_value }}</li>
                    @endforeach
                </ul>
                @else
                {{ $value }}
                @endif
            </div>
        </div>
        @endforeach
    </div>
</details>