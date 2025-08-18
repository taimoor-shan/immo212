/**
 * Vacation Rental Frontend Calendar
 * Adapted for VacationRental model instead of Property model
 * Handles: vacation rental booking calendar, availability display, pricing calculation
 *
 * Dependencies: flatpickr (loaded via CDN or separate script tag)
 */

class VacationRentalFrontendCalendar {
    constructor(options = {}) {
        this.options = {
            vacationRentalId: null,
            container: '#vacation-rental-calendar',
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
        this.tooltip = null;

        this.init();
    }

    async init() {
        // Initialize calendar immediately with default available state
        this.initializeCalendar();
        this.bindEvents();

        // Load availability exceptions in the background and update calendar
        this.loadAvailabilityDataProgressively();
    }

    async loadAvailabilityDataProgressively() {
        if (!this.options.vacationRentalId || !this.options.availabilityEndpoint) {
            console.warn('Missing vacationRentalId or availabilityEndpoint');
            return;
        }

        try {
            const startDate = this.getApiDate(new Date());
            const endDate = this.getApiDate(new Date(), 12);
            const cacheBuster = Date.now();
            const response = await fetch(`${this.options.availabilityEndpoint}?start=${startDate}&end=${endDate}&exceptions_only=true&_=${cacheBuster}`, {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            console.log('Availability exceptions loaded:', data);
            this.availabilityData = data.data || {};

            // Update calendar with exception data
            this.updateCalendarWithExceptions();
        } catch (error) {
            console.error('Failed to load availability exceptions:', error);
            // Calendar remains functional with default available state
            this.showApiFailureNotification();
        }
    }

    showApiFailureNotification() {
        // Show a subtle notification that API failed but calendar is still functional
        const notification = document.createElement('div');
        notification.className = 'calendar-api-warning';
        notification.innerHTML = `
            <div class="alert alert-info alert-dismissible fade show" role="alert" style="margin-bottom: 10px; font-size: 0.875rem;">
                <i class="fas fa-info-circle"></i>
                Calendar is showing default availability. Some dates may have different status.
                <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;

        const container = document.querySelector(this.options.container);
        if (container) {
            container.insertBefore(notification, container.firstChild);

            // Auto-remove after 5 seconds
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 5000);
        }
    }

    updateCalendarWithExceptions() {
        if (!this.calendar) return;

        // Hide any existing tooltip before redrawing
        this.hideTooltip();

        // Manually update all day elements with the new availability data
        const dayElements = this.calendar.calendarContainer.querySelectorAll('.flatpickr-day');
        dayElements.forEach(dayElem => {
            // Skip if this day element doesn't have a date
            if (!dayElem.dateObj) return;

            const date = dayElem.dateObj.toISOString().split('T')[0];
            const availability = this.availabilityData[date];

            // Remove any existing availability classes
            dayElem.classList.remove('calendar-available', 'calendar-booked', 'calendar-blocked', 'calendar-maintenance', 'unavailable', 'available');

            // Apply the correct availability state
            this.applyAvailabilityToDay(dayElem, date, availability);
        });

        console.log('Calendar updated with availability exceptions');
    }

    applyAvailabilityToDay(dayElem, date, availability) {
        // Default to available state for all future dates
        if (!availability) {
            // Default available state - no exception data
            dayElem.classList.add('calendar-available', 'available');
            dayElem.removeAttribute('disabled');
            dayElem.style.cursor = 'pointer';
            dayElem.setAttribute('title', 'Available');
        } else {
            // Apply exception data if available
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
                    this.addTooltipToDate(dayElem, availability, 'booked');
                    break;
                case 'blocked':
                    dayElem.classList.add('calendar-blocked', 'unavailable');
                    dayElem.setAttribute('disabled', 'disabled');
                    dayElem.style.cursor = 'not-allowed';
                    this.addTooltipToDate(dayElem, availability, 'blocked');
                    break;
                case 'maintenance':
                    dayElem.classList.add('calendar-maintenance', 'unavailable');
                    dayElem.setAttribute('disabled', 'disabled');
                    dayElem.style.cursor = 'not-allowed';
                    this.addTooltipToDate(dayElem, availability, 'maintenance');
                    break;
                default:
                    dayElem.classList.add('calendar-blocked', 'unavailable');
                    dayElem.setAttribute('disabled', 'disabled');
                    dayElem.style.cursor = 'not-allowed';
                    dayElem.setAttribute('title', 'Unavailable');
            }
        }
    }
    cleanupTooltip() {
        if (this.tooltip) {
            this.tooltip.remove();
            this.tooltip = null;
        }
    }

    createTooltip() {
        if (this.tooltip) return;

        console.log('Creating tooltip element');
        this.tooltip = document.createElement('div');
        this.tooltip.className = 'calendar-tooltip';
        this.tooltip.style.position = 'absolute';
        this.tooltip.style.zIndex = '9999';
        document.body.appendChild(this.tooltip);
        console.log('Tooltip element created and appended to body');
    }

    showTooltip(element, content, status) {
        if (!content) {
            console.log('No tooltip content provided');
            return;
        }

        console.log('Showing tooltip:', content, status);
        this.createTooltip();

        // Set tooltip content and status class
        this.tooltip.textContent = content;
        this.tooltip.className = `calendar-tooltip ${status}`;

        // Make tooltip visible temporarily to get dimensions
        this.tooltip.style.visibility = 'hidden';
        this.tooltip.style.display = 'block';

        // Position tooltip above the element
        const rect = element.getBoundingClientRect();
        const tooltipRect = this.tooltip.getBoundingClientRect();
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        const scrollLeft = window.pageXOffset || document.documentElement.scrollLeft;

        let left = rect.left + scrollLeft + (rect.width / 2) - (tooltipRect.width / 2);
        let top = rect.top + scrollTop - tooltipRect.height - 10; // 10px gap

        // Ensure tooltip stays within viewport
        const viewportWidth = window.innerWidth;
        const viewportHeight = window.innerHeight;

        if (left < 10) left = 10;
        if (left + tooltipRect.width > viewportWidth - 10) {
            left = viewportWidth - tooltipRect.width - 10;
        }
        if (top < 10) {
            // If no space above, show below the element
            top = rect.bottom + scrollTop + 10;
        }

        this.tooltip.style.left = left + 'px';
        this.tooltip.style.top = top + 'px';
        this.tooltip.style.visibility = 'visible';

        // Show tooltip with animation
        this.tooltip.classList.add('show');
        console.log('Tooltip positioned at:', left, top);
    }

    hideTooltip() {
        if (this.tooltip) {
            this.tooltip.classList.remove('show');
        }
    }

    getTooltipContent(availability) {
        if (!availability || !availability.notes) {
            // Default messages for different statuses
            switch (availability?.status) {
                case 'blocked':
                    return 'Blocked by owner';
                case 'maintenance':
                    return 'Maintenance scheduled';
                case 'booked':
                    return 'Already booked';
                default:
                    return null;
            }
        }

        return availability.notes;
    }

    addTooltipToDate(dayElem, availability, status) {
        const tooltipContent = this.getTooltipContent(availability);

        console.log('Adding tooltip to date:', dayElem, 'content:', tooltipContent, 'status:', status);

        if (!tooltipContent) {
            console.log('No tooltip content, skipping');
            return;
        }

        // Add hover event listeners for tooltip
        dayElem.addEventListener('mouseenter', (e) => {
            console.log('Mouse enter on date element');
            this.showTooltip(e.target, tooltipContent, status);
        });

        dayElem.addEventListener('mouseleave', () => {
            console.log('Mouse leave on date element');
            this.hideTooltip();
        });

        // Remove default title attribute to avoid conflicts
        dayElem.removeAttribute('title');
        console.log('Tooltip events added to date element');
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

                        <div class="legend-item maintenance">
                            <div class="color-box"></div>
                            <span>Maintenance</span>
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
                        <div class="booking-actions" style="display: flex; gap: 10px; flex-wrap: wrap;">
                            <button type="button" class="btn btn-outline-primary btn-inquiry" id="send-inquiry" style="flex: 1; min-width: 140px;">
                                📧 Send Inquiry
                            </button>
                            <button type="button" class="btn btn-primary btn-book" id="proceed-booking" style="flex: 1; min-width: 140px;">
                                🏠 Book Now
                            </button>
                        </div>
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
            onDayCreate: (_, __, ___, dayElem) => {
                // Fix timezone issue - use local date instead of UTC
                const date = this.getApiDate(dayElem.dateObj);
                const availability = this.availabilityData[date];

                // Use the centralized method to apply availability
                this.applyAvailabilityToDay(dayElem, date, availability);
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
            },
            onMonthChange: () => {
                // When month changes, we need to update the availability for the new month
                // Add a small delay to ensure Flatpickr has finished rendering
                setTimeout(() => {
                    this.updateCalendarWithExceptions();
                }, 100);
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

            // With exceptions-only loading: no data = available, data = check if it's an exception
            if (!availability) {
                return true; // No exception data means it's available
            }

            // If we have data, it should be an exception (unavailable)
            return availability.status === 'available';
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

                // With exceptions-only loading: no data = available, data = check if it's an exception
                if (availability && availability.status !== 'available') {
                    console.log(`Date ${dateStr} is not available:`, availability);
                    return false;
                }
                // If no availability data exists, the date is available (default state)

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

        // Send inquiry
        document.getElementById('send-inquiry')?.addEventListener('click', () => {
            this.sendInquiry();
        });
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

            const response = await fetch(this.options.bookingEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({
                    vacation_rental_id: this.options.vacationRentalId,
                    check_in_date: checkInDateFormatted,
                    check_out_date: checkOutDateFormatted,
                    guests_count: parseInt(guestsCount),
                    guest_name: guestName,
                    guest_email: guestEmail,
                    guest_phone: guestPhone || '',
                    special_requests: '',
                    payment_method: 'test',
                    terms_accepted: termsAccepted,
                })
            });

            if (!response.ok) {
                let errorMessage = `HTTP ${response.status}: ${response.statusText}`;
                try {
                    const errorData = await response.json();
                    if (errorData.message) {
                        errorMessage = errorData.message;
                    } else if (errorData.errors) {
                        const validationErrors = Object.values(errorData.errors).flat();
                        errorMessage = validationErrors.join(', ');
                    }
                } catch (jsonError) {
                    errorMessage = `Server error (${response.status}). Please check the console for details.`;
                }
                this.showError(errorMessage);
                return;
            }

            const data = await response.json();
            if (data.error) {
                this.showError(data.message || 'Booking failed');
            } else if (data.data && data.data.checkoutUrl) {
                window.location.href = data.data.checkoutUrl;
            } else {
                this.showError('Booking submitted successfully, but no checkout URL received.');
            }
        } catch (error) {
            console.error('Booking failed:', error);
            this.showError('An unexpected error occurred. Please try again.');
        }
    }

    async sendInquiry() {
        if (!this.checkInDate || !this.checkOutDate) {
            this.showError('Please select check-in and check-out dates');
            return;
        }

        const guestName = document.getElementById('guest_name')?.value?.trim();
        const guestEmail = document.getElementById('guest_email')?.value?.trim();
        const guestPhone = document.getElementById('guest_phone')?.value?.trim();
        const guestsCount = parseInt(document.getElementById('guests_count')?.value) || 1;
        const inquiryMessage = prompt('Please enter your message or questions about this property:') || '';

        if (!guestName || !guestEmail) {
            this.showError('Please fill in your name and email address');
            return;
        }

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(guestEmail)) {
            this.showError('Please enter a valid email address');
            return;
        }

        try {
            const checkInDateFormatted = this.checkInDate.toISOString().split('T')[0];
            const checkOutDateFormatted = this.checkOutDate.toISOString().split('T')[0];

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            const response = await fetch('/vacation-rental/inquiry', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || ''
                },
                body: JSON.stringify({
                    vacation_rental_id: this.options.vacationRentalId,
                    name: guestName,
                    email: guestEmail,
                    phone: guestPhone,
                    check_in_date: checkInDateFormatted,
                    check_out_date: checkOutDateFormatted,
                    guests_count: guestsCount,
                    message: inquiryMessage
                })
            });

            if (!response.ok) {
                this.showError(`Server error: ${response.status} ${response.statusText}`);
                return;
            }

            const data = await response.json();
            if (data.error === false) {
                this.showSuccess(data.message || 'Your inquiry has been sent successfully! The property owner will contact you soon.');
                this.resetBookingForm();
            } else {
                this.showError(data.message || 'Failed to send inquiry');
            }
        } catch (error) {
            console.error('Inquiry failed:', error);
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

    showSuccess(message) {
        // Create a simple success notification
        const notification = document.createElement('div');
        notification.className = 'calendar-success-notification';
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #28a745;
            color: white;
            padding: 15px 20px;
            border-radius: 5px;
            z-index: 9999;
            font-size: 14px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            max-width: 400px;
        `;

        document.body.appendChild(notification);

        // Auto remove after 5 seconds
        setTimeout(() => {
            notification.remove();
        }, 5000);
    }

    resetBookingForm() {
        // Clear selected dates
        this.checkInDate = null;
        this.checkOutDate = null;
        this.selectedDates = [];

        // Reset calendar
        if (this.calendar) {
            this.calendar.clear();
        }

        // Hide booking summary
        const summaryDiv = document.getElementById('booking-summary');
        if (summaryDiv) {
            summaryDiv.style.display = 'none';
        }

        // Reset form fields
        const guestNameField = document.getElementById('guest_name');
        const guestEmailField = document.getElementById('guest_email');
        const guestPhoneField = document.getElementById('guest_phone');
        const guestsCountField = document.getElementById('guests_count');
        const termsCheckbox = document.getElementById('terms_accepted');

        if (guestNameField) guestNameField.value = '';
        if (guestEmailField) guestEmailField.value = '';
        if (guestPhoneField) guestPhoneField.value = '';
        if (guestsCountField) guestsCountField.value = '1';
        if (termsCheckbox) termsCheckbox.checked = false;
    }

    destroy() {
        this.cleanupTooltip();
        if (this.calendar) {
            this.calendar.destroy();
            this.calendar = null;
        }
    }
}

// Auto-initialize when DOM is ready and dependencies are loaded
function initializeVacationRentalCalendar() {
    // Check if required dependencies are available
    if (typeof flatpickr === 'undefined') {
        console.warn('VacationRentalFrontendCalendar: flatpickr is not loaded. Calendar will not initialize.');
        return;
    }

    const calendarContainer = document.querySelector('#vacation-rental-calendar');
    if (calendarContainer) {
        const vacationRentalId = calendarContainer.dataset.vacationRentalId;
        const minStay = parseInt(calendarContainer.dataset.minStay) || 1;
        const maxStay = parseInt(calendarContainer.dataset.maxStay) || null;
        const maxGuests = parseInt(calendarContainer.dataset.maxGuests) || null;
        const isLoggedIn = calendarContainer.dataset.isLoggedIn === 'true';

        if (vacationRentalId) {
            try {
                window.vacationRentalFrontendCalendar = new VacationRentalFrontendCalendar({
                    vacationRentalId: vacationRentalId,
                    minStay: minStay,
                    maxStay: maxStay,
                    maxGuests: maxGuests,
                    isLoggedIn: isLoggedIn,
                    availabilityEndpoint: calendarContainer.dataset.availabilityUrl,
                    pricingEndpoint: calendarContainer.dataset.pricingUrl,
                    bookingEndpoint: calendarContainer.dataset.bookingUrl,
                    loginUrl: calendarContainer.dataset.loginUrl,
                });
                console.log('VacationRentalFrontendCalendar initialized successfully');
            } catch (error) {
                console.error('Failed to initialize VacationRentalFrontendCalendar:', error);
            }
        }
    }
}

// Try to initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeVacationRentalCalendar);
} else {
    // DOM is already ready
    initializeVacationRentalCalendar();
}

// Cleanup tooltips when page unloads
window.addEventListener('beforeunload', function() {
    if (window.vacationRentalFrontendCalendar) {
        window.vacationRentalFrontendCalendar.destroy();
    }
});

// Make the class available globally
window.VacationRentalFrontendCalendar = VacationRentalFrontendCalendar;


