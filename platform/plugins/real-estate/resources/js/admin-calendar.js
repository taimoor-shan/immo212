/**
 * Vacation Rental Admin Calendar Management
 * Dedicated file for admin-only calendar operations
 * Handles: blocking dates, maintenance scheduling, availability management
 */

class VacationRentalAdminCalendar {
    constructor(container) {
        console.log('=== ADMIN CALENDAR INITIALIZATION ===');

        this.container = container;
        this.vacationRentalId = container.dataset.vacationRentalId;
        this.availabilityUrl = container.dataset.availabilityUrl;
        this.blockUrl = container.dataset.blockUrl;
        this.unblockUrl = container.dataset.unblockUrl;
        this.maintenanceUrl = container.dataset.maintenanceUrl;

        this.calendar = null;
        this.availabilityData = {};
        this.selectedDates = [];
        this.currentAction = null;

        console.log('Admin calendar config:', {
            vacationRentalId: this.vacationRentalId,
            availabilityUrl: this.availabilityUrl,
            blockUrl: this.blockUrl,
            unblockUrl: this.unblockUrl,
            maintenanceUrl: this.maintenanceUrl
        });

        this.init();
    }

    init() {
        console.log('Initializing admin calendar...');
        this.initializeCalendar();
        this.bindEvents();
        // Delay availability loading to ensure page is fully loaded
        setTimeout(() => {
            this.loadAvailabilityData();
        }, 500);
    }

    initializeCalendar() {
        if (typeof flatpickr === 'undefined') {
            console.error('Flatpickr library not loaded');
            return;
        }

        const calendarElement = document.getElementById('admin-calendar-flatpickr') || this.container;
        this.calendar = flatpickr(calendarElement, {
            mode: 'multiple',
            inline: true,
            dateFormat: 'Y-m-d',
            minDate: 'today',
            showMonths: 2,
            onDayCreate: (dObj, dStr, fp, dayElem) => {
                const date = dayElem.dateObj.toISOString().split('T')[0];
                const availability = this.availabilityData[date];

                // Remove existing status classes
                dayElem.classList.remove('available', 'booked', 'blocked', 'maintenance');

                if (availability && availability.status) {
                    dayElem.classList.add(availability.status);
                    dayElem.title = `${availability.status.charAt(0).toUpperCase() + availability.status.slice(1)}`;
                    if (availability.reason || availability.notes) {
                        dayElem.title += ` - ${availability.reason || availability.notes}`;
                    }
                } else {
                    dayElem.classList.add('available');
                    dayElem.title = 'Available';
                }
            },
            onChange: (selectedDates) => {
                this.selectedDates = selectedDates.map(date => date.toISOString().split('T')[0]);
                console.log('Selected dates:', this.selectedDates);
                this.updateActionButtons();
            }
        });

        console.log('Admin calendar initialized');
    }

    bindEvents() {
        // Block dates button
        const blockBtn = document.getElementById('admin-block-dates');
        if (blockBtn) {
            blockBtn.addEventListener('click', () => this.handleBlockDates());
        }

        // Unblock dates button
        const unblockBtn = document.getElementById('admin-unblock-dates');
        if (unblockBtn) {
            unblockBtn.addEventListener('click', () => this.handleUnblockDates());
        }

        // Maintenance dates button
        const maintenanceBtn = document.getElementById('admin-maintenance-dates');
        if (maintenanceBtn) {
            maintenanceBtn.addEventListener('click', () => this.handleMaintenanceDates());
        }

        // Apply with reason button
        const applyBtn = document.getElementById('admin-apply-reason');
        if (applyBtn) {
            applyBtn.addEventListener('click', () => this.applyWithReason());
        }

        // Cancel reason button
        const cancelBtn = document.getElementById('admin-cancel-reason');
        if (cancelBtn) {
            cancelBtn.addEventListener('click', () => this.cancelReason());
        }
    }

