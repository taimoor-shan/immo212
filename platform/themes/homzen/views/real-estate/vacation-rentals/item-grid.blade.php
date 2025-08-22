@php
    $class ??= null;
    $itemsPerRow ??= 3;
@endphp

<div @class(['vacation-rental-item homeya-box', $class])
    @if ($vacationRental->latitude && $vacationRental->longitude) data-lat="{{ $vacationRental->latitude }}" data-lng="{{ $vacationRental->longitude }}" @endif>
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
                        <button type="button" class="box-icon w-32" data-type="vacation-rental"
                            data-bb-toggle="add-to-wishlist" data-id="{{ $vacationRental->getKey() }}"
                            data-add-message="{{ __('Added ":name" to wishlist successfully!', ['name' => $vacationRental->name]) }}"
                            data-remove-message="{{ __('Removed ":name" from wishlist successfully!', ['name' => $vacationRental->name]) }}">
                            <x-core::icon name="ti ti-heart" />
                        </button>
                    </div>
                @endif
            </div>
        </a>
        <div class="content">
            {{-- @if ($vacationRental->category)
                <div class="bottom">
                    <span class="textMuted">{{ $vacationRental->category->name }}</span>
                </div>
            @endif --}}
            <div class="h7 text-capitalize fw-6">
                <a href="{{ $vacationRental->url }}" class="link line-clamp-1"
                    title="{{ $vacationRental->name }}">{!! BaseHelper::clean($vacationRental->name) !!}</a>
            </div>
            @if ($vacationRental->short_address)
                <div class="desc align-items-center">

                    <p class="line-clamp-1">{{ $vacationRental->short_address }}</p>
                </div>
            @endif
            <ul class="meta-list">
                @if ($vacationRental->minimum_stay)
                    <li class="item">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"
                            color="#0052cc" fill="none">
                            <path d="M16 2V6M8 2V6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round"></path>
                            <path
                                d="M13 4H11C7.22876 4 5.34315 4 4.17157 5.17157C3 6.34315 3 8.22876 3 12V14C3 17.7712 3 19.6569 4.17157 20.8284C5.34315 22 7.22876 22 11 22H13C16.7712 22 18.6569 22 19.8284 20.8284C21 19.6569 21 17.7712 21 14V12C21 8.22876 21 6.34315 19.8284 5.17157C18.6569 4 16.7712 4 13 4Z"
                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            </path>
                            <path d="M3 10H21" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round"></path>
                            <path d="M11.9955 14H12.0045M11.9955 18H12.0045M15.991 14H16M8 14H8.00897M8 18H8.00897"
                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            </path>
                        </svg>
                        <span>{{ trans_choice('Min :count night|Min :count nights', $vacationRental->minimum_stay, ['count' => $vacationRental->minimum_stay]) }}</span>
                    </li>
                @endif
                @if ($vacationRental->maximum_guests)
                    <li class="item">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"
                            color="#0052cc" fill="none">
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.5"
                                stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M8 15C8.91212 16.2144 10.3643 17 12 17C13.6357 17 15.0879 16.2144 16 15"
                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M8.00897 9L8 9M16 9L15.991 9" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <span>{{ trans_choice(':count guest|:count guests', $vacationRental->maximum_guests, ['count' => $vacationRental->maximum_guests]) }}</span>
                    </li>
                @endif
                @if ($vacationRental->number_bedroom)
                    <li class="item">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"
                            color="#0052cc" fill="none">
                            <path d="M22 17.5H2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round"></path>
                            <path
                                d="M22 21V16C22 14.1144 22 13.1716 21.4142 12.5858C20.8284 12 19.8856 12 18 12H6C4.11438 12 3.17157 12 2.58579 12.5858C2 13.1716 2 14.1144 2 16V21"
                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            </path>
                            <path
                                d="M16 12V10.6178C16 10.1103 15.9085 9.94054 15.4396 9.7405C14.4631 9.32389 13.2778 9 12 9C10.7222 9 9.53688 9.32389 8.5604 9.7405C8.09154 9.94054 8 10.1103 8 10.6178L8 12"
                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path>
                            <path
                                d="M20 12V7.36057C20 6.66893 20 6.32311 19.8292 5.99653C19.6584 5.66995 19.4151 5.50091 18.9284 5.16283C16.9661 3.79978 14.5772 3 12 3C9.42282 3 7.03391 3.79978 5.07163 5.16283C4.58492 5.50091 4.34157 5.66995 4.17079 5.99653C4 6.32311 4 6.66893 4 7.36057V12"
                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path>
                        </svg>
                        <span>{{ $vacationRental->number_bedroom }}</span>
                    </li>
                @endif
                {{-- @if ($vacationRental->number_bathroom)
                    <li class="item">
                        <i class="icon icon-bathtub"></i>
                        <span>{{ $vacationRental->number_bathroom }}</span>
                    </li>
                @endif --}}
                {{-- @if ($vacationRental->square)
                    <li class="item">
                        <i class="icon icon-ruler"></i>
                        <span>{{ $vacationRental->square_text }}</span>
                    </li>
                @endif --}}
            </ul>
            <div class="bot">
                <div class="price">
                    <span class="h7 fw-6 text-prime">{{ $vacationRental->price_format }}</span>
                    <span class="text-variant-1">/ {{ __('night') }}</span>
                </div>
                @if ($vacationRental->reviews_count > 0)
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
