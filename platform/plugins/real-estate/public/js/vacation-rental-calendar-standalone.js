/**
 * Standalone Vacation Rental Calendar for User Dashboard
 * This is separate from the property edit form calendar to avoid conflicts
 */

class StandaloneVacationCalendar {
    constructor(options) {
        this.options = options;
        this.container = document.querySelector(options.container);
        this.propertyId = options.propertyId;
        this.calendar = null;
        this.selectedDates = [];
        this.availabilityData = window.propertyAvailabilityData || {};
        
        this.init();
    }
    
    init() {
        if (!this.container) {
            console.error('Calendar container not found');
            return;
        }
        
        this.initializeCalendar();
        this.setupEventListeners();
    }
    
    initializeCalendar() {
        const self = this;
        
        // Initialize Flatpickr
        this.calendar = flatpickr(this.container, {
            inline: true,
            mode: 'multiple',
            dateFormat: 'Y-m-d',
            minDate: 'today',
            showMonths: 2,
            onDayCreate: function(dObj, dStr, fp, dayElem) {
                const dateStr = dayElem.dateObj.toISOString().split('T')[0];
                const dayData = self.availabilityData[dateStr];
                
                if (dayData) {
                    dayElem.classList.add('has-availability-data');
                    
                    switch(dayData.status) {
                        case 'available':
                            dayElem.classList.add('available');
                            break;
                        case 'booked':
                            dayElem.classList.add('booked');
                            dayElem.classList.add('flatpickr-disabled');
                            break;
                        case 'blocked':
                            dayElem.classList.add('blocked');
                            break;
                        case 'maintenance':
                            dayElem.classList.add('maintenance');
                            break;
                    }
                    
                    if (dayData.reason) {
                        dayElem.title = dayData.reason;
                    }
                    
                    if (dayData.price) {
                        const priceElement = document.createElement('div');
                        priceElement.className = 'day-price';
                        priceElement.textContent = '$' + dayData.price;
                        dayElem.appendChild(priceElement);
                    }
                } else {
                    dayElem.classList.add('available');
                }
            },
            onChange: function(selectedDates) {
                self.selectedDates = selectedDates.map(date => 
                    date.toISOString().split('T')[0]
                );
            }
        });
    }
    
    setupEventListeners() {
        const blockBtn = document.getElementById('block-selected-dates');
        const unblockBtn = document.getElementById('unblock-selected-dates');
        const maintenanceBtn = document.getElementById('set-maintenance-dates');
        
        if (blockBtn) {
            blockBtn.addEventListener('click', () => this.handleAction('block'));
        }
        
        if (unblockBtn) {
            unblockBtn.addEventListener('click', () => this.handleAction('unblock'));
        }
        
        if (maintenanceBtn) {
            maintenanceBtn.addEventListener('click', () => this.handleAction('maintenance'));
        }
    }
    
    handleAction(action) {
        if (!this.selectedDates.length) {
            alert('Please select dates first.');
            return;
        }
        
        const reasonContainer = document.getElementById('block-reason-container');
        const reasonInput = document.getElementById('block-reason');
        
        // Show reason input for block and maintenance
        if (action === 'block' || action === 'maintenance') {
            reasonContainer.style.display = 'block';
            
            // Check if we need to wait for reason input
            if (!reasonContainer.dataset.readyToSubmit) {
                reasonContainer.dataset.readyToSubmit = 'true';
                reasonContainer.dataset.pendingAction = action;
                return;
            }
        }
        
        // Prepare dates
        const sortedDates = [...this.selectedDates].sort();
        const startDate = sortedDates[0];
        const endDate = sortedDates[sortedDates.length - 1];
        const reason = reasonInput ? reasonInput.value : '';
        
        // Reset the ready state
        if (reasonContainer) {
            reasonContainer.dataset.readyToSubmit = '';
            reasonContainer.dataset.pendingAction = '';
        }
        
        // Make AJAX request
        this.updateAvailability(action, startDate, endDate, reason);
    }
    
