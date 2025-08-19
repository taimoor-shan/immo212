@php
    Theme::layout('full-width');
    Theme::set('pageTitle', __('Vacation Rentals'));
@endphp

@include(Theme::getThemeNamespace('views.real-estate.partials.listing'), [
    'actionUrl' => route('public.vacation-rentals'),
    'ajaxUrl' => route('public.vacation-rentals'),
    'mapUrl' => '', // Map functionality to be added later
    'itemLayout' => request()->input('layout', 'grid'),
    'layout' => theme_option('real_estate_property_listing_layout', 'top-map'),
    'perPages' => RealEstateHelper::getPropertiesPerPageList(),
    'filterViewPath' => Theme::getThemeNamespace('views.real-estate.partials.filters.property-search-box'),
    'itemsViewPath' => Theme::getThemeNamespace('views.real-estate.vacation-rentals.index'),
])

@include(Theme::getThemeNamespace('views.real-estate.partials.property-map-content'))
