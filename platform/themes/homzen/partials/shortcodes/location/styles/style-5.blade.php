<section
    class="flat-section-v4 flat-location-v5"
    @style(["background-color: $shortcode->background_color" => $shortcode->background_color])
>
    <div class="container">
        {!! Theme::partial('shortcode-heading', compact('shortcode')) !!}

        <div class="wow fadeInUpSmall" data-wow-delay=".2s" data-wow-duration="2000ms">
            <!-- Grid layout for lg+ screens, 2-row horizontal carousel for smaller screens -->
            <div
                class="grid-location-v5 swiper tf-sw-location-v5"
                data-rows="1"
                data-slides-per-group="2"
                data-space="20"
                data-loop="false"
                data-autoplay="false"
            >
                <div class="swiper-wrapper">
                    @foreach($locations->chunk(2) as $locationPair)
                        <div class="swiper-slide">
                            <div class="slide-column">
                                @foreach($locationPair as $location)
                                    <a href="{{ $location->url }}" class="box-location-v5 hover-img">
                                        <div class="image-wrapper">
                                            <div class="image">
                                                {{ RvMedia::image($location->image, $location->name, 'medium-rectangle-column') }}
                                            </div>
                                            <div class="overlay"></div>
                                            <div class="content">
                                                <h6 class="title">{{ $location->name }}</h6>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
