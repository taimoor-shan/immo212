@php
    $class ??= null;
    $itemsPerRow ??= 3;
@endphp

<div @class(['property-item homeya-box', $class]) @if ($property->latitude && $property->longitude) data-lat="{{ $property->latitude }}" data-lng="{{ $property->longitude }}" @endif>
    <div class="archive-top">
        <a href="{{ $property->url }}" class="images-group">
            <div class="images-style">
                {{ RvMedia::image($property->image, $property->name, 'medium-rectangle') }}
            </div>
            <div class="top">
                <div class="d-flex gap-8">
                    {!! BaseHelper::clean($property->status->toHtml()) !!}
                </div>
                @if (RealEstateHelper::isEnabledWishlist())
                    <div class="d-flex gap-4">
                        <button type="button" class="box-icon w-32"
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
        </a>
        <div class="content">
            @if($property->category)
                <div class="property-type-badge">
                    <span class="fw-5 text-variant-2">{{ $property->category->name }}</span>
                </div>
            @endif
            <!-- Original title (hidden but kept for accessibility/SEO) -->
            <{{ $class === 'lg' ? 'h5' : 'div' }} @class(['text-capitalize', 'h7 fw-5' => $class !== 'lg']) style="display: none;">
                <a href="{{ $property->url }}" class="link line-clamp-1" title="{{ $property->name }}">{!! BaseHelper::clean($property->name) !!}</a>
            </{{ $class === 'lg' ? 'h5' : 'div' }}>
            <!-- Duplicate title element showing price instead -->
            <{{ $class === 'lg' ? 'h5' : 'div' }} @class(['text-capitalize', 'h6 text-prime' => $class !== 'lg'])>
                <span class="link line-clamp-1">{{ format_price($property->price, $property->currency) }}</span>
            </{{ $class === 'lg' ? 'h5' : 'div' }}>
            <!-- Meta-list moved to where location was -->
            <ul class="meta-list">
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
            @if($property->short_address)
                <div class="desc">
                    <i class="icon icon-mapPin"></i>
                    <p class="line-clamp-1">{{ $property->short_address }}</p>
                </div>
            @endif
            @if($class === 'lg' && $property->description)
                <p class="note">{!! Str::limit(BaseHelper::clean($property->description)) !!}</p>
            @endif
        </div>
    </div>
</div>
