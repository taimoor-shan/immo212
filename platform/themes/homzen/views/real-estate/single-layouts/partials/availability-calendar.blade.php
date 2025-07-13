@if($property->type == \Botble\RealEstate\Enums\PropertyTypeEnum::VACATION_RENTAL)
    <div @class(['single-property-availability-calendar', $class ?? null])>
        <div class="h7 title fw-6">{{ __('Availability & Booking') }}</div>
        
        <div class="availability-calendar-widget mt-3" data-property-id="{{ $property->id }}">
            <!-- Date Selection -->
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">{{ __('Check-in') }}</label>
                    <input type="date" id="check-in-date" class="form-control" min="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ __('Check-out') }}</label>
                    <input type="date" id="check-out-date" class="form-control" min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">{{ __('Guests') }}</label>
                    <select id="guests-count" class="form-select">
                        @for($i = 1; $i <= ($property->maximum_guests ?: 10); $i++)
                            <option value="{{ $i }}">{{ $i }} {{ $i == 1 ? __('Guest') : __('Guests') }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <button type="button" id="calculate-price-btn" class="btn btn-primary w-100" disabled>
                        {{ __('Check Availability') }}
                    </button>
                </div>
            </div>

            <!-- Property Info -->
            <div class="property-info mb-3">
                <div class="row text-sm">
                    @if($property->minimum_stay)
                        <div class="col-md-6">
                            <strong>{{ __('Minimum stay:') }}</strong> {{ $property->minimum_stay }} {{ $property->minimum_stay == 1 ? __('night') : __('nights') }}
                        </div>
                    @endif
                    @if($property->maximum_guests)
                        <div class="col-md-6">
                            <strong>{{ __('Max guests:') }}</strong> {{ $property->maximum_guests }}
                        </div>
                    @endif
                    @if($property->check_in_time)
                        <div class="col-md-6">
                            <strong>{{ __('Check-in:') }}</strong> {{ $property->check_in_time }}
                        </div>
                    @endif
                    @if($property->check_out_time)
                        <div class="col-md-6">
                            <strong>{{ __('Check-out:') }}</strong> {{ $property->check_out_time }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Loading State -->
            <div id="calendar-loading" class="text-center py-3" style="display: none;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">{{ __('Loading...') }}</span>
                </div>
            </div>

            <!-- Price Breakdown -->
            <div id="price-breakdown" class="price-breakdown" style="display: none;">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">{{ __('Price Breakdown') }}</h6>
                        <div id="price-details"></div>
                        <hr>
                        <div class="d-flex justify-content-between fw-bold">
                            <span>{{ __('Total') }}</span>
                            <span id="total-price"></span>
                        </div>
                        <div class="mt-3">
                            <button type="button" class="btn btn-success w-100" onclick="initiateBooking()">
                                {{ __('Book Now') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Error Messages -->
            <div id="error-message" class="alert alert-danger" style="display: none;"></div>

            <!-- Enhanced Calendar View with Flatpickr -->
            <div class="calendar-view mt-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6>{{ __('Availability Calendar') }}</h6>
                    <div class="calendar-legend">
                        <span class="legend-item available"><span class="dot"></span> {{ __('Available') }}</span>
                        <span class="legend-item unavailable"><span class="dot"></span> {{ __('Unavailable') }}</span>
                        <span class="legend-item selected"><span class="dot"></span> {{ __('Selected') }}</span>
                    </div>
                </div>

                <!-- Flatpickr Calendar Container -->
                <div class="flatpickr-calendar-container">
                    <input type="text" id="flatpickr-calendar" style="display: none;">
                </div>

                <!-- Fallback: Original Calendar Grid (hidden by default) -->
                <div id="calendar-grid" class="calendar-grid" style="display: none;">
                    <!-- Calendar will be populated by JavaScript as fallback -->
                </div>

                <!-- Legend -->
                <div class="calendar-legend mt-3">
                    <div class="d-flex flex-wrap gap-3 small">
                        <div class="d-flex align-items-center">
                            <div class="legend-color bg-success me-1"></div>
                            <span>{{ __('Available') }}</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="legend-color bg-primary me-1"></div>
                            <span>{{ __('Booked') }}</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="legend-color bg-warning me-1"></div>
                            <span>{{ __('Blocked') }}</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="legend-color bg-secondary me-1"></div>
                            <span>{{ __('Past') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .calendar-grid {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
        }
        
        .calendar-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }
        
        .calendar-day {
            border: 1px solid #dee2e6;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        
        .calendar-day:hover {
            background-color: #f8f9fa;
        }
        
        .calendar-day.available {
            background-color: #d1e7dd;
            color: #0f5132;
        }
        
        .calendar-day.booked {
            background-color: #cff4fc;
            color: #055160;
            cursor: not-allowed;
        }
        
        .calendar-day.blocked {
            background-color: #fff3cd;
            color: #664d03;
            cursor: not-allowed;
        }
        
        .calendar-day.past {
            background-color: #e9ecef;
            color: #6c757d;
            cursor: not-allowed;
        }
        
        .calendar-day.selected {
            background-color: #0d6efd;
            color: white;
        }
        
        .legend-color {
            width: 12px;
            height: 12px;
            border-radius: 2px;
        }
        
        .price-breakdown {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 1rem;
            background-color: #f8f9fa;
        }
    </style>

    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <style>
        /* Enhanced Calendar Styling */
        .calendar-legend {
            display: flex;
            gap: 15px;
            font-size: 12px;
        }

        .calendar-legend .legend-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .calendar-legend .dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            border: 1px solid rgba(0,0,0,0.2);
        }

        .calendar-legend .available .dot {
            background-color: #28a745;
        }

        .calendar-legend .unavailable .dot {
            background-color: #dc3545;
        }

        .calendar-legend .selected .dot {
            background-color: #007bff;
        }

        /* Flatpickr Enhancements */
        .flatpickr-calendar-container .flatpickr-calendar {
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            border: 1px solid #e9ecef;
            border-radius: 12px;
            font-family: inherit;
        }

        .flatpickr-calendar-container .flatpickr-day.available {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .flatpickr-calendar-container .flatpickr-day.unavailable {
            background-color: #f8d7da;
            color: #721c24;
            cursor: not-allowed;
            opacity: 0.6;
        }

        .flatpickr-calendar-container .flatpickr-day.selected,
        .flatpickr-calendar-container .flatpickr-day.startRange,
        .flatpickr-calendar-container .flatpickr-day.endRange,
        .flatpickr-calendar-container .flatpickr-day.inRange {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .calendar-legend {
                flex-wrap: wrap;
                gap: 10px;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const propertyId = {{ $property->id }};
            const checkInInput = document.getElementById('check-in-date');
            const checkOutInput = document.getElementById('check-out-date');
            const guestsSelect = document.getElementById('guests-count');
            const calculateBtn = document.getElementById('calculate-price-btn');
            const priceBreakdown = document.getElementById('price-breakdown');
            const errorMessage = document.getElementById('error-message');
            const loading = document.getElementById('calendar-loading');

            let currentMonth = new Date();
            let availabilityData = {};
            let flatpickrInstance = null;
            
            // Initialize calendar
            loadCalendar();
            initializeFlatpickr();
            
            // Event listeners
            checkInInput.addEventListener('change', validateDates);
            checkOutInput.addEventListener('change', validateDates);
            calculateBtn.addEventListener('click', calculatePrice);
            
            document.getElementById('prev-month').addEventListener('click', () => {
                currentMonth.setMonth(currentMonth.getMonth() - 1);
                loadCalendar();
            });
            
            document.getElementById('next-month').addEventListener('click', () => {
                currentMonth.setMonth(currentMonth.getMonth() + 1);
                loadCalendar();
            });

            // Flatpickr initialization
            function initializeFlatpickr() {
                // Load Flatpickr if not already loaded
                if (typeof flatpickr === 'undefined') {
                    const script = document.createElement('script');
                    script.src = 'https://cdn.jsdelivr.net/npm/flatpickr';
                    script.onload = function() {
                        createFlatpickrInstance();
                    };
                    document.head.appendChild(script);
                } else {
                    createFlatpickrInstance();
                }
            }

            function createFlatpickrInstance() {
                flatpickrInstance = flatpickr('#flatpickr-calendar', {
                    mode: 'range',
                    inline: true,
                    dateFormat: 'Y-m-d',
                    minDate: 'today',
                    showMonths: window.innerWidth > 768 ? 2 : 1,
                    onDayCreate: function(dObj, dStr, fp, dayElem) {
                        const date = dayElem.dateObj.toISOString().split('T')[0];
                        const availability = availabilityData[date];

                        if (availability === false || availability === 'unavailable') {
                            dayElem.classList.add('unavailable');
                            dayElem.setAttribute('disabled', 'disabled');
                        } else if (availability === true || availability === 'available') {
                            dayElem.classList.add('available');
                        }
                    },
                    onChange: function(selectedDates) {
                        if (selectedDates.length === 2) {
                            checkInInput.value = selectedDates[0].toISOString().split('T')[0];
                            checkOutInput.value = selectedDates[1].toISOString().split('T')[0];
                            validateDates();
                        }
                    }
                });

                // Show Flatpickr container and hide fallback
                document.querySelector('.flatpickr-calendar-container').style.display = 'block';
                document.getElementById('calendar-grid').style.display = 'none';
            }

            function validateDates() {
                const checkIn = checkInInput.value;
                const checkOut = checkOutInput.value;
                
                if (checkIn && checkOut) {
                    const checkInDate = new Date(checkIn);
                    const checkOutDate = new Date(checkOut);
                    
                    if (checkOutDate > checkInDate) {
                        calculateBtn.disabled = false;
                        calculateBtn.textContent = '{{ __("Check Availability") }}';
                    } else {
                        calculateBtn.disabled = true;
                        calculateBtn.textContent = '{{ __("Invalid dates") }}';
                    }
                } else {
                    calculateBtn.disabled = true;
                    calculateBtn.textContent = '{{ __("Select dates") }}';
                }
                
                hideMessages();
            }
            
            function loadCalendar() {
                const startDate = new Date(currentMonth.getFullYear(), currentMonth.getMonth(), 1);
                const endDate = new Date(currentMonth.getFullYear(), currentMonth.getMonth() + 1, 0);
                
                document.getElementById('current-month').textContent = 
                    currentMonth.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
                
                loading.style.display = 'block';
                
                fetch(`{{ route('public.ajax.vacation-rentals.availability') }}?property_id=${propertyId}&start_date=${startDate.toISOString().split('T')[0]}&end_date=${endDate.toISOString().split('T')[0]}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        showError(data.message);
                    } else {
                        availabilityData = data.data;
                        renderCalendar();

                        // Refresh Flatpickr if it exists
                        if (flatpickrInstance) {
                            flatpickrInstance.redraw();
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showError('{{ __("Failed to load calendar") }}');
                })
                .finally(() => {
                    loading.style.display = 'none';
                });
            }
            
            function renderCalendar() {
                const calendarGrid = document.getElementById('calendar-grid');
                const startDate = new Date(currentMonth.getFullYear(), currentMonth.getMonth(), 1);
                const endDate = new Date(currentMonth.getFullYear(), currentMonth.getMonth() + 1, 0);
                const today = new Date();
                
                let html = '<div class="row calendar-header text-center fw-bold py-2">';
                const days = ['{{ __("Sun") }}', '{{ __("Mon") }}', '{{ __("Tue") }}', '{{ __("Wed") }}', '{{ __("Thu") }}', '{{ __("Fri") }}', '{{ __("Sat") }}'];
                days.forEach(day => {
                    html += `<div class="col">${day}</div>`;
                });
                html += '</div>';
                
                // Add calendar days
                const firstDayOfWeek = startDate.getDay();
                const daysInMonth = endDate.getDate();
                
                let dayCount = 1;
                for (let week = 0; week < 6; week++) {
                    html += '<div class="row">';
                    for (let day = 0; day < 7; day++) {
                        if (week === 0 && day < firstDayOfWeek) {
                            html += '<div class="col calendar-day"></div>';
                        } else if (dayCount <= daysInMonth) {
                            const currentDate = new Date(currentMonth.getFullYear(), currentMonth.getMonth(), dayCount);
                            const dateKey = currentDate.toISOString().split('T')[0];
                            const dayInfo = availabilityData[dateKey];
                            
                            let cssClass = 'calendar-day';
                            if (currentDate < today) {
                                cssClass += ' past';
                            } else if (dayInfo) {
                                cssClass += ` ${dayInfo.status}`;
                            } else {
                                cssClass += ' available';
                            }
                            
                            html += `<div class="col ${cssClass}" data-date="${dateKey}">${dayCount}</div>`;
                            dayCount++;
                        } else {
                            html += '<div class="col calendar-day"></div>';
                        }
                    }
                    html += '</div>';
                    
                    if (dayCount > daysInMonth) break;
                }
                
                calendarGrid.innerHTML = html;
                
                // Add click handlers for available dates
                calendarGrid.querySelectorAll('.calendar-day.available').forEach(day => {
                    day.addEventListener('click', function() {
                        const date = this.dataset.date;
                        if (!checkInInput.value || (checkInInput.value && checkOutInput.value)) {
                            checkInInput.value = date;
                            checkOutInput.value = '';
                        } else if (checkInInput.value && !checkOutInput.value) {
                            if (new Date(date) > new Date(checkInInput.value)) {
                                checkOutInput.value = date;
                            } else {
                                checkInInput.value = date;
                            }
                        }
                        validateDates();
                    });
                });
            }
            
            function calculatePrice() {
                const checkIn = checkInInput.value;
                const checkOut = checkOutInput.value;
                const guests = guestsSelect.value;
                
                if (!checkIn || !checkOut) {
                    showError('{{ __("Please select check-in and check-out dates") }}');
                    return;
                }
                
                loading.style.display = 'block';
                hideMessages();
                
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                const headers = {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                };

                if (csrfToken) {
                    headers['X-CSRF-TOKEN'] = csrfToken.getAttribute('content');
                }

                fetch('{{ route("public.ajax.vacation-rentals.calculate-price") }}', {
                    method: 'POST',
                    headers: headers,
                    body: JSON.stringify({
                        property_id: propertyId,
                        check_in: checkIn,
                        check_out: checkOut,
                        guests: guests
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        showError(data.message);
                    } else {
                        displayPriceBreakdown(data.data);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showError('{{ __("Failed to calculate price") }}');
                })
                .finally(() => {
                    loading.style.display = 'none';
                });
            }
            
            function displayPriceBreakdown(data) {
                const pricing = data.pricing;

                // Store pricing data globally for the booking modal
                window.currentPricingData = pricing;

                let html = '';

                html += `<div class="d-flex justify-content-between"><span>${pricing.nights} {{ __('nights') }} × $${pricing.base_price_per_night.toFixed(2)}</span><span>$${pricing.total_nights_cost.toFixed(2)}</span></div>`;

                if (pricing.cleaning_fee > 0) {
                    html += `<div class="d-flex justify-content-between"><span>{{ __('Cleaning fee') }}</span><span>$${pricing.cleaning_fee.toFixed(2)}</span></div>`;
                }

                if (pricing.service_fee > 0) {
                    html += `<div class="d-flex justify-content-between"><span>{{ __('Service fee') }}</span><span>$${pricing.service_fee.toFixed(2)}</span></div>`;
                }

                if (pricing.taxes > 0) {
                    html += `<div class="d-flex justify-content-between"><span>{{ __('Taxes') }}</span><span>$${pricing.taxes.toFixed(2)}</span></div>`;
                }

                document.getElementById('price-details').innerHTML = html;
                document.getElementById('total-price').textContent = `$${pricing.total_amount.toFixed(2)}`;

                priceBreakdown.style.display = 'block';
            }
            
            function showError(message) {
                errorMessage.textContent = message;
                errorMessage.style.display = 'block';
                priceBreakdown.style.display = 'none';
            }
            
            function hideMessages() {
                errorMessage.style.display = 'none';
                priceBreakdown.style.display = 'none';
            }
            
            window.initiateBooking = function() {
                const checkIn = checkInInput.value;
                const checkOut = checkOutInput.value;
                const guests = guestsSelect.value;

                if (!checkIn || !checkOut) {
                    alert('{{ __("Please select check-in and check-out dates") }}');
                    return;
                }

                // Get the current pricing data
                const pricingData = window.currentPricingData || null;

                // Populate and show the booking modal
                if (typeof populateBookingModal === 'function') {
                    populateBookingModal(checkIn, checkOut, guests, pricingData);
                }

                // Show the modal
                const bookingModal = new bootstrap.Modal(document.getElementById('bookingModal'));
                bookingModal.show();
            };
        });
    </script>

    <!-- Include Booking Modal -->
    @include(Theme::getThemeNamespace('views.real-estate.single-layouts.partials.booking-modal'))
@endif
