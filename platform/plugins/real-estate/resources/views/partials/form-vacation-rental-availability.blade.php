@php
    $propertyId = $vacationRental && $vacationRental->exists ? $vacationRental->id : null;
    Botble\Base\Facades\Assets::addScriptsDirectly('vendor/core/plugins/real-estate/js/admin-calendar.js');
@endphp

<div class="vacation-rental-availability-section" id="vacation-rental-availability-content">
    <!-- Calendar Section - Always visible for vacation rentals -->
    <div id="calendar-section">
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

            <!-- Calendar Container -->
            <div class="row">
                <div class="col-lg-9">
            <div class="property-availability-calendar">
                <div id="property-availability-calendar" class="vacation-rental-admin-calendar"
                     data-property-id="{{ $propertyId }}"
                     data-vacation-rental-id="{{ $propertyId }}"
                     data-availability-url="{{ route('vacation-rental.admin.availability-data') }}"
                     data-block-url="{{ route('vacation-rental.admin.block-dates') }}"
                     data-unblock-url="{{ route('vacation-rental.admin.unblock-dates') }}"
                     data-maintenance-url="{{ route('vacation-rental.admin.maintenance-dates') }}">
                            <!-- Calendar will be rendered here -->
                        </div>
                    </div>
                </div>

                <div class="col-lg-3">
                    <!-- Quick Actions -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">{{ __('Calendar Actions') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-danger" id="admin-block-dates" disabled>
                                    <x-core::icon name="ti ti-ban" class="me-2" />
                                    {{ __('Block Dates') }}
                                </button>
                                <button type="button" class="btn btn-success" id="admin-unblock-dates" disabled>
                                    <x-core::icon name="ti ti-check" class="me-2" />
                                    {{ __('Unblock Dates') }}
                                </button>
                                <button type="button" class="btn btn-secondary" id="admin-maintenance-dates" disabled>
                                    <x-core::icon name="ti ti-tools" class="me-2" />
                                    {{ __('Maintenance') }}
                                </button>

                            </div>
                            <!-- Block Reason Input -->
                            <div class="mt-3" id="admin-reason-container" style="display: none;">
                                <label for="admin-reason" class="form-label">{{ __('Reason (Optional)') }}</label>
                                <textarea id="admin-reason" class="form-control" rows="2" placeholder="{{ __('Enter reason for blocking dates...') }}"></textarea>
                                <div class="mt-2">
                                    <button type="button" class="btn btn-primary btn-sm" id="admin-apply-reason">
                                        <x-core::icon name="ti ti-check" class="me-1" />
                                        {{ __('Apply') }}
                                    </button>
                                    <button type="button" class="btn btn-secondary btn-sm" id="admin-cancel-reason">
                                        <x-core::icon name="ti ti-x" class="me-1" />
                                        {{ __('Cancel') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <!-- Info Message Section - Hidden for vacation rentals since calendar is always shown -->
    <div class="alert alert-info" id="vacation-rental-info-message" style="display: none;">
        <x-core::icon name="ti ti-info-circle" class="me-2" />
        <span id="info-message-text">
            {{ __('Manage your vacation rental availability using the calendar below.') }}
        </span>
    </div>

    <!-- Hidden Form Fields for Calendar Data -->
    <input type="hidden" name="availability_data[blocked_dates]" id="blocked-dates-input" value="">
    <input type="hidden" name="availability_data[maintenance_dates]" id="maintenance-dates-input" value="">
    <input type="hidden" name="availability_data[unblocked_dates]" id="unblocked-dates-input" value="">
</div>

<!-- Data script - placed outside Vue template to avoid warnings -->
<script>
    // Pass existing availability data to JavaScript
    @if(isset($vacationRental) && $vacationRental->id)
        @php
            $availabilityService = app(\Botble\RealEstate\Services\SaveVacationRentalAvailabilityService::class);
            $existingData = $availabilityService->getVacationRentalAvailabilityForForm($vacationRental);
        @endphp
        window.propertyAvailabilityData = @json($existingData);
        console.log('Vacation rental availability data loaded:', window.propertyAvailabilityData);
    @else
        window.propertyAvailabilityData = {};
        console.log('No vacation rental data - new vacation rental');
    @endif
</script>

    <!-- Include Flatpickr CSS and Vacation Rental Styles -->
    @push('header')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <link rel="stylesheet" href="{{ asset('vendor/core/plugins/real-estate/css/calendar-backend.css') }}?v={{ time() }}">
    @endpush

    <!-- Include JavaScript -->
    @push('footer')
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script>
            // Show calendar section immediately for vacation rentals
            document.addEventListener('DOMContentLoaded', function() {
                const calendarSection = document.getElementById('calendar-section');
                if (calendarSection) {
                    calendarSection.style.display = 'block';
                }
            });
        </script>
    @endpush

