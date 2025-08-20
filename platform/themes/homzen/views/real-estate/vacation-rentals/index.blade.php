@php
    $itemLayout ??= request()->input('layout', 'grid');
    $itemLayout = in_array($itemLayout, ['grid', 'list']) ? $itemLayout : 'grid';
    $layout ??= theme_option('real_estate_vacation_rental_listing_layout', 'top-map');

    if (! isset($itemsPerRow)) {
        $itemsPerRow = $itemLayout === 'grid' ? 2 : 1;
        if (! in_array($layout, ['top-map', 'without-map'])) {
            $itemsPerRow = $itemLayout === 'grid' ? 2 : 1;
        }
    }
@endphp

@if ($vacationRentals->isNotEmpty())
    @include(Theme::getThemeNamespace("views.real-estate.vacation-rentals.$itemLayout"), compact('itemsPerRow'))
@else
    <div class="alert alert-warning" role="alert">
        {{ __('No vacation rentals found.') }}
    </div>
@endif

@if ($vacationRentals instanceof \Illuminate\Pagination\LengthAwarePaginator && $vacationRentals->hasPages())
    <div class="justify-content-center wd-navigation mt-5">
        {{ $vacationRentals->withQueryString()->links(Theme::getThemeNamespace('partials.pagination')) }}
    </div>
@endif
