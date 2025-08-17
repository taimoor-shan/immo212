@php
    // Determine context and configuration
    $context = $context ?? 'frontend'; // frontend, admin, agent
    $vacationRentalId = $vacationRental->id ?? $vacationRentalId ?? null;
    $showActions = $showActions ?? ($context !== 'frontend');
    $showLegend = $showLegend ?? true;
    $showBookingForm = $showBookingForm ?? ($context === 'frontend');
    $calendarId = $calendarId ?? 'vacation-rental-calendar-' . $context;
    $containerId = $containerId ?? 'vacation-rental-calendar-container-' . $context;
@endphp

<div class="vacation-rental-calendar-wrapper" id="{{ $containerId }}"
     data-context="{{ $context }}"
     data-vacation-rental-id="{{ $vacationRentalId }}"
     @if($context === 'frontend')
     data-availability-url="{{ route('public.vacation-rental.api.availability', ['id' => $vacationRentalId]) }}"
     data-pricing-url="{{ route('public.vacation-rental.api.calculate-price', ['id' => $vacationRentalId]) }}"
     data-booking-url="{{ route('public.vacation-rental.booking.process') }}"
     data-login-url="{{ route('public.account.login') }}"
     data-min-stay="{{ $vacationRental->minimum_stay ?? 1 }}"
     data-max-stay="{{ $vacationRental->maximum_stay ?? '' }}"
     data-max-guests="{{ $vacationRental->maximum_guests ?? '' }}"
     data-is-logged-in="{{ auth('account')->check() ? 'true' : 'false' }}"
     @endif>

    @if($showLegend)
        <!-- Calendar Legend -->
        <div class="calendar-legend mb-3">
            <div class="legend-item">
                <span class="legend-color available"></span>
                {{ __('Available') }}
            </div>
            <div class="legend-item">
                <span class="legend-color booked"></span>
                {{ __('Booked') }}
            </div>
            <div class="legend-item">
                <span class="legend-color blocked"></span>
                {{ __('Blocked') }}
            </div>
            <div class="legend-item">
                <span class="legend-color maintenance"></span>
                {{ __('Maintenance') }}
            </div>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-{{ $showActions ? '9' : '12' }}">
            <!-- Calendar Container -->
            <div class="vacation-rental-calendar-container">
                <div id="{{ $calendarId }}" class="vacation-rental-calendar">
                    <!-- Calendar will be rendered here by JavaScript -->
                </div>
            </div>

            @if($context === 'frontend' && $showBookingForm)
                <!-- Frontend Booking Form -->
                <div id="booking-form-container" class="mt-4" style="display: none;">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">{{ __('Book This Vacation Rental') }}</h5>
                        </div>
                        <div class="card-body">
                            <form id="vacation-rental-booking-form">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="guests_count" class="form-label">{{ __('Number of Guests') }}</label>
                                            <select id="guests_count" name="guests_count" class="form-control" required>
                                                @for($i = 1; $i <= ($vacationRental->maximum_guests ?? 10); $i++)
                                                    <option value="{{ $i }}">{{ $i }} {{ $i == 1 ? __('Guest') : __('Guests') }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">{{ __('Selected Dates') }}</label>
                                            <div id="selected-dates-display" class="form-control-plaintext">
                                                {{ __('Please select dates from the calendar') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="pricing-breakdown" class="mb-3" style="display: none;">
                                    <!-- Pricing details will be populated by JavaScript -->
                                </div>

                                @auth('account')
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="guest_name" class="form-label">{{ __('Full Name') }}</label>
                                                <input type="text" id="guest_name" name="guest_name" class="form-control"
                                                       value="{{ auth('account')->user()->name }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="guest_email" class="form-label">{{ __('Email') }}</label>
                                                <input type="email" id="guest_email" name="guest_email" class="form-control"
                                                       value="{{ auth('account')->user()->email }}" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="guest_phone" class="form-label">{{ __('Phone') }}</label>
                                        <input type="tel" id="guest_phone" name="guest_phone" class="form-control"
                                               value="{{ auth('account')->user()->phone }}">
                                    </div>

                                    <div class="form-check mb-3">
                                        <input type="checkbox" id="terms_accepted" name="terms_accepted" class="form-check-input" required>
                                        <label for="terms_accepted" class="form-check-label">
                                            {{ __('I agree to the terms and conditions') }}
                                        </label>
                                    </div>

                                    <button type="submit" id="book-now-btn" class="btn btn-primary w-100">
                                        {{ __('Book Now') }}
                                    </button>
                                @else
                                    <div class="text-center">
                                        <p>{{ __('Please log in to make a booking') }}</p>
                                        <a href="{{ route('public.account.login') }}" class="btn btn-primary">
                                            {{ __('Login to Book') }}
                                        </a>
                                    </div>
                                @endauth
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        @if($showActions)
            <div class="col-lg-3">
                <!-- Admin/Agent Actions -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">{{ __('Calendar Actions') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-danger" id="block-selected-dates">
                                <i class="fas fa-ban me-2"></i>
                                {{ __('Block Dates') }}
                            </button>
                            <button type="button" class="btn btn-success" id="unblock-selected-dates">
                                <i class="fas fa-check me-2"></i>
                                {{ __('Unblock Dates') }}
                            </button>
                            <button type="button" class="btn btn-secondary" id="set-maintenance-dates">
                                <i class="fas fa-tools me-2"></i>
                                {{ __('Maintenance') }}
                            </button>
                        </div>

                        <!-- Block Reason Input -->
                        <div class="mt-3" id="block-reason-container" style="display: none;">
                            <label for="block-reason" class="form-label">{{ __('Reason (Optional)') }}</label>
                            <textarea id="block-reason" class="form-control" rows="2"
                                      placeholder="{{ __('Enter reason for blocking dates...') }}"></textarea>
                        </div>
                    </div>
                </div>

                @if($context === 'admin')
                    <!-- Admin-specific actions -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title">{{ __('Admin Actions') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-info" id="view-bookings">
                                    <i class="fas fa-calendar-check me-2"></i>
                                    {{ __('View Bookings') }}
                                </button>
                                <button type="button" class="btn btn-warning" id="export-calendar">
                                    <i class="fas fa-download me-2"></i>
                                    {{ __('Export Calendar') }}
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>

    <!-- Loading and error messages -->
    <div id="calendar-loading" class="text-center p-3">
        <div class="spinner-border" role="status">
            <span class="visually-hidden">{{ __('Loading...') }}</span>
        </div>
    </div>

    <div id="calendar-error" class="alert alert-danger" style="display: none;">
        <!-- Error messages will be populated by JavaScript -->
    </div>
</div>

@once
    @push('header')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <link rel="stylesheet" href="/vendor/core/plugins/real-estate/css/vacation-rental-calendar.css?v={{ time() }}">
    @endpush

    @push('footer')
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        @if($context === 'frontend')
            <script src="/vendor/core/plugins/real-estate/js/vacation-rental-calendar-frontend.js?v={{ time() }}"></script>
        @elseif($context === 'admin')
            <script src="/vendor/core/plugins/real-estate/js/vacation-rental-calendar-admin.js?v={{ time() }}"></script>
        @elseif($context === 'agent')
            <script src="/vendor/core/plugins/real-estate/js/vacation-rental-calendar-agent.js?v={{ time() }}"></script>
        @endif
    @endpush
@endonce
