/**
 * Vacation Rental Agent Calendar
 * Handles agent/property owner functionality for managing vacation rental availability
 */

class VacationRentalAgentCalendar {
    constructor(container) {
        this.container = container;
        this.vacationRentalId = container.dataset.vacationRentalId;
        this.context = container.dataset.context;
        
        this.calendar = null;
        this.availabilityData = {};
        this.selectedDates = [];
        
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
        if (!this.vacationRentalId) {
            console.warn('Missing vacation rental ID');
            return;
        }

        try {
            const startDate = this.formatDate(new Date());
            const endDate = this.formatDate(new Date(Date.now() + 365 * 24 * 60 * 60 * 1000)); // 1 year ahead
            
            // Use agent API endpoint
            const response = await fetch(`/account/vacation-rentals/availability-data?property_id=${this.vacationRentalId}&start_date=${startDate}&end_date=${endDate}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            this.availabilityData = data.data || {};
            
            console.log('Agent availability data loaded:', this.availabilityData);
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

        // Initialize Flatpickr for agent use
        this.calendar = flatpickr(calendarElement, {
            mode: 'multiple',
            inline: true,
            dateFormat: 'Y-m-d',
            minDate: 'today',
            showMonths: 2,
            onDayCreate: (dObj, dStr, fp, dayElem) => {
                const date = this.formatDate(dayElem.dateObj);
                const availability = this.availabilityData[date];
                
                if (availability) {
                    dayElem.classList.add(`calendar-${availability.status}`);
                    if (availability.notes) {
                        dayElem.title = `${availability.status.toUpperCase()}: ${availability.notes}`;
                    } else {
                        dayElem.title = availability.status.toUpperCase();
                    }
                }
            },
            onChange: (selectedDates) => {
                this.selectedDates = selectedDates;
                this.updateActionButtons();
            },
            onReady: () => {
                this.addCustomStyles();
            }
        });
    }

    updateActionButtons() {
        const hasSelection = this.selectedDates.length > 0;
        const blockBtn = this.container.querySelector('#block-selected-dates');
        const unblockBtn = this.container.querySelector('#unblock-selected-dates');
        const maintenanceBtn = this.container.querySelector('#set-maintenance-dates');
        
        if (blockBtn) blockBtn.disabled = !hasSelection;
        if (unblockBtn) unblockBtn.disabled = !hasSelection;
        if (maintenanceBtn) maintenanceBtn.disabled = !hasSelection;
    }

    bindEvents() {
        // Block dates
        const blockBtn = this.container.querySelector('#block-selected-dates');
        if (blockBtn) {
            blockBtn.addEventListener('click', () => this.handleBlockDates());
        }

        // Unblock dates
        const unblockBtn = this.container.querySelector('#unblock-selected-dates');
        if (unblockBtn) {
            unblockBtn.addEventListener('click', () => this.handleUnblockDates());
        }

        // Maintenance dates
        const maintenanceBtn = this.container.querySelector('#set-maintenance-dates');
        if (maintenanceBtn) {
            maintenanceBtn.addEventListener('click', () => this.handleMaintenanceDates());
        }

        // Show/hide reason input
        const reasonContainer = this.container.querySelector('#block-reason-container');
        if (blockBtn && reasonContainer) {
            blockBtn.addEventListener('click', () => {
                reasonContainer.style.display = 'block';
            });
        }
    }

    async handleBlockDates() {
        if (this.selectedDates.length === 0) {
            this.showError('Please select dates to block');
            return;
        }

        const reason = this.container.querySelector('#block-reason')?.value || 'Blocked by owner';
        const startDate = this.formatDate(Math.min(...this.selectedDates));
        const endDate = this.formatDate(Math.max(...this.selectedDates));

        try {
            const response = await fetch('/account/vacation-rentals/block-dates', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: new URLSearchParams({
                    property_id: this.vacationRentalId,
                    start_date: startDate,
                    end_date: endDate,
                    reason: reason
                })
            });

            const data = await response.json();
            
            if (data.error) {
                this.showError(data.message);
                return;
            }

            this.showSuccess('Dates blocked successfully');
            this.refreshCalendar();
            this.clearSelection();
        } catch (error) {
            console.error('Failed to block dates:', error);
            this.showError('Failed to block dates');
        }
    }

    async handleUnblockDates() {
        if (this.selectedDates.length === 0) {
            this.showError('Please select dates to unblock');
            return;
        }

        const startDate = this.formatDate(Math.min(...this.selectedDates));
        const endDate = this.formatDate(Math.max(...this.selectedDates));

        try {
            const response = await fetch('/account/vacation-rentals/unblock-dates', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: new URLSearchParams({
                    property_id: this.vacationRentalId,
                    start_date: startDate,
                    end_date: endDate
                })
            });

            const data = await response.json();
            
            if (data.error) {
                this.showError(data.message);
                return;
            }

            this.showSuccess('Dates unblocked successfully');
            this.refreshCalendar();
            this.clearSelection();
        } catch (error) {
            console.error('Failed to unblock dates:', error);
            this.showError('Failed to unblock dates');
        }
    }

    async handleMaintenanceDates() {
        if (this.selectedDates.length === 0) {
            this.showError('Please select dates for maintenance');
            return;
        }

        const reason = this.container.querySelector('#block-reason')?.value || 'Maintenance scheduled';
        const startDate = this.formatDate(Math.min(...this.selectedDates));
        const endDate = this.formatDate(Math.max(...this.selectedDates));

        try {
            const response = await fetch('/account/vacation-rentals/maintenance-dates', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: new URLSearchParams({
                    property_id: this.vacationRentalId,
                    start_date: startDate,
                    end_date: endDate,
                    reason: reason
                })
            });

            const data = await response.json();
            
            if (data.error) {
                this.showError(data.message);
                return;
            }

            this.showSuccess('Maintenance dates set successfully');
            this.refreshCalendar();
            this.clearSelection();
        } catch (error) {
            console.error('Failed to set maintenance dates:', error);
            this.showError('Failed to set maintenance dates');
        }
    }

    async refreshCalendar() {
        await this.loadAvailabilityData();
        this.calendar.redraw();
    }

    clearSelection() {
        this.calendar.clear();
        this.selectedDates = [];
        this.updateActionButtons();
        
        const reasonContainer = this.container.querySelector('#block-reason-container');
        if (reasonContainer) {
            reasonContainer.style.display = 'none';
        }
        
        const reasonInput = this.container.querySelector('#block-reason');
        if (reasonInput) {
            reasonInput.value = '';
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
        
        // Also show browser notification if available
        if (typeof Botble !== 'undefined' && Botble.showError) {
            Botble.showError(message);
        }
    }

    showSuccess(message) {
        // Show browser notification if available
        if (typeof Botble !== 'undefined' && Botble.showSuccess) {
            Botble.showSuccess(message);
        }
    }

    formatDate(date) {
        if (typeof date === 'number') {
            date = new Date(date);
        }
        return date.toISOString().split('T')[0];
    }
}

// Auto-initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    const containers = document.querySelectorAll('.vacation-rental-calendar-wrapper[data-context="agent"]');
    containers.forEach(container => {
        new VacationRentalAgentCalendar(container);
    });
});
