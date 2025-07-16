import flatpickr from "flatpickr";


class VacationRentalFrontendCalendar {
    constructor(options = {}) {
        this.options = {
            propertyId: null,
            container: '#property-calendar',
            availabilityEndpoint: null,
            pricingEndpoint: null,
            bookingEndpoint: null,
            loginUrl: null,
            minStay: 1,
            maxStay: null,
            maxGuests: null,
            isLoggedIn: false,
            ...options
        };
        
        this.calendar = null;
        this.availabilityData = {};
        this.pricingData = {};
        this.selectedDates = [];
        this.checkInDate = null;
        this.checkOutDate = null;
        
        this.init();
    }

    init() {
        this.loadAvailabilityData();
        this.initializeCalendar();
        this.bindEvents();
    }

    async loadAvailabilityData() {
        if (!this.options.propertyId || !this.options.availabilityEndpoint) return;

        try {
            const startDate = this.getApiDate(new Date());
            const endDate = this.getApiDate(new Date(), 12);
            const response = await fetch(`${this.options.availabilityEndpoint}?start=${startDate}&end=${endDate}`, {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            console.log('Availability data loaded:', data);
            this.availabilityData = data.data || {};
        } catch (error) {
            console.error('Failed to load availability data:', error);
            this.availabilityData = {};
        }
    }

    initializeCalendar() {
        const container = document.querySelector(this.options.container);
        if (!container) return;

        // Create calendar HTML structure
        container.innerHTML = `
            <div class="calendar-wrapper">
                <div class="calendar-header">
                    <h5>Select your dates</h5>
                    <div class="calendar-legend">
                        <div class="legend-item available">
                            <div class="color-box"></div>
                            <span>Available</span>
                        </div>
                        <div class="legend-item booked">
                            <div class="color-box"></div>
                            <span>Booked</span>
                        </div>
                        <div class="legend-item blocked">
                            <div class="color-box"></div>
                            <span>Blocked</span>
                        </div>
                        <div class="legend-item maintenance">
                            <div class="color-box"></div>
                            <span>Maintenance</span>
                        </div>
                        <div class="legend-item selected">
                            <div class="color-box"></div>
                            <span>Selected</span>
                        </div>
                    </div>
                </div>
                <div class="calendar-container">
                    <input type="text" id="frontend-calendar-picker" style="display: none;">
                </div>
                <div class="booking-summary" id="booking-summary" style="display: none;">
                    <div class="summary-content">
                        <div class="date-range">
                            <div class="date-item">
                                <label>Check-in</label>
                                <span id="checkin-display">-</span>
                            </div>
                            <div class="date-item">
                                <label>Check-out</label>
                                <span id="checkout-display">-</span>
                            </div>
                            <div class="date-item">
                                <label>Nights</label>
                                <span id="nights-display">-</span>
                            </div>
                        </div>
                        <div class="guest-details">
                            <div class="form-group">
                                <label for="guest_name">Full Name</label>
                                <input type="text" id="guest_name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="guest_email">Email</label>
                                <input type="email" id="guest_email" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="guest_phone">Phone</label>
                                <input type="tel" id="guest_phone" class="form-control">
                            </div>
                             <div class="form-group">
                                <label for="guests_count">Guests</label>
                                <input type="number" id="guests_count" class="form-control" value="1" min="1" max="${this.options.maxGuests || 20}">
                            </div>
                        </div>
                        <div class="pricing-breakdown" id="pricing-breakdown">
                            <!-- Pricing details will be inserted here -->
                        </div>
                        <div class="total-price">
                            <strong>Total: <span id="total-price">$0</span></strong>
                        </div>
                        <div class="form-group terms-and-conditions">
                            <input type="checkbox" id="terms_accepted" required>
                            <label for="terms_accepted">I agree to the <a href="/terms-and-conditions" target="_blank">terms and conditions</a></label>
                        </div>
                        <button type="button" class="btn btn-primary btn-book" id="proceed-booking">
                            Book Now
                        </button>
                    </div>
                </div>
            </div>
        `;

        // Initialize Flatpickr
        this.calendar = flatpickr('#frontend-calendar-picker', {
            mode: 'range',
            inline: true,
            dateFormat: 'Y-m-d',
            minDate: 'today',
            showMonths: 1,
            onDayCreate: (dObj, dStr, fp, dayElem) => {
                const date = dayElem.dateObj.toISOString().split('T')[0];
                const availability = this.availabilityData[date];

                // Remove any existing availability classes
                dayElem.classList.remove('calendar-available', 'calendar-booked', 'calendar-blocked', 'calendar-maintenance', 'unavailable', 'available');

                if (!availability) {
                    // No availability data - assume unavailable
                    dayElem.classList.add('calendar-blocked', 'unavailable');
                    dayElem.setAttribute('disabled', 'disabled');
                    dayElem.style.cursor = 'not-allowed';
                } else {
                    switch (availability.status) {
                        case 'available':
                            dayElem.classList.add('calendar-available', 'available');
                            dayElem.removeAttribute('disabled');
                            dayElem.style.cursor = 'pointer';
                            // Add price information as a data attribute and tooltip
                            if (availability.price) {
                                dayElem.setAttribute('data-price', availability.price);
                                dayElem.setAttribute('title', `Available - $${availability.price}/night`);
                            } else {
                                dayElem.setAttribute('title', 'Available');
                            }
                            break;
                        case 'booked':
                            dayElem.classList.add('calendar-booked', 'unavailable');
                            dayElem.setAttribute('disabled', 'disabled');
                            dayElem.style.cursor = 'not-allowed';
                            dayElem.setAttribute('title', 'Already booked');
                            break;
                        case 'blocked':
                            dayElem.classList.add('calendar-blocked', 'unavailable');
                            dayElem.setAttribute('disabled', 'disabled');
                            dayElem.style.cursor = 'not-allowed';
                            dayElem.setAttribute('title', 'Blocked by owner');
                            break;
                        case 'maintenance':
                            dayElem.classList.add('calendar-maintenance', 'unavailable');
                            dayElem.setAttribute('disabled', 'disabled');
                            dayElem.style.cursor = 'not-allowed';
                            dayElem.setAttribute('title', 'Maintenance day');
                            break;
                        default:
                            dayElem.classList.add('calendar-blocked', 'unavailable');
                            dayElem.setAttribute('disabled', 'disabled');
                            dayElem.style.cursor = 'not-allowed';
                            dayElem.setAttribute('title', 'Unavailable');
                    }
                }

                console.log(`Date ${date}: status=${availability?.status || 'no data'}, classes=${dayElem.className}`);
            },
            onChange: (selectedDates) => {
                if (this.validateDateSelection(selectedDates)) {
                    this.handleDateSelection(selectedDates);
                } else {
                    // Clear invalid selection
                    this.calendar.clear();
                    this.showValidationError('Selected date range contains unavailable dates. Please select a different range.');
                }
            },
            onReady: () => {
                this.addCustomStyles();
            }
        });
    }

    validateDateSelection(selectedDates) {
        if (selectedDates.length === 0) {
            return true; // Empty selection is valid
        }

        if (selectedDates.length === 1) {
            // Single date selection - check if it's available
            const date = selectedDates[0].toISOString().split('T')[0];
            const availability = this.availabilityData[date];
            return availability && availability.status === 'available';
        }

        if (selectedDates.length === 2) {
            // Date range selection - check all dates in range are available
            const startDate = selectedDates[0];
            const endDate = selectedDates[1];

            // Check minimum stay requirement
            const nights = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24));
            if (nights < this.options.minStay) {
                this.showValidationError(`Minimum stay is ${this.options.minStay} night(s). Selected range is ${nights} night(s).`);
                return false;
            }

            // Check maximum stay requirement
            if (this.options.maxStay && nights > this.options.maxStay) {
                this.showValidationError(`Maximum stay is ${this.options.maxStay} night(s). Selected range is ${nights} night(s).`);
                return false;
            }

            // Check all dates in range are available
            const currentDate = new Date(startDate);
            while (currentDate < endDate) {
                const dateStr = currentDate.toISOString().split('T')[0];
                const availability = this.availabilityData[dateStr];

                if (!availability || availability.status !== 'available') {
                    console.log(`Date ${dateStr} is not available:`, availability);
                    return false;
                }

                currentDate.setDate(currentDate.getDate() + 1);
            }
        }

        return true;
    }

