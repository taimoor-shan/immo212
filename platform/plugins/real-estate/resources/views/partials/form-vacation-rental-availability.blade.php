@php
    $propertyId = $vacationRental && $vacationRental->exists ? $vacationRental->id : null;
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
                        <div id="property-availability-calendar"
                             data-property-id="{{ $propertyId }}">
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
                                <button type="button" class="btn btn-danger" id="block-selected-dates">
                                    <x-core::icon name="ti ti-ban" class="me-2" />
                                    {{ __('Block Dates') }}
                                </button>
                                <button type="button" class="btn btn-success" id="unblock-selected-dates">
                                    <x-core::icon name="ti ti-check" class="me-2" />
                                    {{ __('Unblock Dates') }}
                                </button>
                                <button type="button" class="btn btn-secondary" id="set-maintenance-dates">
                                    <x-core::icon name="ti ti-tools" class="me-2" />
                                    {{ __('Maintenance') }}
                                </button>
                            </div>
                            <!-- Block Reason Input -->
                            <div class="mt-3" id="block-reason-container" style="display: none;">
                                <label for="block-reason" class="form-label">{{ __('Reason (Optional)') }}</label>
                                <textarea id="block-reason" class="form-control" rows="2" placeholder="{{ __('Enter reason for blocking dates...') }}"></textarea>
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
            // Initialize vacation rental calendar immediately since this is a vacation rental form
            document.addEventListener('DOMContentLoaded', function() {
                console.log('Initializing vacation rental calendar...');

                // Show calendar section immediately for vacation rentals
                const calendarSection = document.getElementById('calendar-section');
                if (calendarSection) {
                    calendarSection.style.display = 'block';
                    console.log('Calendar section shown');
                }

                // Initialize the calendar if the function exists
                if (typeof initializePropertyAvailabilityCalendar === 'function') {
                    initializePropertyAvailabilityCalendar();
                    console.log('Calendar initialized');
                } else {
                    console.log('Calendar initialization function not found');
                }
            });
        </script>
    @endpush
