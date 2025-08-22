/******/ (() => { // webpackBootstrap
/*!*********************************************************************!*\
  !*** ./platform/plugins/real-estate/resources/js/admin-calendar.js ***!
  \*********************************************************************/
function _toConsumableArray(r) { return _arrayWithoutHoles(r) || _iterableToArray(r) || _unsupportedIterableToArray(r) || _nonIterableSpread(); }
function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _iterableToArray(r) { if ("undefined" != typeof Symbol && null != r[Symbol.iterator] || null != r["@@iterator"]) return Array.from(r); }
function _arrayWithoutHoles(r) { if (Array.isArray(r)) return _arrayLikeToArray(r); }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
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
 * Vacation Rental Admin Calendar Management
 * Dedicated file for admin-only calendar operations
 * Handles: blocking dates, maintenance scheduling, availability management
 */
var VacationRentalAdminCalendar = /*#__PURE__*/function () {
  function VacationRentalAdminCalendar(container) {
    _classCallCheck(this, VacationRentalAdminCalendar);
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
  return _createClass(VacationRentalAdminCalendar, [{
    key: "init",
    value: function init() {
      var _this = this;
      console.log('Initializing admin calendar...');
      this.initializeCalendar();
      this.bindEvents();
      // Delay availability loading to ensure page is fully loaded
      setTimeout(function () {
        _this.loadAvailabilityData();
      }, 500);
    }
  }, {
    key: "initializeCalendar",
    value: function initializeCalendar() {
      var _this2 = this;
      if (typeof flatpickr === 'undefined') {
        console.error('Flatpickr library not loaded');
        return;
      }
      var calendarElement = document.getElementById('admin-calendar-flatpickr') || this.container;
      this.calendar = flatpickr(calendarElement, {
        mode: 'multiple',
        inline: true,
        dateFormat: 'Y-m-d',
        minDate: 'today',
        showMonths: 2,
        onDayCreate: function onDayCreate(dObj, dStr, fp, dayElem) {
          var date = dayElem.dateObj.toISOString().split('T')[0];
          var availability = _this2.availabilityData[date];

          // Remove existing status classes
          dayElem.classList.remove('available', 'booked', 'blocked', 'maintenance');
          if (availability && availability.status) {
            dayElem.classList.add(availability.status);
            dayElem.title = "".concat(availability.status.charAt(0).toUpperCase() + availability.status.slice(1));
            if (availability.reason || availability.notes) {
              dayElem.title += " - ".concat(availability.reason || availability.notes);
            }
          } else {
            dayElem.classList.add('available');
            dayElem.title = 'Available';
          }
        },
        onChange: function onChange(selectedDates) {
          _this2.selectedDates = selectedDates.map(function (date) {
            return date.toISOString().split('T')[0];
          });
          console.log('Selected dates:', _this2.selectedDates);
          _this2.updateActionButtons();
        }
      });
      console.log('Admin calendar initialized');
    }
  }, {
    key: "bindEvents",
    value: function bindEvents() {
      var _this3 = this;
      // Block dates button
      var blockBtn = document.getElementById('admin-block-dates');
      if (blockBtn) {
        blockBtn.addEventListener('click', function () {
          return _this3.handleBlockDates();
        });
      }

      // Unblock dates button
      var unblockBtn = document.getElementById('admin-unblock-dates');
      if (unblockBtn) {
        unblockBtn.addEventListener('click', function () {
          return _this3.handleUnblockDates();
        });
      }

      // Maintenance dates button
      var maintenanceBtn = document.getElementById('admin-maintenance-dates');
      if (maintenanceBtn) {
        maintenanceBtn.addEventListener('click', function () {
          return _this3.handleMaintenanceDates();
        });
      }

      // Apply with reason button
      var applyBtn = document.getElementById('admin-apply-reason');
      if (applyBtn) {
        applyBtn.addEventListener('click', function () {
          return _this3.applyWithReason();
        });
      }

      // Cancel reason button
      var cancelBtn = document.getElementById('admin-cancel-reason');
      if (cancelBtn) {
        cancelBtn.addEventListener('click', function () {
          return _this3.cancelReason();
        });
      }
    }
  }, {
    key: "loadAvailabilityData",
    value: function () {
      var _loadAvailabilityData = _asyncToGenerator(/*#__PURE__*/_regeneratorRuntime().mark(function _callee2() {
        var _document$querySelect;
        var startDate, endDate, url, response, data;
        return _regeneratorRuntime().wrap(function _callee2$(_context2) {
          while (1) switch (_context2.prev = _context2.next) {
            case 0:
              console.log('=== LOADING ADMIN AVAILABILITY DATA ===');
              if (!(!this.availabilityUrl || !this.vacationRentalId)) {
                _context2.next = 4;
                break;
              }
              console.error('Missing required data for loading availability:', {
                availabilityUrl: this.availabilityUrl,
                vacationRentalId: this.vacationRentalId
              });
              return _context2.abrupt("return");
            case 4:
              // Debug authentication context
              console.log('🔐 Authentication context:', {
                csrfToken: ((_document$querySelect = document.querySelector('meta[name="csrf-token"]')) === null || _document$querySelect === void 0 || (_document$querySelect = _document$querySelect.getAttribute('content')) === null || _document$querySelect === void 0 ? void 0 : _document$querySelect.substring(0, 10)) + '...',
                hasCsrfToken: !!document.querySelector('meta[name="csrf-token"]'),
                cookies: document.cookie.split(';').map(function (c) {
                  return c.trim().split('=')[0];
                }),
                location: window.location.href
              });
              _context2.prev = 5;
              startDate = new Date();
              endDate = new Date();
              endDate.setFullYear(endDate.getFullYear() + 1);
              url = "".concat(this.availabilityUrl, "?property_id=").concat(this.vacationRentalId, "&start_date=").concat(this.formatDate(startDate), "&end_date=").concat(this.formatDate(endDate));
              console.log('📡 API request:', url);

              // Try using XMLHttpRequest instead of fetch for better cookie handling
              _context2.next = 13;
              return new Promise(function (resolve, reject) {
                var _document$querySelect2;
                var xhr = new XMLHttpRequest();
                xhr.open('GET', url, true);
                xhr.withCredentials = true; // Include cookies
                xhr.setRequestHeader('Accept', 'application/json');
                xhr.setRequestHeader('Content-Type', 'application/json');
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                var csrfToken = (_document$querySelect2 = document.querySelector('meta[name="csrf-token"]')) === null || _document$querySelect2 === void 0 ? void 0 : _document$querySelect2.getAttribute('content');
                if (csrfToken) {
                  xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                }
                xhr.onload = function () {
                  var mockResponse = {
                    ok: xhr.status >= 200 && xhr.status < 300,
                    status: xhr.status,
                    statusText: xhr.statusText,
                    json: function () {
                      var _json = _asyncToGenerator(/*#__PURE__*/_regeneratorRuntime().mark(function _callee() {
                        return _regeneratorRuntime().wrap(function _callee$(_context) {
                          while (1) switch (_context.prev = _context.next) {
                            case 0:
                              return _context.abrupt("return", JSON.parse(xhr.responseText));
                            case 1:
                            case "end":
                              return _context.stop();
                          }
                        }, _callee);
                      }));
                      function json() {
                        return _json.apply(this, arguments);
                      }
                      return json;
                    }()
                  };
                  resolve(mockResponse);
                };
                xhr.onerror = function () {
                  reject(new Error('Network error'));
                };
                xhr.send();
              });
            case 13:
              response = _context2.sent;
              console.log('📡 Response:', response.status, response.statusText);
              if (response.ok) {
                _context2.next = 17;
                break;
              }
              throw new Error("HTTP ".concat(response.status, ": ").concat(response.statusText));
            case 17:
              _context2.next = 19;
              return response.json();
            case 19:
              data = _context2.sent;
              console.log('📡 Response data:', data);
              if (!data.error) {
                _context2.next = 23;
                break;
              }
              throw new Error(data.message || 'Failed to load availability data');
            case 23:
              this.availabilityData = data.data || {};
              console.log('✓ Loaded availability data:', Object.keys(this.availabilityData).length, 'records');
              this.refreshCalendarDisplay();
              _context2.next = 32;
              break;
            case 28:
              _context2.prev = 28;
              _context2.t0 = _context2["catch"](5);
              console.error('✗ Error loading availability data:', _context2.t0);
              this.showError('Failed to load calendar data. Please refresh the page.');
            case 32:
            case "end":
              return _context2.stop();
          }
        }, _callee2, this, [[5, 28]]);
      }));
      function loadAvailabilityData() {
        return _loadAvailabilityData.apply(this, arguments);
      }
      return loadAvailabilityData;
    }()
  }, {
    key: "handleBlockDates",
    value: function () {
      var _handleBlockDates = _asyncToGenerator(/*#__PURE__*/_regeneratorRuntime().mark(function _callee3() {
        return _regeneratorRuntime().wrap(function _callee3$(_context3) {
          while (1) switch (_context3.prev = _context3.next) {
            case 0:
              console.log('=== BLOCKING DATES ===');
              if (!(this.selectedDates.length === 0)) {
                _context3.next = 4;
                break;
              }
              this.showError('Please select dates to block');
              return _context3.abrupt("return");
            case 4:
              this.currentAction = 'block';
              this.showReasonInput();
            case 6:
            case "end":
              return _context3.stop();
          }
        }, _callee3, this);
      }));
      function handleBlockDates() {
        return _handleBlockDates.apply(this, arguments);
      }
      return handleBlockDates;
    }()
  }, {
    key: "handleUnblockDates",
    value: function () {
      var _handleUnblockDates = _asyncToGenerator(/*#__PURE__*/_regeneratorRuntime().mark(function _callee4() {
        return _regeneratorRuntime().wrap(function _callee4$(_context4) {
          while (1) switch (_context4.prev = _context4.next) {
            case 0:
              if (!(this.selectedDates.length === 0)) {
                _context4.next = 3;
                break;
              }
              this.showError('Please select dates to unblock');
              return _context4.abrupt("return");
            case 3:
              _context4.next = 5;
              return this.performAction(this.unblockUrl, {
                dates: this.selectedDates
              });
            case 5:
            case "end":
              return _context4.stop();
          }
        }, _callee4, this);
      }));
      function handleUnblockDates() {
        return _handleUnblockDates.apply(this, arguments);
      }
      return handleUnblockDates;
    }()
  }, {
    key: "handleMaintenanceDates",
    value: function () {
      var _handleMaintenanceDates = _asyncToGenerator(/*#__PURE__*/_regeneratorRuntime().mark(function _callee5() {
        return _regeneratorRuntime().wrap(function _callee5$(_context5) {
          while (1) switch (_context5.prev = _context5.next) {
            case 0:
              if (!(this.selectedDates.length === 0)) {
                _context5.next = 3;
                break;
              }
              this.showError('Please select dates for maintenance');
              return _context5.abrupt("return");
            case 3:
              this.currentAction = 'maintenance';
              this.showReasonInput();
            case 5:
            case "end":
              return _context5.stop();
          }
        }, _callee5, this);
      }));
      function handleMaintenanceDates() {
        return _handleMaintenanceDates.apply(this, arguments);
      }
      return handleMaintenanceDates;
    }()
  }, {
    key: "showReasonInput",
    value: function showReasonInput() {
      var reasonContainer = document.getElementById('admin-reason-container');
      if (reasonContainer) {
        reasonContainer.style.display = 'block';
        var textarea = reasonContainer.querySelector('textarea');
        if (textarea) {
          textarea.focus();
        }
      }
    }
  }, {
    key: "cancelReason",
    value: function cancelReason() {
      var reasonContainer = document.getElementById('admin-reason-container');
      if (reasonContainer) {
        reasonContainer.style.display = 'none';
        var textarea = reasonContainer.querySelector('textarea');
        if (textarea) {
          textarea.value = '';
        }
      }
      this.currentAction = null;
    }
  }, {
    key: "applyWithReason",
    value: function () {
      var _applyWithReason = _asyncToGenerator(/*#__PURE__*/_regeneratorRuntime().mark(function _callee6() {
        var reason;
        return _regeneratorRuntime().wrap(function _callee6$(_context6) {
          while (1) switch (_context6.prev = _context6.next) {
            case 0:
              reason = this.getReasonInput();
              if (!(this.currentAction === 'block')) {
                _context6.next = 6;
                break;
              }
              _context6.next = 4;
              return this.performAction(this.blockUrl, {
                dates: this.selectedDates,
                reason: reason
              });
            case 4:
              _context6.next = 9;
              break;
            case 6:
              if (!(this.currentAction === 'maintenance')) {
                _context6.next = 9;
                break;
              }
              _context6.next = 9;
              return this.performAction(this.maintenanceUrl, {
                dates: this.selectedDates,
                reason: reason
              });
            case 9:
              this.cancelReason();
            case 10:
            case "end":
              return _context6.stop();
          }
        }, _callee6, this);
      }));
      function applyWithReason() {
        return _applyWithReason.apply(this, arguments);
      }
      return applyWithReason;
    }()
  }, {
    key: "performAction",
    value: function () {
      var _performAction = _asyncToGenerator(/*#__PURE__*/_regeneratorRuntime().mark(function _callee7(url, data) {
        var csrfToken, response, errorText, result;
        return _regeneratorRuntime().wrap(function _callee7$(_context7) {
          while (1) switch (_context7.prev = _context7.next) {
            case 0:
              console.log('=== PERFORMING ADMIN ACTION ===');
              console.log('🎯 URL:', url);
              console.log('🎯 Data:', data);
              _context7.prev = 3;
              data.property_id = this.vacationRentalId;

              // Get CSRF token
              csrfToken = document.querySelector('meta[name="csrf-token"]');
              if (csrfToken) {
                _context7.next = 8;
                break;
              }
              throw new Error('CSRF token not found. Please refresh the page.');
            case 8:
              _context7.next = 10;
              return fetch(url, {
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
            case 10:
              response = _context7.sent;
              console.log('📡 Action response:', response.status, response.statusText);
              if (response.ok) {
                _context7.next = 18;
                break;
              }
              _context7.next = 15;
              return response.text();
            case 15:
              errorText = _context7.sent;
              console.error('✗ Action failed:', errorText);
              throw new Error("HTTP error! status: ".concat(response.status, " - ").concat(errorText));
            case 18:
              _context7.next = 20;
              return response.json();
            case 20:
              result = _context7.sent;
              console.log('✓ Action result:', result);
              if (!result.error) {
                _context7.next = 24;
                break;
              }
              throw new Error(result.message || 'Action failed');
            case 24:
              this.showSuccess(result.message || 'Action completed successfully');
              this.clearSelection();
              _context7.next = 28;
              return this.loadAvailabilityData();
            case 28:
              _context7.next = 34;
              break;
            case 30:
              _context7.prev = 30;
              _context7.t0 = _context7["catch"](3);
              console.error('✗ Error performing action:', _context7.t0);
              this.showError(_context7.t0.message || 'Action failed. Please try again.');
            case 34:
            case "end":
              return _context7.stop();
          }
        }, _callee7, this, [[3, 30]]);
      }));
      function performAction(_x, _x2) {
        return _performAction.apply(this, arguments);
      }
      return performAction;
    }()
  }, {
    key: "getReasonInput",
    value: function getReasonInput() {
      var textarea = document.querySelector('#admin-reason');
      return textarea ? textarea.value.trim() : '';
    }
  }, {
    key: "updateActionButtons",
    value: function updateActionButtons() {
      var hasSelection = this.selectedDates.length > 0;
      var blockBtn = document.getElementById('admin-block-dates');
      var unblockBtn = document.getElementById('admin-unblock-dates');
      var maintenanceBtn = document.getElementById('admin-maintenance-dates');
      if (blockBtn) blockBtn.disabled = !hasSelection;
      if (unblockBtn) unblockBtn.disabled = !hasSelection;
      if (maintenanceBtn) maintenanceBtn.disabled = !hasSelection;
    }
  }, {
    key: "clearSelection",
    value: function clearSelection() {
      if (this.calendar) {
        this.calendar.clear();
      }
      this.selectedDates = [];
      this.updateActionButtons();
    }
  }, {
    key: "refreshCalendarDisplay",
    value: function refreshCalendarDisplay() {
      if (this.calendar) {
        this.calendar.redraw();
      }
    }
  }, {
    key: "formatDate",
    value: function formatDate(date) {
      return date.toISOString().split('T')[0];
    }
  }, {
    key: "showSuccess",
    value: function showSuccess(message) {
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
  }, {
    key: "showError",
    value: function showError(message) {
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
  }, {
    key: "showCustomNotification",
    value: function showCustomNotification(message) {
      var type = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'info';
      // Create a custom notification element
      var notification = document.createElement('div');
      notification.className = "admin-notification admin-notification-".concat(type);
      notification.innerHTML = "\n            <div class=\"notification-content\">\n                <i class=\"fas ".concat(type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle', "\"></i>\n                <span class=\"notification-message\">").concat(message, "</span>\n                <button type=\"button\" class=\"notification-close\" onclick=\"this.parentElement.parentElement.remove()\">\n                    <i class=\"fas fa-times\"></i>\n                </button>\n            </div>\n        ");

      // Apply styles
      notification.style.cssText = "\n            position: fixed;\n            top: 20px;\n            right: 20px;\n            z-index: 9999;\n            min-width: 300px;\n            max-width: 500px;\n            padding: 15px;\n            border-radius: 8px;\n            box-shadow: 0 4px 12px rgba(0,0,0,0.15);\n            color: white;\n            font-size: 14px;\n            background: ".concat(type === 'success' ? '#28a745' : '#dc3545', ";\n            animation: slideInFromRight 0.3s ease-out;\n        ");

      // Add CSS animation if not exists
      if (!document.querySelector('#admin-notification-styles')) {
        var styles = document.createElement('style');
        styles.id = 'admin-notification-styles';
        styles.textContent = "\n                @keyframes slideInFromRight {\n                    from { transform: translateX(100%); opacity: 0; }\n                    to { transform: translateX(0); opacity: 1; }\n                }\n                .notification-content {\n                    display: flex;\n                    align-items: center;\n                    gap: 10px;\n                }\n                .notification-close {\n                    background: none;\n                    border: none;\n                    color: white;\n                    cursor: pointer;\n                    padding: 0;\n                    margin-left: auto;\n                }\n                .notification-close:hover {\n                    opacity: 0.7;\n                }\n            ";
        document.head.appendChild(styles);
      }
      document.body.appendChild(notification);

      // Auto-remove after 5 seconds
      setTimeout(function () {
        if (notification.parentNode) {
          notification.style.animation = 'slideInFromRight 0.3s ease-out reverse';
          setTimeout(function () {
            return notification.remove();
          }, 300);
        }
      }, 5000);
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
}(); // General Admin Functionality for Vacation Rental Pages
var VacationRentalAdminHelper = /*#__PURE__*/function () {
  function VacationRentalAdminHelper() {
    _classCallCheck(this, VacationRentalAdminHelper);
  }
  return _createClass(VacationRentalAdminHelper, null, [{
    key: "init",
    value: function init() {
      this.bindPropertySelection();
      this.bindRefreshButtons();
      this.bindDeleteConfirmations();
    }
  }, {
    key: "bindPropertySelection",
    value: function bindPropertySelection() {
      // Property selection for availability and calendar pages
      var propertySelect = document.getElementById('property-select');
      if (propertySelect) {
        propertySelect.addEventListener('change', function (e) {
          var propertyId = e.target.value;
          if (propertyId) {
            var url = new URL(window.location);
            url.searchParams.set('property_id', propertyId);
            window.location.href = url.toString();
          }
        });
      }
    }
  }, {
    key: "bindRefreshButtons",
    value: function bindRefreshButtons() {
      // Refresh button functionality
      var refreshButtons = document.querySelectorAll('.btn-refresh');
      refreshButtons.forEach(function (button) {
        button.addEventListener('click', function (e) {
          e.preventDefault();
          window.location.reload();
        });
      });
    }
  }, {
    key: "bindDeleteConfirmations",
    value: function bindDeleteConfirmations() {
      // Delete confirmation dialogs
      var deleteButtons = document.querySelectorAll('.btn-delete');
      deleteButtons.forEach(function (button) {
        button.addEventListener('click', function (e) {
          if (!confirm('Are you sure you want to delete this item?')) {
            e.preventDefault();
            return false;
          }
        });
      });
    }
  }]);
}(); // Property Form Calendar Integration
var PropertyFormCalendar = /*#__PURE__*/function () {
  function PropertyFormCalendar(container) {
    _classCallCheck(this, PropertyFormCalendar);
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
  return _createClass(PropertyFormCalendar, [{
    key: "init",
    value: function init() {
      this.loadExistingData();
      this.initializeCalendar();
      this.bindEvents();
    }
  }, {
    key: "loadExistingData",
    value: function loadExistingData() {
      // Load existing availability data from global variable
      var existingData = window.propertyAvailabilityData || {};
      if (existingData.availability_by_date) {
        this.availabilityData = existingData.availability_by_date;
      }
    }
  }, {
    key: "initializeCalendar",
    value: function initializeCalendar() {
      var _this4 = this;
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
        onDayCreate: function onDayCreate(dObj, dStr, fp, dayElem) {
          var date = dayElem.dateObj.toISOString().split('T')[0];
          var availability = _this4.availabilityData[date];
          dayElem.classList.remove('available', 'booked', 'blocked', 'maintenance');
          if (availability && availability.status) {
            dayElem.classList.add(availability.status);
            dayElem.title = availability.status.charAt(0).toUpperCase() + availability.status.slice(1);
          } else {
            dayElem.classList.add('available');
            dayElem.title = 'Available';
          }
        },
        onChange: function onChange(selectedDates) {
          console.log('=== FLATPICKR ONCHANGE TRIGGERED ===');
          console.log('Raw selectedDates from Flatpickr:', selectedDates);
          _this4.selectedDates = selectedDates.map(function (date) {
            return date.toISOString().split('T')[0];
          });
          console.log('Processed selectedDates:', _this4.selectedDates);
          console.log('Selected dates count:', _this4.selectedDates.length);

          // Update button states directly
          console.log('Calling updateActionButtons...');
          _this4.updateActionButtons();

          // Emit custom event for button state updates
          document.dispatchEvent(new CustomEvent('calendarSelectionChanged', {
            detail: {
              selectedDates: _this4.selectedDates
            }
          }));
          console.log('Custom event dispatched');
        },
        onReady: function onReady() {
          console.log('Flatpickr calendar is ready');
        }
      });
      console.log('Flatpickr initialized:', !!this.calendar);
    }
  }, {
    key: "bindEvents",
    value: function bindEvents() {
      var _this5 = this;
      console.log('Binding PropertyFormCalendar events...');

      // Block dates button (form template uses different IDs)
      var blockBtn = document.getElementById('block-selected-dates');
      if (blockBtn) {
        blockBtn.addEventListener('click', function () {
          console.log('Block button clicked');
          _this5.blockSelectedDates();
        });
        console.log('Block button event bound');
      } else {
        console.warn('Block button not found: #block-selected-dates');
      }

      // Unblock dates button
      var unblockBtn = document.getElementById('unblock-selected-dates');
      if (unblockBtn) {
        unblockBtn.addEventListener('click', function () {
          console.log('Unblock button clicked');
          _this5.unblockSelectedDates();
        });
        console.log('Unblock button event bound');
      } else {
        console.warn('Unblock button not found: #unblock-selected-dates');
      }

      // Maintenance dates button
      var maintenanceBtn = document.getElementById('set-maintenance-dates');
      if (maintenanceBtn) {
        maintenanceBtn.addEventListener('click', function () {
          console.log('Maintenance button clicked');
          _this5.setMaintenanceDates();
        });
        console.log('Maintenance button event bound');
      } else {
        console.warn('Maintenance button not found: #set-maintenance-dates');
      }

      // Apply reason button
      var applyBtn = document.getElementById('apply-reason');
      if (applyBtn) {
        applyBtn.addEventListener('click', function () {
          console.log('Apply reason button clicked');
          _this5.applyReasonToSelectedDates();
        });
      }

      // Cancel reason button
      var cancelBtn = document.getElementById('cancel-reason');
      if (cancelBtn) {
        cancelBtn.addEventListener('click', function () {
          console.log('Cancel reason button clicked');
          _this5.cancelReason();
        });
      }
    }
  }, {
    key: "blockSelectedDates",
    value: function blockSelectedDates() {
      if (!this.selectedDates.length) {
        console.warn('No dates selected for blocking');
        return;
      }
      console.log('Blocking selected dates:', this.selectedDates);
      this.currentAction = 'block'; // Set current action

      // Show reason input container
      var reasonContainer = document.getElementById('block-reason-container');
      if (reasonContainer) {
        reasonContainer.style.display = 'block';
        var textarea = document.getElementById('block-reason');
        if (textarea) {
          textarea.placeholder = 'Enter reason for blocking dates...';
          textarea.focus();
        }
      } else {
        var _this$pendingChanges$;
        // Fallback to prompt if container not found
        var reason = prompt('Enter reason for blocking these dates:') || 'Blocked by admin';
        (_this$pendingChanges$ = this.pendingChanges.blocked_dates).push.apply(_this$pendingChanges$, _toConsumableArray(this.selectedDates.map(function (date) {
          return {
            date: date,
            reason: reason
          };
        })));
        this.updateFormInputs();
        this.refreshCalendarDisplay();
        this.clearSelection();
      }
    }
  }, {
    key: "unblockSelectedDates",
    value: function unblockSelectedDates() {
      var _this$pendingChanges$2;
      if (!this.selectedDates.length) {
        console.warn('No dates selected for unblocking');
        return;
      }
      console.log('Unblocking selected dates:', this.selectedDates);
      (_this$pendingChanges$2 = this.pendingChanges.unblocked_dates).push.apply(_this$pendingChanges$2, _toConsumableArray(this.selectedDates));
      this.updateFormInputs();
      this.refreshCalendarDisplay();
      this.clearSelection();
    }
  }, {
    key: "setMaintenanceDates",
    value: function setMaintenanceDates() {
      if (!this.selectedDates.length) {
        console.warn('No dates selected for maintenance');
        return;
      }
      console.log('Setting maintenance for selected dates:', this.selectedDates);
      this.currentAction = 'maintenance'; // Set current action

      // Show reason input container
      var reasonContainer = document.getElementById('block-reason-container');
      if (reasonContainer) {
        reasonContainer.style.display = 'block';
        var textarea = document.getElementById('block-reason');
        if (textarea) {
          textarea.placeholder = 'Enter reason for maintenance...';
          textarea.focus();
        }
      } else {
        var _this$pendingChanges$3;
        // Fallback to prompt if container not found
        var reason = prompt('Enter reason for maintenance:') || 'Maintenance';
        (_this$pendingChanges$3 = this.pendingChanges.maintenance_dates).push.apply(_this$pendingChanges$3, _toConsumableArray(this.selectedDates.map(function (date) {
          return {
            date: date,
            reason: reason
          };
        })));
        this.updateFormInputs();
        this.refreshCalendarDisplay();
        this.clearSelection();
      }
    }
  }, {
    key: "updateFormInputs",
    value: function updateFormInputs() {
      console.log('=== UPDATING FORM INPUTS ===');
      console.log('Pending changes:', this.pendingChanges);

      // Update hidden form inputs with pending changes
      this.updateHiddenInput('blocked_dates', this.pendingChanges.blocked_dates);
      this.updateHiddenInput('maintenance_dates', this.pendingChanges.maintenance_dates);
      this.updateHiddenInput('unblocked_dates', this.pendingChanges.unblocked_dates);
      console.log('Form inputs updated');
    }
  }, {
    key: "updateHiddenInput",
    value: function updateHiddenInput(name, data) {
      var input = document.querySelector("input[name=\"".concat(name, "\"]"));
      if (!input) {
        // Try to find existing input with ID-based selector for form fields
        var idMap = {
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
          var form = this.findPropertyForm();
          if (form) form.appendChild(input);
        }
      }
      input.value = JSON.stringify(data);
      console.log("Updated hidden input ".concat(name, ":"), data);
    }
  }, {
    key: "findPropertyForm",
    value: function findPropertyForm() {
      // Try multiple selectors to find the form
      return document.querySelector('form[action*="vacation-rentals"]') || document.querySelector('form[action*="properties"]') || document.querySelector('form.main-form-body') || document.querySelector('form');
    }
  }, {
    key: "setupFormIntegration",
    value: function setupFormIntegration() {
      var _this6 = this;
      var form = this.findPropertyForm();
      if (form) {
        form.addEventListener('submit', function () {
          _this6.updateFormInputs();
        });
      }
    }
  }, {
    key: "updateActionButtons",
    value: function updateActionButtons() {
      var hasSelection = this.selectedDates.length > 0;
      console.log('PropertyFormCalendar updating action buttons, hasSelection:', hasSelection);
      var blockBtn = document.getElementById('block-selected-dates');
      var unblockBtn = document.getElementById('unblock-selected-dates');
      var maintenanceBtn = document.getElementById('set-maintenance-dates');
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
  }, {
    key: "refreshCalendarDisplay",
    value: function refreshCalendarDisplay() {
      if (this.calendar) {
        this.calendar.redraw();
      }
    }
  }, {
    key: "clearSelection",
    value: function clearSelection() {
      if (this.calendar) {
        this.calendar.clear();
      }
      this.selectedDates = [];
      this.updateActionButtons();

      // Emit event to update button states
      document.dispatchEvent(new CustomEvent('calendarSelectionChanged', {
        detail: {
          selectedDates: this.selectedDates
        }
      }));
    }
  }, {
    key: "cancelReason",
    value: function cancelReason() {
      var reasonContainer = document.getElementById('block-reason-container');
      var textarea = document.getElementById('block-reason');
      if (reasonContainer) {
        reasonContainer.style.display = 'none';
      }
      if (textarea) {
        textarea.value = '';
        textarea.placeholder = 'Enter reason for blocking dates...';
      }
    }
  }, {
    key: "applyReasonToSelectedDates",
    value: function applyReasonToSelectedDates() {
      var textarea = document.getElementById('block-reason');
      var reason = textarea ? textarea.value.trim() || 'No reason provided' : 'No reason provided';
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
        var _this$pendingChanges$4;
        (_this$pendingChanges$4 = this.pendingChanges.blocked_dates).push.apply(_this$pendingChanges$4, _toConsumableArray(this.selectedDates.map(function (date) {
          return {
            date: date,
            reason: reason
          };
        })));
        console.log('Added to blocked_dates:', this.pendingChanges.blocked_dates);
      } else if (this.currentAction === 'maintenance') {
        var _this$pendingChanges$5;
        (_this$pendingChanges$5 = this.pendingChanges.maintenance_dates).push.apply(_this$pendingChanges$5, _toConsumableArray(this.selectedDates.map(function (date) {
          return {
            date: date,
            reason: reason
          };
        })));
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
      var reasonContainer = document.getElementById('block-reason-container');
      if (reasonContainer) {
        reasonContainer.style.display = 'none';
      }
      if (textarea) {
        textarea.value = '';
        textarea.placeholder = 'Enter reason for blocking dates...';
      }
      console.log('Reason applied successfully');
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
}(); // Auto-initialize admin calendars and general functionality
document.addEventListener('DOMContentLoaded', function () {
  // Initialize admin calendars
  var adminCalendars = document.querySelectorAll('.vacation-rental-admin-calendar');
  adminCalendars.forEach(function (container) {
    new VacationRentalAdminCalendar(container);
  });

  // Initialize property form calendar ONLY if it doesn't have the admin class
  var propertyFormCalendar = document.getElementById('property-availability-calendar');
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
window.reloadPropertyAvailabilityCalendar = function () {
  if (window.propertyAvailabilityCalendar && window.propertyAvailabilityCalendar.loadExistingData) {
    window.propertyAvailabilityCalendar.loadExistingData();
    window.propertyAvailabilityCalendar.refreshCalendarDisplay();
  }
};
/******/ })()
;
//# sourceMappingURL=admin-calendar.js.map