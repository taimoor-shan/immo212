<section class="flat-section flat-property-v3 wow fadeInUpSmall" data-wow-delay=".2s" data-wow-duration="2000ms">
    <div class="container">
        {!! Theme::partial('shortcode-heading', compact('shortcode')) !!}
        <div class="wrap-sw-property">
            <div class="swiper tf-sw-property">
                <div class="swiper-wrapper">
                    @foreach($properties as $property)
                        <div class="swiper-slide">
                            <div class="wrap-property-v2 style-1">
                                <div class="box-inner-left">
                                    {{ RvMedia::image($property->image, $property->name) }}
                                </div>
                                <div class="box-inner-right">
                                    <div class="content-property">
                                        <!-- Property type badge moved to content area -->
                                        <div class="box-tag">
                                            @if($property->category)
                                                <span class="flag-tag style-2">{{ $property->category->name }}</span>
                                            @endif
                                            @if($property->is_featured)
                                                <span class="flag-tag success">{{ __('Featured') }}</span>
                                            @endif
                                            {!! BaseHelper::clean($property->status->toHtml()) !!}
                                        </div>
                                        <div class="box-name">
                                            <!-- Original title (hidden but kept for accessibility/SEO) -->
                                            <h5 class="title" style="display: none;">
                                                <a href="{{ $property->url }}" class="link line-clamp-1">{{ $property->name }}</a>
                                            </h5>
                                            <!-- Duplicate title element showing price instead -->
                                            <h5 class="title">
                                                <span class="link line-clamp-1">{{ format_price($property->price, $property->currency) }}</span>
                                            </h5>
                                        </div>
                                        <!-- Meta-list moved to where location was -->
                                        <ul class="list-info">
                                            @if($property->number_bedroom)
                                                <li class="item">
                                                    <i class="icon icon-bed"></i>
                                                    <span>{{ number_format($property->number_bedroom) }}</span>
                                                </li>
                                            @endif
                                            @if($property->number_bathroom)
                                                <li class="item">
                                                    <i class="icon icon-bathtub"></i>
                                                    <span>{{ number_format($property->number_bathroom) }}</span>
                                                </li>
                                            @endif
                                            @if($property->square)
                                                <li class="item">
                                                    <i class="icon icon-ruler"></i>
                                                    <span>{{ $property->square_text }}</span>
                                                </li>
                                            @endif
                                        </ul>
                                        <!-- Location moved to where meta-list was -->
                                        @if($property->address)
                                            <p class="location">

                                                {{ $property->address }}
                                            </p>
                                        @endif
                                        @if($author = $property->author)
                                            <div class="box-avatar d-flex gap-12 align-items-center">
                                                <div class="avatar avt-60 round">
                                                    {{ RvMedia::image($author->avatar_url, $author->name, 'thumb') }}
                                                </div>
                                                <div class="info">
                                                    <p class="body-2 text-variant-1">{{ __('Agent') }}</p>
                                                    <div class="mt-4 h7 fw-6">{{ $author->name }}</div>
                                                </div>
                                            </div>
                                        @endif
                                        <!-- Wishlist functionality moved outside pricing section -->
                                        @if (RealEstateHelper::isEnabledWishlist())
                                            <div class="d-flex gap-12">
                                                <button type="button" class="box-icon w-52"
                                                        data-type="property"
                                                        data-bb-toggle="add-to-wishlist"
                                                        data-id="{{ $property->getKey() }}"
                                                        data-add-message="{{ __('Added ":name" to wishlist successfully!', ['name' => $property->name]) }}"
                                                        data-remove-message="{{ __('Removed ":name" from wishlist successfully!', ['name' => $property->name]) }}"
                                                >
                                                    <x-core::icon name="ti ti-heart" />
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="box-navigation">
                <div class="navigation swiper-nav-next nav-next-property">
                    <x-core::icon name="ti ti-chevron-left" />
                </div>
                <div class="navigation swiper-nav-prev nav-prev-property">
                    <x-core::icon name="ti ti-chevron-right" />
                </div>
            </div>
        </div>
    </div>
</section>
