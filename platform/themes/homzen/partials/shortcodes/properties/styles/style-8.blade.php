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
