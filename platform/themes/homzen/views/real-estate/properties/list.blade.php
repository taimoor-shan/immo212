@php
    $itemsPerRow ??= 2;
@endphp

<div class="row row-cols-1 row-cols-lg-{{ $itemsPerRow }}">
    @foreach($properties as $property)
        <div class="col">
            @if($property->type == \Botble\RealEstate\Enums\PropertyTypeEnum::VACATION_RENTAL)
                @include(Theme::getThemeNamespace('views.real-estate.properties.item-vacation-rental'))
            @else
                @include(Theme::getThemeNamespace('views.real-estate.properties.item-list'))
            @endif
        </div>
    @endforeach
</div>
