@php
    $itemsPerRow ??= 2;
@endphp

<div class="row row-cols-1 row-cols-lg-{{ $itemsPerRow }}">
    @foreach($vacationRentals as $vacationRental)
        <div class="col">
            @include(Theme::getThemeNamespace('views.real-estate.vacation-rentals.item-list'))
        </div>
    @endforeach
</div>
