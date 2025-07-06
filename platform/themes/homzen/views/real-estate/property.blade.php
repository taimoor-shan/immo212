@php
    Theme::layout('full-width');
    Theme::set('breadcrumbEnabled', 'no');

    Theme::asset()->usePath()->add('fancybox', 'plugins/fancybox/jquery.fancybox.min.css');
    Theme::asset()->container('footer')->usePath()->add('fancybox', 'plugins/fancybox/jquery.fancybox.min.js');
    Theme::asset()->usePath()->add('leaflet', 'plugins/leaflet/leaflet.css');
    Theme::asset()->container('footer')->usePath()->add('leaflet', 'plugins/leaflet/leaflet.js');

    $style = theme_option('real_estate_property_detail_layout', 1);
    $style = in_array($style, range(1, 4)) ? $style : 1;
    Theme::set('pageTitle', $property->name);
@endphp

@include(Theme::getThemeNamespace("views.real-estate.single-layouts.style-$style"))

<template id="map-popup-content">
    <div class="map-listing-item">
        <div class="inner-box">
            <div class="image-box">
                <a href="{{ $property->url }}">
                    {{ RvMedia::image($property->image_thumb, $property->name) }}
                </a>
                {!! BaseHelper::clean($property->status_html) !!}
            </div>
            <div class="content">
                @if($property->category)
                    <div class="property-type-badge">
                        <span class="flag-tag style-2">{{ $property->category->name }}</span>
                    </div>
                @endif
                <!-- Original title (hidden but kept for accessibility/SEO) -->
                <div class="title" style="display: none;">
                    <a href="{{ $property->url }}" title="{{ $property->name }}">
                        {{ $property->name }}
                    </a>
                </div>
                <!-- Duplicate title element showing price instead -->
                <div class="title">
                    <span>{{ $property->price_html }}</span>
                </div>
                <!-- Meta-list moved to where location was -->
                <ul class="list-info">
                    <li>
                        <x-core::icon name="ti ti-bed" />
                        {{ number_format($property->number_bedroom) }}
                    </li>
                    <li>
                        <x-core::icon name="ti ti-bath" />
                        {{ number_format($property->number_bathroom) }}
                    </li>
                    <li>
                        <x-core::icon name="ti ti-ruler" />
                        {{ $property->square_text }}
                    </li>
                </ul>
                <!-- Location moved to where meta-list was -->
                @if($property->short_address)
                    <p class="location">
                        <x-core::icon name="ti ti-map-pin" />
                        {{ $property->short_address }}
                    </p>
                @endif
            </div>
        </div>
    </div>
</template>
