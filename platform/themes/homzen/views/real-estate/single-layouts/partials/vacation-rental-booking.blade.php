@if(isset($rental) && method_exists($rental, 'isVacationRental') && $rental->isVacationRental())
    <div @class(['widget-box single-property-vacation-rental-booking single-property-contact', $class ?? null])>
        <div class="h7 title fw-6">{{ __('Book This Property') }}</div>
        
        @if (! RealEstateHelper::hideAgentInfoInPropertyDetailPage() && ($account = $property->author))
            <div class="box-avatar mb-3">
                <div class="avatar avt-100 round">
                    <a href="{{ $account->url }}" class="d-block">
                        {{ RvMedia::image($account->avatar?->url ?: $account->avatar_url, $account->name) }}
                    </a>
                </div>
                <div class="info line-clamp-1">
                    <div class="text-1 name">
                        <a href="{{ $account->url }}">{{ $account->name }}</a>
                    </div>
                    @if ($account->phone && ! setting('real_estate_hide_agency_phone', false))
                        <a href="tel:{{ $account->phone }}" class="info-item">{{ $account->phone }}</a>
                    @elseif($hotline = theme_option('hotline'))
                        <a href="tel:{{ $hotline }}" class="info-item">{{ $hotline }}</a>
                    @endif
                    @if ($account->email && ! setting('real_estate_hide_agency_email', false))
                        <a href="mailto:{{ $account->email }}" class="info-item">{{ $account->email }}</a>
                    @endif
                </div>
            </div>
        @endif

        {!! apply_filters('property_right_details_info', null, $property) !!}

        {!! apply_filters('before_vacation_rental_booking_form', null, $property) !!}

        <div id="property-calendar"
             data-property-id="{{ $property->id }}"
             data-availability-url="{{ route('public.vacation-rental.api.availability', ['id' => $property->id]) }}"
             data-pricing-url="{{ route('public.vacation-rental.api.calculate-price', ['id' => $property->id]) }}"
             data-booking-url="{{ route('public.vacation-rental.booking.process') }}"
             data-login-url="{{ route('public.account.login') }}"
             data-min-stay="{{ $property->min_stay ?: 1 }}"
             data-max-stay="{{ $property->max_stay ?: '' }}"
             data-max-guests="{{ $property->max_guests ?: '' }}"
             data-is-logged-in="{{ auth('account')->check() ? 'true' : 'false' }}">
        </div>

        {!! apply_filters('after_vacation_rental_booking_form', null, $property) !!}

    </div>
@endif
