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

        console.log('=== TOGGLE VACATION RENTAL METABOX ===');
        console.log('Selected type:', selectedType);
        console.log('Vacation rental metabox found:', !!vacationRentalMetabox);

        if (selectedType === 'vacation_rental') {
            console.log('Showing vacation rental metabox and calendar...');
            vacationRentalMetabox.style.display = 'block';
            showAvailabilityCalendar();
        } else {
            console.log('Hiding vacation rental metabox and calendar...');
            vacationRentalMetabox.style.display = 'none';
            hideAvailabilityCalendar();
        }
    }

    // Function to show availability calendar
    function showAvailabilityCalendar() {
        console.log('=== SHOW AVAILABILITY CALENDAR CALLED ===');

        const calendarSection = document.getElementById('calendar-section');
        const infoMessage = document.getElementById('vacation-rental-info-message');
        const calendarContainer = document.getElementById('property-availability-calendar');

        console.log('Elements found:', {
            calendarSection: !!calendarSection,
            infoMessage: !!infoMessage,
            calendarContainer: !!calendarContainer
        });

        if (calendarSection) {
            console.log('Showing calendar section...');
            calendarSection.style.display = 'block';
        }

        if (infoMessage) {
            console.log('Hiding info message...');
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

    // Initial toggles
    toggleVacationRentalFields();
    toggleVacationRentalMetabox();

    // Listen for type changes
    typeSelect.addEventListener('change', function() {
        toggleVacationRentalFields();
        toggleVacationRentalMetabox();
    });
});

// Property Availability Calendar Class
class PropertyAvailabilityCalendar {
    constructor(options) {
        console.log('PropertyAvailabilityCalendar constructor called with options:', options);

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

        console.log('Starting calendar initialization...');
        this.init();
        this.setupFormSubmissionInterceptor();
    }

    init() {
        console.log('Calendar init() called');

        try {
            console.log('Loading existing availability data...');
            this.loadExistingAvailabilityData();

            console.log('Initializing calendar...');
            this.initializeCalendar();

            console.log('Binding events...');
            this.bindEvents();

            console.log('Calendar initialization complete');
        } catch (error) {
            console.error('Error during calendar initialization:', error);
        }
    }

    loadExistingAvailabilityData() {
        // Load existing availability data from the page if available
        const existingData = window.propertyAvailabilityData || {};

        // Set availability data for calendar display
        if (existingData.availability_by_date) {
            this.availabilityData = existingData.availability_by_date;
        } else {
            this.availabilityData = {};
        }

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

        console.log('Loaded availability data:', this.availabilityData);
        console.log('Loaded pending changes:', this.pendingChanges);
    }

