/******/ (() => { // webpackBootstrap
/*!***************************************************************************!*\
  !*** ./platform/plugins/real-estate/resources/js/vacation-rental-form.js ***!
  \***************************************************************************/
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _toConsumableArray(r) { return _arrayWithoutHoles(r) || _iterableToArray(r) || _unsupportedIterableToArray(r) || _nonIterableSpread(); }
function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _iterableToArray(r) { if ("undefined" != typeof Symbol && null != r[Symbol.iterator] || null != r["@@iterator"]) return Array.from(r); }
function _arrayWithoutHoles(r) { if (Array.isArray(r)) return _arrayLikeToArray(r); }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
/**
 * Vacation Rental Form JavaScript
 * Handles showing/hiding vacation rental specific fields and metaboxes
 */

document.addEventListener('DOMContentLoaded', function () {
  var typeSelect = document.getElementById('type');
  var vacationRentalMetabox = document.querySelector('[data-type="vacation_rental"]');
  if (!typeSelect || !vacationRentalMetabox) {
    return;
  }

  // Function to toggle vacation rental metabox visibility
  function toggleVacationRentalMetabox() {
    var selectedType = typeSelect.value;
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
    var calendarSection = document.getElementById('calendar-section');
    var infoMessage = document.getElementById('vacation-rental-info-message');
    var calendarContainer = document.getElementById('property-availability-calendar');
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
    setTimeout(function () {
      if (calendarContainer && !window.propertyAvailabilityCalendar) {
        // For new properties, create a mock calendar that shows available dates
        // For existing properties, use the actual property ID
        var propertyId = calendarContainer.dataset.propertyId || 'new';
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
    var calendarSection = document.getElementById('calendar-section');
    var infoMessage = document.getElementById('vacation-rental-info-message');
    var infoMessageText = document.getElementById('info-message-text');
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
    var calendarContainer = document.getElementById('property-availability-calendar');
    if (!calendarContainer) return;

    // Create a simple calendar that shows all dates as available
    if (typeof flatpickr !== 'undefined') {
      var calendar = flatpickr(calendarContainer, {
        mode: 'multiple',
        inline: true,
        dateFormat: 'Y-m-d',
        minDate: 'today',
        showMonths: 1,
        onDayCreate: function onDayCreate(dObj, dStr, fp, dayElem) {
          // Mark all future dates as available for new properties
          dayElem.classList.add('available');
          dayElem.title = 'Available (Property needs to be saved to manage availability)';
        },
        onChange: function onChange(selectedDates) {
          // For new properties, just show a message
          if (selectedDates.length > 0) {
            alert('Please save the property first to manage availability dates.');
            calendar.clear();
          }
        }
      });

      // Store reference for cleanup
      window.propertyAvailabilityCalendar = {
        destroy: function destroy() {
          return calendar.destroy();
        }
      };
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
  var vacationRentalFields = ['check_in_time', 'check_out_time', 'minimum_stay', 'maximum_stay', 'maximum_guests', 'cleaning_fee', 'security_deposit', 'house_rules', 'cancellation_policy'];
  function toggleVacationRentalFields() {
    var selectedType = typeSelect.value;
    var isVacationRental = selectedType === 'vacation_rental';
    vacationRentalFields.forEach(function (fieldName) {
      var _document$querySelect, _document$querySelect2, _document$querySelect3, _document$querySelect4;
      var fieldContainer = document.querySelector("[data-field-name=\"".concat(fieldName, "\"]")) || ((_document$querySelect = document.querySelector("#".concat(fieldName))) === null || _document$querySelect === void 0 ? void 0 : _document$querySelect.closest('.form-group')) || ((_document$querySelect2 = document.querySelector("input[name=\"".concat(fieldName, "\"]"))) === null || _document$querySelect2 === void 0 ? void 0 : _document$querySelect2.closest('.form-group')) || ((_document$querySelect3 = document.querySelector("textarea[name=\"".concat(fieldName, "\"]"))) === null || _document$querySelect3 === void 0 ? void 0 : _document$querySelect3.closest('.form-group')) || ((_document$querySelect4 = document.querySelector("select[name=\"".concat(fieldName, "\"]"))) === null || _document$querySelect4 === void 0 ? void 0 : _document$querySelect4.closest('.form-group'));
      if (fieldContainer) {
        fieldContainer.style.display = isVacationRental ? 'block' : 'none';
      }
    });
  }

  // Initial toggles
  toggleVacationRentalFields();
  toggleVacationRentalMetabox();

  // Listen for type changes
  typeSelect.addEventListener('change', function () {
    toggleVacationRentalFields();
    toggleVacationRentalMetabox();
  });
});

// Property Availability Calendar Class
var PropertyAvailabilityCalendar = /*#__PURE__*/function () {
  function PropertyAvailabilityCalendar(options) {
    _classCallCheck(this, PropertyAvailabilityCalendar);
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
  return _createClass(PropertyAvailabilityCalendar, [{
    key: "init",
    value: function init() {
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
  }, {
    key: "loadExistingAvailabilityData",
    value: function loadExistingAvailabilityData() {
      // Load existing availability data from the page if available
      var existingData = window.propertyAvailabilityData || {};

      // Set availability data for calendar display
      if (existingData.availability_by_date) {
        this.availabilityData = existingData.availability_by_date;
      } else {
        this.availabilityData = {};
      }

      // Load existing pending changes from hidden inputs if they exist
      var blockedInput = document.querySelector('input[name="availability_data[blocked_dates]"]');
      var maintenanceInput = document.querySelector('input[name="availability_data[maintenance_dates]"]');
      var unblockedInput = document.querySelector('input[name="availability_data[unblocked_dates]"]');
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
  }, {
    key: "initializeCalendar",
    value: function initializeCalendar() {
      var _this = this;
      var container = document.getElementById('property-availability-calendar');
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
        showMonths: 2,
        onDayCreate: function onDayCreate(dObj, dStr, fp, dayElem) {
          var date = dayElem.dateObj.toISOString().split('T')[0];
          var availability = _this.availabilityData[date];

          // Remove any existing status classes
          dayElem.classList.remove('available', 'booked', 'blocked', 'maintenance');
          if (availability && availability.status) {
            // Add the specific status class
            dayElem.classList.add(availability.status);
            dayElem.title = "".concat(availability.status.charAt(0).toUpperCase() + availability.status.slice(1));
            if (availability.reason || availability.notes) {
              dayElem.title += " - ".concat(availability.reason || availability.notes);
            }
          } else {
            // Default to available for dates without specific status
            dayElem.classList.add('available');
            dayElem.title = 'Available';
          }
        },
        onChange: function onChange(selectedDates) {
          console.log('=== CALENDAR DATE SELECTION ===');
          console.log('Selected dates count:', selectedDates.length);
          _this.selectedDates = selectedDates.map(function (date) {
            return date.toISOString().split('T')[0];
          });
          console.log('Selected dates (ISO):', _this.selectedDates);
          console.log('Current action:', _this.currentAction);
          console.log('Pending changes before:', JSON.parse(JSON.stringify(_this.pendingChanges)));
        }
      });
      console.log('Flatpickr calendar initialized:', this.calendar);
    }
  }, {
    key: "bindEvents",
    value: function bindEvents() {
      var _document$getElementB,
        _this2 = this,
        _document$getElementB2,
        _document$getElementB3;
      (_document$getElementB = document.getElementById('block-selected-dates')) === null || _document$getElementB === void 0 || _document$getElementB.addEventListener('click', function () {
        _this2.currentAction = 'block';
        document.getElementById('block-reason-container').style.display = 'block';
        _this2.applyAction();
      });
      (_document$getElementB2 = document.getElementById('unblock-selected-dates')) === null || _document$getElementB2 === void 0 || _document$getElementB2.addEventListener('click', function () {
        _this2.currentAction = 'unblock';
        document.getElementById('block-reason-container').style.display = 'none';
        _this2.applyAction();
      });
      (_document$getElementB3 = document.getElementById('set-maintenance-dates')) === null || _document$getElementB3 === void 0 || _document$getElementB3.addEventListener('click', function () {
        _this2.currentAction = 'maintenance';
        document.getElementById('block-reason-container').style.display = 'block';
        _this2.applyAction();
      });
    }
  }, {
    key: "applyAction",
    value: function applyAction() {
      var _this3 = this;
      if (!this.selectedDates.length || !this.currentAction) {
        alert('Please select dates and an action');
        return;
      }

      // Get reason for blocking/maintenance actions
      var reason = '';
      if (this.currentAction === 'block' || this.currentAction === 'maintenance') {
        var reasonInput = document.getElementById('block-reason');
        if (reasonInput) {
          reason = reasonInput.value.trim();
        }

        // Prompt for reason if not provided
        if (!reason) {
          var _actionName = this.currentAction === 'block' ? 'blocking' : 'maintenance';
          reason = prompt("Enter reason for ".concat(_actionName, " these dates:"));
          if (!reason) {
            return; // User cancelled
          }
        }
      }

      // Add individual dates to pending changes
      switch (this.currentAction) {
        case 'block':
          this.selectedDates.forEach(function (date) {
            if (!_this3.pendingChanges.blocked_dates.includes(date)) {
              _this3.pendingChanges.blocked_dates.push(date);
            }
          });
          break;
        case 'unblock':
          this.selectedDates.forEach(function (date) {
            if (!_this3.pendingChanges.unblocked_dates.includes(date)) {
              _this3.pendingChanges.unblocked_dates.push(date);
            }
            // Remove from blocked and maintenance if present
            var blockedIndex = _this3.pendingChanges.blocked_dates.indexOf(date);
            if (blockedIndex > -1) {
              _this3.pendingChanges.blocked_dates.splice(blockedIndex, 1);
            }
            var maintenanceIndex = _this3.pendingChanges.maintenance_dates.indexOf(date);
            if (maintenanceIndex > -1) {
              _this3.pendingChanges.maintenance_dates.splice(maintenanceIndex, 1);
            }
          });
          break;
        case 'maintenance':
          this.selectedDates.forEach(function (date) {
            if (!_this3.pendingChanges.maintenance_dates.includes(date)) {
              _this3.pendingChanges.maintenance_dates.push(date);
            }
          });
          break;
        default:
          return;
      }

      // Show success message before resetting currentAction
      var actionName = this.currentAction === 'block' ? 'blocked' : this.currentAction === 'maintenance' ? 'set to maintenance' : 'unblocked';

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
      alert("Dates ".concat(actionName, " successfully. Changes will be saved when you save the property."));
    }
  }, {
    key: "updateCalendarVisualState",
    value: function updateCalendarVisualState() {
      // Update the visual state of calendar dates based on pending changes
      var sortedDates = _toConsumableArray(this.selectedDates).sort();
      var startDate = sortedDates[0];
      var endDate = sortedDates[sortedDates.length - 1];

      // Get all dates in the range
      var currentDate = new Date(startDate);
      var endDateObj = new Date(endDate);
      while (currentDate <= endDateObj) {
        var dateStr = currentDate.toISOString().split('T')[0];

        // Update availability data for visual feedback
        if (this.currentAction === 'block') {
          var _document$getElementB4;
          this.availabilityData[dateStr] = {
            status: 'blocked',
            reason: ((_document$getElementB4 = document.getElementById('block-reason')) === null || _document$getElementB4 === void 0 ? void 0 : _document$getElementB4.value) || 'Blocked by owner'
          };
        } else if (this.currentAction === 'maintenance') {
          var _document$getElementB5;
          this.availabilityData[dateStr] = {
            status: 'maintenance',
            reason: ((_document$getElementB5 = document.getElementById('block-reason')) === null || _document$getElementB5 === void 0 ? void 0 : _document$getElementB5.value) || 'Maintenance'
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
  }, {
    key: "updateFormInputs",
    value: function updateFormInputs() {
      var _this4 = this;
      // Create hidden inputs with individual dates
      var inputs = [{
        name: 'availability_data[blocked_dates]',
        value: JSON.stringify(this.pendingChanges.blocked_dates)
      }, {
        name: 'availability_data[maintenance_dates]',
        value: JSON.stringify(this.pendingChanges.maintenance_dates)
      }, {
        name: 'availability_data[unblocked_dates]',
        value: JSON.stringify(this.pendingChanges.unblocked_dates)
      }];
      inputs.forEach(function (inputData) {
        _this4.createHiddenInput(inputData.name, inputData.value);
      });
    }
  }, {
    key: "verifyFormInputs",
    value: function verifyFormInputs() {
      var form = this.findPropertyForm();
      if (!form) {
        console.error('Cannot verify form inputs - no form found');
        return;
      }
      console.log('=== VERIFYING FORM INPUTS ===');
      var expectedInputs = ['availability_data[blocked_dates]', 'availability_data[maintenance_dates]', 'availability_data[unblocked_dates]'];
      var verification = {};
      expectedInputs.forEach(function (name) {
        var input = form.querySelector("input[name=\"".concat(name, "\"]"));
        verification[name] = {
          exists: !!input,
          value: input ? input.value : null,
          type: input ? input.type : null
        };
      });
      console.log('Input verification results:', verification);

      // Count total form inputs for context
      var allInputs = form.querySelectorAll('input');
      var availabilityInputs = form.querySelectorAll('input[name*="availability_data"]');
      console.log('Form input summary:', {
        total_inputs: allInputs.length,
        availability_inputs: availabilityInputs.length,
        form_action: form.action,
        form_method: form.method
      });
      return verification;
    }
  }, {
    key: "createHiddenInput",
    value: function createHiddenInput(name, value) {
      // Remove existing input if it exists
      var existingInput = document.querySelector("input[name=\"".concat(name, "\"]"));
      if (existingInput) {
        existingInput.remove();
      }

      // Create new hidden input
      var input = document.createElement('input');
      input.type = 'hidden';
      input.name = name;
      input.value = value;

      // Find form and add input
      var form = this.findPropertyForm();
      if (form) {
        form.appendChild(input);
      }
    }
  }, {
    key: "findPropertyForm",
    value: function findPropertyForm() {
      // Try multiple selectors in order of specificity
      var selectors = ['form.js-base-form',
      // Botble CMS standard form class
      'form[action*="properties/edit"]',
      // Form with properties/edit in action URL
      'form[action*="properties"]',
      // Form with properties in action URL
      'form[action*="property"]',
      // Form with property in action URL
      'form.property-form',
      // Custom property form class
      'form[method="POST"]',
      // Generic POST form
      'form' // Last resort - any form
      ];
      for (var _i = 0, _selectors = selectors; _i < _selectors.length; _i++) {
        var selector = _selectors[_i];
        var form = document.querySelector(selector);
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
  }, {
    key: "setupFormSubmissionInterceptor",
    value: function setupFormSubmissionInterceptor() {
      var _this5 = this;
      var form = this.findPropertyForm();
      if (!form) {
        console.error('Cannot setup form submission interceptor - no form found');
        return;
      }

      // Add event listener for form submission
      form.addEventListener('submit', function (event) {
        _this5.updateFormInputs();
      }, true);

      // Also intercept button clicks for additional safety
      var submitButtons = form.querySelectorAll('button[type="submit"], input[type="submit"]');
      submitButtons.forEach(function (button) {
        button.addEventListener('click', function (event) {
          _this5.updateFormInputs();
          setTimeout(function () {
            return _this5.updateFormInputs();
          }, 10);
        });
      });
    }

    /**
     * Reload availability data and refresh calendar display
     * Call this after property is saved to sync with database
     */
  }, {
    key: "reloadAvailabilityData",
    value: function reloadAvailabilityData() {
      // Reload data from the global variable (updated by server after save)
      this.loadExistingAvailabilityData();

      // Refresh calendar display
      if (this.calendar) {
        this.calendar.destroy();
        this.initializeCalendar();
      }
    }
  }, {
    key: "destroy",
    value: function destroy() {
      if (this.calendar) {
        this.calendar.destroy();
        this.calendar = null;
      }
    }
  }]);
}(); // Initialize calendar when DOM is ready
document.addEventListener('DOMContentLoaded', function () {
  var calendarContainer = document.getElementById('property-availability-calendar');
  if (calendarContainer) {
    var options = {
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
  var propertyForm = document.querySelector('form[action*="properties/edit"]') || document.querySelector('form[action*="properties"]');
  if (propertyForm) {
    // Listen for Botble success notifications (indicates successful save)
    var observer = new MutationObserver(function (mutations) {
      mutations.forEach(function (mutation) {
        if (mutation.type === 'childList') {
          mutation.addedNodes.forEach(function (node) {
            if (node.nodeType === 1 && node.classList && (node.classList.contains('alert-success') || node.querySelector && node.querySelector('.alert-success'))) {
              // Success message detected, reload calendar
              console.log('Success message detected, reloading calendar in 1 second...');
              setTimeout(function () {
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
window.reloadPropertyAvailabilityCalendar = function () {
  if (window.propertyAvailabilityCalendar) {
    window.propertyAvailabilityCalendar.reloadAvailabilityData();
  }
};
/******/ })()
;
//# sourceMappingURL=vacation-rental-form.js.map