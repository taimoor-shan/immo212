/******/ (() => { // webpackBootstrap
/*!**********************************************************************!*\
  !*** ./platform/themes/homzen/assets/js/vacation-rental-calendar.js ***!
  \**********************************************************************/
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _regeneratorRuntime() { "use strict"; /*! regenerator-runtime -- Copyright (c) 2014-present, Facebook, Inc. -- license (MIT): https://github.com/facebook/regenerator/blob/main/LICENSE */ _regeneratorRuntime = function _regeneratorRuntime() { return e; }; var t, e = {}, r = Object.prototype, n = r.hasOwnProperty, o = Object.defineProperty || function (t, e, r) { t[e] = r.value; }, i = "function" == typeof Symbol ? Symbol : {}, a = i.iterator || "@@iterator", c = i.asyncIterator || "@@asyncIterator", u = i.toStringTag || "@@toStringTag"; function define(t, e, r) { return Object.defineProperty(t, e, { value: r, enumerable: !0, configurable: !0, writable: !0 }), t[e]; } try { define({}, ""); } catch (t) { define = function define(t, e, r) { return t[e] = r; }; } function wrap(t, e, r, n) { var i = e && e.prototype instanceof Generator ? e : Generator, a = Object.create(i.prototype), c = new Context(n || []); return o(a, "_invoke", { value: makeInvokeMethod(t, r, c) }), a; } function tryCatch(t, e, r) { try { return { type: "normal", arg: t.call(e, r) }; } catch (t) { return { type: "throw", arg: t }; } } e.wrap = wrap; var h = "suspendedStart", l = "suspendedYield", f = "executing", s = "completed", y = {}; function Generator() {} function GeneratorFunction() {} function GeneratorFunctionPrototype() {} var p = {}; define(p, a, function () { return this; }); var d = Object.getPrototypeOf, v = d && d(d(values([]))); v && v !== r && n.call(v, a) && (p = v); var g = GeneratorFunctionPrototype.prototype = Generator.prototype = Object.create(p); function defineIteratorMethods(t) { ["next", "throw", "return"].forEach(function (e) { define(t, e, function (t) { return this._invoke(e, t); }); }); } function AsyncIterator(t, e) { function invoke(r, o, i, a) { var c = tryCatch(t[r], t, o); if ("throw" !== c.type) { var u = c.arg, h = u.value; return h && "object" == _typeof(h) && n.call(h, "__await") ? e.resolve(h.__await).then(function (t) { invoke("next", t, i, a); }, function (t) { invoke("throw", t, i, a); }) : e.resolve(h).then(function (t) { u.value = t, i(u); }, function (t) { return invoke("throw", t, i, a); }); } a(c.arg); } var r; o(this, "_invoke", { value: function value(t, n) { function callInvokeWithMethodAndArg() { return new e(function (e, r) { invoke(t, n, e, r); }); } return r = r ? r.then(callInvokeWithMethodAndArg, callInvokeWithMethodAndArg) : callInvokeWithMethodAndArg(); } }); } function makeInvokeMethod(e, r, n) { var o = h; return function (i, a) { if (o === f) throw Error("Generator is already running"); if (o === s) { if ("throw" === i) throw a; return { value: t, done: !0 }; } for (n.method = i, n.arg = a;;) { var c = n.delegate; if (c) { var u = maybeInvokeDelegate(c, n); if (u) { if (u === y) continue; return u; } } if ("next" === n.method) n.sent = n._sent = n.arg;else if ("throw" === n.method) { if (o === h) throw o = s, n.arg; n.dispatchException(n.arg); } else "return" === n.method && n.abrupt("return", n.arg); o = f; var p = tryCatch(e, r, n); if ("normal" === p.type) { if (o = n.done ? s : l, p.arg === y) continue; return { value: p.arg, done: n.done }; } "throw" === p.type && (o = s, n.method = "throw", n.arg = p.arg); } }; } function maybeInvokeDelegate(e, r) { var n = r.method, o = e.iterator[n]; if (o === t) return r.delegate = null, "throw" === n && e.iterator["return"] && (r.method = "return", r.arg = t, maybeInvokeDelegate(e, r), "throw" === r.method) || "return" !== n && (r.method = "throw", r.arg = new TypeError("The iterator does not provide a '" + n + "' method")), y; var i = tryCatch(o, e.iterator, r.arg); if ("throw" === i.type) return r.method = "throw", r.arg = i.arg, r.delegate = null, y; var a = i.arg; return a ? a.done ? (r[e.resultName] = a.value, r.next = e.nextLoc, "return" !== r.method && (r.method = "next", r.arg = t), r.delegate = null, y) : a : (r.method = "throw", r.arg = new TypeError("iterator result is not an object"), r.delegate = null, y); } function pushTryEntry(t) { var e = { tryLoc: t[0] }; 1 in t && (e.catchLoc = t[1]), 2 in t && (e.finallyLoc = t[2], e.afterLoc = t[3]), this.tryEntries.push(e); } function resetTryEntry(t) { var e = t.completion || {}; e.type = "normal", delete e.arg, t.completion = e; } function Context(t) { this.tryEntries = [{ tryLoc: "root" }], t.forEach(pushTryEntry, this), this.reset(!0); } function values(e) { if (e || "" === e) { var r = e[a]; if (r) return r.call(e); if ("function" == typeof e.next) return e; if (!isNaN(e.length)) { var o = -1, i = function next() { for (; ++o < e.length;) if (n.call(e, o)) return next.value = e[o], next.done = !1, next; return next.value = t, next.done = !0, next; }; return i.next = i; } } throw new TypeError(_typeof(e) + " is not iterable"); } return GeneratorFunction.prototype = GeneratorFunctionPrototype, o(g, "constructor", { value: GeneratorFunctionPrototype, configurable: !0 }), o(GeneratorFunctionPrototype, "constructor", { value: GeneratorFunction, configurable: !0 }), GeneratorFunction.displayName = define(GeneratorFunctionPrototype, u, "GeneratorFunction"), e.isGeneratorFunction = function (t) { var e = "function" == typeof t && t.constructor; return !!e && (e === GeneratorFunction || "GeneratorFunction" === (e.displayName || e.name)); }, e.mark = function (t) { return Object.setPrototypeOf ? Object.setPrototypeOf(t, GeneratorFunctionPrototype) : (t.__proto__ = GeneratorFunctionPrototype, define(t, u, "GeneratorFunction")), t.prototype = Object.create(g), t; }, e.awrap = function (t) { return { __await: t }; }, defineIteratorMethods(AsyncIterator.prototype), define(AsyncIterator.prototype, c, function () { return this; }), e.AsyncIterator = AsyncIterator, e.async = function (t, r, n, o, i) { void 0 === i && (i = Promise); var a = new AsyncIterator(wrap(t, r, n, o), i); return e.isGeneratorFunction(r) ? a : a.next().then(function (t) { return t.done ? t.value : a.next(); }); }, defineIteratorMethods(g), define(g, u, "Generator"), define(g, a, function () { return this; }), define(g, "toString", function () { return "[object Generator]"; }), e.keys = function (t) { var e = Object(t), r = []; for (var n in e) r.push(n); return r.reverse(), function next() { for (; r.length;) { var t = r.pop(); if (t in e) return next.value = t, next.done = !1, next; } return next.done = !0, next; }; }, e.values = values, Context.prototype = { constructor: Context, reset: function reset(e) { if (this.prev = 0, this.next = 0, this.sent = this._sent = t, this.done = !1, this.delegate = null, this.method = "next", this.arg = t, this.tryEntries.forEach(resetTryEntry), !e) for (var r in this) "t" === r.charAt(0) && n.call(this, r) && !isNaN(+r.slice(1)) && (this[r] = t); }, stop: function stop() { this.done = !0; var t = this.tryEntries[0].completion; if ("throw" === t.type) throw t.arg; return this.rval; }, dispatchException: function dispatchException(e) { if (this.done) throw e; var r = this; function handle(n, o) { return a.type = "throw", a.arg = e, r.next = n, o && (r.method = "next", r.arg = t), !!o; } for (var o = this.tryEntries.length - 1; o >= 0; --o) { var i = this.tryEntries[o], a = i.completion; if ("root" === i.tryLoc) return handle("end"); if (i.tryLoc <= this.prev) { var c = n.call(i, "catchLoc"), u = n.call(i, "finallyLoc"); if (c && u) { if (this.prev < i.catchLoc) return handle(i.catchLoc, !0); if (this.prev < i.finallyLoc) return handle(i.finallyLoc); } else if (c) { if (this.prev < i.catchLoc) return handle(i.catchLoc, !0); } else { if (!u) throw Error("try statement without catch or finally"); if (this.prev < i.finallyLoc) return handle(i.finallyLoc); } } } }, abrupt: function abrupt(t, e) { for (var r = this.tryEntries.length - 1; r >= 0; --r) { var o = this.tryEntries[r]; if (o.tryLoc <= this.prev && n.call(o, "finallyLoc") && this.prev < o.finallyLoc) { var i = o; break; } } i && ("break" === t || "continue" === t) && i.tryLoc <= e && e <= i.finallyLoc && (i = null); var a = i ? i.completion : {}; return a.type = t, a.arg = e, i ? (this.method = "next", this.next = i.finallyLoc, y) : this.complete(a); }, complete: function complete(t, e) { if ("throw" === t.type) throw t.arg; return "break" === t.type || "continue" === t.type ? this.next = t.arg : "return" === t.type ? (this.rval = this.arg = t.arg, this.method = "return", this.next = "end") : "normal" === t.type && e && (this.next = e), y; }, finish: function finish(t) { for (var e = this.tryEntries.length - 1; e >= 0; --e) { var r = this.tryEntries[e]; if (r.finallyLoc === t) return this.complete(r.completion, r.afterLoc), resetTryEntry(r), y; } }, "catch": function _catch(t) { for (var e = this.tryEntries.length - 1; e >= 0; --e) { var r = this.tryEntries[e]; if (r.tryLoc === t) { var n = r.completion; if ("throw" === n.type) { var o = n.arg; resetTryEntry(r); } return o; } } throw Error("illegal catch attempt"); }, delegateYield: function delegateYield(e, r, n) { return this.delegate = { iterator: values(e), resultName: r, nextLoc: n }, "next" === this.method && (this.arg = t), y; } }, e; }
function asyncGeneratorStep(n, t, e, r, o, a, c) { try { var i = n[a](c), u = i.value; } catch (n) { return void e(n); } i.done ? t(u) : Promise.resolve(u).then(r, o); }
function _asyncToGenerator(n) { return function () { var t = this, e = arguments; return new Promise(function (r, o) { var a = n.apply(t, e); function _next(n) { asyncGeneratorStep(a, r, o, _next, _throw, "next", n); } function _throw(n) { asyncGeneratorStep(a, r, o, _next, _throw, "throw", n); } _next(void 0); }); }; }
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
/**
 * Frontend Vacation Rental Calendar
 * Following Homzen theme JavaScript patterns
 */
var VacationRentalCalendar = /*#__PURE__*/function () {
  function VacationRentalCalendar(container) {
    _classCallCheck(this, VacationRentalCalendar);
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
  return _createClass(VacationRentalCalendar, [{
    key: "init",
    value: function init() {
      this.initializeCalendar();
      this.bindEvents();
      this.loadAvailabilityData();
    }
  }, {
    key: "initializeCalendar",
    value: function initializeCalendar() {
      var _this = this;
      var calendarElement = this.container.querySelector('.flatpickr-calendar-container');
      if (!calendarElement) {
        console.error(window.__ ? window.__('calendar_element_not_found') : 'Calendar element not found');
        return;
      }
      this.calendar = flatpickr(calendarElement, {
        mode: 'range',
        inline: true,
        dateFormat: 'Y-m-d',
        minDate: 'today',
        // showMonths: window.innerWidth > 768 ? 2 : 1,
        showMonths: 1,
        onDayCreate: function onDayCreate(dObj, dStr, fp, dayElem) {
          _this.styleDayElement(dayElem);
        },
        onChange: function onChange(selectedDates) {
          _this.handleDateSelection(selectedDates);
        },
        onReady: function onReady() {
          _this.addCustomStyles();
        }
      });
    }
  }, {
    key: "styleDayElement",
    value: function styleDayElement(dayElem) {
      var date = this.formatDate(new Date(dayElem.dateObj));
      var availability = this.availabilityData[date];

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
  }, {
    key: "addPriceToDay",
    value: function addPriceToDay(dayElem, price) {
      var priceElement = document.createElement('div');
      priceElement.className = 'day-price';
      priceElement.textContent = price;
      priceElement.style.cssText = "\n            position: absolute;\n            bottom: 2px;\n            right: 2px;\n            font-size: 10px;\n            background: rgba(255, 87, 34, 0.9);\n            color: white;\n            padding: 1px 3px;\n            border-radius: 3px;\n            line-height: 1;\n        ";
      dayElem.appendChild(priceElement);
    }
  }, {
    key: "addCustomStyles",
    value: function addCustomStyles() {
      var style = document.createElement('style');
      style.textContent = "\n            .flatpickr-calendar .flatpickr-day.available:hover {\n                background-color: #c3e6cb !important;\n                transform: translateY(-1px);\n            }\n            .flatpickr-calendar .flatpickr-day.unavailable {\n                opacity: 0.5;\n                cursor: not-allowed !important;\n            }\n        ";
      document.head.appendChild(style);
    }
  }, {
    key: "loadAvailabilityData",
    value: function () {
      var _loadAvailabilityData = _asyncToGenerator(/*#__PURE__*/_regeneratorRuntime().mark(function _callee() {
        var startDate, endDate, url, response, data;
        return _regeneratorRuntime().wrap(function _callee$(_context) {
          while (1) switch (_context.prev = _context.next) {
            case 0:
              if (!(!this.availabilityUrl || !this.vacationRentalId)) {
                _context.next = 3;
                break;
              }
              console.error(window.__ ? window.__('missing_availability_url') : 'Missing availability URL or vacation rental ID');
              return _context.abrupt("return");
            case 3:
              _context.prev = 3;
              startDate = new Date();
              endDate = new Date();
              endDate.setFullYear(endDate.getFullYear() + 1);
              url = "".concat(this.availabilityUrl, "?property_id=").concat(this.vacationRentalId, "&start_date=").concat(this.formatDate(startDate), "&end_date=").concat(this.formatDate(endDate), "&exceptions_only=true");
              _context.next = 10;
              return fetch(url, {
                headers: {
                  'Accept': 'application/json',
                  'Content-Type': 'application/json'
                }
              });
            case 10:
              response = _context.sent;
              _context.next = 13;
              return response.json();
            case 13:
              data = _context.sent;
              if (!data.error) {
                _context.next = 16;
                break;
              }
              throw new Error(data.message || (window.__ ? window.__('failed_load_availability') : 'Failed to load availability data'));
            case 16:
              this.availabilityData = data.data || {};
              this.refreshCalendarDisplay();
              _context.next = 24;
              break;
            case 20:
              _context.prev = 20;
              _context.t0 = _context["catch"](3);
              console.error('Error loading availability data:', _context.t0);
              this.showError(window.__ ? window.__('failed_load_calendar') : 'Failed to load calendar data. Please refresh the page.');
            case 24:
            case "end":
              return _context.stop();
          }
        }, _callee, this, [[3, 20]]);
      }));
      function loadAvailabilityData() {
        return _loadAvailabilityData.apply(this, arguments);
      }
      return loadAvailabilityData;
    }()
  }, {
    key: "refreshCalendarDisplay",
    value: function refreshCalendarDisplay() {
      if (this.calendar) {
        this.calendar.redraw();
      }
    }
  }, {
    key: "handleDateSelection",
    value: function handleDateSelection(selectedDates) {
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
        var nights = Math.ceil((this.checkOutDate - this.checkInDate) / (1000 * 60 * 60 * 24));
        if (nights < this.minStay) {
          this.showError(window.__ ? window.__('minimum_stay_error', {min_stay: this.minStay}) : "Minimum stay is ".concat(this.minStay, " night(s)"));
          this.calendar.clear();
          return;
        }
        if (this.maxStay && nights > this.maxStay) {
          this.showError(window.__ ? window.__('maximum_stay_error', {max_stay: this.maxStay}) : "Maximum stay is ".concat(this.maxStay, " night(s)"));
          this.calendar.clear();
          return;
        }

        // Check if all dates in range are available
        if (!this.validateDateRange()) {
          this.showError(window.__ ? window.__('dates_unavailable') : 'Some dates in the selected range are not available');
          this.calendar.clear();
          return;
        }
        this.calculatePricing();
      }
      this.updateBookingSummary();
    }
  }, {
    key: "validateDateRange",
    value: function validateDateRange() {
      if (!this.checkInDate || !this.checkOutDate) return false;
      var current = new Date(this.checkInDate);
      var end = new Date(this.checkOutDate);
      while (current < end) {
        var dateStr = this.formatDate(current);
        var availability = this.availabilityData[dateStr];
        if (availability && availability.status !== 'available') {
          return false;
        }
        current.setDate(current.getDate() + 1);
      }
      return true;
    }
  }, {
    key: "calculatePricing",
    value: function () {
      var _calculatePricing = _asyncToGenerator(/*#__PURE__*/_regeneratorRuntime().mark(function _callee2() {
        var _document$querySelect, response, data;
        return _regeneratorRuntime().wrap(function _callee2$(_context2) {
          while (1) switch (_context2.prev = _context2.next) {
            case 0:
              if (!(!this.checkInDate || !this.checkOutDate || !this.pricingUrl)) {
                _context2.next = 2;
                break;
              }
              return _context2.abrupt("return");
            case 2:
              _context2.prev = 2;
              _context2.next = 5;
              return fetch(this.pricingUrl, {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json',
                  'X-CSRF-TOKEN': ((_document$querySelect = document.querySelector('meta[name="csrf-token"]')) === null || _document$querySelect === void 0 ? void 0 : _document$querySelect.getAttribute('content')) || ''
                },
                body: JSON.stringify({
                  property_id: this.vacationRentalId,
                  check_in: this.formatDate(this.checkInDate),
                  check_out: this.formatDate(this.checkOutDate),
                  guests: this.getGuestCount()
                })
              });
            case 5:
              response = _context2.sent;
              _context2.next = 8;
              return response.json();
            case 8:
              data = _context2.sent;
              if (!data.error) {
                _context2.next = 11;
                break;
              }
              throw new Error(data.message || (window.__ ? window.__('pricing_calculation_error') : 'Failed to calculate pricing'));
            case 11:
              this.currentPricing = data.data;
              this.updateBookingSummary();
              _context2.next = 19;
              break;
            case 15:
              _context2.prev = 15;
              _context2.t0 = _context2["catch"](2);
              console.error('Error calculating pricing:', _context2.t0);
              this.showError(window.__ ? window.__('pricing_calculation_error') : 'Failed to calculate pricing. Please try again.');
            case 19:
            case "end":
              return _context2.stop();
          }
        }, _callee2, this, [[2, 15]]);
      }));
      function calculatePricing() {
        return _calculatePricing.apply(this, arguments);
      }
      return calculatePricing;
    }()
  }, {
    key: "updateBookingSummary",
    value: function updateBookingSummary() {
      var summaryElement = this.container.querySelector('.booking-summary');
      if (!summaryElement) return;
      if (!this.checkInDate) {
        summaryElement.style.display = 'none';
        return;
      }
      summaryElement.style.display = 'block';
      var checkInElement = summaryElement.querySelector('.check-in-date');
      var checkOutElement = summaryElement.querySelector('.check-out-date');
      var nightsElement = summaryElement.querySelector('.nights-count');
      var totalElement = summaryElement.querySelector('.total-price');
      if (checkInElement) {
        checkInElement.textContent = this.formatDisplayDate(this.checkInDate);
      }
      if (this.checkOutDate) {
        if (checkOutElement) {
          checkOutElement.textContent = this.formatDisplayDate(this.checkOutDate);
        }
        var nights = Math.ceil((this.checkOutDate - this.checkInDate) / (1000 * 60 * 60 * 24));
        if (nightsElement) {
          var nightText = nights === 1 ? (window.__ ? window.__('night') : 'night') : (window.__ ? window.__('nights') : 'nights');
          nightsElement.textContent = "".concat(nights, " ").concat(nightText);
        }
      }
      if (this.currentPricing && totalElement) {
        totalElement.textContent = this.currentPricing.total_formatted || this.currentPricing.total;
      }
      this.updateBookingButton();
    }
  }, {
    key: "updateBookingButton",
    value: function updateBookingButton() {
      var bookButton = this.container.querySelector('.btn-book-now');
      if (!bookButton) return;
      var canBook = this.checkInDate && this.checkOutDate && this.currentPricing;
      bookButton.disabled = !canBook;
      if (canBook) {
        bookButton.textContent = window.__ ? window.__('book_now') : 'Book Now';
      } else {
        bookButton.textContent = window.__ ? window.__('select_dates') : 'Select Dates';
      }
    }
  }, {
    key: "bindEvents",
    value: function bindEvents() {
      var _this2 = this;
      // Book now button
      var bookButton = this.container.querySelector('.btn-book-now');
      if (bookButton) {
        bookButton.addEventListener('click', function () {
          return _this2.handleBooking();
        });
      }

      // Guest count input
      var guestInput = this.container.querySelector('.guest-count-input');
      if (guestInput) {
        guestInput.addEventListener('change', function () {
          if (_this2.checkInDate && _this2.checkOutDate) {
            _this2.calculatePricing();
          }
        });
      }
    }
  }, {
    key: "handleBooking",
    value: function handleBooking() {
      if (!this.isLoggedIn) {
        if (this.loginUrl) {
          window.location.href = this.loginUrl;
        } else {
          this.showError(window.__ ? window.__('login_required') : 'Please log in to make a booking');
        }
        return;
      }
      if (!this.checkInDate || !this.checkOutDate) {
        this.showError(window.__ ? window.__('select_dates_required') : 'Please select check-in and check-out dates');
        return;
      }

      // Redirect to booking form with selected dates
      var params = new URLSearchParams({
        check_in: this.formatDate(this.checkInDate),
        check_out: this.formatDate(this.checkOutDate),
        guests: this.getGuestCount()
      });
      window.location.href = "".concat(this.bookingUrl, "?").concat(params.toString());
    }
  }, {
    key: "getGuestCount",
    value: function getGuestCount() {
      var guestInput = this.container.querySelector('.guest-count-input');
      return guestInput ? parseInt(guestInput.value) || 1 : 1;
    }
  }, {
    key: "clearSelection",
    value: function clearSelection() {
      this.checkInDate = null;
      this.checkOutDate = null;
      this.currentPricing = null;
      this.updateBookingSummary();
    }
  }, {
    key: "showError",
    value: function showError(message) {
      // Use theme's notification system or fallback to alert
      if (window.showNotification) {
        window.showNotification('error', message);
      } else {
        alert(message);
      }
    }
  }, {
    key: "formatDate",
    value: function formatDate(date) {
      return date.toISOString().split('T')[0];
    }
  }, {
    key: "formatDisplayDate",
    value: function formatDisplayDate(date) {
      return date.toLocaleDateString('en-US', {
        weekday: 'short',
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      });
    }
  }]);
}(); // Auto-initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function () {
  var containers = document.querySelectorAll('.vacation-rental-booking-calendar');
  containers.forEach(function (container) {
    new VacationRentalCalendar(container);
  });
});
/******/ })()
;
//# sourceMappingURL=vacation-rental-calendar.js.map