    async loadAvailabilityData() {
        console.log('=== LOADING ADMIN AVAILABILITY DATA ===');

        if (!this.availabilityUrl || !this.vacationRentalId) {
            console.error('Missing required data for loading availability:', {
                availabilityUrl: this.availabilityUrl,
                vacationRentalId: this.vacationRentalId
            });
            return;
        }

        // Debug authentication context
        console.log('🔐 Authentication context:', {
            csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')?.substring(0, 10) + '...',
            hasCsrfToken: !!document.querySelector('meta[name="csrf-token"]'),
            cookies: document.cookie.split(';').map(c => c.trim().split('=')[0]),
            location: window.location.href
        });

        try {
            const startDate = new Date();
            const endDate = new Date();
            endDate.setFullYear(endDate.getFullYear() + 1);

            const url = `${this.availabilityUrl}?property_id=${this.vacationRentalId}&start_date=${this.formatDate(startDate)}&end_date=${this.formatDate(endDate)}`;
            console.log('📡 API request:', url);

            // Try using XMLHttpRequest instead of fetch for better cookie handling
            const response = await new Promise((resolve, reject) => {
                const xhr = new XMLHttpRequest();
                xhr.open('GET', url, true);
                xhr.withCredentials = true; // Include cookies
                xhr.setRequestHeader('Accept', 'application/json');
                xhr.setRequestHeader('Content-Type', 'application/json');
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                if (csrfToken) {
                    xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                }
                
                xhr.onload = function() {
                    const mockResponse = {
                        ok: xhr.status >= 200 && xhr.status < 300,
                        status: xhr.status,
                        statusText: xhr.statusText,
                        json: async () => JSON.parse(xhr.responseText)
                    };
                    resolve(mockResponse);
                };
                
                xhr.onerror = function() {
                    reject(new Error('Network error'));
                };
                
                xhr.send();
            });

            console.log('📡 Response:', response.status, response.statusText);

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const data = await response.json();
            console.log('📡 Response data:', data);

            if (data.error) {
                throw new Error(data.message || 'Failed to load availability data');
            }

            this.availabilityData = data.data || {};
            console.log('✓ Loaded availability data:', Object.keys(this.availabilityData).length, 'records');

            this.refreshCalendarDisplay();

        } catch (error) {
            console.error('✗ Error loading availability data:', error);
            this.showError('Failed to load calendar data. Please refresh the page.');
        }
    }

    async handleBlockDates() {
        console.log('=== BLOCKING DATES ===');
        if (this.selectedDates.length === 0) {
            this.showError('Please select dates to block');
            return;
        }

        this.currentAction = 'block';
        this.showReasonInput();
    }


    async handleUnblockDates() {
        if (this.selectedDates.length === 0) {
            this.showError('Please select dates to unblock');
            return;
        }

        // Unblock doesn't need a reason, execute immediately
        await this.performAction(this.unblockUrl, { dates: this.selectedDates });
    }

    async handleMaintenanceDates() {
        if (this.selectedDates.length === 0) {
            this.showError('Please select dates for maintenance');
            return;
        }

        this.currentAction = 'maintenance';
        this.showReasonInput();
    }

    showReasonInput() {
        const reasonContainer = document.getElementById('admin-reason-container');
        if (reasonContainer) {
            reasonContainer.style.display = 'block';
            const textarea = reasonContainer.querySelector('textarea');
            if (textarea) {
                textarea.focus();
            }
        }
    }

    cancelReason() {
        const reasonContainer = document.getElementById('admin-reason-container');
        if (reasonContainer) {
            reasonContainer.style.display = 'none';
            const textarea = reasonContainer.querySelector('textarea');
            if (textarea) {
                textarea.value = '';
            }
        }
        this.currentAction = null;
    }

    async applyWithReason() {
        const reason = this.getReasonInput();

        if (this.currentAction === 'block') {
            await this.performAction(this.blockUrl, { dates: this.selectedDates, reason });
        } else if (this.currentAction === 'maintenance') {
            await this.performAction(this.maintenanceUrl, { dates: this.selectedDates, reason });
        }

        this.cancelReason();
    }

    async performAction(url, data) {
        console.log('=== PERFORMING ADMIN ACTION ===');
        console.log('🎯 URL:', url);
        console.log('🎯 Data:', data);

        try {
            data.property_id = this.vacationRentalId;

            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                throw new Error('CSRF token not found. Please refresh the page.');
            }

            const response = await fetch(url, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(data)
            });

            console.log('📡 Action response:', response.status, response.statusText);

            if (!response.ok) {
                const errorText = await response.text();
                console.error('✗ Action failed:', errorText);
                throw new Error(`HTTP error! status: ${response.status} - ${errorText}`);
            }

            const result = await response.json();
            console.log('✓ Action result:', result);

            if (result.error) {
                throw new Error(result.message || 'Action failed');
            }

