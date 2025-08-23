@php
    $uniqueId = 'tf-sw-vacation-rental-v8-' . uniqid();
    $swiperConfig = [
        'rtl' => BaseHelper::isRtlEnabled(),
        'slidesPerView' => 1.2,
        'spaceBetween' => 16,
        'navigation' => [
            'clickable' => true,
            'nextEl' => '.nav-next-' . $uniqueId,
            'prevEl' => '.nav-prev-' . $uniqueId,
            'disabledClass' => 'swiper-button-disabled',
        ],
        'watchSlidesProgress' => true,
        'breakpoints' => [
            600 => [
                'slidesPerView' => 2,
                'spaceBetween' => 20,
            ],
            991 => [
                'slidesPerView' => 3,
                'spaceBetween' => 20,
            ],
            1200 => [
                'slidesPerView' => 4,
                'spaceBetween' => 30,
            ],
        ],
    ];
@endphp

<section
    class="flat-section-v4 flat-vacation-rental-v8 wow fadeInUpSmall"
    data-wow-delay=".2s"
    data-wow-duration="2000ms"
    @style(["background-color: $shortcode->background_color" => $shortcode->background_color])>
    <div class="container">
        {!! Theme::partial('shortcode-heading', compact('shortcode')) !!}

        <div class="wrap-sw-vacation-rental-v8 position-relative">
            <div class="swiper tf-sw-vacation-rental-v8" id="{{ $uniqueId }}" data-swiper-config='@json($swiperConfig)'>
                <div class="swiper-wrapper">
                    @foreach($vacationRentals as $vacationRental)
                        <div class="swiper-slide">
                            @include(Theme::getThemeNamespace('views.real-estate.vacation-rentals.item-grid-2'), ['vacationRental' => $vacationRental, 'class' => 'style-2'])
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Navigation positioned outside carousel on sides -->
            <div class="carousel-navigation-v8">
                <div class="navigation-btn nav-prev-{{ $uniqueId }} nav-prev">
                    <x-core::icon name="ti ti-chevron-left" />
                </div>
                <div class="navigation-btn nav-next-{{ $uniqueId }} nav-next">
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
