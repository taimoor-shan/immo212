/**
 * Vacation Rental Form JavaScript
 * Handles showing/hiding vacation rental specific fields and metaboxes
 */

document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type');
    const vacationRentalMetabox = document.querySelector('[data-type="vacation_rental"]');
    
    if (!typeSelect || !vacationRentalMetabox) {
        return;
    }

    // Function to toggle vacation rental metabox visibility
    function toggleVacationRentalMetabox() {
        const selectedType = typeSelect.value;

        if (selectedType === 'vacation_rental') {
            vacationRentalMetabox.style.display = 'block';
            showAvailabilityCalendar();
        } else {
            vacationRentalMetabox.style.display = 'none';
            hideAvailabilityCalendar();
        }
    }

    // Function to show availability calendar
    function showAvailabilityCalendar() {
        const calendarSection = document.getElementById('calendar-section');
        const infoMessage = document.getElementById('vacation-rental-info-message');
        const calendarContainer = document.getElementById('property-availability-calendar');

        if (calendarSection) {
            calendarSection.style.display = 'block';
        }

        if (infoMessage) {
            infoMessage.style.display = 'none';
        }

        // Initialize calendar immediately for vacation rental type
        setTimeout(() => {
            if (calendarContainer && !window.propertyAvailabilityCalendar) {
                // For new properties, create a mock calendar that shows available dates
                // For existing properties, use the actual property ID
                const propertyId = calendarContainer.dataset.propertyId || 'new';

                if (propertyId === 'new') {
                    // Initialize a basic calendar for new properties
                    initializeBasicCalendar();
                } else {
                    // Initialize full calendar with API integration for existing properties
                    window.propertyAvailabilityCalendar = new PropertyAvailabilityCalendar({
                        propertyId: propertyId,
                        container: '#property-availability-calendar',
                        apiEndpoint: calendarContainer.dataset.apiEndpoint,
                        blockEndpoint: calendarContainer.dataset.blockEndpoint,
                        unblockEndpoint: calendarContainer.dataset.unblockEndpoint,
                        maintenanceEndpoint: calendarContainer.dataset.maintenanceEndpoint
                    });
                }
            }
        }, 100);
    }

    // Function to hide availability calendar
    function hideAvailabilityCalendar() {
        const calendarSection = document.getElementById('calendar-section');
        const infoMessage = document.getElementById('vacation-rental-info-message');
        const infoMessageText = document.getElementById('info-message-text');

        if (calendarSection) {
            calendarSection.style.display = 'none';
        }

        if (infoMessage && infoMessageText) {
            infoMessage.style.display = 'block';
            infoMessageText.textContent = 'Select "Vacation Rental" as property type to enable availability calendar management.';
        }

        // Destroy existing calendar
        if (window.propertyAvailabilityCalendar && window.propertyAvailabilityCalendar.destroy) {
            window.propertyAvailabilityCalendar.destroy();
            window.propertyAvailabilityCalendar = null;
        }
    }

    // Function to initialize basic calendar for new properties
    function initializeBasicCalendar() {
        const calendarContainer = document.getElementById('property-availability-calendar');
        if (!calendarContainer) return;

        // Create a simple calendar that shows all dates as available
        if (typeof flatpickr !== 'undefined') {
            const calendar = flatpickr(calendarContainer, {
                mode: 'multiple',
                inline: true,
                dateFormat: 'Y-m-d',
                minDate: 'today',
                showMonths: 1,
                onDayCreate: (dObj, dStr, fp, dayElem) => {
                    // Mark all future dates as available for new properties
                    dayElem.classList.add('available');
                    dayElem.title = 'Available (Property needs to be saved to manage availability)';
                },
                onChange: (selectedDates) => {
                    // For new properties, just show a message
                    if (selectedDates.length > 0) {
                        alert('Please save the property first to manage availability dates.');
                        calendar.clear();
                    }
                }
            });

            // Store reference for cleanup
            window.propertyAvailabilityCalendar = { destroy: () => calendar.destroy() };
        } else {
            // Fallback if flatpickr is not loaded
            calendarContainer.innerHTML = '<div class="alert alert-warning">Calendar will be available after saving the property.</div>';
        }
    }

    // Initial check - show/hide based on current selection
    toggleVacationRentalMetabox();

    // Listen for type changes
    typeSelect.addEventListener('change', toggleVacationRentalMetabox);

    // Also toggle vacation rental specific fields if they exist
    const vacationRentalFields = [
        'check_in_time',
        'check_out_time', 
        'minimum_stay',
        'maximum_stay',
        'maximum_guests',
        'cleaning_fee',
        'security_deposit',
        'house_rules',
        'cancellation_policy'
    ];

    function toggleVacationRentalFields() {
        const selectedType = typeSelect.value;
        const isVacationRental = selectedType === 'vacation_rental';

        vacationRentalFields.forEach(fieldName => {
            const fieldContainer = document.querySelector(`[data-field-name="${fieldName}"]`) || 
                                 document.querySelector(`#${fieldName}`)?.closest('.form-group') ||
                                 document.querySelector(`input[name="${fieldName}"]`)?.closest('.form-group') ||
                                 document.querySelector(`textarea[name="${fieldName}"]`)?.closest('.form-group') ||
                                 document.querySelector(`select[name="${fieldName}"]`)?.closest('.form-group');

            if (fieldContainer) {
                fieldContainer.style.display = isVacationRental ? 'block' : 'none';
            }
        });
    }

    // Initial field toggle
    toggleVacationRentalFields();

    // Listen for type changes for fields
    typeSelect.addEventListener('change', toggleVacationRentalFields);
});

