/*
 * ATTENTION: An "eval-source-map" devtool has been used.
 * This devtool is neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file with attached SourceMaps in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./platform/core/base/resources/js/cache.js":
/*!**************************************************!*\
  !*** ./platform/core/base/resources/js/cache.js ***!
  \**************************************************/
/***/ (() => {

eval("function _typeof(o) { \"@babel/helpers - typeof\"; return _typeof = \"function\" == typeof Symbol && \"symbol\" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && \"function\" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? \"symbol\" : typeof o; }, _typeof(o); }\nfunction _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError(\"Cannot call a class as a function\"); }\nfunction _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, \"value\" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }\nfunction _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, \"prototype\", { writable: !1 }), e; }\nfunction _toPropertyKey(t) { var i = _toPrimitive(t, \"string\"); return \"symbol\" == _typeof(i) ? i : i + \"\"; }\nfunction _toPrimitive(t, r) { if (\"object\" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || \"default\"); if (\"object\" != _typeof(i)) return i; throw new TypeError(\"@@toPrimitive must return a primitive value.\"); } return (\"string\" === r ? String : Number)(t); }\nvar CacheManagement = /*#__PURE__*/function () {\n  function CacheManagement() {\n    _classCallCheck(this, CacheManagement);\n  }\n  return _createClass(CacheManagement, [{\n    key: \"init\",\n    value: function init() {\n      $(document).on('click', '.btn-clear-cache', function (event) {\n        event.preventDefault();\n        var _self = $(event.currentTarget);\n        Botble.showButtonLoading(_self);\n        $httpClient.make().post(_self.data('url'), {\n          type: _self.data('type')\n        }).then(function (_ref) {\n          var data = _ref.data;\n          return Botble.showSuccess(data.message);\n        })[\"finally\"](function () {\n          return Botble.hideButtonLoading(_self);\n        });\n      });\n    }\n  }]);\n}();\n$(function () {\n  new CacheManagement().init();\n});//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJuYW1lcyI6WyJDYWNoZU1hbmFnZW1lbnQiLCJfY2xhc3NDYWxsQ2hlY2siLCJfY3JlYXRlQ2xhc3MiLCJrZXkiLCJ2YWx1ZSIsImluaXQiLCIkIiwiZG9jdW1lbnQiLCJvbiIsImV2ZW50IiwicHJldmVudERlZmF1bHQiLCJfc2VsZiIsImN1cnJlbnRUYXJnZXQiLCJCb3RibGUiLCJzaG93QnV0dG9uTG9hZGluZyIsIiRodHRwQ2xpZW50IiwibWFrZSIsInBvc3QiLCJkYXRhIiwidHlwZSIsInRoZW4iLCJfcmVmIiwic2hvd1N1Y2Nlc3MiLCJtZXNzYWdlIiwiaGlkZUJ1dHRvbkxvYWRpbmciXSwic291cmNlcyI6WyJ3ZWJwYWNrOi8vLy4vcGxhdGZvcm0vY29yZS9iYXNlL3Jlc291cmNlcy9qcy9jYWNoZS5qcz80OWQ0Il0sInNvdXJjZXNDb250ZW50IjpbImNsYXNzIENhY2hlTWFuYWdlbWVudCB7XG4gICAgaW5pdCgpIHtcbiAgICAgICAgJChkb2N1bWVudCkub24oJ2NsaWNrJywgJy5idG4tY2xlYXItY2FjaGUnLCAoZXZlbnQpID0+IHtcbiAgICAgICAgICAgIGV2ZW50LnByZXZlbnREZWZhdWx0KClcblxuICAgICAgICAgICAgbGV0IF9zZWxmID0gJChldmVudC5jdXJyZW50VGFyZ2V0KVxuXG4gICAgICAgICAgICBCb3RibGUuc2hvd0J1dHRvbkxvYWRpbmcoX3NlbGYpXG5cbiAgICAgICAgICAgICRodHRwQ2xpZW50XG4gICAgICAgICAgICAgICAgLm1ha2UoKVxuICAgICAgICAgICAgICAgIC5wb3N0KF9zZWxmLmRhdGEoJ3VybCcpLCB7IHR5cGU6IF9zZWxmLmRhdGEoJ3R5cGUnKSB9KVxuICAgICAgICAgICAgICAgIC50aGVuKCh7IGRhdGEgfSkgPT4gQm90YmxlLnNob3dTdWNjZXNzKGRhdGEubWVzc2FnZSkpXG4gICAgICAgICAgICAgICAgLmZpbmFsbHkoKCkgPT4gQm90YmxlLmhpZGVCdXR0b25Mb2FkaW5nKF9zZWxmKSlcbiAgICAgICAgfSlcbiAgICB9XG59XG5cbiQoKCkgPT4ge1xuICAgIG5ldyBDYWNoZU1hbmFnZW1lbnQoKS5pbml0KClcbn0pXG4iXSwibWFwcGluZ3MiOiI7Ozs7OztJQUFNQSxlQUFlO0VBQUEsU0FBQUEsZ0JBQUE7SUFBQUMsZUFBQSxPQUFBRCxlQUFBO0VBQUE7RUFBQSxPQUFBRSxZQUFBLENBQUFGLGVBQUE7SUFBQUcsR0FBQTtJQUFBQyxLQUFBLEVBQ2pCLFNBQUFDLElBQUlBLENBQUEsRUFBRztNQUNIQyxDQUFDLENBQUNDLFFBQVEsQ0FBQyxDQUFDQyxFQUFFLENBQUMsT0FBTyxFQUFFLGtCQUFrQixFQUFFLFVBQUNDLEtBQUssRUFBSztRQUNuREEsS0FBSyxDQUFDQyxjQUFjLENBQUMsQ0FBQztRQUV0QixJQUFJQyxLQUFLLEdBQUdMLENBQUMsQ0FBQ0csS0FBSyxDQUFDRyxhQUFhLENBQUM7UUFFbENDLE1BQU0sQ0FBQ0MsaUJBQWlCLENBQUNILEtBQUssQ0FBQztRQUUvQkksV0FBVyxDQUNOQyxJQUFJLENBQUMsQ0FBQyxDQUNOQyxJQUFJLENBQUNOLEtBQUssQ0FBQ08sSUFBSSxDQUFDLEtBQUssQ0FBQyxFQUFFO1VBQUVDLElBQUksRUFBRVIsS0FBSyxDQUFDTyxJQUFJLENBQUMsTUFBTTtRQUFFLENBQUMsQ0FBQyxDQUNyREUsSUFBSSxDQUFDLFVBQUFDLElBQUE7VUFBQSxJQUFHSCxJQUFJLEdBQUFHLElBQUEsQ0FBSkgsSUFBSTtVQUFBLE9BQU9MLE1BQU0sQ0FBQ1MsV0FBVyxDQUFDSixJQUFJLENBQUNLLE9BQU8sQ0FBQztRQUFBLEVBQUMsV0FDN0MsQ0FBQztVQUFBLE9BQU1WLE1BQU0sQ0FBQ1csaUJBQWlCLENBQUNiLEtBQUssQ0FBQztRQUFBLEVBQUM7TUFDdkQsQ0FBQyxDQUFDO0lBQ047RUFBQztBQUFBO0FBR0xMLENBQUMsQ0FBQyxZQUFNO0VBQ0osSUFBSU4sZUFBZSxDQUFDLENBQUMsQ0FBQ0ssSUFBSSxDQUFDLENBQUM7QUFDaEMsQ0FBQyxDQUFDIiwiaWdub3JlTGlzdCI6W10sImZpbGUiOiIuL3BsYXRmb3JtL2NvcmUvYmFzZS9yZXNvdXJjZXMvanMvY2FjaGUuanMiLCJzb3VyY2VSb290IjoiIn0=\n//# sourceURL=webpack-internal:///./platform/core/base/resources/js/cache.js\n");

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module can't be inlined because the eval-source-map devtool is used.
/******/ 	var __webpack_exports__ = {};
/******/ 	__webpack_modules__["./platform/core/base/resources/js/cache.js"]();
/******/ 	
/******/ })()
;