            this.showSuccess(result.message || 'Action completed successfully');
            this.clearSelection();
            await this.loadAvailabilityData();

        } catch (error) {
            console.error('✗ Error performing action:', error);
            this.showError(error.message || 'Action failed. Please try again.');
        }
    }

    getReasonInput() {
        const textarea = document.querySelector('#admin-reason');
        return textarea ? textarea.value.trim() : '';
    }

    updateActionButtons() {
        const hasSelection = this.selectedDates.length > 0;

        const blockBtn = document.getElementById('admin-block-dates');
        const unblockBtn = document.getElementById('admin-unblock-dates');
        const maintenanceBtn = document.getElementById('admin-maintenance-dates');

        if (blockBtn) blockBtn.disabled = !hasSelection;
        if (unblockBtn) unblockBtn.disabled = !hasSelection;
        if (maintenanceBtn) maintenanceBtn.disabled = !hasSelection;
    }

    clearSelection() {
        if (this.calendar) {
            this.calendar.clear();
        }
        this.selectedDates = [];
        this.updateActionButtons();
    }

    refreshCalendarDisplay() {
        if (this.calendar) {
            this.calendar.redraw();
        }
    }

    formatDate(date) {
        return date.toISOString().split('T')[0];
    }

    showSuccess(message) {
        console.log('✓ Success:', message);
        
        // Try multiple notification systems in order of preference
        if (typeof Botble !== 'undefined' && Botble.showSuccess) {
            Botble.showSuccess(message);
        } else if (typeof toastr !== 'undefined') {
            toastr.success(message);
        } else {
            // Fallback to custom notification
            this.showCustomNotification(message, 'success');
        }
    }

    showError(message) {
        console.error('✗ Error:', message);
        
        // Try multiple notification systems in order of preference
        if (typeof Botble !== 'undefined' && Botble.showError) {
            Botble.showError(message);
        } else if (typeof toastr !== 'undefined') {
            toastr.error(message);
        } else {
            // Fallback to custom notification
            this.showCustomNotification(message, 'error');
        }
    }

    showCustomNotification(message, type = 'info') {
        // Create a custom notification element
        const notification = document.createElement('div');
        notification.className = `admin-notification admin-notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'}"></i>
                <span class="notification-message">${message}</span>
                <button type="button" class="notification-close" onclick="this.parentElement.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        
        // Apply styles
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            max-width: 500px;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            color: white;
            font-size: 14px;
            background: ${type === 'success' ? '#28a745' : '#dc3545'};
            animation: slideInFromRight 0.3s ease-out;
        `;
        
        // Add CSS animation if not exists
        if (!document.querySelector('#admin-notification-styles')) {
            const styles = document.createElement('style');
            styles.id = 'admin-notification-styles';
            styles.textContent = `
                @keyframes slideInFromRight {
                    from { transform: translateX(100%); opacity: 0; }
                    to { transform: translateX(0); opacity: 1; }
                }
                .notification-content {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                }
                .notification-close {
                    background: none;
                    border: none;
                    color: white;
                    cursor: pointer;
                    padding: 0;
                    margin-left: auto;
                }
                .notification-close:hover {
                    opacity: 0.7;
                }
            `;
            document.head.appendChild(styles);
        }
        
        document.body.appendChild(notification);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.style.animation = 'slideInFromRight 0.3s ease-out reverse';
                setTimeout(() => notification.remove(), 300);
            }
        }, 5000);
    }

    destroy() {
        if (this.calendar) {
            this.calendar.destroy();
            this.calendar = null;
        }
    }
}

// General Admin Functionality for Vacation Rental Pages
class VacationRentalAdminHelper {
    static init() {
        this.bindPropertySelection();
        this.bindRefreshButtons();
        this.bindDeleteConfirmations();
    }

    static bindPropertySelection() {
        // Property selection for availability and calendar pages
        const propertySelect = document.getElementById('property-select');
        if (propertySelect) {
            propertySelect.addEventListener('change', (e) => {
                const propertyId = e.target.value;
                if (propertyId) {
                    const url = new URL(window.location);
                    url.searchParams.set('property_id', propertyId);
                    window.location.href = url.toString();
                }
            });
        }
    }

    static bindRefreshButtons() {
        // Refresh button functionality
        const refreshButtons = document.querySelectorAll('.btn-refresh');
        refreshButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                window.location.reload();
            });
        });
    }

    static bindDeleteConfirmations() {
        // Delete confirmation dialogs
        const deleteButtons = document.querySelectorAll('.btn-delete');
        deleteButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                if (!confirm('Are you sure you want to delete this item?')) {
                    e.preventDefault();
                    return false;
                }
            });
        });
    }
}


