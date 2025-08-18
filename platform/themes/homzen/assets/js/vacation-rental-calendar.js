/**
 * Frontend Vacation Rental Calendar
 * Following Homzen theme JavaScript patterns
 */

class VacationRentalCalendar {
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
        this.currentPricing = null;

        this.init();
    }

    init() {
        this.initializeCalendar();
        this.bindEvents();
        this.loadAvailabilityData();
    }

    initializeCalendar() {
        const calendarElement = this.container.querySelector('.flatpickr-calendar-container');
        if (!calendarElement) {
            console.error('Calendar element not found');
            return;
        }

        this.calendar = flatpickr(calendarElement, {
            mode: 'range',
            inline: true,
            dateFormat: 'Y-m-d',
            minDate: 'today',
            // showMonths: window.innerWidth > 768 ? 2 : 1,
            showMonths: 1,
            onDayCreate: (dObj, dStr, fp, dayElem) => {
                this.styleDayElement(dayElem);
            },
            onChange: (selectedDates) => {
                this.handleDateSelection(selectedDates);
            },
            onReady: () => {
                this.addCustomStyles();
            }
        });
    }

    styleDayElement(dayElem) {
        const date = this.formatDate(new Date(dayElem.dateObj));
        const availability = this.availabilityData[date];

        // Remove existing classes
        dayElem.classList.remove('available', 'booked', 'unavailable');

        if (availability) {
            if (availability.status === 'available') {
                dayElem.classList.add('available');
            } else {
                dayElem.classList.add('unavailable');
                dayElem.style.pointerEvents = 'none';
            }

            if (availability.price) {
                this.addPriceToDay(dayElem, availability.price);
            }
        } else {
            dayElem.classList.add('available');
        }
    }

    addPriceToDay(dayElem, price) {
        const priceElement = document.createElement('div');
        priceElement.className = 'day-price';
        priceElement.textContent = price;
        priceElement.style.cssText = `
            position: absolute;
            bottom: 2px;
            right: 2px;
            font-size: 10px;
            background: rgba(255, 87, 34, 0.9);
            color: white;
            padding: 1px 3px;
            border-radius: 3px;
            line-height: 1;
        `;
        dayElem.appendChild(priceElement);
    }

    addCustomStyles() {
        const style = document.createElement('style');
        style.textContent = `
            .flatpickr-calendar .flatpickr-day.available:hover {
                background-color: #c3e6cb !important;
                transform: translateY(-1px);
            }
            .flatpickr-calendar .flatpickr-day.unavailable {
                opacity: 0.5;
                cursor: not-allowed !important;
            }
        `;
        document.head.appendChild(style);
    }

    async loadAvailabilityData() {
        if (!this.availabilityUrl || !this.vacationRentalId) {
            console.error('Missing availability URL or vacation rental ID');
            return;
        }

        try {
            const startDate = new Date();
            const endDate = new Date();
            endDate.setFullYear(endDate.getFullYear() + 1);

            const url = `${this.availabilityUrl}?property_id=${this.vacationRentalId}&start_date=${this.formatDate(startDate)}&end_date=${this.formatDate(endDate)}&exceptions_only=true`;

            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                }
            });
            const data = await response.json();

            if (data.error) {
                throw new Error(data.message || 'Failed to load availability data');
            }

            this.availabilityData = data.data || {};
            this.refreshCalendarDisplay();

        } catch (error) {
            console.error('Error loading availability data:', error);
            this.showError('Failed to load calendar data. Please refresh the page.');
        }
    }

    refreshCalendarDisplay() {
        if (this.calendar) {
            this.calendar.redraw();
        }
    }

    handleDateSelection(selectedDates) {
        if (selectedDates.length === 0) {
            this.clearSelection();
            return;
        }

        if (selectedDates.length === 1) {
            this.checkInDate = selectedDates[0];
            this.checkOutDate = null;
        } else if (selectedDates.length === 2) {
            this.checkInDate = selectedDates[0];
            this.checkOutDate = selectedDates[1];

            // Validate stay duration
            const nights = Math.ceil((this.checkOutDate - this.checkInDate) / (1000 * 60 * 60 * 24));

            if (nights < this.minStay) {
                this.showError(`Minimum stay is ${this.minStay} night(s)`);
                this.calendar.clear();
                return;
            }

            if (this.maxStay && nights > this.maxStay) {
                this.showError(`Maximum stay is ${this.maxStay} night(s)`);
                this.calendar.clear();
                return;
            }

            // Check if all dates in range are available
            if (!this.validateDateRange()) {
                this.showError('Some dates in the selected range are not available');
                this.calendar.clear();
                return;
            }

            this.calculatePricing();
        }

        this.updateBookingSummary();
    }

    validateDateRange() {
        if (!this.checkInDate || !this.checkOutDate) return false;

        const current = new Date(this.checkInDate);
        const end = new Date(this.checkOutDate);

        while (current < end) {
            const dateStr = this.formatDate(current);
            const availability = this.availabilityData[dateStr];

            if (availability && availability.status !== 'available') {
                return false;
            }

            current.setDate(current.getDate() + 1);
        }

        return true;
    }

    async calculatePricing() {
        if (!this.checkInDate || !this.checkOutDate || !this.pricingUrl) {
            return;
        }

        try {
            const response = await fetch(this.pricingUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({
                    property_id: this.vacationRentalId,
                    check_in: this.formatDate(this.checkInDate),
                    check_out: this.formatDate(this.checkOutDate),
                    guests: this.getGuestCount()
                })
            });

            const data = await response.json();

            if (data.error) {
                throw new Error(data.message || 'Failed to calculate pricing');
            }

            this.currentPricing = data.data;
            this.updateBookingSummary();

        } catch (error) {
            console.error('Error calculating pricing:', error);
            this.showError('Failed to calculate pricing. Please try again.');
        }
    }

    updateBookingSummary() {
        const summaryElement = this.container.querySelector('.booking-summary');
        if (!summaryElement) return;

        if (!this.checkInDate) {
            summaryElement.style.display = 'none';
            return;
        }

        summaryElement.style.display = 'block';

        const checkInElement = summaryElement.querySelector('.check-in-date');
        const checkOutElement = summaryElement.querySelector('.check-out-date');
        const nightsElement = summaryElement.querySelector('.nights-count');
        const totalElement = summaryElement.querySelector('.total-price');

        if (checkInElement) {
            checkInElement.textContent = this.formatDisplayDate(this.checkInDate);
        }

        if (this.checkOutDate) {
            if (checkOutElement) {
                checkOutElement.textContent = this.formatDisplayDate(this.checkOutDate);
            }

            const nights = Math.ceil((this.checkOutDate - this.checkInDate) / (1000 * 60 * 60 * 24));
            if (nightsElement) {
                nightsElement.textContent = `${nights} night${nights > 1 ? 's' : ''}`;
            }
        }

        if (this.currentPricing && totalElement) {
            totalElement.textContent = this.currentPricing.total_formatted || this.currentPricing.total;
        }

        this.updateBookingButton();
    }

    updateBookingButton() {
        const bookButton = this.container.querySelector('.btn-book-now');
        if (!bookButton) return;

        const canBook = this.checkInDate && this.checkOutDate && this.currentPricing;
        bookButton.disabled = !canBook;

        if (canBook) {
            bookButton.textContent = 'Book Now';
        } else {
            bookButton.textContent = 'Select Dates';
        }
    }

    bindEvents() {
        // Book now button
        const bookButton = this.container.querySelector('.btn-book-now');
        if (bookButton) {
            bookButton.addEventListener('click', () => this.handleBooking());
        }

        // Guest count input
        const guestInput = this.container.querySelector('.guest-count-input');
        if (guestInput) {
            guestInput.addEventListener('change', () => {
                if (this.checkInDate && this.checkOutDate) {
                    this.calculatePricing();
                }
            });
        }
    }

    handleBooking() {
        if (!this.isLoggedIn) {
            if (this.loginUrl) {
                window.location.href = this.loginUrl;
            } else {
                this.showError('Please log in to make a booking');
            }
            return;
        }

        if (!this.checkInDate || !this.checkOutDate) {
            this.showError('Please select check-in and check-out dates');
            return;
        }

        // Redirect to booking form with selected dates
        const params = new URLSearchParams({
            check_in: this.formatDate(this.checkInDate),
            check_out: this.formatDate(this.checkOutDate),
            guests: this.getGuestCount()
        });

        window.location.href = `${this.bookingUrl}?${params.toString()}`;
    }

    getGuestCount() {
        const guestInput = this.container.querySelector('.guest-count-input');
        return guestInput ? parseInt(guestInput.value) || 1 : 1;
    }

    clearSelection() {
        this.checkInDate = null;
        this.checkOutDate = null;
        this.currentPricing = null;
        this.updateBookingSummary();
    }

    showError(message) {
        // Use theme's notification system or fallback to alert
        if (window.showNotification) {
            window.showNotification('error', message);
        } else {
            alert(message);
        }
    }

    formatDate(date) {
        return date.toISOString().split('T')[0];
    }

    formatDisplayDate(date) {
        return date.toLocaleDateString('en-US', {
            weekday: 'short',
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    }
}

// Auto-initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    const containers = document.querySelectorAll('.vacation-rental-booking-calendar');
    containers.forEach(container => {
        new VacationRentalCalendar(container);
    });
});
