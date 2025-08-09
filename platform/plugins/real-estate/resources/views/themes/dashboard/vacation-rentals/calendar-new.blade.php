@extends('plugins/real-estate::themes.dashboard.layouts.master')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('Availability Calendar') }}</h3>
        </div>
        
        <div class="card-body">
            <!-- Property Selection -->
            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">{{ __('Select Property') }}</label>
                    <select name="property_id" class="form-select" id="property-select" onchange="this.form.submit()">
                        <option value="">{{ __('Choose a property...') }}</option>
                        @foreach($properties as $property)
                            <option value="{{ $property->id }}" {{ request('property_id') == $property->id ? 'selected' : '' }}>
                                {{ $property->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>

            @if($selectedProperty)
                <div class="vacation-rental-availability-section">
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
                            <div class="col-md-9">
                                <div class="property-availability-calendar">
                                    <div id="property-availability-calendar"
                                         data-property-id="{{ $selectedProperty->id }}">
                                        <!-- Calendar will be rendered here -->
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
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
                </div>

                <!-- Data script -->
                <script>
                    // Pass existing availability data to JavaScript
                    @if($selectedProperty->id)
                        @php
                            $availabilityService = app(\Botble\RealEstate\Services\SavePropertyAvailabilityService::class);
                            $existingData = $availabilityService->getPropertyAvailabilityForForm($selectedProperty);
                        @endphp
                        window.propertyAvailabilityData = @json($existingData);
                    @else
                        window.propertyAvailabilityData = {};
                    @endif
                </script>
            @else
                <div class="empty">
                    <div class="empty-icon">
                        <x-core::icon name="ti ti-calendar" />
                    </div>
                    <p class="empty-title">{{ __('Select a property') }}</p>
                    <p class="empty-subtitle text-muted">
                        {{ __('Choose a vacation rental property from the dropdown above to manage its availability.') }}
                    </p>
                </div>
            @endif
        </div>
    </div>

    <style>
        .calendar-legend {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .legend-color {
            width: 16px;
            height: 16px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        
        .legend-color.available {
            background-color: #d4edda;
            border-color: #28a745;
        }
        
        .legend-color.booked {
            background-color: #f8d7da;
            border-color: #dc3545;
        }
        
        .legend-color.blocked {
            background-color: #fff3cd;
            border-color: #ffc107;
        }
        
        .legend-color.maintenance {
            background-color: #e2e3e5;
            border-color: #6c757d;
        }
        
        .property-availability-calendar {
            margin-bottom: 20px;
        }
        
        #property-availability-calendar {
            min-height: auto;
        }
        
        /* Calendar day colors to match legend */
        .flatpickr-day.available {
            background-color: #d4edda !important;
            border-color: #28a745 !important;
        }
        
        .flatpickr-day.booked {
            background-color: #f8d7da !important;
            border-color: #dc3545 !important;
        }
        
        .flatpickr-day.blocked {
            background-color: #fff3cd !important;
            border-color: #ffc107 !important;
        }
        
        .flatpickr-day.maintenance {
            background-color: #e2e3e5 !important;
            border-color: #6c757d !important;
        }
        
        .flatpickr-day.selected {
            background-color: #007bff !important;
            border-color: #007bff !important;
            color: white !important;
        }
        
        .day-price {
            position: absolute;
            bottom: 2px;
            right: 2px;
            font-size: 10px;
            line-height: 1;
            background: rgba(255, 255, 255, 0.8);
            padding: 2px 4px;
            border-radius: 2px;
        }
    </style>
@endsection

@push('header')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="{{ asset('vendor/core/plugins/real-estate/css/calendar-backend.css') }}?v={{ time() }}">
@endpush

@push('footer')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="{{ asset('vendor/core/plugins/real-estate/js/vacation-rental-calendar-standalone.js') }}?v={{ time() }}"></script>
    <script>
        $(document).ready(function() {
            @if($selectedProperty)
                // Initialize the standalone calendar
                const standaloneCalendar = new StandaloneVacationCalendar({
                    container: '#property-availability-calendar',
                    propertyId: {{ $selectedProperty->id }}
                });
                
                // Handle confirm button for reason input
                const confirmBtn = document.createElement('button');
                confirmBtn.className = 'btn btn-primary btn-sm mt-2';
                confirmBtn.textContent = '{{ __('Confirm') }}';
                confirmBtn.id = 'confirm-action';
                document.getElementById('block-reason-container').appendChild(confirmBtn);
                
                confirmBtn.addEventListener('click', function() {
                    const reasonContainer = document.getElementById('block-reason-container');
                    const pendingAction = reasonContainer.dataset.pendingAction;
                    if (pendingAction) {
                        // Trigger the action with reason
                        standaloneCalendar.handleAction(pendingAction);
                    }
                });
            @endif
        });
    </script>
@endpush
