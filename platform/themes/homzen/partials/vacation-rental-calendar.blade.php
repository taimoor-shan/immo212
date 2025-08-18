{{-- Frontend Vacation Rental Calendar Component --}}

{{-- Flatpickr is loaded via theme config.php when needed --}}

@if($vacationRental)
<div id="vacation-rental-calendar"
     data-vacation-rental-id="{{ $vacationRental->id }}"
     data-availability-url="{{ route('public.vacation-rental.api.availability', ['vacation_rental_id' => $vacationRental->id]) }}"
     data-pricing-url="{{ route('public.vacation-rental.api.calculate-price', ['vacation_rental_id' => $vacationRental->id]) }}"
     data-booking-url="{{ route('public.vacation-rental.booking.process') }}"
     data-login-url="{{ route('public.account.login') }}"
     data-min-stay="{{ $vacationRental->minimum_stay ?? 1 }}"
     data-max-stay="{{ $vacationRental->maximum_stay ?? '' }}"
     data-max-guests="{{ $vacationRental->maximum_guests ?? '' }}"
     data-is-logged-in="{{ auth('account')->check() ? 'true' : 'false' }}">

    <!-- Calendar Section -->
    <div class="calendar-section">
        <div class="calendar-title">
            <i class="ti ti-calendar"></i>
            {{ __('Select Your Dates') }}
        </div>

        <!-- Calendar Legend -->
        <div class="calendar-legend">
            <div class="legend-item">
                <span class="legend-color available"></span>
                {{ __('Available') }}
            </div>
            <div class="legend-item">
                <span class="legend-color booked"></span>
                {{ __('Booked') }}
            </div>
            <div class="legend-item">
                <span class="legend-color selected"></span>
                {{ __('Selected') }}
            </div>
        </div>

        <!-- Calendar Container -->
        <div class="calendar-container">
            <div class="flatpickr-calendar-container"></div>
        </div>
    </div>

    <!-- Booking Summary -->
    <div class="booking-summary" style="display: none;">
        <div class="summary-title">
            <i class="ti ti-receipt"></i>
            {{ __('Booking Summary') }}
        </div>

        <div class="summary-item">
            <span class="label">{{ __('Check-in') }}</span>
            <span class="value check-in-date">-</span>
        </div>

        <div class="summary-item">
            <span class="label">{{ __('Check-out') }}</span>
            <span class="value check-out-date">-</span>
        </div>

        <div class="summary-item">
            <span class="label">{{ __('Duration') }}</span>
            <span class="value nights-count">-</span>
        </div>

        <div class="summary-item">
            <span class="label">{{ __('Total Price') }}</span>
            <span class="value total-price">-</span>
        </div>
    </div>

    <!-- Booking Form -->
    <div class="booking-form">
        <div class="form-group">
            <label for="guest-count">{{ __('Number of Guests') }}</label>
            <select id="guest-count" class="form-control guest-count-input">
                @for($i = 1; $i <= ($vacationRental->maximum_guests ?? 10); $i++)
                    <option value="{{ $i }}">{{ $i }} {{ $i == 1 ? __('Guest') : __('Guests') }}</option>
                @endfor
            </select>
        </div>

        @if($vacationRental->minimum_stay > 1)
            <div class="form-group">
                <small class="text-muted">
                    <i class="ti ti-info-circle"></i>
                    {{ __('Minimum stay: :nights nights', ['nights' => $vacationRental->minimum_stay]) }}
                </small>
            </div>
        @endif

        @if($vacationRental->maximum_stay)
            <div class="form-group">
                <small class="text-muted">
                    <i class="ti ti-info-circle"></i>
                    {{ __('Maximum stay: :nights nights', ['nights' => $vacationRental->maximum_stay]) }}
                </small>
            </div>
        @endif

        <button type="button" class="btn btn-book-now" disabled>
            {{ __('Select Dates') }}
        </button>

        @if(!auth('account')->check())
            <div class="mt-3 text-center">
                <small class="text-muted">
                    {{ __('You need to') }}
                    <a href="{{ route('public.account.login') }}" class="text-primary">{{ __('login') }}</a>
                    {{ __('to make a booking') }}
                </small>
            </div>
        @endif
    </div>

    <!-- Vacation Rental Info -->
    @if($vacationRental->price)
        <div class="mt-3 text-center">
            <small class="text-muted">
                {{ __('Starting from') }}
                <strong class="text-primary">{{ format_price($vacationRental->price) }}</strong>
                {{ __('per night') }}
            </small>
        </div>
    @endif
</div>
@else
<div class="alert alert-warning">
    <i class="ti ti-alert-triangle"></i>
    {{ __('Vacation rental information not available.') }}
</div>
@endif
