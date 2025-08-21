@php
    $class ??= null;
    $itemsPerRow ??= 3;
@endphp

<div @class(['vacation-rental-item homeya-box', $class]) @if ($vacationRental->latitude && $vacationRental->longitude) data-lat="{{ $vacationRental->latitude }}" data-lng="{{ $vacationRental->longitude }}" @endif>
    <div class="archive-top">
        <a href="{{ $vacationRental->url }}" class="images-group">
            <div class="images-style">
                {{ RvMedia::image($vacationRental->image, $vacationRental->name, 'medium-rectangle') }}
            </div>
            <div class="top">
                <div class="d-flex gap-8">
                                <span class="flag-tag primary">{{ $vacationRental->category->name }}</span>

                </div>
                @if (RealEstateHelper::isEnabledWishlist())
                    <div class="d-flex gap-4">
                        <button type="button" class="box-icon w-32"
                                data-type="vacation-rental"
                                data-bb-toggle="add-to-wishlist"
                                data-id="{{ $vacationRental->getKey() }}"
                                data-add-message="{{ __('Added ":name" to wishlist successfully!', ['name' => $vacationRental->name]) }}"
                                data-remove-message="{{ __('Removed ":name" from wishlist successfully!', ['name' => $vacationRental->name]) }}"
                        >
                            <x-core::icon name="ti ti-heart" />
                        </button>
                    </div>
                @endif
            </div>
        </a>
        <div class="content">
            {{-- @if($vacationRental->category)
                <div class="bottom">
                    <span class="textMuted">{{ $vacationRental->category->name }}</span>
                </div>
            @endif --}}
            <div class="h7 text-capitalize fw-6">
                <a href="{{ $vacationRental->url }}" class="link line-clamp-1" title="{{ $vacationRental->name }}">{!! BaseHelper::clean($vacationRental->name) !!}</a>
            </div>
            @if($vacationRental->short_address)
                <div class="desc align-items-center">
                    <i class="fa-solid fa-globe text-prime"></i>
                    <p class="line-clamp-1">{{ $vacationRental->short_address }}</p>
                </div>
            @endif
            <ul class="meta-list">
                @if($vacationRental->minimum_stay)
                    <li class="item">
                       <i class="fa-solid fa-calendar text-prime"></i>
                        <span>{{ trans_choice('Min :count night|Min :count nights', $vacationRental->minimum_stay, ['count' => $vacationRental->minimum_stay]) }}</span>
                    </li>
                @endif
                @if($vacationRental->maximum_guests)
                    <li class="item">
                        <i class="fa-solid fa-user text-prime"></i>
                        <span>{{ trans_choice(':count guest|:count guests', $vacationRental->maximum_guests, ['count' => $vacationRental->maximum_guests]) }}</span>
                    </li>
                @endif
                @if($vacationRental->number_bedroom)
                    <li class="item">
                        <i class="fa-solid fa-bed text-prime"></i>
                        <span>{{ $vacationRental->number_bedroom }}</span>
                    </li>
                @endif
                {{-- @if($vacationRental->number_bathroom)
                    <li class="item">
                        <i class="icon icon-bathtub"></i>
                        <span>{{ $vacationRental->number_bathroom }}</span>
                    </li>
                @endif --}}
                {{-- @if($vacationRental->square)
                    <li class="item">
                        <i class="icon icon-ruler"></i>
                        <span>{{ $vacationRental->square_text }}</span>
                    </li>
                @endif --}}
            </ul>
            <div class="bot">
                <div class="price">
                    <span class="h7 fw-6">{{ $vacationRental->price_format }}</span>
                    <span class="text-variant-1">/ {{ __('night') }}</span>
                </div>
                @if($vacationRental->reviews_count > 0)
                    <div class="rating">
                        <div class="number h7 fw-6">{{ number_format($vacationRental->reviews_avg_star, 1) }}</div>
                        <div class="icon">
                            <x-core::icon name="ti ti-star-filled" />
                        </div>
                        <div class="text text-variant-1">({{ $vacationRental->reviews_count }})</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
