/**
 * Frontend Calendar functionality for Vacation Rental booking
 * Beautiful, minimal calendar for property availability and booking
 */

class VacationRentalFrontendCalendar {
    constructor(options = {}) {
        this.options = {
            propertyId: null,
            container: '#property-calendar',
            availabilityEndpoint: '/vacation-rental/availability',
            pricingEndpoint: '/vacation-rental/pricing',
            minStay: 1,
            maxStay: null,
            maxGuests: null,
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
        if (!this.options.propertyId) return;

        try {
            const response = await fetch(`${this.options.availabilityEndpoint}?property_id=${this.options.propertyId}`);
            const data = await response.json();
            this.availabilityData = data.availability || {};
            this.pricingData = data.pricing || {};
        } catch (error) {
            console.error('Failed to load availability data:', error);
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
                        <span class="legend-item available"><span class="dot"></span> Available</span>
                        <span class="legend-item unavailable"><span class="dot"></span> Unavailable</span>
                        <span class="legend-item selected"><span class="dot"></span> Selected</span>
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
                        <div class="pricing-breakdown" id="pricing-breakdown">
                            <!-- Pricing details will be inserted here -->
                        </div>
                        <div class="total-price">
                            <strong>Total: <span id="total-price">$0</span></strong>
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
            showMonths: window.innerWidth > 768 ? 2 : 1,
            onDayCreate: (dObj, dStr, fp, dayElem) => {
                const date = dayElem.dateObj.toISOString().split('T')[0];
                const availability = this.availabilityData[date];
                
                if (availability === false || availability === 'unavailable') {
                    dayElem.classList.add('unavailable');
                    dayElem.setAttribute('disabled', 'disabled');
                } else if (availability === true || availability === 'available') {
                    dayElem.classList.add('available');
                    
                    // Add pricing info if available
                    const pricing = this.pricingData[date];
                    if (pricing) {
                        dayElem.title = `$${pricing.price}/night`;
                        dayElem.setAttribute('data-price', pricing.price);
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
        if (selectedDates.length === 2) {
            this.checkInDate = selectedDates[0];
            this.checkOutDate = selectedDates[1];
            
            // Validate minimum stay
            const nights = Math.ceil((this.checkOutDate - this.checkInDate) / (1000 * 60 * 60 * 24));
            if (nights < this.options.minStay) {
                this.showError(`Minimum stay is ${this.options.minStay} night(s)`);
                this.calendar.clear();
                return;
            }
            
            // Validate maximum stay
            if (this.options.maxStay && nights > this.options.maxStay) {
                this.showError(`Maximum stay is ${this.options.maxStay} night(s)`);
                this.calendar.clear();
                return;
            }
            
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
        if (!this.checkInDate || !this.checkOutDate) return;

        try {
            const response = await fetch(`${this.options.pricingEndpoint}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({
                    property_id: this.options.propertyId,
                    check_in: this.formatDateForAPI(this.checkInDate),
                    check_out: this.formatDateForAPI(this.checkOutDate),
                    guests: 1 // Default, can be made dynamic
                })
            });

            const data = await response.json();
            
            if (data.error) {
                this.showError(data.message);
                return;
            }

            this.displayPricingBreakdown(data.pricing);
        } catch (error) {
            console.error('Failed to calculate pricing:', error);
        }
    }

    displayPricingBreakdown(pricing) {
        const breakdown = document.getElementById('pricing-breakdown');
        const totalPrice = document.getElementById('total-price');
        
        let html = '';
        
        if (pricing.base_price) {
            html += `<div class="price-item">
                <span>${pricing.nights} nights × $${pricing.base_price_per_night}</span>
                <span>$${pricing.base_price.toFixed(2)}</span>
            </div>`;
        }
        
        if (pricing.cleaning_fee > 0) {
            html += `<div class="price-item">
                <span>Cleaning fee</span>
                <span>$${pricing.cleaning_fee.toFixed(2)}</span>
            </div>`;
        }
        
        if (pricing.service_fee > 0) {
            html += `<div class="price-item">
                <span>Service fee</span>
                <span>$${pricing.service_fee.toFixed(2)}</span>
            </div>`;
        }
        
        if (pricing.taxes > 0) {
            html += `<div class="price-item">
                <span>Taxes</span>
                <span>$${pricing.taxes.toFixed(2)}</span>
            </div>`;
        }
        
        breakdown.innerHTML = html;
        totalPrice.textContent = `$${pricing.total_amount.toFixed(2)}`;
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
        window.addEventListener('resize', () => {
            if (this.calendar) {
                this.calendar.set('showMonths', window.innerWidth > 768 ? 2 : 1);
            }
        });
    }

    proceedToBooking() {
        if (!this.checkInDate || !this.checkOutDate) {
            this.showError('Please select check-in and check-out dates');
            return;
        }

        // Trigger booking modal or redirect to booking page
        if (typeof window.initiateBooking === 'function') {
            window.initiateBooking();
        } else {
            // Fallback: redirect to booking form
            const checkIn = this.formatDateForAPI(this.checkInDate);
            const checkOut = this.formatDateForAPI(this.checkOutDate);
            const guests = 1; // Default, can be made dynamic
            
            const bookingUrl = `/vacation-rental/book?property_id=${this.options.propertyId}&check_in=${checkIn}&check_out=${checkOut}&guests=${guests}`;
            window.location.href = bookingUrl;
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

    formatDateForAPI(date) {
        return date.toISOString().split('T')[0];
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
        const propertySlug = calendarContainer.dataset.propertySlug;
        const minStay = parseInt(calendarContainer.dataset.minStay) || 1;
        const maxStay = parseInt(calendarContainer.dataset.maxStay) || null;
        const maxGuests = parseInt(calendarContainer.dataset.maxGuests) || null;
        
        if (propertyId) {
            window.vacationRentalFrontendCalendar = new VacationRentalFrontendCalendar({
                propertyId: propertyId,
                propertySlug: propertySlug,
                minStay: minStay,
                maxStay: maxStay,
                maxGuests: maxGuests
            });
        }
    }
});
