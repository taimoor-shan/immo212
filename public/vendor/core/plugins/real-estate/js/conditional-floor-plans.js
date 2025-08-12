/******/ (() => { // webpackBootstrap
/*!******************************************************************************!*\
  !*** ./platform/plugins/real-estate/resources/js/conditional-floor-plans.js ***!
  \******************************************************************************/
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
$(document).ready(function () {
  'use strict';

  /**
   * Conditional Floor Plans Handler
   * Toggles between single floor plan fields and multiple floor plans repeater
   * based on the number_floor field value
   */
  var ConditionalFloorPlans = /*#__PURE__*/function () {
    function ConditionalFloorPlans() {
      _classCallCheck(this, ConditionalFloorPlans);
      this.numberFloorField = $('#number_floor');
      this.singleFloorSection = $('#single-floor-plan');
      this.multiFloorSection = $('#multi-floor-plans');
      this.singleFloorFields = $('.single-floor-field');
      this.multiFloorFields = $('.multi-floor-field');
      this.init();
    }
    return _createClass(ConditionalFloorPlans, [{
      key: "init",
      value: function init() {
        var _this = this;
        // Initial toggle on page load
        this.toggleFloorPlansInterface();

        // Toggle when number_floor changes
        this.numberFloorField.on('change input', function () {
          _this.toggleFloorPlansInterface();
        });

        // Auto-generate floor plans when number increases
        this.numberFloorField.on('change', function () {
          _this.handleFloorCountChange();
        });
      }

      /**
       * Toggle floor plans interface based on number_floor value
       */
    }, {
      key: "toggleFloorPlansInterface",
      value: function toggleFloorPlansInterface() {
        var numberFloor = parseInt(this.numberFloorField.val()) || 1;
        if (numberFloor === 1) {
          this.showSingleFloorInterface();
        } else {
          this.showMultiFloorInterface();
        }
      }

      /**
       * Show single floor plan fields
       */
    }, {
      key: "showSingleFloorInterface",
      value: function showSingleFloorInterface() {
        this.singleFloorSection.show();
        this.multiFloorSection.hide();
        this.singleFloorFields.show();
        this.multiFloorFields.hide();

        // Clear multi-floor data when switching to single
        this.clearMultiFloorData();
      }

      /**
       * Show multiple floor plans repeater
       */
    }, {
      key: "showMultiFloorInterface",
      value: function showMultiFloorInterface() {
        this.singleFloorSection.hide();
        this.multiFloorSection.show();
        this.singleFloorFields.hide();
        this.multiFloorFields.show();

        // Clear single floor data when switching to multi
        this.clearSingleFloorData();
      }

      /**
       * Handle floor count changes for auto-generation
       */
    }, {
      key: "handleFloorCountChange",
      value: function handleFloorCountChange() {
        var numberFloor = parseInt(this.numberFloorField.val()) || 1;
        if (numberFloor > 1) {
          this.autoGenerateFloorPlans(numberFloor);
        }
      }

      /**
       * Auto-generate floor plan entries based on floor count
       */
    }, {
      key: "autoGenerateFloorPlans",
      value: function autoGenerateFloorPlans(floorCount) {
        var repeaterContainer = $('.repeater-container');
        var currentItems = repeaterContainer.find('.repeater-item').length;

        // Only auto-generate if no items exist
        if (currentItems === 0) {
          for (var i = 1; i <= floorCount; i++) {
            var floorName = this.generateFloorName(i);
            this.addFloorPlanItem(floorName, i);
          }
        }
      }

      /**
       * Generate appropriate floor name based on floor number
       */
    }, {
      key: "generateFloorName",
      value: function generateFloorName(floorNumber) {
        if (floorNumber === 1) {
          return 'Ground Floor';
        } else if (floorNumber === 2) {
          return '1st Floor';
        } else if (floorNumber === 3) {
          return '2nd Floor';
        } else if (floorNumber === 4) {
          return '3rd Floor';
        } else {
          return "".concat(floorNumber - 1, "th Floor");
        }
      }

      /**
       * Add a new floor plan item to the repeater
       */
    }, {
      key: "addFloorPlanItem",
      value: function addFloorPlanItem(floorName, floorNumber) {
        // Find the repeater add button using the correct selector
        var addButton = $('[data-target="repeater-add"]');
        if (addButton.length) {
          addButton.trigger('click');

          // Set the floor name in the newly added item
          setTimeout(function () {
            var repeaterGroup = $('.repeater-group');
            var lastFieldset = repeaterGroup.find('fieldset').last();
            var nameField = lastFieldset.find('input[name*="[name]"]');
            if (nameField.length) {
              nameField.val(floorName);
            }
          }, 200);
        }
      }

      /**
       * Clear single floor data when switching to multi-floor
       */
    }, {
      key: "clearSingleFloorData",
      value: function clearSingleFloorData() {
        // Only clear if user confirms and there's data to clear
        var floorName = $('#floor_name').val();
        var floorImage = $('#floor_plan_image').val();
        var floorDocument = $('#floor_plan_document').val();
        if (floorName || floorImage || floorDocument) {
          var confirmClear = confirm('Switching to multiple floors will clear single floor data. Continue?');
          if (confirmClear) {
            $('#floor_name').val('');
            // Clear media fields - these are handled by the media field components
            $('#floor_plan_image').val('');
            $('#floor_plan_document').val('');

            // Trigger change events to update UI
            $('#floor_plan_image, #floor_plan_document').trigger('change');
          }
        }
      }

      /**
       * Clear multi-floor data when switching to single
       */
    }, {
      key: "clearMultiFloorData",
      value: function clearMultiFloorData() {
        // Only clear if user confirms and there are items to clear
        var repeaterFieldsets = $('.repeater-group fieldset');
        if (repeaterFieldsets.length > 0) {
          var confirmClear = confirm('Switching to single floor will clear multiple floor plans data. Continue?');
          if (confirmClear) {
            repeaterFieldsets.each(function () {
              var removeButton = $(this).find('[data-target="repeater-remove"]');
              if (removeButton.length) {
                removeButton.trigger('click');
              }
            });
          }
        }
      }

      /**
       * Migrate single floor data to multi-floor format
       */
    }, {
      key: "migrateSingleToMulti",
      value: function migrateSingleToMulti() {
        var floorName = $('#floor_name').val();
        var floorImage = $('#floor_plan_image').val();
        var floorDocument = $('#floor_plan_document').val();
        if (floorName || floorImage || floorDocument) {
          // Add the single floor data as the first item in multi-floor
          this.addFloorPlanItem(floorName || 'Ground Floor', 1);

          // Set the image and document if available
          setTimeout(function () {
            var repeaterGroup = $('.repeater-group');
            var firstFieldset = repeaterGroup.find('fieldset').first();
            if (floorImage) {
              var imageField = firstFieldset.find('input[name*="[image]"]');
              if (imageField.length) {
                imageField.val(floorImage);
              }
            }
          }, 300);
        }
      }

      /**
       * Migrate multi-floor data to single floor format
       */
    }, {
      key: "migrateMultiToSingle",
      value: function migrateMultiToSingle() {
        var repeaterGroup = $('.repeater-group');
        var firstFieldset = repeaterGroup.find('fieldset').first();
        if (firstFieldset.length) {
          var firstName = firstFieldset.find('input[name*="[name]"]').val();
          var firstImage = firstFieldset.find('input[name*="[image]"]').val();
          if (firstName) {
            $('#floor_name').val(firstName);
          }
          if (firstImage) {
            $('#floor_plan_image').val(firstImage);
            $('#floor_plan_image').trigger('change');
          }
        }
      }
    }]);
  }(); // Initialize the conditional floor plans handler
  new ConditionalFloorPlans();

  // Additional helper functions for form validation
  window.validateFloorPlans = function () {
    var numberFloor = parseInt($('#number_floor').val()) || 1;
    if (numberFloor === 1) {
      // Validate single floor plan
      var floorName = $('#floor_name').val();
      var floorImage = $('#floor_plan_image').val();
      if (!floorName && !floorImage) {
        alert('Please provide at least a floor name or floor plan image.');
        return false;
      }
    } else {
      // Validate multi-floor plans
      var repeaterItems = $('.repeater-item');
      if (repeaterItems.length === 0) {
        alert('Please add floor plans for your multi-story property.');
        return false;
      }

      // Check each floor plan has at least a name
      var hasError = false;
      repeaterItems.each(function () {
        var name = $(this).find('input[name*="[name]"]').val();
        if (!name) {
          hasError = true;
          return false;
        }
      });
      if (hasError) {
        alert('Please provide a name for each floor plan.');
        return false;
      }
    }
    return true;
  };
});
/******/ })()
;
//# sourceMappingURL=conditional-floor-plans.js.map