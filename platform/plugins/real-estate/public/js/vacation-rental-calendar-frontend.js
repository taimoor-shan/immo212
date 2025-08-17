/**
 * Vacation Rental Frontend Calendar
 * Handles booking functionality for end users
 */

class VacationRentalFrontendCalendar {
    constructor(container) {
        this.container = container;
        this.vacationRentalId = container.dataset.vacationRentalId;
        this.availabilityUrl = container.dataset.availabilityUrl;
        this.pricingUrl = container.dataset.pricingUrl;
        this.bookingUrl = container.dataset.bookingUrl;
        this.loginUrl = container.dataset.loginUrl;
        this.minStay = parseInt(container.dataset.minStay) || 1;
        this.maxStay = parseInt(container.dataset.maxStay) || null;
        this.maxGuests = parseInt(container.dataset.maxGuests) || null;
        this.isLoggedIn = container.dataset.isLoggedIn === 'true';
        
        this.calendar = null;
        this.availabilityData = {};
        this.selectedDates = [];
        this.checkInDate = null;
        this.checkOutDate = null;
        
        this.init();
    }

    async init() {
        this.showLoading();
        await this.loadAvailabilityData();
        this.initializeCalendar();
        this.bindEvents();
        this.hideLoading();
    }

    async loadAvailabilityData() {
        if (!this.vacationRentalId || !this.availabilityUrl) {
            console.warn('Missing vacation rental ID or availability URL');
            return;
        }

        try {
            const startDate = this.formatDate(new Date());
            const endDate = this.formatDate(new Date(Date.now() + 365 * 24 * 60 * 60 * 1000)); // 1 year ahead
            
            const response = await fetch(`${this.availabilityUrl}?start=${startDate}&end=${endDate}&exceptions_only=true`);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            this.availabilityData = data.data || {};
            
            console.log('Availability data loaded:', this.availabilityData);
        } catch (error) {
            console.error('Failed to load availability data:', error);
            this.showError('Failed to load calendar data. Please refresh the page.');
        }
    }

    initializeCalendar() {
        const calendarElement = this.container.querySelector('.vacation-rental-calendar');
        if (!calendarElement) {
            console.error('Calendar element not found');
            return;
        }

        // Initialize Flatpickr
        this.calendar = flatpickr(calendarElement, {
            mode: 'range',
            inline: true,
            dateFormat: 'Y-m-d',
            minDate: 'today',
            showMonths: 1,
            onDayCreate: (dObj, dStr, fp, dayElem) => {
                const date = this.formatDate(dayElem.dateObj);
                const availability = this.availabilityData[date];
                
                if (availability) {
                    dayElem.classList.add(`calendar-${availability.status}`);
                    if (availability.notes) {
                        dayElem.title = availability.notes;
                    }
                }
            },
            onChange: (selectedDates) => {
                this.handleDateSelection(selectedDates);
            },
            onReady: () => {
                this.addCustomStyles();
            }
        });
    }

    handleDateSelection(selectedDates) {
        if (selectedDates.length === 0) {
            this.clearSelection();
            return;
        }

        if (selectedDates.length === 2) {
            const [checkIn, checkOut] = selectedDates;
            
            if (!this.validateDateSelection(checkIn, checkOut)) {
                this.calendar.clear();
                return;
            }

            this.checkInDate = checkIn;
            this.checkOutDate = checkOut;
            this.updateSelectedDatesDisplay();
            this.calculatePricing();
            this.showBookingForm();
        }
    }