    initializeCalendar() {
        const container = document.getElementById('property-availability-calendar');
        if (!container || typeof flatpickr === 'undefined') {
            console.error('Calendar container not found or Flatpickr not loaded');
            return;
        }

        console.log('Initializing Flatpickr calendar...');

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
                console.log('=== CALENDAR DATE SELECTION ===');
                console.log('Selected dates count:', selectedDates.length);

                this.selectedDates = selectedDates.map(date => date.toISOString().split('T')[0]);

                console.log('Selected dates (ISO):', this.selectedDates);
                console.log('Current action:', this.currentAction);
                console.log('Pending changes before:', JSON.parse(JSON.stringify(this.pendingChanges)));
            }
        });

        console.log('Flatpickr calendar initialized:', this.calendar);
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

        // Add individual dates to pending changes
        switch (this.currentAction) {
            case 'block':
                this.selectedDates.forEach(date => {
                    if (!this.pendingChanges.blocked_dates.includes(date)) {
                        this.pendingChanges.blocked_dates.push(date);
                    }
                });
                break;

            case 'unblock':
                this.selectedDates.forEach(date => {
                    if (!this.pendingChanges.unblocked_dates.includes(date)) {
                        this.pendingChanges.unblocked_dates.push(date);
                    }
                    // Remove from blocked and maintenance if present
                    const blockedIndex = this.pendingChanges.blocked_dates.indexOf(date);
                    if (blockedIndex > -1) {
                        this.pendingChanges.blocked_dates.splice(blockedIndex, 1);
                    }
                    const maintenanceIndex = this.pendingChanges.maintenance_dates.indexOf(date);
                    if (maintenanceIndex > -1) {
                        this.pendingChanges.maintenance_dates.splice(maintenanceIndex, 1);
                    }
                });
                break;

            case 'maintenance':
                this.selectedDates.forEach(date => {
                    if (!this.pendingChanges.maintenance_dates.includes(date)) {
                        this.pendingChanges.maintenance_dates.push(date);
                    }
                });
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
        // Create hidden inputs with individual dates
        const inputs = [
            { name: 'availability_data[blocked_dates]', value: JSON.stringify(this.pendingChanges.blocked_dates) },
            { name: 'availability_data[maintenance_dates]', value: JSON.stringify(this.pendingChanges.maintenance_dates) },
            { name: 'availability_data[unblocked_dates]', value: JSON.stringify(this.pendingChanges.unblocked_dates) }
        ];

        inputs.forEach(inputData => {
            this.createHiddenInput(inputData.name, inputData.value);
        });
    }



    verifyFormInputs() {
        const form = this.findPropertyForm();
        if (!form) {
            console.error('Cannot verify form inputs - no form found');
            return;
        }

        console.log('=== VERIFYING FORM INPUTS ===');

        const expectedInputs = [
            'availability_data[blocked_dates]',
            'availability_data[maintenance_dates]',
            'availability_data[unblocked_dates]'
        ];

        const verification = {};
        expectedInputs.forEach(name => {
            const input = form.querySelector(`input[name="${name}"]`);
            verification[name] = {
                exists: !!input,
                value: input ? input.value : null,
                type: input ? input.type : null
            };
        });

        console.log('Input verification results:', verification);

        // Count total form inputs for context
        const allInputs = form.querySelectorAll('input');
        const availabilityInputs = form.querySelectorAll('input[name*="availability_data"]');

        console.log('Form input summary:', {
            total_inputs: allInputs.length,
            availability_inputs: availabilityInputs.length,
            form_action: form.action,
            form_method: form.method
        });

        return verification;
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

        // Find form and add input
        const form = this.findPropertyForm();
        if (form) {
            form.appendChild(input);
        }
    }

    findPropertyForm() {
        // Try multiple selectors in order of specificity
        const selectors = [
            'form.js-base-form',                    // Botble CMS standard form class
            'form[action*="properties/edit"]',      // Form with properties/edit in action URL
            'form[action*="properties"]',           // Form with properties in action URL
            'form[action*="property"]',             // Form with property in action URL
            'form.property-form',                   // Custom property form class
            'form[method="POST"]',                  // Generic POST form
            'form'                                  // Last resort - any form
        ];

        for (const selector of selectors) {
            const form = document.querySelector(selector);
            if (form) {
                console.log('Found form using selector:', selector, {
                    action: form.action || form.getAttribute('action'),
                    method: form.method || form.getAttribute('method'),
                    className: form.className,
                    hasPropertyEdit: (form.action || '').includes('properties/edit')
                });
                return form;
            }
        }

        console.error('No form found with any selector');
        return null;
    }

    setupFormSubmissionInterceptor() {
        const form = this.findPropertyForm();
        if (!form) {
            console.error('Cannot setup form submission interceptor - no form found');
            return;
        }



        // Add event listener for form submission
        form.addEventListener('submit', (event) => {
            this.updateFormInputs();
        }, true);

        // Also intercept button clicks for additional safety
        const submitButtons = form.querySelectorAll('button[type="submit"], input[type="submit"]');
        submitButtons.forEach(button => {
            button.addEventListener('click', (event) => {
                this.updateFormInputs();
                setTimeout(() => this.updateFormInputs(), 10);
            });
        });
    }





    /**
     * Reload availability data and refresh calendar display
     * Call this after property is saved to sync with database
     */
    reloadAvailabilityData() {
        // Reload data from the global variable (updated by server after save)
        this.loadExistingAvailabilityData();

        // Refresh calendar display
        if (this.calendar) {
            this.calendar.destroy();
            this.initializeCalendar();
        }


    }

    destroy() {
        if (this.calendar) {
            this.calendar.destroy();
            this.calendar = null;
        }
    }
}

// Initialize calendar when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    const calendarContainer = document.getElementById('property-availability-calendar');

    if (calendarContainer) {
        const options = {
            propertyId: calendarContainer.dataset.propertyId
        };

        try {
            window.propertyAvailabilityCalendar = new PropertyAvailabilityCalendar(options);
            setupFormSubmissionListener();
        } catch (error) {
            console.error('Error initializing calendar:', error);
        }
    }
});

// Set up listener for form submission success
function setupFormSubmissionListener() {
    const propertyForm = document.querySelector('form[action*="properties/edit"]') ||
                        document.querySelector('form[action*="properties"]');
    if (propertyForm) {
        // Listen for Botble success notifications (indicates successful save)
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList') {
                    mutation.addedNodes.forEach(function(node) {
                        if (node.nodeType === 1 && node.classList &&
                            (node.classList.contains('alert-success') ||
                             node.querySelector && node.querySelector('.alert-success'))) {
                            // Success message detected, reload calendar
                            console.log('Success message detected, reloading calendar in 1 second...');
                            setTimeout(function() {
                                console.log('Calling calendar reload...');
                                window.reloadPropertyAvailabilityCalendar();
                            }, 1000); // Small delay to ensure data is updated
                        }
                    });
                }
            });
        });

        // Start observing the document for success messages
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }
}

// Global function to reload calendar after property save
window.reloadPropertyAvailabilityCalendar = function() {
    if (window.propertyAvailabilityCalendar) {
        window.propertyAvailabilityCalendar.reloadAvailabilityData();
    }
};