    showValidationError(message) {
        // Remove any existing error messages
        const existingError = document.querySelector('.calendar-validation-error');
        if (existingError) {
            existingError.remove();
        }

        // Create and show error message
        const errorDiv = document.createElement('div');
        errorDiv.className = 'calendar-validation-error alert alert-danger';
        errorDiv.style.marginTop = '10px';
        errorDiv.innerHTML = `
            <i class="fas fa-exclamation-triangle"></i>
            ${message}
        `;

        const calendarWrapper = document.querySelector('.calendar-wrapper');
        if (calendarWrapper) {
            calendarWrapper.appendChild(errorDiv);

            // Auto-remove error after 5 seconds
            setTimeout(() => {
                if (errorDiv.parentNode) {
                    errorDiv.remove();
                }
            }, 5000);
        }

        console.warn('Calendar validation error:', message);
    }

    handleDateSelection(selectedDates) {
        if (selectedDates.length === 0) {
            this.checkInDate = null;
            this.checkOutDate = null;
            this.hideBookingSummary();
            return;
        }

        if (selectedDates.length === 1) {
            this.checkInDate = selectedDates[0];
            this.checkOutDate = null;
            this.hideBookingSummary();
        } else if (selectedDates.length === 2) {
            this.checkInDate = selectedDates[0];
            this.checkOutDate = selectedDates[1];
            this.updateBookingSummary();
            this.calculatePricing();
        } else {
            this.hideBookingSummary();
        }
    }

