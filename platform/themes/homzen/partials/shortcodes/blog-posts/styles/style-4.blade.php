<section class="flat-section-v3 flat-latest-new" @style(["background-color: $shortcode->background_color" => $shortcode->background_color])>
    <div class="container">
        {!! Theme::partial('shortcode-heading', compact('shortcode')) !!}

        @if($posts->count() >= 4)
            <div class="blog-bento-grid wow fadeInUpSmall" data-wow-delay=".4s" data-wow-duration="2000ms">
                @php
                    $postsArray = $posts->take(4)->values();
                @endphp

                {{-- Post 1: Content in cta1 area, image in image1 area --}}
                @if(isset($postsArray[0]))
                    @php $post = $postsArray[0]; @endphp
                    <div class="bento-blog-item content-area cta1">
                        <div class="content-box">
                            <span class="date-post">{{ Theme::formatDate($post->created_at) }}</span>
                            <div class="title h6 fw-6">
                                <a href="{{ $post->url }}" class="line-clamp-2">{{ $post->name }}</a>
                            </div>
                            @if($post->description)
                                <div class="description line-clamp-3">{!! BaseHelper::clean(Str::limit($post->description, 120)) !!}</div>
                            @endif
                            @if (theme_option('blog_show_author_name', 'yes') == 'yes' && class_exists($post->author_type) && ($author = $post->author ?? null) && trim($author->name))
                                <div class="post-author">
                                    <span class="fw-5">{{ $post->author->name }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                    <a href="{{ $post->url }}" class="bento-blog-item image-area image1">
                        <div class="img-style">
                            {{ RvMedia::image($post->image, $post->name, 'medium-rectangle') }}
                        </div>
                    </a>
                @endif

                {{-- Post 2: Image with overlay content in image2 area (tall area) --}}
                @if(isset($postsArray[1]))
                    @php $post = $postsArray[1]; @endphp
                    <a href="{{ $post->url }}" class="bento-blog-item image-overlay image2">
                        <div class="img-style">
                            {{ RvMedia::image($post->image, $post->name, 'medium-rectangle') }}
                        </div>
                        <div class="content-overlay">
                            <span class="date-post">{{ Theme::formatDate($post->created_at) }}</span>
                            <div class="title">
                                <a href="{{ $post->url }}" class="line-clamp-2">{{ $post->name }}</a>
                            </div>
                            @if (theme_option('blog_show_author_name', 'yes') == 'yes' && class_exists($post->author_type) && ($author = $post->author ?? null) && trim($author->name))
                                <div class="post-author">
                                    <span>{{ $post->author->name }}</span>
                                </div>
                            @endif
                        </div>
                    </a>
                @endif

                {{-- Post 3: Content in cta3 area, image in image3 area --}}
                @if(isset($postsArray[2]))
                    @php $post = $postsArray[2]; @endphp
                    <a href="{{ $post->url }}" class="bento-blog-item image-area image3">
                        <div class="img-style">
                            {{ RvMedia::image($post->image, $post->name, 'medium-rectangle') }}
                        </div>
                    </a>
                    <div class="bento-blog-item content-area cta3">
                        <div class="content-box">
                            <span class="date-post">{{ Theme::formatDate($post->created_at) }}</span>
                            <div class="title h6 fw-6">
                                <a href="{{ $post->url }}" class="line-clamp-2">{{ $post->name }}</a>
                            </div>
                            @if($post->description)
                                <div class="description line-clamp-3">{!! BaseHelper::clean(Str::limit($post->description, 120)) !!}</div>
                            @endif
                            @if (theme_option('blog_show_author_name', 'yes') == 'yes' && class_exists($post->author_type) && ($author = $post->author ?? null) && trim($author->name))
                                <div class="post-author">
                                    <span class="fw-5">{{ $post->author->name }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Post 4: Content in cta2 area, image in image4 area --}}
                @if(isset($postsArray[3]))
                    @php $post = $postsArray[3]; @endphp
                    <div class="bento-blog-item content-area cta2">
                        <div class="content-box">
                            <span class="date-post">{{ Theme::formatDate($post->created_at) }}</span>
                            <div class="title h6 fw-6">
                                <a href="{{ $post->url }}" class="line-clamp-2">{{ $post->name }}</a>
                            </div>
                            @if($post->description)
                                <div class="description line-clamp-3">{!! BaseHelper::clean(Str::limit($post->description, 120)) !!}</div>
                            @endif
                            @if (theme_option('blog_show_author_name', 'yes') == 'yes' && class_exists($post->author_type) && ($author = $post->author ?? null) && trim($author->name))
                                <div class="post-author">
                                    <span class="fw-5">{{ $post->author->name }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                    <a href="{{ $post->url }}" class="bento-blog-item image-area image4">
                        <div class="img-style">
                            {{ RvMedia::image($post->image, $post->name, 'medium-rectangle') }}
                        </div>
                    </a>
                @endif
            </div>
        @else
            <div class="alert alert-warning text-center">
                <p>{{ __('Please ensure you have at least 4 blog posts to display the bento grid layout. For best results, use "Featured" post type with exactly 4 posts.') }}</p>
            </div>
        @endif
    </div>
</section>
