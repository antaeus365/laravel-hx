<div id="{{ $component->getIdentifier() }}" 
     {{ $attributes }}
     @if(request()->header('HX-Request'))
     hx-target="#{{ $component->getIdentifier() }}"
     hx-swap="outerHTML"
     @endif
>
    {{ $slot }}
</div>