// Property Availability Calendar Class
class PropertyAvailabilityCalendar {
    constructor(options) {
        this.options = options;
        this.calendar = null;
        this.availabilityData = {};
        this.selectedDates = [];
        this.currentAction = null;

        // Form data storage for availability changes
        this.pendingChanges = {
            blocked_dates: [],
            maintenance_dates: [],
            unblocked_dates: []
        };

        this.init();
    }

    init() {
        this.loadExistingAvailabilityData();
        this.initializeCalendar();
        this.bindEvents();
    }

    loadExistingAvailabilityData() {
        // Load existing availability data from the page if available
        // This would be populated by the server when editing an existing property
        const existingData = window.propertyAvailabilityData || {};
        this.availabilityData = existingData;

        // Load existing pending changes from hidden inputs if they exist
        const blockedInput = document.querySelector('input[name="availability_data[blocked_dates]"]');
        const maintenanceInput = document.querySelector('input[name="availability_data[maintenance_dates]"]');
        const unblockedInput = document.querySelector('input[name="availability_data[unblocked_dates]"]');

        if (blockedInput && blockedInput.value) {
            try {
                this.pendingChanges.blocked_dates = JSON.parse(blockedInput.value);
            } catch (e) {
                console.warn('Failed to parse blocked dates:', e);
            }
        }

        if (maintenanceInput && maintenanceInput.value) {
            try {
                this.pendingChanges.maintenance_dates = JSON.parse(maintenanceInput.value);
            } catch (e) {
                console.warn('Failed to parse maintenance dates:', e);
            }
        }

        if (unblockedInput && unblockedInput.value) {
            try {
                this.pendingChanges.unblocked_dates = JSON.parse(unblockedInput.value);
            } catch (e) {
                console.warn('Failed to parse unblocked dates:', e);
            }
        }
    }

    initializeCalendar() {
        const container = document.querySelector(this.options.container);
        if (!container || typeof flatpickr === 'undefined') return;

        this.calendar = flatpickr(container, {
            mode: 'multiple',
            inline: true,
            dateFormat: 'Y-m-d',
            minDate: 'today',
            showMonths: 1,
            onDayCreate: (dObj, dStr, fp, dayElem) => {
                const date = dayElem.dateObj.toISOString().split('T')[0];
                const availability = this.availabilityData[date];

                // Remove any existing status classes
                dayElem.classList.remove('available', 'booked', 'blocked', 'maintenance');

                if (availability && availability.status) {
                    // Add the specific status class
                    dayElem.classList.add(availability.status);
                    dayElem.title = `${availability.status.charAt(0).toUpperCase() + availability.status.slice(1)}`;

                    if (availability.reason || availability.notes) {
                        dayElem.title += ` - ${availability.reason || availability.notes}`;
                    }
                } else {
                    // Default to available for dates without specific status
                    dayElem.classList.add('available');
                    dayElem.title = 'Available';
                }
            },
            onChange: (selectedDates) => {
                this.selectedDates = selectedDates.map(date => date.toISOString().split('T')[0]);
            }
        });
    }

    bindEvents() {
        document.getElementById('block-selected-dates')?.addEventListener('click', () => {
            this.currentAction = 'block';
            document.getElementById('block-reason-container').style.display = 'block';
            this.applyAction();
        });

        document.getElementById('unblock-selected-dates')?.addEventListener('click', () => {
            this.currentAction = 'unblock';
            document.getElementById('block-reason-container').style.display = 'none';
            this.applyAction();
        });

        document.getElementById('set-maintenance-dates')?.addEventListener('click', () => {
            this.currentAction = 'maintenance';
            document.getElementById('block-reason-container').style.display = 'block';
            this.applyAction();
        });
    }

