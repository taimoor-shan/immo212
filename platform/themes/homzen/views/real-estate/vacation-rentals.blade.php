@php
    Theme::layout('full-width');
    Theme::set('pageTitle', __('Vacation Rentals'));
@endphp

@include(Theme::getThemeNamespace('views.real-estate.partials.listing'), [
    'actionUrl' => route('public.vacation-rentals'),
    'ajaxUrl' => route('public.vacation-rentals'),
    'mapUrl' => route('public.ajax.vacation-rentals.map'),
    'itemLayout' => request()->input('layout', 'grid'),
    'layout' => theme_option('real_estate_vacation_rental_listing_layout', 'top-map'),
    'perPages' => RealEstateHelper::getVacationRentalsPerPageList(),
    'filterViewPath' => Theme::getThemeNamespace('views.real-estate.partials.filters.vacation-rental-search-box'),
    'itemsViewPath' => Theme::getThemeNamespace('views.real-estate.vacation-rentals.index'),
    'vacationRentals' => $vacationRentals,
])

@include(Theme::getThemeNamespace('views.real-estate.partials.vacation-rental-map-content'))

@include(Theme::getThemeNamespace('views.real-estate.partials.property-map-content'))