    updateAvailability(action, startDate, endDate, reason) {
        const self = this;
        const propertyId = this.propertyId;
        
        // Show loading
        this.showLoading();
        
        // Prepare endpoint
        let endpoint = '';
        switch(action) {
            case 'block':
                endpoint = '/account/vacation-rentals/block-dates';
                break;
            case 'unblock':
                endpoint = '/account/vacation-rentals/unblock-dates';
                break;
            case 'maintenance':
                endpoint = '/account/vacation-rentals/maintenance-dates';
                break;
        }
        
        // Make AJAX request
        $.ajax({
            url: endpoint,
            type: 'POST',
            data: {
                property_id: propertyId,
                start_date: startDate,
                end_date: endDate,
                reason: reason,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.error === false) {
                    // Show success message
                    if (window.Botble) {
                        Botble.showSuccess(response.message || 'Dates updated successfully!');
                    } else {
                        alert(response.message || 'Dates updated successfully!');
                    }
                    
                    // Reset form
                    self.resetForm();
                    
                    // Reload calendar data
                    self.reloadCalendarData();
                } else {
                    self.hideLoading();
                    if (window.Botble) {
                        Botble.showError(response.message || 'An error occurred.');
                    } else {
                        alert(response.message || 'An error occurred.');
                    }
                }
            },
            error: function(xhr) {
                self.hideLoading();
                let errorMessage = 'An error occurred while updating dates.';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMessage = Object.values(xhr.responseJSON.errors).flat().join(', ');
                }
                
                if (window.Botble) {
                    Botble.showError(errorMessage);
                } else {
                    alert(errorMessage);
                }
            }
        });
    }
    
    showLoading() {
        const overlay = document.createElement('div');
        overlay.className = 'calendar-loading-overlay';
        overlay.innerHTML = '<div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div>';
        overlay.style.cssText = 'position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.8); display: flex; align-items: center; justify-content: center; z-index: 1000;';
        
        this.container.style.position = 'relative';
        this.container.appendChild(overlay);
    }
    
    hideLoading() {
        const overlay = this.container.querySelector('.calendar-loading-overlay');
        if (overlay) {
            overlay.remove();
        }
    }
    
    resetForm() {
        const reasonContainer = document.getElementById('block-reason-container');
        const reasonInput = document.getElementById('block-reason');
        
        if (reasonContainer) {
            reasonContainer.style.display = 'none';
            reasonContainer.dataset.readyToSubmit = '';
            reasonContainer.dataset.pendingAction = '';
        }
        
        if (reasonInput) {
            reasonInput.value = '';
        }
        
        // Clear selected dates
        this.selectedDates = [];
        if (this.calendar) {
            this.calendar.clear();
        }
    }
    
    reloadCalendarData() {
        const self = this;
        
        // Fetch fresh data
        $.ajax({
            url: '/account/vacation-rentals/availability-data',
            type: 'GET',
            data: {
                property_id: this.propertyId,
                start_date: new Date().toISOString().split('T')[0],
                end_date: new Date(new Date().setMonth(new Date().getMonth() + 3)).toISOString().split('T')[0]
            },
            success: function(response) {
                if (response.error === false && response.data) {
                    // Update availability data
                    self.availabilityData = response.data;
                    window.propertyAvailabilityData = response.data;
                    
                    // Refresh calendar
                    self.refreshCalendar();
                }
                self.hideLoading();
            },
            error: function() {
                console.error('Failed to reload availability data');
                self.hideLoading();
            }
        });
    }
    
    refreshCalendar() {
        // Destroy and recreate calendar with new data
        if (this.calendar) {
            this.calendar.destroy();
        }
        
        // Clear container
        this.container.innerHTML = '';
        
        // Reinitialize
        this.initializeCalendar();
    }
    
    destroy() {
        if (this.calendar) {
            this.calendar.destroy();
        }
    }
}

// Export for global use
window.StandaloneVacationCalendar = StandaloneVacationCalendar;