// Property Form Calendar Integration
class PropertyFormCalendar {
    constructor(container) {
        this.container = container;
        this.calendar = null;
        this.availabilityData = {};
        this.selectedDates = [];
        this.currentAction = null; // Track current action: 'block', 'maintenance', 'unblock'
        this.pendingChanges = {
            blocked_dates: [],
            maintenance_dates: [],
            unblocked_dates: []
        };

        this.init();
        this.setupFormIntegration();
    }

    init() {
        this.loadExistingData();
        this.initializeCalendar();
        this.bindEvents();
    }

    loadExistingData() {
        // Load existing availability data from global variable
        const existingData = window.propertyAvailabilityData || {};
        if (existingData.availability_by_date) {
            this.availabilityData = existingData.availability_by_date;
        }
    }

    initializeCalendar() {
        if (!this.container || typeof flatpickr === 'undefined') {
            console.error('Calendar container not found or Flatpickr not loaded');
            return;
        }

        console.log('Initializing Flatpickr on container:', this.container);

        this.calendar = flatpickr(this.container, {
            mode: 'multiple',
            inline: true,
            dateFormat: 'Y-m-d',
            minDate: 'today',
            showMonths: 2,
            onDayCreate: (dObj, dStr, fp, dayElem) => {
                const date = dayElem.dateObj.toISOString().split('T')[0];
                const availability = this.availabilityData[date];

                dayElem.classList.remove('available', 'booked', 'blocked', 'maintenance');

                if (availability && availability.status) {
                    dayElem.classList.add(availability.status);
                    dayElem.title = availability.status.charAt(0).toUpperCase() + availability.status.slice(1);
                } else {
                    dayElem.classList.add('available');
                    dayElem.title = 'Available';
                }
            },
            onChange: (selectedDates) => {
                console.log('=== FLATPICKR ONCHANGE TRIGGERED ===');
                console.log('Raw selectedDates from Flatpickr:', selectedDates);

                this.selectedDates = selectedDates.map(date => date.toISOString().split('T')[0]);
                console.log('Processed selectedDates:', this.selectedDates);
                console.log('Selected dates count:', this.selectedDates.length);

                // Update button states directly
                console.log('Calling updateActionButtons...');
                this.updateActionButtons();

                // Emit custom event for button state updates
                document.dispatchEvent(new CustomEvent('calendarSelectionChanged', {
                    detail: { selectedDates: this.selectedDates }
                }));

                console.log('Custom event dispatched');
            },
            onReady: () => {
                console.log('Flatpickr calendar is ready');
            }
        });

        console.log('Flatpickr initialized:', !!this.calendar);
    }

    bindEvents() {
        console.log('Binding PropertyFormCalendar events...');

        // Block dates button (form template uses different IDs)
        const blockBtn = document.getElementById('block-selected-dates');
        if (blockBtn) {
            blockBtn.addEventListener('click', () => {
                console.log('Block button clicked');
                this.blockSelectedDates();
            });
            console.log('Block button event bound');
        } else {
            console.warn('Block button not found: #block-selected-dates');
        }

        // Unblock dates button
        const unblockBtn = document.getElementById('unblock-selected-dates');
        if (unblockBtn) {
            unblockBtn.addEventListener('click', () => {
                console.log('Unblock button clicked');
                this.unblockSelectedDates();
            });
            console.log('Unblock button event bound');
        } else {
            console.warn('Unblock button not found: #unblock-selected-dates');
        }

        // Maintenance dates button
        const maintenanceBtn = document.getElementById('set-maintenance-dates');
        if (maintenanceBtn) {
            maintenanceBtn.addEventListener('click', () => {
                console.log('Maintenance button clicked');
                this.setMaintenanceDates();
            });
            console.log('Maintenance button event bound');
        } else {
            console.warn('Maintenance button not found: #set-maintenance-dates');
        }

        // Apply reason button
        const applyBtn = document.getElementById('apply-reason');
        if (applyBtn) {
            applyBtn.addEventListener('click', () => {
                console.log('Apply reason button clicked');
                this.applyReasonToSelectedDates();
            });
        }

        // Cancel reason button
        const cancelBtn = document.getElementById('cancel-reason');
        if (cancelBtn) {
            cancelBtn.addEventListener('click', () => {
                console.log('Cancel reason button clicked');
                this.cancelReason();
            });
        }
    }

