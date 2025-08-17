@if($vacationRental)
    <div @class(['widget-box single-vacation-rental-booking single-property-contact', $class ?? null])>
        <div class="h7 title fw-6">{{ __('Book This Vacation Rental') }}</div>

        @if (! RealEstateHelper::hideAgentInfoInPropertyDetailPage() && ($account = $vacationRental->author))
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

        {!! apply_filters('vacation_rental_right_details_info', null, $vacationRental) !!}

        {!! apply_filters('before_vacation_rental_booking_form', null, $vacationRental) !!}

        <div id="vacation-rental-calendar"
             data-vacation-rental-id="{{ $vacationRental->id }}"
             data-availability-url="{{ route('public.vacation-rental.api.availability', ['id' => $vacationRental->id]) }}"
             data-pricing-url="{{ route('public.vacation-rental.api.calculate-price', ['id' => $vacationRental->id]) }}"
             data-booking-url="{{ route('public.vacation-rental.booking.process') }}"
             data-login-url="{{ route('public.account.login') }}"
             data-min-stay="{{ $vacationRental->minimum_stay ?: 1 }}"
             data-max-stay="{{ $vacationRental->maximum_stay ?: '' }}"
             data-max-guests="{{ $vacationRental->maximum_guests ?: '' }}"
             data-is-logged-in="{{ auth('account')->check() ? 'true' : 'false' }}">
        </div>

        {!! apply_filters('after_vacation_rental_booking_form', null, $vacationRental) !!}

    </div>
@endif
