<section
    class="flat-section flat-property-v8 wow fadeInUpSmall"
    data-wow-delay=".2s"
    data-wow-duration="2000ms"
    @style(["background-color: $shortcode->background_color" => $shortcode->background_color])
>
    <div class="container">
        {!! Theme::partial('shortcode-heading', compact('shortcode')) !!}

        <div class="wrap-sw-property-v8 position-relative">
            <div class="swiper tf-sw-property-v8">
                <div class="swiper-wrapper">
                    @foreach($properties as $property)
                        <div class="swiper-slide">
                            @include(Theme::getThemeNamespace('views.real-estate.properties.item-grid'), ['property' => $property, 'class' => 'style-2'])
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Navigation positioned outside carousel on sides -->
            <div class="carousel-navigation-v8">
                <div class="navigation-btn nav-prev-property-v8">
                    <x-core::icon name="ti ti-chevron-left" />
                </div>
                <div class="navigation-btn nav-next-property-v8">
                    <x-core::icon name="ti ti-chevron-right" />
                </div>
            </div>
        </div>

        @if($shortcode->button_url && $shortcode->button_label)
            <div class="text-center mt-4">
                <a href="{{ $shortcode->button_url }}" class="tf-btn primary size-1">
                    {!! BaseHelper::clean($shortcode->button_label) !!}
                </a>
            </div>
        @endif
    </div>
</section>

<style>
.wrap-sw-property-v8 {
    position: relative;
    overflow: visible; /* Allow navigation buttons to show outside */
}

/* Mobile-first: Enable overflow for partial card visibility */
.tf-sw-property-v8 {
    overflow: visible;
    padding-right: 0; /* Remove default padding on mobile */
}

.carousel-navigation-v8 {
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    transform: translateY(-50%);
    pointer-events: none;
    z-index: 10;
}

.navigation-btn {
    position: absolute;
    top: 0;
    width: 48px;
    height: 48px;
    background: rgba(255, 255, 255, 0.95);
    border: 1px solid #e5e5e5;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    pointer-events: auto;
    transition: all 0.3s ease;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
}

.navigation-btn:hover {
    background: #fff;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
    transform: scale(1.05);
}

.navigation-btn svg {
    width: 20px;
    height: 20px;
    color: #333;
}

.nav-prev-property-v8 {
    left: -24px;
}

.nav-next-property-v8 {
    right: -24px;
}

/* Mobile styles: Hide navigation, enable touch/swipe */
@media (max-width: 767px) {
    .carousel-navigation-v8 {
        display: none;
    }

    .wrap-sw-property-v8 {
        margin: 0 -15px; /* Extend to screen edges for better mobile UX */
        padding: 0 15px;
    }

    .tf-sw-property-v8 {
        padding-right: 15px; /* Add padding to show partial next card */
    }
}

/* Tablet and desktop: Show navigation with proper spacing */
@media (min-width: 768px) {
    .wrap-sw-property-v8 {
        margin: 0 30px; /* Space for navigation buttons */
    }

    .tf-sw-property-v8 {
        overflow: hidden; /* Contain slides on larger screens */
    }
}

/* Ensure smooth transitions and proper card spacing */
.tf-sw-property-v8 .swiper-slide {
    transition: transform 0.3s ease;
}

/* Improve mobile touch interaction */
@media (max-width: 767px) {
    .tf-sw-property-v8 .swiper-slide {
        width: calc(100% - 30px) !important; /* Ensure partial visibility works */
    }
}
</style>