    blockSelectedDates() {
        if (!this.selectedDates.length) {
            console.warn('No dates selected for blocking');
            return;
        }

        console.log('Blocking selected dates:', this.selectedDates);
        this.currentAction = 'block'; // Set current action

        // Show reason input container
        const reasonContainer = document.getElementById('block-reason-container');
        if (reasonContainer) {
            reasonContainer.style.display = 'block';
            const textarea = document.getElementById('block-reason');
            if (textarea) {
                textarea.placeholder = 'Enter reason for blocking dates...';
                textarea.focus();
            }
        } else {
            // Fallback to prompt if container not found
            const reason = prompt('Enter reason for blocking these dates:') || 'Blocked by admin';
            this.pendingChanges.blocked_dates.push(...this.selectedDates.map(date => ({ date, reason })));
            this.updateFormInputs();
            this.refreshCalendarDisplay();
            this.clearSelection();
        }
    }

    unblockSelectedDates() {
        if (!this.selectedDates.length) {
            console.warn('No dates selected for unblocking');
            return;
        }

        console.log('Unblocking selected dates:', this.selectedDates);
        this.pendingChanges.unblocked_dates.push(...this.selectedDates);
        this.updateFormInputs();
        this.refreshCalendarDisplay();
        this.clearSelection();
    }

    setMaintenanceDates() {
        if (!this.selectedDates.length) {
            console.warn('No dates selected for maintenance');
            return;
        }

        console.log('Setting maintenance for selected dates:', this.selectedDates);
        this.currentAction = 'maintenance'; // Set current action

        // Show reason input container
        const reasonContainer = document.getElementById('block-reason-container');
        if (reasonContainer) {
            reasonContainer.style.display = 'block';
            const textarea = document.getElementById('block-reason');
            if (textarea) {
                textarea.placeholder = 'Enter reason for maintenance...';
                textarea.focus();
            }
        } else {
            // Fallback to prompt if container not found
            const reason = prompt('Enter reason for maintenance:') || 'Maintenance';
            this.pendingChanges.maintenance_dates.push(...this.selectedDates.map(date => ({ date, reason })));
            this.updateFormInputs();
            this.refreshCalendarDisplay();
            this.clearSelection();
        }
    }

    updateFormInputs() {
        console.log('=== UPDATING FORM INPUTS ===');
        console.log('Pending changes:', this.pendingChanges);

        // Update hidden form inputs with pending changes
        this.updateHiddenInput('blocked_dates', this.pendingChanges.blocked_dates);
        this.updateHiddenInput('maintenance_dates', this.pendingChanges.maintenance_dates);
        this.updateHiddenInput('unblocked_dates', this.pendingChanges.unblocked_dates);

        console.log('Form inputs updated');
    }

    updateHiddenInput(name, data) {
        let input = document.querySelector(`input[name="${name}"]`);
        if (!input) {
            // Try to find existing input with ID-based selector for form fields
            const idMap = {
                'availability_data[blocked_dates]': 'blocked-dates-input',
                'availability_data[maintenance_dates]': 'maintenance-dates-input',
                'availability_data[unblocked_dates]': 'unblocked-dates-input'
            };

            if (idMap[name]) {
                input = document.getElementById(idMap[name]);
            }

            if (!input) {
                input = document.createElement('input');
                input.type = 'hidden';
                input.name = name;
                const form = this.findPropertyForm();
                if (form) form.appendChild(input);
            }
        }
        input.value = JSON.stringify(data);
        console.log(`Updated hidden input ${name}:`, data);
    }

    findPropertyForm() {
        // Try multiple selectors to find the form
        return document.querySelector('form[action*="vacation-rentals"]') ||
               document.querySelector('form[action*="properties"]') ||
               document.querySelector('form.main-form-body') ||
               document.querySelector('form');
    }

    setupFormIntegration() {
        const form = this.findPropertyForm();
        if (form) {
            form.addEventListener('submit', () => {
                this.updateFormInputs();
            });
        }
    }

