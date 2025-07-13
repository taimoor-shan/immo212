@php
    $itemsPerRow ??= 3;
@endphp

@if ($properties->isNotEmpty())
    <div class="row row-cols-1 row-cols-sm-2 @if ($itemsPerRow > 2) row-cols-md-{{ $itemsPerRow - 1 }} @endif row-cols-xl-{{ $itemsPerRow }}">
        @foreach($properties as $property)
            <div class="col">
                @if($property->type == \Botble\RealEstate\Enums\PropertyTypeEnum::VACATION_RENTAL)
                    @include(Theme::getThemeNamespace('views.real-estate.properties.item-vacation-rental'))
                @else
                    @include(Theme::getThemeNamespace('views.real-estate.properties.item-grid'))
                @endif
            </div>
        @endforeach
    </div>
@endif