    validateDateSelection(checkIn, checkOut) {
        const nights = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));
        
        // Check minimum stay
        if (nights < this.minStay) {
            this.showError(`Minimum stay is ${this.minStay} nights`);
            return false;
        }

        // Check maximum stay
        if (this.maxStay && nights > this.maxStay) {
            this.showError(`Maximum stay is ${this.maxStay} nights`);
            return false;
        }

        // Check if any dates in range are unavailable
        const currentDate = new Date(checkIn);
        while (currentDate < checkOut) {
            const dateStr = this.formatDate(currentDate);
            const availability = this.availabilityData[dateStr];
            
            if (availability && availability.status !== 'available') {
                this.showError('Selected date range contains unavailable dates');
                return false;
            }
            
            currentDate.setDate(currentDate.getDate() + 1);
        }

        return true;
    }

    async calculatePricing() {
        if (!this.checkInDate || !this.checkOutDate || !this.pricingUrl) {
            return;
        }

        const guestsCount = this.container.querySelector('#guests_count')?.value || 1;

        try {
            const response = await fetch(this.pricingUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({
                    check_in: this.formatDate(this.checkInDate),
                    check_out: this.formatDate(this.checkOutDate),
                    guests: parseInt(guestsCount)
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
            this.showError('Failed to calculate pricing');
        }
    }

    displayPricingBreakdown(pricing) {
        const breakdownElement = this.container.querySelector('#pricing-breakdown');
        if (!breakdownElement) return;

        const html = `
            <h6>Pricing Breakdown</h6>
            <div class="pricing-row">
                <span class="pricing-label">${pricing.nights} nights × $${pricing.base_price_per_night}</span>
                <span class="pricing-value">$${pricing.total_nights_cost.toFixed(2)}</span>
            </div>
            ${pricing.cleaning_fee > 0 ? `
            <div class="pricing-row">
                <span class="pricing-label">Cleaning fee</span>
                <span class="pricing-value">$${pricing.cleaning_fee.toFixed(2)}</span>
            </div>` : ''}
            ${pricing.service_fee > 0 ? `
            <div class="pricing-row">
                <span class="pricing-label">Service fee</span>
                <span class="pricing-value">$${pricing.service_fee.toFixed(2)}</span>
            </div>` : ''}
            ${pricing.taxes > 0 ? `
            <div class="pricing-row">
                <span class="pricing-label">Taxes</span>
                <span class="pricing-value">$${pricing.taxes.toFixed(2)}</span>
            </div>` : ''}
            <div class="pricing-row">
                <span class="pricing-label"><strong>Total</strong></span>
                <span class="pricing-value"><strong>$${pricing.total_amount.toFixed(2)}</strong></span>
            </div>
        `;

        breakdownElement.innerHTML = html;
        breakdownElement.style.display = 'block';
    }

    updateSelectedDatesDisplay() {
        const displayElement = this.container.querySelector('#selected-dates-display');
        if (!displayElement) return;

        if (this.checkInDate && this.checkOutDate) {
            const checkInStr = this.checkInDate.toLocaleDateString();
            const checkOutStr = this.checkOutDate.toLocaleDateString();
            const nights = Math.ceil((this.checkOutDate - this.checkInDate) / (1000 * 60 * 60 * 24));
            
            displayElement.textContent = `${checkInStr} - ${checkOutStr} (${nights} nights)`;
        } else {
            displayElement.textContent = 'Please select dates from the calendar';
        }
    }

    showBookingForm() {
        const formContainer = this.container.querySelector('#booking-form-container');
        if (formContainer) {
            formContainer.style.display = 'block';
        }
    }

    clearSelection() {
        this.checkInDate = null;
        this.checkOutDate = null;
        this.updateSelectedDatesDisplay();
        
        const formContainer = this.container.querySelector('#booking-form-container');
        if (formContainer) {
            formContainer.style.display = 'none';
        }

        const breakdownElement = this.container.querySelector('#pricing-breakdown');
        if (breakdownElement) {
            breakdownElement.style.display = 'none';
        }
    }

    bindEvents() {
        // Booking form submission
        const bookingForm = this.container.querySelector('#vacation-rental-booking-form');
        if (bookingForm) {
            bookingForm.addEventListener('submit', (e) => this.handleBookingSubmission(e));
        }

        // Guest count change
        const guestsSelect = this.container.querySelector('#guests_count');
        if (guestsSelect) {
            guestsSelect.addEventListener('change', () => this.calculatePricing());
        }
    }

    async handleBookingSubmission(event) {
        event.preventDefault();
        
        if (!this.isLoggedIn) {
            window.location.href = this.loginUrl;
            return;
        }

        const formData = new FormData(event.target);
        const bookingData = {
            property_id: this.vacationRentalId,
            check_in_date: this.formatDate(this.checkInDate),
            check_out_date: this.formatDate(this.checkOutDate),
            guests_count: parseInt(formData.get('guests_count')),
            guest_name: formData.get('guest_name'),
            guest_email: formData.get('guest_email'),
            guest_phone: formData.get('guest_phone') || '',
            special_requests: '',
            payment_method: 'stripe',
            terms_accepted: formData.get('terms_accepted') === 'on'
        };

        try {
            const response = await fetch(this.bookingUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify(bookingData)
            });

            const data = await response.json();
            
            if (data.error) {
                this.showError(data.message);
                return;
            }

            // Redirect to success page or payment
            if (data.data && data.data.redirect_url) {
                window.location.href = data.data.redirect_url;
            }
        } catch (error) {
            console.error('Booking submission failed:', error);
            this.showError('Failed to submit booking. Please try again.');
        }
    }

    addCustomStyles() {
        // Add any additional custom styling if needed
    }

    showLoading() {
        const loadingElement = this.container.querySelector('#calendar-loading');
        if (loadingElement) {
            loadingElement.style.display = 'block';
        }
        this.container.classList.add('loading');
    }

    hideLoading() {
        const loadingElement = this.container.querySelector('#calendar-loading');
        if (loadingElement) {
            loadingElement.style.display = 'none';
        }
        this.container.classList.remove('loading');
    }

    showError(message) {
        const errorElement = this.container.querySelector('#calendar-error');
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.style.display = 'block';
            
            // Auto-hide after 5 seconds
            setTimeout(() => {
                errorElement.style.display = 'none';
            }, 5000);
        }
    }

    formatDate(date) {
        return date.toISOString().split('T')[0];
    }
}

// Auto-initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    const containers = document.querySelectorAll('.vacation-rental-calendar-wrapper[data-context="frontend"]');
    containers.forEach(container => {
        new VacationRentalFrontendCalendar(container);
    });
});