    updateActionButtons() {
        const hasSelection = this.selectedDates.length > 0;
        console.log('PropertyFormCalendar updating action buttons, hasSelection:', hasSelection);

        const blockBtn = document.getElementById('block-selected-dates');
        const unblockBtn = document.getElementById('unblock-selected-dates');
        const maintenanceBtn = document.getElementById('set-maintenance-dates');

        if (blockBtn) {
            blockBtn.disabled = !hasSelection;
            console.log('Block button disabled:', blockBtn.disabled);
        }
        if (unblockBtn) {
            unblockBtn.disabled = !hasSelection;
            console.log('Unblock button disabled:', unblockBtn.disabled);
        }
        if (maintenanceBtn) {
            maintenanceBtn.disabled = !hasSelection;
            console.log('Maintenance button disabled:', maintenanceBtn.disabled);
        }
    }

    refreshCalendarDisplay() {
        if (this.calendar) {
            this.calendar.redraw();
        }
    }

    clearSelection() {
        if (this.calendar) {
            this.calendar.clear();
        }
        this.selectedDates = [];
        this.updateActionButtons();

        // Emit event to update button states
        document.dispatchEvent(new CustomEvent('calendarSelectionChanged', {
            detail: { selectedDates: this.selectedDates }
        }));
    }

    cancelReason() {
        const reasonContainer = document.getElementById('block-reason-container');
        const textarea = document.getElementById('block-reason');

        if (reasonContainer) {
            reasonContainer.style.display = 'none';
        }
        if (textarea) {
            textarea.value = '';
            textarea.placeholder = 'Enter reason for blocking dates...';
        }
    }

    applyReasonToSelectedDates() {
        const textarea = document.getElementById('block-reason');
        const reason = textarea ? textarea.value.trim() || 'No reason provided' : 'No reason provided';

        console.log('=== APPLYING REASON TO SELECTED DATES ===');
        console.log('Current action:', this.currentAction);
        console.log('Selected dates:', this.selectedDates);
        console.log('Reason:', reason);

        if (!this.selectedDates.length) {
            console.warn('No dates selected to apply reason to');
            return;
        }

        // Apply the action based on currentAction
        if (this.currentAction === 'block') {
            this.pendingChanges.blocked_dates.push(...this.selectedDates.map(date => ({ date, reason })));
            console.log('Added to blocked_dates:', this.pendingChanges.blocked_dates);
        } else if (this.currentAction === 'maintenance') {
            this.pendingChanges.maintenance_dates.push(...this.selectedDates.map(date => ({ date, reason })));
            console.log('Added to maintenance_dates:', this.pendingChanges.maintenance_dates);
        } else {
            console.warn('Unknown action:', this.currentAction);
            return;
        }

        // Update form inputs
        console.log('Updating form inputs...');
        this.updateFormInputs();

        // Refresh calendar display
        this.refreshCalendarDisplay();

        // Clear selection and reset action
        this.clearSelection();
        this.currentAction = null;

        // Hide reason container
        const reasonContainer = document.getElementById('block-reason-container');
        if (reasonContainer) {
            reasonContainer.style.display = 'none';
        }
        if (textarea) {
            textarea.value = '';
            textarea.placeholder = 'Enter reason for blocking dates...';
        }

        console.log('Reason applied successfully');
    }

    destroy() {
        if (this.calendar) {
            this.calendar.destroy();
            this.calendar = null;
        }
    }
}

// Auto-initialize admin calendars and general functionality
document.addEventListener('DOMContentLoaded', function() {
    // Initialize admin calendars
    const adminCalendars = document.querySelectorAll('.vacation-rental-admin-calendar');
    adminCalendars.forEach(container => {
        new VacationRentalAdminCalendar(container);
    });

    // Initialize property form calendar ONLY if it doesn't have the admin class
    const propertyFormCalendar = document.getElementById('property-availability-calendar');
    if (propertyFormCalendar && !propertyFormCalendar.classList.contains('vacation-rental-admin-calendar')) {
        window.propertyAvailabilityCalendar = new PropertyFormCalendar(propertyFormCalendar);
    }

    // Initialize general admin functionality
    VacationRentalAdminHelper.init();
});

// Export for manual initialization
window.VacationRentalAdminCalendar = VacationRentalAdminCalendar;
window.VacationRentalAdminHelper = VacationRentalAdminHelper;
window.PropertyFormCalendar = PropertyFormCalendar;

// Global function for backward compatibility
window.reloadPropertyAvailabilityCalendar = function() {
    if (window.propertyAvailabilityCalendar && window.propertyAvailabilityCalendar.loadExistingData) {
        window.propertyAvailabilityCalendar.loadExistingData();
        window.propertyAvailabilityCalendar.refreshCalendarDisplay();
    }
};
