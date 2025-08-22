@php
    Theme::layout('full-width');
    Theme::set('breadcrumbEnabled', 'no');

    Theme::asset()->usePath()->add('fancybox', 'plugins/fancybox/jquery.fancybox.min.css');
    Theme::asset()->container('footer')->usePath()->add('fancybox', 'plugins/fancybox/jquery.fancybox.min.js');
    Theme::asset()->usePath()->add('leaflet', 'plugins/leaflet/leaflet.css');
    Theme::asset()->container('footer')->usePath()->add('leaflet', 'plugins/leaflet/leaflet.js');

    Theme::set('pageTitle', $vacationRental->name);
@endphp


@include(Theme::getThemeNamespace("views.real-estate.single-layouts.vacation-rental-style"))

<template id="map-popup-content">
    <div class="map-listing-item">
        <div class="inner-box">
            <div class="image-box">
                <a href="{{ $vacationRental->url }}">
                    {{ RvMedia::image($vacationRental->image_thumb, $vacationRental->name) }}
                </a>
                <span class="flag-tag vacation-rental">{{ __('Vacation Rental') }}</span>
            </div>
            <div class="content">
                @if($vacationRental->category)
                    <div class="property-type-badge">
                        <span class="flag-tag style-2">{{ $vacationRental->category->name }}</span>
                    </div>
                @endif
                <!-- Original title (hidden but kept for accessibility/SEO) -->
                <div class="title" style="display: none;">
                    <a href="{{ $vacationRental->url }}" title="{{ $vacationRental->name }}">
                        {{ $vacationRental->name }}
                    </a>
                </div>
                <!-- Duplicate title element showing price instead -->
                <div class="title">
                    <span>{{ $vacationRental->price_html }}</span>
                </div>
                <!-- Meta-list for vacation rental specific info -->
                <ul class="list-info">
                    @if($vacationRental->minimum_stay)
                        <li>
                            <x-core::icon name="ti ti-calendar" />
                            {{ $vacationRental->minimum_stay }} {{ $vacationRental->minimum_stay == 1 ? __('night min') : __('nights min') }}
                        </li>
                    @endif
                    @if($vacationRental->maximum_guests)
                        <li>
                            <x-core::icon name="ti ti-users" />
                            {{ $vacationRental->maximum_guests }} {{ __('guests max') }}
                        </li>
                    @endif
                    <li>
                        <x-core::icon name="ti ti-currency-dollar" />
                        {{ __('per night') }}
                    </li>
                </ul>
                <!-- Location -->
                @if($vacationRental->short_address)
                    <p class="location">
                        {{ $vacationRental->short_address }}
                    </p>
                @endif
            </div>
        </div>
    </div>
</template>

<style>
.vacation-rental-specific {
    /* Add any vacation rental specific styles here */
}

.flag-tag.vacation-rental {
    background-color: #28a745;
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
}

.booking-widget {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
}

.booking-widget-title {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 15px;
    color: #212529;
}

.agent-info {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 20px;
}

.agent-info-title {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 15px;
    color: #212529;
}

.agent-details {
    text-align: center;
}

.agent-avatar {
    margin-bottom: 10px;
}

.agent-avatar img {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    object-fit: cover;
}

.agent-name {
    font-weight: 600;
    margin-bottom: 10px;
    color: #212529;
}

.agent-phone,
.agent-email {
    margin-bottom: 5px;
}

.agent-phone a,
.agent-email a {
    color: #6c757d;
    text-decoration: none;
}

.agent-phone a:hover,
.agent-email a:hover {
    color: #007bff;
}

.additional-fee {
    display: flex;
    justify-content: space-between;
    margin-bottom: 5px;
    font-size: 14px;
}

.fee-label {
    color: #6c757d;
}

.fee-amount {
    font-weight: 500;
    color: #212529;
}

.price-per-night {
    margin-bottom: 10px;
}

.price-per-night .price {
    font-size: 24px;
    font-weight: 700;
    color: #007bff;
}

.price-per-night .per-night {
    color: #6c757d;
    margin-left: 5px;
}

.item-info {
    margin-bottom: 10px;
}

.item-info-label {
    font-size: 12px;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.item-info-value {
    font-weight: 600;
    color: #212529;
}

.item-feature {
    display: flex;
    align-items: center;
    margin-bottom: 8px;
    font-size: 14px;
}

.item-feature i {
    margin-right: 8px;
    color: #007bff;
    width: 16px;
}
</style>