    updateBookingSummary() {
        const summary = document.getElementById('booking-summary');
        const checkinDisplay = document.getElementById('checkin-display');
        const checkoutDisplay = document.getElementById('checkout-display');
        const nightsDisplay = document.getElementById('nights-display');
        
        if (this.checkInDate && this.checkOutDate) {
            const nights = Math.ceil((this.checkOutDate - this.checkInDate) / (1000 * 60 * 60 * 24));
            
            checkinDisplay.textContent = this.formatDate(this.checkInDate);
            checkoutDisplay.textContent = this.formatDate(this.checkOutDate);
            nightsDisplay.textContent = nights;
            
            summary.style.display = 'block';
        }
    }

    async calculatePricing() {
        if (!this.checkInDate || !this.checkOutDate || !this.options.pricingEndpoint) return;

        const guestsCount = document.getElementById('guests_count')?.value || 1;

        try {
            const response = await fetch(this.options.pricingEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({
                    check_in: this.getApiDate(this.checkInDate),
                    check_out: this.getApiDate(this.checkOutDate),
                    guests: guestsCount
                })
            });

            const data = await response.json();
            
            if (data.error) {
                this.showError(data.message);
                return;
            }

            this.displayPricingBreakdown(data.data);
        } catch (error) {
            console.error('Failed to calculate pricing:', error);
        }
    }

    displayPricingBreakdown(pricing) {
        const breakdown = document.getElementById('pricing-breakdown');
        const totalPrice = document.getElementById('total-price');
        
        let html = '';
        
        if (pricing.total_nights_cost) {
            html += `<div class="price-item">
                <span>${pricing.nights} nights × ${pricing.average_nightly_rate.toFixed(2)}</span>
                <span>${pricing.total_nights_cost.toFixed(2)}</span>
            </div>`;
        }
        
        if (pricing.cleaning_fee > 0) {
            html += `<div class="price-item">
                <span>Cleaning fee</span>
                <span>${pricing.cleaning_fee.toFixed(2)}</span>
            </div>`;
        }
        
        if (pricing.service_fee > 0) {
            html += `<div class="price-item">
                <span>Service fee</span>
                <span>${pricing.service_fee.toFixed(2)}</span>
            </div>`;
        }
        
        if (pricing.taxes > 0) {
            html += `<div class="price-item">
                <span>Taxes</span>
                <span>${pricing.taxes.toFixed(2)}</span>
            </div>`;
        }
        
        breakdown.innerHTML = html;
        totalPrice.textContent = `${pricing.total_amount.toFixed(2)}`;
    }

    hideBookingSummary() {
        document.getElementById('booking-summary').style.display = 'none';
    }

    bindEvents() {
        // Proceed to booking
        document.getElementById('proceed-booking')?.addEventListener('click', () => {
            this.proceedToBooking();
        });

        // Handle window resize for responsive calendar
        // window.addEventListener('resize', () => {
        //     if (this.calendar) {
        //         this.calendar.set('showMonths', window.innerWidth > 768 ? 2 : 1);
        //     }
        // });
    }

    async proceedToBooking() {
        if (!this.checkInDate || !this.checkOutDate) {
            this.showError('Please select check-in and check-out dates');
            return;
        }

        if (!this.options.isLoggedIn) {
            window.location.href = this.options.loginUrl;
            return;
        }

        const guestName = document.getElementById('guest_name').value;
        const guestEmail = document.getElementById('guest_email').value;
        const guestPhone = document.getElementById('guest_phone').value;
        const guestsCount = document.getElementById('guests_count').value;
        const termsAccepted = document.getElementById('terms_accepted').checked;

        if (!guestName || !guestEmail) {
            this.showError('Please fill in your name and email.');
            return;
        }

        if (!termsAccepted) {
            this.showError('You must accept the terms and conditions.');
            return;
        }

        try {
            const checkInDateFormatted = this.getApiDate(this.checkInDate);
            const checkOutDateFormatted = this.getApiDate(this.checkOutDate);

            console.log('Date debugging:', {
                checkInDate_raw: this.checkInDate,
                checkOutDate_raw: this.checkOutDate,
                checkInDate_formatted: checkInDateFormatted,
                checkOutDate_formatted: checkOutDateFormatted,
                checkInDate_iso: this.checkInDate?.toISOString(),
                checkOutDate_iso: this.checkOutDate?.toISOString(),
                timezone: Intl.DateTimeFormat().resolvedOptions().timeZone
            });

            console.log('Submitting booking with data:', {
                property_id: this.options.propertyId,
                check_in_date: checkInDateFormatted,
                check_out_date: checkOutDateFormatted,
                guests_count: guestsCount,
                guest_name: guestName,
                guest_email: guestEmail,
                guest_phone: guestPhone,
                payment_method: 'test', // Default payment method for development - change to 'stripe' for production
                terms_accepted: termsAccepted,
            });

            const response = await fetch(this.options.bookingEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({
                    property_id: this.options.propertyId,
                    check_in_date: checkInDateFormatted,
                    check_out_date: checkOutDateFormatted,
                    guests_count: parseInt(guestsCount),
                    adults_count: parseInt(guestsCount), // Assume all guests are adults for now
                    children_count: 0, // Default to 0 children
                    guest_name: guestName,
                    guest_email: guestEmail,
                    guest_phone: guestPhone || '', // Ensure it's not null
                    special_requests: '', // Add empty special requests
                    payment_method: 'test', // Default payment method for development - change to 'stripe' for production
                    terms_accepted: termsAccepted,
                })
            });

            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);

            // Check if response is not ok (4xx, 5xx status codes)
            if (!response.ok) {
                let errorMessage = `HTTP ${response.status}: ${response.statusText}`;

                try {
                    const errorData = await response.json();
                    console.error('Server error response:', errorData);

                    if (errorData.message) {
                        errorMessage = errorData.message;
                    } else if (errorData.errors) {
                        // Handle validation errors
                        const validationErrors = Object.values(errorData.errors).flat();
                        errorMessage = validationErrors.join(', ');
                    }
                } catch (jsonError) {
                    // Response is not JSON, might be HTML error page
                    const textResponse = await response.text();
                    console.error('Non-JSON error response:', textResponse);
                    errorMessage = `Server error (${response.status}). Please check the console for details.`;
                }

                this.showError(errorMessage);
                return;
            }

            const data = await response.json();
            console.log('Booking response data:', data);

            if (data.error) {
                this.showError(data.message || 'Booking failed');
            } else if (data.data && data.data.checkoutUrl) {
                console.log('Redirecting to checkout:', data.data.checkoutUrl);
                window.location.href = data.data.checkoutUrl;
            } else {
                this.showError('Booking submitted successfully, but no checkout URL received.');
                console.error('Unexpected response data:', data);
            }
        } catch (error) {
            console.error('Booking failed:', error);
            this.showError('An unexpected error occurred. Please try again.');
        }
    }

    addCustomStyles() {
        // Add custom CSS classes for better styling
        const calendarElement = document.querySelector('.flatpickr-calendar');
        if (calendarElement) {
            calendarElement.classList.add('frontend-calendar');
        }
    }

    formatDate(date) {
        return date.toLocaleDateString('en-US', {
            weekday: 'short',
            month: 'short',
            day: 'numeric'
        });
    }

    getApiDate(date, addMonths = 0) {
        const newDate = new Date(date);
        if (addMonths > 0) {
            newDate.setMonth(newDate.getMonth() + addMonths);
        }

        // Use local timezone instead of UTC to avoid timezone issues
        const year = newDate.getFullYear();
        const month = String(newDate.getMonth() + 1).padStart(2, '0');
        const day = String(newDate.getDate()).padStart(2, '0');

        return `${year}-${month}-${day}`;
    }

    showError(message) {
        // Create a simple error notification
        const notification = document.createElement('div');
        notification.className = 'calendar-error-notification';
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #dc3545;
            color: white;
            padding: 12px 20px;
            border-radius: 6px;
            z-index: 9999;
            font-size: 14px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
}

// Auto-initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    const calendarContainer = document.querySelector('#property-calendar');
    if (calendarContainer) {
        const propertyId = calendarContainer.dataset.propertyId;
        const minStay = parseInt(calendarContainer.dataset.minStay) || 1;
        const maxStay = parseInt(calendarContainer.dataset.maxStay) || null;
        const maxGuests = parseInt(calendarContainer.dataset.maxGuests) || null;
        const isLoggedIn = calendarContainer.dataset.isLoggedIn === 'true';
        
        if (propertyId) {
            window.vacationRentalFrontendCalendar = new VacationRentalFrontendCalendar({
                propertyId: propertyId,
                minStay: minStay,
                maxStay: maxStay,
                maxGuests: maxGuests,
                isLoggedIn: isLoggedIn,
                availabilityEndpoint: calendarContainer.dataset.availabilityUrl,
                pricingEndpoint: calendarContainer.dataset.pricingUrl,
                bookingEndpoint: calendarContainer.dataset.bookingUrl,
                loginUrl: calendarContainer.dataset.loginUrl,
            });
        }
    }
});
