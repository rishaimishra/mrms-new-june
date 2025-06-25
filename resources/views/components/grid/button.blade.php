<button type="button" 
        @if(isset($button['url']))
            onclick="window.location='{{ $button['url']($item) }}'"
        @endif
        class="item"
        data-toggle="tooltip" data-placement="top" title=""
        data-original-title="{{ $button['label'] }}">
    <i class="material-icons">{{ $button['icon'] ?? strtolower($button['label']) }}</i>

</button>
