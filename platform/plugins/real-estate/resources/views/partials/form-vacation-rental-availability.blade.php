@php
    $propertyId = $property && $property->exists ? $property->id : null;
@endphp

<div class="vacation-rental-availability-section" id="vacation-rental-availability-content">
    <!-- Calendar Section - Always present, controlled by JavaScript -->
    <div id="calendar-section" style="display: none;">
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

    <!-- Info Message Section - Always present, controlled by JavaScript -->
    <div class="alert alert-info" id="vacation-rental-info-message" style="display: none;">
        <x-core::icon name="ti ti-info-circle" class="me-2" />
        <span id="info-message-text">
            {{ __('Select "Vacation Rental" as property type to enable availability calendar management.') }}
        </span>
    </div>
</div>

<!-- Data script - placed outside Vue template to avoid warnings -->
<script>
    // Pass existing availability data to JavaScript
    @if(isset($property) && $property->id)
        @php
            $availabilityService = app(\Botble\RealEstate\Services\SavePropertyAvailabilityService::class);
            $existingData = $availabilityService->getPropertyAvailabilityForForm($property);
        @endphp
        window.propertyAvailabilityData = @json($existingData);
        console.log('Property availability data loaded:', window.propertyAvailabilityData);
    @else
        window.propertyAvailabilityData = {};
        console.log('No property data - new property');
    @endif
    
    // Initialize calendar when page loads
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Initializing Vacation Rental Calendar');
        
        // Show the calendar section
        var calendarSection = document.getElementById('calendar-section');
        if (calendarSection) {
            calendarSection.style.display = 'block';
        }
        
        // Wait a bit for scripts to load, then initialize calendar
        setTimeout(function() {
            @if(isset($property) && $property->id)
                // Try to find VacationRentalCalendar in window scope
                if (typeof window.VacationRentalCalendar !== 'undefined') {
                    console.log('Found VacationRentalCalendar class, initializing...');
                    window.vacationRentalCalendar = new window.VacationRentalCalendar({
                        propertyId: {{ $property->id }},
                        container: '#property-availability-calendar',
                        apiEndpoint: '{{ route('vacation-rental.availability-data') }}',
                        blockDatesEndpoint: '{{ route('vacation-rental.block-dates') }}',
                        unblockDatesEndpoint: '{{ route('vacation-rental.unblock-dates') }}'
                    });
                } else {
                    console.error('VacationRentalCalendar class not found in window scope');
                    
                    // Try a simpler initialization with just the calendar
                    var calendarContainer = document.getElementById('property-availability-calendar');
                    if (calendarContainer && typeof flatpickr !== 'undefined') {
                        console.log('Using flatpickr directly for calendar');
                        
                        // Initialize flatpickr directly
                        var calendar = flatpickr(calendarContainer, {
                            inline: true,
                            mode: 'multiple',
                            dateFormat: 'Y-m-d',
                            minDate: 'today',
                            showMonths: 2,
                            onDayCreate: function(dObj, dStr, fp, dayElem) {
                                var date = dayElem.dateObj.toISOString().split('T')[0];
                                if (window.propertyAvailabilityData && window.propertyAvailabilityData.availability_by_date) {
                                    var dayData = window.propertyAvailabilityData.availability_by_date[date];
                                    if (dayData) {
                                        dayElem.classList.add('has-availability-data');
                                        dayElem.classList.add(dayData.status);
                                        if (dayData.reason) {
                                            dayElem.title = dayData.reason;
                                        }
                                    }
                                }
                            }
                        });
                        
                        // Store calendar instance
                        window.vacationRentalCalendar = calendar;
                        
                        // Add button event handlers
                        setupButtonHandlers();
                    }
                }
            @endif
        }, 1000); // Wait 1 second for scripts to fully load
    });
    
    // Setup button handlers for calendar actions
    function setupButtonHandlers() {
        console.log('Setting up button handlers');
        
        // Apply button for confirming action
        var applyButton = document.createElement('button');
        applyButton.type = 'button';
        applyButton.className = 'btn btn-primary btn-sm mt-2';
        applyButton.textContent = 'Apply';
        applyButton.id = 'apply-action';
        
        var reasonContainer = document.getElementById('block-reason-container');
        if (reasonContainer && !document.getElementById('apply-action')) {
            reasonContainer.appendChild(applyButton);
        }
        
        var currentAction = null;
        
        // Block dates button
        document.getElementById('block-selected-dates')?.addEventListener('click', function() {
            var dates = getSelectedDates();
            if (dates.length === 0) {
                alert('Please select dates first');
                return;
            }
            
            currentAction = 'block';
            document.getElementById('block-reason-container').style.display = 'block';
            document.getElementById('block-reason').placeholder = 'Enter reason for blocking dates...';
            document.getElementById('block-reason').value = '';
        });
        
        // Unblock dates button
        document.getElementById('unblock-selected-dates')?.addEventListener('click', function() {
            var dates = getSelectedDates();
            if (dates.length === 0) {
                alert('Please select dates first');
                return;
            }
            
            // Direct action for unblock
            if (confirm('Are you sure you want to unblock the selected dates?')) {
                updateAvailability('unblock', dates);
            }
        });
        
        // Maintenance dates button
        document.getElementById('set-maintenance-dates')?.addEventListener('click', function() {
            var dates = getSelectedDates();
            if (dates.length === 0) {
                alert('Please select dates first');
                return;
            }
            
            currentAction = 'maintenance';
            document.getElementById('block-reason-container').style.display = 'block';
            document.getElementById('block-reason').placeholder = 'Enter maintenance reason...';
            document.getElementById('block-reason').value = '';
        });
        
        // Apply action button
        applyButton?.addEventListener('click', function() {
            if (!currentAction) return;
            
            var dates = getSelectedDates();
            var reason = document.getElementById('block-reason').value || 
                        (currentAction === 'maintenance' ? 'Maintenance scheduled' : 'Blocked by owner');
            
            updateAvailability(currentAction, dates, reason);
            
            // Hide reason container
            document.getElementById('block-reason-container').style.display = 'none';
            currentAction = null;
        });
    }
    
    // Get selected dates from calendar
    function getSelectedDates() {
        var calendar = window.vacationRentalCalendar;
        if (!calendar) {
            console.error('Calendar not initialized');
            return [];
        }
        
        // For Flatpickr instance
        if (calendar.selectedDates) {
            return calendar.selectedDates;
        }
        
        // For custom calendar instance
        if (calendar.getSelectedDates && typeof calendar.getSelectedDates === 'function') {
            return calendar.getSelectedDates();
        }
        
        console.warn('Could not get selected dates from calendar');
        return [];
    }
    
    // Update availability via AJAX
    function updateAvailability(action, dates, reason) {
        var propertyId = {{ $property->id ?? 'null' }};
        if (!propertyId) return;
        
        var endpoint = '';
        switch(action) {
            case 'block':
                endpoint = '{{ route('vacation-rental.block-dates') }}';
                break;
            case 'unblock':
                endpoint = '{{ route('vacation-rental.unblock-dates') }}';
                break;
            case 'maintenance':
                endpoint = '{{ route('vacation-rental.maintenance-dates') ?? route('vacation-rental.block-dates') }}';
                break;
        }
        
        // Format dates
        var formattedDates = dates.map(function(date) {
            if (typeof date === 'string') return date;
            return date.toISOString().split('T')[0];
        });
        
        // Sort dates to get start and end
        formattedDates.sort();
        var startDate = formattedDates[0];
        var endDate = formattedDates[formattedDates.length - 1];
        
        // Make AJAX request
        fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify({
                vacation_rental_id: propertyId,
                start_date: startDate,
                end_date: endDate,
                reason: reason || ''
            })
        })
        .then(response => {
            // Check if response is ok
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.status);
            }
            // Try to parse as JSON
            return response.text().then(text => {
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error('Response is not JSON:', text);
                    throw new Error('Server returned non-JSON response');
                }
            });
        })
        .then(data => {
            if (data.error === false || data.success) {
                alert(data.message || 'Dates updated successfully!');
                
                // Reload the page to refresh calendar
                window.location.reload();
            } else {
                alert(data.message || 'An error occurred');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating dates: ' + error.message);
        });
    }
</script>
