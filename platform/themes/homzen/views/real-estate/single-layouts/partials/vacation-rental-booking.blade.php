@if($vacationRental)
    <div @class(['widget-box single-vacation-rental-booking single-property-contact', $class ?? null])>
        <div class="h7 title fw-6">{{ __('Book This Vacation Rental') }}</div>

        {!! apply_filters('vacation_rental_right_details_info', null, $vacationRental) !!}

        {!! apply_filters('before_vacation_rental_booking_form', null, $vacationRental) !!}

        @include('theme.homzen::partials.vacation-rental-calendar', [
            'vacationRental' => $vacationRental
        ])

        {!! apply_filters('after_vacation_rental_booking_form', null, $vacationRental) !!}

    </div>
@endif
