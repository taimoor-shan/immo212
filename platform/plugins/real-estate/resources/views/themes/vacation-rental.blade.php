@php
    Theme::asset()
        ->usePath()
        ->add('leaflet-css', 'libraries/leaflet/leaflet.css');
    Theme::asset()
        ->container('footer')
        ->usePath()
        ->add('leaflet-js', 'libraries/leaflet/leaflet.js');
    Theme::asset()
        ->usePath()
        ->add('magnific-css', 'libraries/magnific/magnific-popup.css');
    Theme::asset()
        ->container('footer')
        ->usePath()
        ->add('magnific-js', 'libraries/magnific/jquery.magnific-popup.min.js');
    Theme::asset()
        ->container('footer')
        ->usePath()
        ->add('vacation-rental-js', 'js/vacation-rental.js');
@endphp
<main class="detailvacationrental bg-white">
    <div data-vacation-rental-id="{{ $vacationRental->id }}"></div>
    @include('plugins/real-estate::themes.includes.slider', ['object' => $vacationRental])

    <div class="container-fluid w90 padtop20">
        <h1 class="titlehouse">{{ $vacationRental->name }}</h1>
        @if (RealEstateHelper::isEnabledReview())
            <p style="margin-bottom: 5px;">@include('plugins/real-estate::themes.partials.review-star', [
                'avgStar' => $vacationRental->reviews_avg_star,
                'count' => $vacationRental->reviews_count,
            ])</p>
        @endif
        <p class="addresshouse">
            @if ($vacationRental->short_address)
                <span
                    class="d-inline-block"
                    style="margin-right: 10px"
                >
                    <i class="fas fa-map-marker-alt"></i>
                    {{ $vacationRental->short_address }}
                </span>
            @endif
            @if (setting('real_estate_display_views_count_in_detail_page', 0) == 1)
                <span
                    class="d-inline-block"
                    style="margin-right: 10px"
                ><i class="fa fa-eye"></i> {{ number_format($vacationRental->views) }} {{ __('views') }}</span>
            @endif
            <span class="d-inline-block">
                <i class="fas fa-calendar-alt"></i>
                {{ __('Available for booking') }}
            </span>
        </p>
        <div class="row">
            <div class="col-md-8">
                <div class="contentdetail">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="boxleft">
                                <div class="titleboxleft">{{ __('Vacation Rental Information') }}</div>
                                <div class="row">
                                    @if ($vacationRental->minimum_stay)
                                        <div class="col-6">
                                            <div class="item-info">
                                                <div class="item-info-label">{{ __('Minimum Stay') }}</div>
                                                <div class="item-info-value">{{ $vacationRental->minimum_stay }} {{ $vacationRental->minimum_stay == 1 ? __('night') : __('nights') }}</div>
                                            </div>
                                        </div>
                                    @endif
                                    @if ($vacationRental->maximum_guests)
                                        <div class="col-6">
                                            <div class="item-info">
                                                <div class="item-info-label">{{ __('Maximum Guests') }}</div>
                                                <div class="item-info-value">{{ $vacationRental->maximum_guests }}</div>
                                            </div>
                                        </div>
                                    @endif
                                    @if ($vacationRental->check_in_time)
                                        <div class="col-6">
                                            <div class="item-info">
                                                <div class="item-info-label">{{ __('Check-in Time') }}</div>
                                                <div class="item-info-value">{{ $vacationRental->check_in_time }}</div>
                                            </div>
                                        </div>
                                    @endif
                                    @if ($vacationRental->check_out_time)
                                        <div class="col-6">
                                            <div class="item-info">
                                                <div class="item-info-label">{{ __('Check-out Time') }}</div>
                                                <div class="item-info-value">{{ $vacationRental->check_out_time }}</div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="boxright">
                                <div class="titleboxright">{{ __('Pricing') }}</div>
                                <div class="pricedetail">
                                    <div class="price-per-night">
                                        <span class="price">{{ $vacationRental->price_format }}</span>
                                        <span class="per-night">{{ __('per night') }}</span>
                                    </div>
                                    @if ($vacationRental->cleaning_fee)
                                        <div class="additional-fee">
                                            <span class="fee-label">{{ __('Cleaning fee:') }}</span>
                                            <span class="fee-amount">{{ format_price($vacationRental->cleaning_fee) }}</span>
                                        </div>
                                    @endif
                                    @if ($vacationRental->security_deposit)
                                        <div class="additional-fee">
                                            <span class="fee-label">{{ __('Security deposit:') }}</span>
                                            <span class="fee-amount">{{ format_price($vacationRental->security_deposit) }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($vacationRental->description)
                    <div class="contentdetail">
                        <div class="titleboxleft">{{ __('Description') }}</div>
                        <div class="contenttext">
                            {!! BaseHelper::clean($vacationRental->description) !!}
                        </div>
                    </div>
                @endif

                @if ($vacationRental->features->isNotEmpty())
                    <div class="contentdetail">
                        <div class="titleboxleft">{{ __('Features') }}</div>
                        <div class="row">
                            @foreach($vacationRental->features as $feature)
                                <div class="col-md-4 col-sm-6">
                                    <div class="item-feature">
                                        @if ($feature->icon)
                                            <i class="{{ $feature->icon }}"></i>
                                        @endif
                                        {{ $feature->name }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($vacationRental->house_rules)
                    <div class="contentdetail">
                        <div class="titleboxleft">{{ __('House Rules') }}</div>
                        <div class="contenttext">
                            {!! nl2br(e($vacationRental->house_rules)) !!}
                        </div>
                    </div>
                @endif

                @if ($vacationRental->latitude && $vacationRental->longitude)
                    <div class="contentdetail">
                        <div class="titleboxleft">{{ __('Location') }}</div>
                        <div id="map-vacation-rental" style="height: 300px;"></div>
                    </div>
                @endif

                @if (RealEstateHelper::isEnabledReview())
                    @include('plugins/real-estate::themes.partials.reviews', ['model' => $vacationRental])
                @endif
            </div>
            <div class="col-md-4">
                <div class="sidebar-vacation-rental">
                    <div class="booking-widget">
                        <div class="booking-widget-title">{{ __('Book This Vacation Rental') }}</div>
                        <div class="booking-form">
                            <!-- Booking form will be loaded here via JavaScript -->
                            <div id="vacation-rental-booking-form" 
                                 data-vacation-rental-id="{{ $vacationRental->id }}"
                                 data-min-stay="{{ $vacationRental->minimum_stay ?: 1 }}"
                                 data-max-guests="{{ $vacationRental->maximum_guests ?: '' }}">
                                <div class="text-center">
                                    <div class="spinner-border" role="status">
                                        <span class="sr-only">{{ __('Loading...') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if ($vacationRental->author)
                        <div class="agent-info">
                            <div class="agent-info-title">{{ __('Contact Agent') }}</div>
                            <div class="agent-details">
                                @if ($vacationRental->author->avatar)
                                    <div class="agent-avatar">
                                        {{ RvMedia::image($vacationRental->author->avatar, $vacationRental->author->name, 'thumb') }}
                                    </div>
                                @endif
                                <div class="agent-name">{{ $vacationRental->author->name }}</div>
                                @if ($vacationRental->author->phone)
                                    <div class="agent-phone">
                                        <i class="fas fa-phone"></i>
                                        <a href="tel:{{ $vacationRental->author->phone }}">{{ $vacationRental->author->phone }}</a>
                                    </div>
                                @endif
                                @if ($vacationRental->author->email)
                                    <div class="agent-email">
                                        <i class="fas fa-envelope"></i>
                                        <a href="mailto:{{ $vacationRental->author->email }}">{{ $vacationRental->author->email }}</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</main>

@if ($vacationRental->latitude && $vacationRental->longitude)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof L !== 'undefined') {
                var map = L.map('map-vacation-rental').setView([{{ $vacationRental->latitude }}, {{ $vacationRental->longitude }}], 15);
                
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);
                
                L.marker([{{ $vacationRental->latitude }}, {{ $vacationRental->longitude }}])
                    .addTo(map)
                    .bindPopup('{{ addslashes($vacationRental->name) }}');
            }
        });
    </script>
@endif