    applyAction() {
        if (!this.selectedDates.length || !this.currentAction) {
            alert('Please select dates and an action');
            return;
        }

        // Get reason for blocking/maintenance actions
        let reason = '';
        if (this.currentAction === 'block' || this.currentAction === 'maintenance') {
            const reasonInput = document.getElementById('block-reason');
            if (reasonInput) {
                reason = reasonInput.value.trim();
            }

            // Prompt for reason if not provided
            if (!reason) {
                const actionName = this.currentAction === 'block' ? 'blocking' : 'maintenance';
                reason = prompt(`Enter reason for ${actionName} these dates:`);
                if (!reason) {
                    return; // User cancelled
                }
            }
        }

        // Sort dates and get start/end range
        const sortedDates = [...this.selectedDates].sort();
        const startDate = sortedDates[0];
        const endDate = sortedDates[sortedDates.length - 1];

        // Add to pending changes instead of making AJAX call
        const dateRange = {
            start_date: startDate,
            end_date: endDate,
            reason: reason
        };

        switch (this.currentAction) {
            case 'block':
                this.pendingChanges.blocked_dates.push(dateRange);
                break;
            case 'unblock':
                this.pendingChanges.unblocked_dates.push(dateRange);
                break;
            case 'maintenance':
                this.pendingChanges.maintenance_dates.push(dateRange);
                break;
            default:
                return;
        }

        // Show success message before resetting currentAction
        const actionName = this.currentAction === 'block' ? 'blocked' :
                          this.currentAction === 'maintenance' ? 'set to maintenance' : 'unblocked';

        // Update calendar visual state immediately for better UX
        this.updateCalendarVisualState();

        // Update form hidden inputs
        this.updateFormInputs();

        // Hide reason container and reset
        document.getElementById('block-reason-container').style.display = 'none';
        document.getElementById('block-reason').value = '';
        this.currentAction = null;

        // Clear selected dates
        this.selectedDates = [];

        alert(`Dates ${actionName} successfully. Changes will be saved when you save the property.`);
    }

    updateCalendarVisualState() {
        // Update the visual state of calendar dates based on pending changes
        const sortedDates = [...this.selectedDates].sort();
        const startDate = sortedDates[0];
        const endDate = sortedDates[sortedDates.length - 1];

        // Get all dates in the range
        const currentDate = new Date(startDate);
        const endDateObj = new Date(endDate);

        while (currentDate <= endDateObj) {
            const dateStr = currentDate.toISOString().split('T')[0];

            // Update availability data for visual feedback
            if (this.currentAction === 'block') {
                this.availabilityData[dateStr] = {
                    status: 'blocked',
                    reason: document.getElementById('block-reason')?.value || 'Blocked by owner'
                };
            } else if (this.currentAction === 'maintenance') {
                this.availabilityData[dateStr] = {
                    status: 'maintenance',
                    reason: document.getElementById('block-reason')?.value || 'Maintenance'
                };
            } else if (this.currentAction === 'unblock') {
                this.availabilityData[dateStr] = {
                    status: 'available',
                    reason: null
                };
            }

            currentDate.setDate(currentDate.getDate() + 1);
        }

        // Refresh calendar display
        if (this.calendar) {
            this.calendar.destroy();
            this.initializeCalendar();
        }
    }

    updateFormInputs() {
        // Create or update hidden form inputs with pending changes
        this.createHiddenInput('availability_data[blocked_dates]', JSON.stringify(this.pendingChanges.blocked_dates));
        this.createHiddenInput('availability_data[maintenance_dates]', JSON.stringify(this.pendingChanges.maintenance_dates));
        this.createHiddenInput('availability_data[unblocked_dates]', JSON.stringify(this.pendingChanges.unblocked_dates));
    }

    createHiddenInput(name, value) {
        // Remove existing input if it exists
        const existingInput = document.querySelector(`input[name="${name}"]`);
        if (existingInput) {
            existingInput.remove();
        }

        // Create new hidden input
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;

        // Add to form
        const form = document.querySelector('form');
        if (form) {
            form.appendChild(input);
        }
    }

    destroy() {
        if (this.calendar) {
            this.calendar.destroy();
            this.calendar = null;
        }
    }
}
