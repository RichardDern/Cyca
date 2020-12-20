<img src="{{ $url }}" alt="{{ $url }}" />

@foreach(collect($exif)->sortKeys() as $section => $data)
<details>
    <summary>{{ $section }}</summary>
    <div class="vertical list striped items-rounded compact">
        @foreach(collect($data)->sortKeys() as $key => $value)
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
@endforeach