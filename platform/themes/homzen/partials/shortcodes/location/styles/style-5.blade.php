<section
    class="flat-section flat-location-v5"
    @style(["background-color: $shortcode->background_color" => $shortcode->background_color])
>
    <div class="container">
        {!! Theme::partial('shortcode-heading', compact('shortcode')) !!}

        <div class="grid-location-v5 wow fadeInUpSmall" data-wow-delay=".2s" data-wow-duration="2000ms">
            @foreach($locations as $location)
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
</section>
