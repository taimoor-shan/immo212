<section class="flat-section pt-0 flat-banner">
    <div class="container">
        <div class="wrap-banner bg-surface">
            <div class="box-left">
                <div class="box-title">
                    @if($shortcode->subtitle)
                        <div class="text-subtitle text-prime">{!! BaseHelper::clean($shortcode->subtitle) !!}</div>
                    @endif
                    @if($shortcode->title)
                        <h2 class="section-title mt-4">{!! BaseHelper::clean($shortcode->title) !!}</h2>
                    @endif
                    @if($shortcode->description)
                        <p class="mt-3">{!! BaseHelper::clean($shortcode->description) !!}</p>
                    @endif
                </div>
                <div class="box-buttons d-flex align-items-center justify-content-start gap-3">
                @if($shortcode->button_label)
                        <a href="{{ $shortcode->button_url }}" class="tf-btn primary">
                            {{ $shortcode->button_label }}
                        </a>
                    @endif
                    @if($shortcode->button_2_label)
                        <a href="{{ $shortcode->button_2_url }}" class="tf-btn secondary">
                            {{ $shortcode->button_2_label }}
                        </a>
                    @endif
                </div>
            </div>
            @if($shortcode->image)
                <div class="box-right">
                    {{ RvMedia::image($shortcode->image, $shortcode->title) }}
                </div>
            @endif
        </div>
    </div>
</section>
