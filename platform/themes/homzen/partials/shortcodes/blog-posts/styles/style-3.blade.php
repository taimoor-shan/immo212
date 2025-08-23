@php
    $uniqueId = 'tf-sw-blog-v8-' . uniqid();
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
    class="flat-section-v4 flat-blog-v8 wow fadeInUpSmall"
    data-wow-delay=".2s"
    data-wow-duration="2000ms"
    @style(["background-color: $shortcode->background_color" => $shortcode->background_color])
>
    <div class="container">
        {!! Theme::partial('shortcode-heading', compact('shortcode')) !!}

        <div class="wrap-sw-property-v8 position-relative">
            <div class="swiper tf-sw-property-v8" id="{{ $uniqueId }}" data-swiper-config='@json($swiperConfig)'>
                <div class="swiper-wrapper">
                    @foreach($posts as $post)
                        <div class="swiper-slide">
                            <a href="{{ $post->url }}" class="flat-blog-item-carousel hover-img">
                                <div class="img-style" href="{{ $post->url }}">
                                    {{ RvMedia::image($post->image, $post->name, 'medium-rectangle') }}
                                    <!-- <span class="date-post">{{ Theme::formatDate($post->created_at) }}</span> -->
</div>
                                <div class="content-box">
                                    <!-- <div class="post-author">
                                        @if (theme_option('blog_show_author_name', 'yes') == 'yes' && class_exists($post->author_type) && ($author = $post->author ?? null) && trim($author->name))
                                            <span class="fw-6">{{ $post->author->name }}</span>
                                        @endif

                                        @if($category = $post->firstCategory)
                                            <span>
                                                <a href="{{ $category->url }}">{{ $category->name }}</a>
                                            </span>
                                        @endif
                                    </div> -->
                                    <h6 class="title text-center">{!! BaseHelper::clean($post->name) !!}</h6>
                                    <!-- @if($post->description)
                                        <p class="description">{!! BaseHelper::clean(Str::limit($post->description, 100)) !!}</p>
                                    @endif -->
                                    <span class="tf-btn secWhite ">Learn More</span>
                                </div>
</a>
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
    </div>
</section>
