@php
    $itemsPerRow ??= 3;
@endphp

@if ($vacationRentals->isNotEmpty())
    <div class="row row-cols-1 row-cols-sm-2 @if ($itemsPerRow > 2) row-cols-md-{{ $itemsPerRow - 1 }} @endif row-cols-xl-{{ $itemsPerRow }}">
        @foreach($vacationRentals as $vacationRental)
            <div class="col">
                @include(Theme::getThemeNamespace('views.real-estate.vacation-rentals.item-grid'))
            </div>
        @endforeach
    </div>
@endif
