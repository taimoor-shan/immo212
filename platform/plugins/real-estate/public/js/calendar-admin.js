/******/ (() => { // webpackBootstrap
/*!****************************************************************************!*\
  !*** ./platform/plugins/real-estate/resources/assets/js/calendar-admin.js ***!
  \****************************************************************************/
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _regeneratorRuntime() { "use strict"; /*! regenerator-runtime -- Copyright (c) 2014-present, Facebook, Inc. -- license (MIT): https://github.com/facebook/regenerator/blob/main/LICENSE */ _regeneratorRuntime = function _regeneratorRuntime() { return e; }; var t, e = {}, r = Object.prototype, n = r.hasOwnProperty, o = Object.defineProperty || function (t, e, r) { t[e] = r.value; }, i = "function" == typeof Symbol ? Symbol : {}, a = i.iterator || "@@iterator", c = i.asyncIterator || "@@asyncIterator", u = i.toStringTag || "@@toStringTag"; function define(t, e, r) { return Object.defineProperty(t, e, { value: r, enumerable: !0, configurable: !0, writable: !0 }), t[e]; } try { define({}, ""); } catch (t) { define = function define(t, e, r) { return t[e] = r; }; } function wrap(t, e, r, n) { var i = e && e.prototype instanceof Generator ? e : Generator, a = Object.create(i.prototype), c = new Context(n || []); return o(a, "_invoke", { value: makeInvokeMethod(t, r, c) }), a; } function tryCatch(t, e, r) { try { return { type: "normal", arg: t.call(e, r) }; } catch (t) { return { type: "throw", arg: t }; } } e.wrap = wrap; var h = "suspendedStart", l = "suspendedYield", f = "executing", s = "completed", y = {}; function Generator() {} function GeneratorFunction() {} function GeneratorFunctionPrototype() {} var p = {}; define(p, a, function () { return this; }); var d = Object.getPrototypeOf, v = d && d(d(values([]))); v && v !== r && n.call(v, a) && (p = v); var g = GeneratorFunctionPrototype.prototype = Generator.prototype = Object.create(p); function defineIteratorMethods(t) { ["next", "throw", "return"].forEach(function (e) { define(t, e, function (t) { return this._invoke(e, t); }); }); } function AsyncIterator(t, e) { function invoke(r, o, i, a) { var c = tryCatch(t[r], t, o); if ("throw" !== c.type) { var u = c.arg, h = u.value; return h && "object" == _typeof(h) && n.call(h, "__await") ? e.resolve(h.__await).then(function (t) { invoke("next", t, i, a); }, function (t) { invoke("throw", t, i, a); }) : e.resolve(h).then(function (t) { u.value = t, i(u); }, function (t) { return invoke("throw", t, i, a); }); } a(c.arg); } var r; o(this, "_invoke", { value: function value(t, n) { function callInvokeWithMethodAndArg() { return new e(function (e, r) { invoke(t, n, e, r); }); } return r = r ? r.then(callInvokeWithMethodAndArg, callInvokeWithMethodAndArg) : callInvokeWithMethodAndArg(); } }); } function makeInvokeMethod(e, r, n) { var o = h; return function (i, a) { if (o === f) throw Error("Generator is already running"); if (o === s) { if ("throw" === i) throw a; return { value: t, done: !0 }; } for (n.method = i, n.arg = a;;) { var c = n.delegate; if (c) { var u = maybeInvokeDelegate(c, n); if (u) { if (u === y) continue; return u; } } if ("next" === n.method) n.sent = n._sent = n.arg;else if ("throw" === n.method) { if (o === h) throw o = s, n.arg; n.dispatchException(n.arg); } else "return" === n.method && n.abrupt("return", n.arg); o = f; var p = tryCatch(e, r, n); if ("normal" === p.type) { if (o = n.done ? s : l, p.arg === y) continue; return { value: p.arg, done: n.done }; } "throw" === p.type && (o = s, n.method = "throw", n.arg = p.arg); } }; } function maybeInvokeDelegate(e, r) { var n = r.method, o = e.iterator[n]; if (o === t) return r.delegate = null, "throw" === n && e.iterator["return"] && (r.method = "return", r.arg = t, maybeInvokeDelegate(e, r), "throw" === r.method) || "return" !== n && (r.method = "throw", r.arg = new TypeError("The iterator does not provide a '" + n + "' method")), y; var i = tryCatch(o, e.iterator, r.arg); if ("throw" === i.type) return r.method = "throw", r.arg = i.arg, r.delegate = null, y; var a = i.arg; return a ? a.done ? (r[e.resultName] = a.value, r.next = e.nextLoc, "return" !== r.method && (r.method = "next", r.arg = t), r.delegate = null, y) : a : (r.method = "throw", r.arg = new TypeError("iterator result is not an object"), r.delegate = null, y); } function pushTryEntry(t) { var e = { tryLoc: t[0] }; 1 in t && (e.catchLoc = t[1]), 2 in t && (e.finallyLoc = t[2], e.afterLoc = t[3]), this.tryEntries.push(e); } function resetTryEntry(t) { var e = t.completion || {}; e.type = "normal", delete e.arg, t.completion = e; } function Context(t) { this.tryEntries = [{ tryLoc: "root" }], t.forEach(pushTryEntry, this), this.reset(!0); } function values(e) { if (e || "" === e) { var r = e[a]; if (r) return r.call(e); if ("function" == typeof e.next) return e; if (!isNaN(e.length)) { var o = -1, i = function next() { for (; ++o < e.length;) if (n.call(e, o)) return next.value = e[o], next.done = !1, next; return next.value = t, next.done = !0, next; }; return i.next = i; } } throw new TypeError(_typeof(e) + " is not iterable"); } return GeneratorFunction.prototype = GeneratorFunctionPrototype, o(g, "constructor", { value: GeneratorFunctionPrototype, configurable: !0 }), o(GeneratorFunctionPrototype, "constructor", { value: GeneratorFunction, configurable: !0 }), GeneratorFunction.displayName = define(GeneratorFunctionPrototype, u, "GeneratorFunction"), e.isGeneratorFunction = function (t) { var e = "function" == typeof t && t.constructor; return !!e && (e === GeneratorFunction || "GeneratorFunction" === (e.displayName || e.name)); }, e.mark = function (t) { return Object.setPrototypeOf ? Object.setPrototypeOf(t, GeneratorFunctionPrototype) : (t.__proto__ = GeneratorFunctionPrototype, define(t, u, "GeneratorFunction")), t.prototype = Object.create(g), t; }, e.awrap = function (t) { return { __await: t }; }, defineIteratorMethods(AsyncIterator.prototype), define(AsyncIterator.prototype, c, function () { return this; }), e.AsyncIterator = AsyncIterator, e.async = function (t, r, n, o, i) { void 0 === i && (i = Promise); var a = new AsyncIterator(wrap(t, r, n, o), i); return e.isGeneratorFunction(r) ? a : a.next().then(function (t) { return t.done ? t.value : a.next(); }); }, defineIteratorMethods(g), define(g, u, "Generator"), define(g, a, function () { return this; }), define(g, "toString", function () { return "[object Generator]"; }), e.keys = function (t) { var e = Object(t), r = []; for (var n in e) r.push(n); return r.reverse(), function next() { for (; r.length;) { var t = r.pop(); if (t in e) return next.value = t, next.done = !1, next; } return next.done = !0, next; }; }, e.values = values, Context.prototype = { constructor: Context, reset: function reset(e) { if (this.prev = 0, this.next = 0, this.sent = this._sent = t, this.done = !1, this.delegate = null, this.method = "next", this.arg = t, this.tryEntries.forEach(resetTryEntry), !e) for (var r in this) "t" === r.charAt(0) && n.call(this, r) && !isNaN(+r.slice(1)) && (this[r] = t); }, stop: function stop() { this.done = !0; var t = this.tryEntries[0].completion; if ("throw" === t.type) throw t.arg; return this.rval; }, dispatchException: function dispatchException(e) { if (this.done) throw e; var r = this; function handle(n, o) { return a.type = "throw", a.arg = e, r.next = n, o && (r.method = "next", r.arg = t), !!o; } for (var o = this.tryEntries.length - 1; o >= 0; --o) { var i = this.tryEntries[o], a = i.completion; if ("root" === i.tryLoc) return handle("end"); if (i.tryLoc <= this.prev) { var c = n.call(i, "catchLoc"), u = n.call(i, "finallyLoc"); if (c && u) { if (this.prev < i.catchLoc) return handle(i.catchLoc, !0); if (this.prev < i.finallyLoc) return handle(i.finallyLoc); } else if (c) { if (this.prev < i.catchLoc) return handle(i.catchLoc, !0); } else { if (!u) throw Error("try statement without catch or finally"); if (this.prev < i.finallyLoc) return handle(i.finallyLoc); } } } }, abrupt: function abrupt(t, e) { for (var r = this.tryEntries.length - 1; r >= 0; --r) { var o = this.tryEntries[r]; if (o.tryLoc <= this.prev && n.call(o, "finallyLoc") && this.prev < o.finallyLoc) { var i = o; break; } } i && ("break" === t || "continue" === t) && i.tryLoc <= e && e <= i.finallyLoc && (i = null); var a = i ? i.completion : {}; return a.type = t, a.arg = e, i ? (this.method = "next", this.next = i.finallyLoc, y) : this.complete(a); }, complete: function complete(t, e) { if ("throw" === t.type) throw t.arg; return "break" === t.type || "continue" === t.type ? this.next = t.arg : "return" === t.type ? (this.rval = this.arg = t.arg, this.method = "return", this.next = "end") : "normal" === t.type && e && (this.next = e), y; }, finish: function finish(t) { for (var e = this.tryEntries.length - 1; e >= 0; --e) { var r = this.tryEntries[e]; if (r.finallyLoc === t) return this.complete(r.completion, r.afterLoc), resetTryEntry(r), y; } }, "catch": function _catch(t) { for (var e = this.tryEntries.length - 1; e >= 0; --e) { var r = this.tryEntries[e]; if (r.tryLoc === t) { var n = r.completion; if ("throw" === n.type) { var o = n.arg; resetTryEntry(r); } return o; } } throw Error("illegal catch attempt"); }, delegateYield: function delegateYield(e, r, n) { return this.delegate = { iterator: values(e), resultName: r, nextLoc: n }, "next" === this.method && (this.arg = t), y; } }, e; }
function asyncGeneratorStep(n, t, e, r, o, a, c) { try { var i = n[a](c), u = i.value; } catch (n) { return void e(n); } i.done ? t(u) : Promise.resolve(u).then(r, o); }
function _asyncToGenerator(n) { return function () { var t = this, e = arguments; return new Promise(function (r, o) { var a = n.apply(t, e); function _next(n) { asyncGeneratorStep(a, r, o, _next, _throw, "next", n); } function _throw(n) { asyncGeneratorStep(a, r, o, _next, _throw, "throw", n); } _next(void 0); }); }; }
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { _defineProperty(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
function _defineProperty(e, r, t) { return (r = _toPropertyKey(r)) in e ? Object.defineProperty(e, r, { value: t, enumerable: !0, configurable: !0, writable: !0 }) : e[r] = t, e; }
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
/**
 * Admin Calendar functionality for Vacation Rental management
 * Uses Flatpickr for beautiful, minimal calendar interface
 */
var VacationRentalCalendar = /*#__PURE__*/function () {
  function VacationRentalCalendar() {
    var options = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
    _classCallCheck(this, VacationRentalCalendar);
    this.options = _objectSpread({
      propertyId: null,
      container: '#vacation-rental-calendar',
      apiEndpoint: '/admin/vacation-rentals/availability-data',
      blockDatesEndpoint: '/admin/vacation-rentals/block-dates',
      unblockDatesEndpoint: '/admin/vacation-rentals/unblock-dates'
    }, options);
    this.calendar = null;
    this.availabilityData = {};
    this.selectedDates = [];
    this.init();
  }
  return _createClass(VacationRentalCalendar, [{
    key: "init",
    value: function init() {
      this.loadAvailabilityData();
      this.initializeCalendar();
      this.bindEvents();
    }
  }, {
    key: "loadAvailabilityData",
    value: function () {
      var _loadAvailabilityData = _asyncToGenerator(/*#__PURE__*/_regeneratorRuntime().mark(function _callee() {
        var response, data;
        return _regeneratorRuntime().wrap(function _callee$(_context) {
          while (1) switch (_context.prev = _context.next) {
            case 0:
              if (this.options.propertyId) {
                _context.next = 2;
                break;
              }
              return _context.abrupt("return");
            case 2:
              _context.prev = 2;
              _context.next = 5;
              return fetch("".concat(this.options.apiEndpoint, "?property_id=").concat(this.options.propertyId));
            case 5:
              response = _context.sent;
              _context.next = 8;
              return response.json();
            case 8:
              data = _context.sent;
              this.availabilityData = data.data || {};
              _context.next = 15;
              break;
            case 12:
              _context.prev = 12;
              _context.t0 = _context["catch"](2);
              console.error('Failed to load availability data:', _context.t0);
            case 15:
            case "end":
              return _context.stop();
          }
        }, _callee, this, [[2, 12]]);
      }));
      function loadAvailabilityData() {
        return _loadAvailabilityData.apply(this, arguments);
      }
      return loadAvailabilityData;
    }()
  }, {
    key: "initializeCalendar",
    value: function initializeCalendar() {
      var _this = this;
      var container = document.querySelector(this.options.container);
      if (!container) return;

      // Create calendar HTML structure
      container.innerHTML = "\n            <div class=\"calendar-header\">\n                <div class=\"calendar-controls\">\n                    <button type=\"button\" class=\"btn btn-sm btn-success\" id=\"mark-available\">\n                        <i class=\"fas fa-check\"></i> Mark Available\n                    </button>\n                    <button type=\"button\" class=\"btn btn-sm btn-warning\" id=\"mark-blocked\">\n                        <i class=\"fas fa-ban\"></i> Block Dates\n                    </button>\n                    <button type=\"button\" class=\"btn btn-sm btn-danger\" id=\"mark-maintenance\">\n                        <i class=\"fas fa-tools\"></i> Maintenance\n                    </button>\n                </div>\n                <div class=\"calendar-legend\">\n                    <span class=\"legend-item available\"><span class=\"color-box\"></span> Available</span>\n                    <span class=\"legend-item booked\"><span class=\"color-box\"></span> Booked</span>\n                    <span class=\"legend-item blocked\"><span class=\"color-box\"></span> Blocked</span>\n                    <span class=\"legend-item maintenance\"><span class=\"color-box\"></span> Maintenance</span>\n                </div>\n            </div>\n            <div class=\"calendar-container\">\n                <input type=\"text\" id=\"calendar-picker\" style=\"display: none;\">\n            </div>\n            <div class=\"selected-dates-info\" id=\"selected-dates-info\" style=\"display: none;\">\n                <h6>Selected Dates:</h6>\n                <div id=\"selected-dates-list\"></div>\n                <div class=\"mt-2\">\n                    <input type=\"text\" class=\"form-control\" id=\"block-reason\" placeholder=\"Reason for blocking (optional)\">\n                    <div class=\"mt-2\">\n                        <button type=\"button\" class=\"btn btn-primary btn-sm\" id=\"apply-changes\">Apply Changes</button>\n                        <button type=\"button\" class=\"btn btn-secondary btn-sm\" id=\"clear-selection\">Clear Selection</button>\n                    </div>\n                </div>\n            </div>\n        ";

      // Initialize Flatpickr
      this.calendar = flatpickr('#calendar-picker', {
        mode: 'multiple',
        inline: true,
        dateFormat: 'Y-m-d',
        minDate: 'today',
        showMonths: 2,
        onDayCreate: function onDayCreate(dObj, dStr, fp, dayElem) {
          var date = dayElem.dateObj.toISOString().split('T')[0];
          var availability = _this.availabilityData[date];
          if (availability) {
            dayElem.classList.add("calendar-".concat(availability.status));
            if (availability.booking_number) {
              dayElem.title = "Booked: ".concat(availability.booking_number);
            } else if (availability.reason) {
              dayElem.title = availability.reason;
            }
          }
        },
        onChange: function onChange(selectedDates) {
          _this.selectedDates = selectedDates.map(function (date) {
            return date.toISOString().split('T')[0];
          });
          _this.updateSelectedDatesInfo();
        }
      });
    }
  }, {
    key: "updateSelectedDatesInfo",
    value: function updateSelectedDatesInfo() {
      var infoContainer = document.getElementById('selected-dates-info');
      var datesList = document.getElementById('selected-dates-list');
      if (this.selectedDates.length > 0) {
        infoContainer.style.display = 'block';
        datesList.innerHTML = this.selectedDates.map(function (date) {
          return "<span class=\"badge badge-primary me-1\">".concat(date, "</span>");
        }).join('');
      } else {
        infoContainer.style.display = 'none';
      }
    }
  }, {
    key: "bindEvents",
    value: function bindEvents() {
      var _document$getElementB,
        _this2 = this,
        _document$getElementB2,
        _document$getElementB3,
        _document$getElementB4,
        _document$getElementB5;
      // Mark Available
      (_document$getElementB = document.getElementById('mark-available')) === null || _document$getElementB === void 0 || _document$getElementB.addEventListener('click', function () {
        _this2.currentAction = 'available';
        _this2.highlightActionButton('mark-available');
      });

      // Mark Blocked
      (_document$getElementB2 = document.getElementById('mark-blocked')) === null || _document$getElementB2 === void 0 || _document$getElementB2.addEventListener('click', function () {
        _this2.currentAction = 'blocked';
        _this2.highlightActionButton('mark-blocked');
      });

      // Mark Maintenance
      (_document$getElementB3 = document.getElementById('mark-maintenance')) === null || _document$getElementB3 === void 0 || _document$getElementB3.addEventListener('click', function () {
        _this2.currentAction = 'maintenance';
        _this2.highlightActionButton('mark-maintenance');
      });

      // Apply Changes
      (_document$getElementB4 = document.getElementById('apply-changes')) === null || _document$getElementB4 === void 0 || _document$getElementB4.addEventListener('click', function () {
        _this2.applyChanges();
      });

      // Clear Selection
      (_document$getElementB5 = document.getElementById('clear-selection')) === null || _document$getElementB5 === void 0 || _document$getElementB5.addEventListener('click', function () {
        _this2.clearSelection();
      });
    }
  }, {
    key: "highlightActionButton",
    value: function highlightActionButton(activeId) {
      var _document$getElementB6;
      // Remove active class from all buttons
      document.querySelectorAll('.calendar-controls .btn').forEach(function (btn) {
        btn.classList.remove('active');
      });

      // Add active class to selected button
      (_document$getElementB6 = document.getElementById(activeId)) === null || _document$getElementB6 === void 0 || _document$getElementB6.classList.add('active');
    }
  }, {
    key: "applyChanges",
    value: function () {
      var _applyChanges = _asyncToGenerator(/*#__PURE__*/_regeneratorRuntime().mark(function _callee2() {
        var _document$getElementB7;
        var reason, response, result;
        return _regeneratorRuntime().wrap(function _callee2$(_context2) {
          while (1) switch (_context2.prev = _context2.next) {
            case 0:
              if (!(!this.selectedDates.length || !this.currentAction)) {
                _context2.next = 3;
                break;
              }
              alert('Please select dates and an action');
              return _context2.abrupt("return");
            case 3:
              reason = ((_document$getElementB7 = document.getElementById('block-reason')) === null || _document$getElementB7 === void 0 ? void 0 : _document$getElementB7.value) || '';
              _context2.prev = 4;
              _context2.next = 7;
              return fetch(this.options.blockDatesEndpoint, {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json',
                  'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                  property_id: this.options.propertyId,
                  dates: this.selectedDates,
                  status: this.currentAction,
                  reason: reason
                })
              });
            case 7:
              response = _context2.sent;
              _context2.next = 10;
              return response.json();
            case 10:
              result = _context2.sent;
              if (!result.error) {
                _context2.next = 15;
                break;
              }
              alert(result.message || 'Failed to update dates');
              _context2.next = 20;
              break;
            case 15:
              _context2.next = 17;
              return this.loadAvailabilityData();
            case 17:
              this.refreshCalendar();
              this.clearSelection();

              // Show success message
              this.showNotification('Dates updated successfully', 'success');
            case 20:
              _context2.next = 26;
              break;
            case 22:
              _context2.prev = 22;
              _context2.t0 = _context2["catch"](4);
              console.error('Failed to apply changes:', _context2.t0);
              alert('Failed to update dates');
            case 26:
            case "end":
              return _context2.stop();
          }
        }, _callee2, this, [[4, 22]]);
      }));
      function applyChanges() {
        return _applyChanges.apply(this, arguments);
      }
      return applyChanges;
    }()
  }, {
    key: "clearSelection",
    value: function clearSelection() {
      this.selectedDates = [];
      this.calendar.clear();
      this.updateSelectedDatesInfo();

      // Clear active button
      document.querySelectorAll('.calendar-controls .btn').forEach(function (btn) {
        btn.classList.remove('active');
      });
      this.currentAction = null;

      // Clear reason input
      document.getElementById('block-reason').value = '';
    }
  }, {
    key: "refreshCalendar",
    value: function refreshCalendar() {
      this.calendar.redraw();
    }
  }, {
    key: "showNotification",
    value: function showNotification(message) {
      var type = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'info';
      // Create a simple notification
      var notification = document.createElement('div');
      notification.className = "alert alert-".concat(type, " alert-dismissible fade show position-fixed");
      notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
      notification.innerHTML = "\n            ".concat(message, "\n            <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\"></button>\n        ");
      document.body.appendChild(notification);

      // Auto remove after 3 seconds
      setTimeout(function () {
        notification.remove();
      }, 3000);
    }
  }]);
}(); // Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function () {
  // Auto-initialize if container exists
  var calendarContainer = document.querySelector('#vacation-rental-calendar');
  if (calendarContainer) {
    var propertyId = calendarContainer.dataset.propertyId;
    if (propertyId) {
      window.vacationRentalCalendar = new VacationRentalCalendar({
        propertyId: propertyId
      });
    }
  }
});
/******/ })()
;
//# sourceMappingURL=calendar-admin.js.map
