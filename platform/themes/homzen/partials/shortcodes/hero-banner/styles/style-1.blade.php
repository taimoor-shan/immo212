@php
    $backgroundImage = $shortcode->background_image ? RvMedia::getImageUrl($shortcode->background_image) : null;
@endphp

<section class="flat-slider home-1" @style(["background-image: url('$backgroundImage') !important" => $backgroundImage])>
    <div class="container relative">
        <div class="row">
            <div class="col-lg-12">
                <div class="slider-content">
                    <div class="text-center">
                        <div class="heading">
                            <h1 class="text-white animationtext slide">
                                {!! BaseHelper::clean($shortcode->title) !!}
                                {!! Theme::partial('shortcodes.hero-banner.partials.animation-text', compact('shortcode')) !!}
                            </h1>
                            @if ($shortcode->description)
                                <p class="subtitle text-white body-1 wow fadeIn" data-wow-delay=".8s" data-wow-duration="2000ms">
                                    {!! BaseHelper::clean($shortcode->description) !!}
                                </p>
                            @endif
                        </div>
                        {!! Theme::partial('shortcodes.hero-banner.partials.action-button', ['shortcode' => $shortcode, 'class' => 'mb-4']) !!}
                    </div>
                    @if(is_plugin_active('real-estate') && $shortcode->search_box_enabled)
                        @include(Theme::getThemeNamespace('views.real-estate.partials.search-box'), ['style' => 1, 'centeredTabs' => true])
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="overlay"></div>
</section>
