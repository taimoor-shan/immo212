{{-- Frontend Vacation Rental Calendar Component --}}

{{-- Flatpickr is loaded via theme config.php when needed --}}

@if($vacationRental)
<div id="vacation-rental-calendar"
     data-vacation-rental-id="{{ $vacationRental->id }}"
     data-availability-url="{{ route('public.vacation-rental.availability', $vacationRental->id) }}"
     data-pricing-url="{{ route('public.ajax.vacation-rentals.calculate-price', $vacationRental->id) }}"
     data-booking-url="{{ route('public.vacation-rental.booking.process') }}"
     data-login-url="{{ route('public.account.login') }}"
     data-min-stay="{{ $vacationRental->minimum_stay ?? 1 }}"
     data-max-stay="{{ $vacationRental->maximum_stay ?? '' }}"
     data-max-guests="{{ $vacationRental->maximum_guests ?? '' }}"
     data-is-logged-in="{{ auth('account')->check() ? 'true' : 'false' }}">

    <!-- Calendar Section -->

</div>


@endif
