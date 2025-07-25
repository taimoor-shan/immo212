/*
 * ATTENTION: An "eval-source-map" devtool has been used.
 * This devtool is neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file with attached SourceMaps in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./platform/core/dashboard/resources/js/components/CheckForUpdates.vue?vue&type=script&lang=js":
/*!*********************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./platform/core/dashboard/resources/js/components/CheckForUpdates.vue?vue&type=script&lang=js ***!
  \*********************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   \"default\": () => (__WEBPACK_DEFAULT_EXPORT__)\n/* harmony export */ });\n/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({\n  props: {\n    checkUpdateUrl: {\n      type: String,\n      \"default\": function _default() {\n        return null;\n      },\n      required: true\n    }\n  },\n  data: function data() {\n    return {\n      hasNewVersion: false,\n      message: null\n    };\n  },\n  mounted: function mounted() {\n    this.checkUpdate();\n  },\n  methods: {\n    checkUpdate: function checkUpdate() {\n      var _this = this;\n      axios.get(this.checkUpdateUrl).then(function (_ref) {\n        var data = _ref.data;\n        if (!data.error && data.data.has_new_version) {\n          _this.hasNewVersion = true;\n          _this.message = data.message;\n        }\n      })[\"catch\"](function () {});\n    }\n  }\n});//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiLi9ub2RlX21vZHVsZXMvYmFiZWwtbG9hZGVyL2xpYi9pbmRleC5qcz8/Y2xvbmVkUnVsZVNldC01LnVzZVswXSEuL25vZGVfbW9kdWxlcy92dWUtbG9hZGVyL2Rpc3QvaW5kZXguanM/P3J1bGVTZXRbMF0udXNlWzBdIS4vcGxhdGZvcm0vY29yZS9kYXNoYm9hcmQvcmVzb3VyY2VzL2pzL2NvbXBvbmVudHMvQ2hlY2tGb3JVcGRhdGVzLnZ1ZT92dWUmdHlwZT1zY3JpcHQmbGFuZz1qcyIsIm1hcHBpbmdzIjoiOzs7O0FBS0EsaUVBQWU7RUFDWEEsS0FBSyxFQUFFO0lBQ0hDLGNBQWMsRUFBRTtNQUNaQyxJQUFJLEVBQUVDLE1BQU07TUFDWixXQUFTLFNBQVRDLFFBQU9BLENBQUE7UUFBQSxPQUFRLElBQUk7TUFBQTtNQUNuQkMsUUFBUSxFQUFFO0lBQ2Q7RUFDSixDQUFDO0VBRURDLElBQUksV0FBSkEsSUFBSUEsQ0FBQSxFQUFHO0lBQ0gsT0FBTztNQUNIQyxhQUFhLEVBQUUsS0FBSztNQUNwQkMsT0FBTyxFQUFFO0lBQ2I7RUFDSixDQUFDO0VBQ0RDLE9BQU8sV0FBUEEsT0FBT0EsQ0FBQSxFQUFHO0lBQ04sSUFBSSxDQUFDQyxXQUFXLENBQUM7RUFDckIsQ0FBQztFQUVEQyxPQUFPLEVBQUU7SUFDTEQsV0FBVyxXQUFYQSxXQUFXQSxDQUFBLEVBQUc7TUFBQSxJQUFBRSxLQUFBO01BQ1ZDLEtBQUksQ0FDQ0MsR0FBRyxDQUFDLElBQUksQ0FBQ2IsY0FBYyxFQUN2QmMsSUFBSSxDQUFDLFVBQUFDLElBQUEsRUFBYztRQUFBLElBQVhWLElBQUcsR0FBQVUsSUFBQSxDQUFIVixJQUFHO1FBQ1IsSUFBSSxDQUFDQSxJQUFJLENBQUNXLEtBQUksSUFBS1gsSUFBSSxDQUFDQSxJQUFJLENBQUNZLGVBQWUsRUFBRTtVQUMxQ04sS0FBSSxDQUFDTCxhQUFZLEdBQUksSUFBRztVQUN4QkssS0FBSSxDQUFDSixPQUFNLEdBQUlGLElBQUksQ0FBQ0UsT0FBTTtRQUM5QjtNQUNKLENBQUMsVUFDSyxDQUFDLFlBQU0sQ0FBQyxDQUFDO0lBQ3ZCO0VBQ0o7QUFDSixDQUFDIiwic291cmNlcyI6WyJ3ZWJwYWNrOi8vLy4vcGxhdGZvcm0vY29yZS9kYXNoYm9hcmQvcmVzb3VyY2VzL2pzL2NvbXBvbmVudHMvQ2hlY2tGb3JVcGRhdGVzLnZ1ZT9jYzQ1Il0sInNvdXJjZXNDb250ZW50IjpbIjx0ZW1wbGF0ZT5cbiAgICA8c2xvdCB2LWJpbmQ9XCJ7IGhhc05ld1ZlcnNpb24sIG1lc3NhZ2UgfVwiPjwvc2xvdD5cbjwvdGVtcGxhdGU+XG5cbjxzY3JpcHQ+XG5leHBvcnQgZGVmYXVsdCB7XG4gICAgcHJvcHM6IHtcbiAgICAgICAgY2hlY2tVcGRhdGVVcmw6IHtcbiAgICAgICAgICAgIHR5cGU6IFN0cmluZyxcbiAgICAgICAgICAgIGRlZmF1bHQ6ICgpID0+IG51bGwsXG4gICAgICAgICAgICByZXF1aXJlZDogdHJ1ZSxcbiAgICAgICAgfSxcbiAgICB9LFxuXG4gICAgZGF0YSgpIHtcbiAgICAgICAgcmV0dXJuIHtcbiAgICAgICAgICAgIGhhc05ld1ZlcnNpb246IGZhbHNlLFxuICAgICAgICAgICAgbWVzc2FnZTogbnVsbCxcbiAgICAgICAgfVxuICAgIH0sXG4gICAgbW91bnRlZCgpIHtcbiAgICAgICAgdGhpcy5jaGVja1VwZGF0ZSgpXG4gICAgfSxcblxuICAgIG1ldGhvZHM6IHtcbiAgICAgICAgY2hlY2tVcGRhdGUoKSB7XG4gICAgICAgICAgICBheGlvc1xuICAgICAgICAgICAgICAgIC5nZXQodGhpcy5jaGVja1VwZGF0ZVVybClcbiAgICAgICAgICAgICAgICAudGhlbigoeyBkYXRhIH0pID0+IHtcbiAgICAgICAgICAgICAgICAgICAgaWYgKCFkYXRhLmVycm9yICYmIGRhdGEuZGF0YS5oYXNfbmV3X3ZlcnNpb24pIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHRoaXMuaGFzTmV3VmVyc2lvbiA9IHRydWVcbiAgICAgICAgICAgICAgICAgICAgICAgIHRoaXMubWVzc2FnZSA9IGRhdGEubWVzc2FnZVxuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfSlcbiAgICAgICAgICAgICAgICAuY2F0Y2goKCkgPT4ge30pXG4gICAgICAgIH0sXG4gICAgfSxcbn1cbjwvc2NyaXB0PlxuIl0sIm5hbWVzIjpbInByb3BzIiwiY2hlY2tVcGRhdGVVcmwiLCJ0eXBlIiwiU3RyaW5nIiwiZGVmYXVsdCIsInJlcXVpcmVkIiwiZGF0YSIsImhhc05ld1ZlcnNpb24iLCJtZXNzYWdlIiwibW91bnRlZCIsImNoZWNrVXBkYXRlIiwibWV0aG9kcyIsIl90aGlzIiwiYXhpb3MiLCJnZXQiLCJ0aGVuIiwiX3JlZiIsImVycm9yIiwiaGFzX25ld192ZXJzaW9uIl0sInNvdXJjZVJvb3QiOiIifQ==\n//# sourceURL=webpack-internal:///./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./platform/core/dashboard/resources/js/components/CheckForUpdates.vue?vue&type=script&lang=js\n");

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./platform/core/dashboard/resources/js/components/CheckForUpdates.vue?vue&type=template&id=e8615b02":
/*!*************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./platform/core/dashboard/resources/js/components/CheckForUpdates.vue?vue&type=template&id=e8615b02 ***!
  \*************************************************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   render: () => (/* binding */ render)\n/* harmony export */ });\n/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ \"vue\");\n/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(vue__WEBPACK_IMPORTED_MODULE_0__);\n\nfunction render(_ctx, _cache, $props, $setup, $data, $options) {\n  return (0,vue__WEBPACK_IMPORTED_MODULE_0__.renderSlot)(_ctx.$slots, \"default\", (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeProps)((0,vue__WEBPACK_IMPORTED_MODULE_0__.guardReactiveProps)({\n    hasNewVersion: $data.hasNewVersion,\n    message: $data.message\n  })));\n}//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiLi9ub2RlX21vZHVsZXMvYmFiZWwtbG9hZGVyL2xpYi9pbmRleC5qcz8/Y2xvbmVkUnVsZVNldC01LnVzZVswXSEuL25vZGVfbW9kdWxlcy92dWUtbG9hZGVyL2Rpc3QvdGVtcGxhdGVMb2FkZXIuanM/P3J1bGVTZXRbMV0ucnVsZXNbMl0hLi9ub2RlX21vZHVsZXMvdnVlLWxvYWRlci9kaXN0L2luZGV4LmpzPz9ydWxlU2V0WzBdLnVzZVswXSEuL3BsYXRmb3JtL2NvcmUvZGFzaGJvYXJkL3Jlc291cmNlcy9qcy9jb21wb25lbnRzL0NoZWNrRm9yVXBkYXRlcy52dWU/dnVlJnR5cGU9dGVtcGxhdGUmaWQ9ZTg2MTViMDIiLCJtYXBwaW5ncyI6Ijs7Ozs7Ozs7U0FDSUEsK0NBQUEsQ0FBaURDLElBQUEsQ0FBQUMsTUFBQSxhQURyREMsbURBQUEsQ0FBQUMsdURBQUE7SUFBQUMsYUFBQSxFQUNvQkMsS0FBQSxDQUFBRCxhQUFhO0lBQUFFLE9BQUEsRUFBRUQsS0FBQSxDQUFBQztFQUFPIiwic291cmNlcyI6WyJ3ZWJwYWNrOi8vLy4vcGxhdGZvcm0vY29yZS9kYXNoYm9hcmQvcmVzb3VyY2VzL2pzL2NvbXBvbmVudHMvQ2hlY2tGb3JVcGRhdGVzLnZ1ZT9jYzQ1Il0sInNvdXJjZXNDb250ZW50IjpbIjx0ZW1wbGF0ZT5cbiAgICA8c2xvdCB2LWJpbmQ9XCJ7IGhhc05ld1ZlcnNpb24sIG1lc3NhZ2UgfVwiPjwvc2xvdD5cbjwvdGVtcGxhdGU+XG5cbjxzY3JpcHQ+XG5leHBvcnQgZGVmYXVsdCB7XG4gICAgcHJvcHM6IHtcbiAgICAgICAgY2hlY2tVcGRhdGVVcmw6IHtcbiAgICAgICAgICAgIHR5cGU6IFN0cmluZyxcbiAgICAgICAgICAgIGRlZmF1bHQ6ICgpID0+IG51bGwsXG4gICAgICAgICAgICByZXF1aXJlZDogdHJ1ZSxcbiAgICAgICAgfSxcbiAgICB9LFxuXG4gICAgZGF0YSgpIHtcbiAgICAgICAgcmV0dXJuIHtcbiAgICAgICAgICAgIGhhc05ld1ZlcnNpb246IGZhbHNlLFxuICAgICAgICAgICAgbWVzc2FnZTogbnVsbCxcbiAgICAgICAgfVxuICAgIH0sXG4gICAgbW91bnRlZCgpIHtcbiAgICAgICAgdGhpcy5jaGVja1VwZGF0ZSgpXG4gICAgfSxcblxuICAgIG1ldGhvZHM6IHtcbiAgICAgICAgY2hlY2tVcGRhdGUoKSB7XG4gICAgICAgICAgICBheGlvc1xuICAgICAgICAgICAgICAgIC5nZXQodGhpcy5jaGVja1VwZGF0ZVVybClcbiAgICAgICAgICAgICAgICAudGhlbigoeyBkYXRhIH0pID0+IHtcbiAgICAgICAgICAgICAgICAgICAgaWYgKCFkYXRhLmVycm9yICYmIGRhdGEuZGF0YS5oYXNfbmV3X3ZlcnNpb24pIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHRoaXMuaGFzTmV3VmVyc2lvbiA9IHRydWVcbiAgICAgICAgICAgICAgICAgICAgICAgIHRoaXMubWVzc2FnZSA9IGRhdGEubWVzc2FnZVxuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfSlcbiAgICAgICAgICAgICAgICAuY2F0Y2goKCkgPT4ge30pXG4gICAgICAgIH0sXG4gICAgfSxcbn1cbjwvc2NyaXB0PlxuIl0sIm5hbWVzIjpbIl9yZW5kZXJTbG90IiwiX2N0eCIsIiRzbG90cyIsIl9ub3JtYWxpemVQcm9wcyIsIl9ndWFyZFJlYWN0aXZlUHJvcHMiLCJoYXNOZXdWZXJzaW9uIiwiJGRhdGEiLCJtZXNzYWdlIl0sInNvdXJjZVJvb3QiOiIifQ==\n//# sourceURL=webpack-internal:///./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./platform/core/dashboard/resources/js/components/CheckForUpdates.vue?vue&type=template&id=e8615b02\n");

/***/ }),

/***/ "./node_modules/vue-loader/dist/exportHelper.js":
/*!******************************************************!*\
  !*** ./node_modules/vue-loader/dist/exportHelper.js ***!
  \******************************************************/
/***/ ((__unused_webpack_module, exports) => {

eval("\nObject.defineProperty(exports, \"__esModule\", ({ value: true }));\n// runtime helper for setting properties on components\n// in a tree-shakable way\nexports[\"default\"] = (sfc, props) => {\n    const target = sfc.__vccOpts || sfc;\n    for (const [key, val] of props) {\n        target[key] = val;\n    }\n    return target;\n};\n//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiLi9ub2RlX21vZHVsZXMvdnVlLWxvYWRlci9kaXN0L2V4cG9ydEhlbHBlci5qcyIsIm1hcHBpbmdzIjoiQUFBYTtBQUNiLDhDQUE2QyxFQUFFLGFBQWEsRUFBQztBQUM3RDtBQUNBO0FBQ0Esa0JBQWU7QUFDZjtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EiLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9ub2RlX21vZHVsZXMvdnVlLWxvYWRlci9kaXN0L2V4cG9ydEhlbHBlci5qcz8zN2RmIl0sInNvdXJjZXNDb250ZW50IjpbIlwidXNlIHN0cmljdFwiO1xuT2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsIFwiX19lc01vZHVsZVwiLCB7IHZhbHVlOiB0cnVlIH0pO1xuLy8gcnVudGltZSBoZWxwZXIgZm9yIHNldHRpbmcgcHJvcGVydGllcyBvbiBjb21wb25lbnRzXG4vLyBpbiBhIHRyZWUtc2hha2FibGUgd2F5XG5leHBvcnRzLmRlZmF1bHQgPSAoc2ZjLCBwcm9wcykgPT4ge1xuICAgIGNvbnN0IHRhcmdldCA9IHNmYy5fX3ZjY09wdHMgfHwgc2ZjO1xuICAgIGZvciAoY29uc3QgW2tleSwgdmFsXSBvZiBwcm9wcykge1xuICAgICAgICB0YXJnZXRba2V5XSA9IHZhbDtcbiAgICB9XG4gICAgcmV0dXJuIHRhcmdldDtcbn07XG4iXSwibmFtZXMiOltdLCJzb3VyY2VSb290IjoiIn0=\n//# sourceURL=webpack-internal:///./node_modules/vue-loader/dist/exportHelper.js\n");

/***/ }),

/***/ "./platform/core/dashboard/resources/js/check-for-updates.js":
/*!*******************************************************************!*\
  !*** ./platform/core/dashboard/resources/js/check-for-updates.js ***!
  \*******************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _components_CheckForUpdates_vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./components/CheckForUpdates.vue */ \"./platform/core/dashboard/resources/js/components/CheckForUpdates.vue\");\n\nif (typeof vueApp !== 'undefined') {\n  vueApp.booting(function (vue) {\n    vue.component('v-check-for-updates', _components_CheckForUpdates_vue__WEBPACK_IMPORTED_MODULE_0__[\"default\"]);\n  });\n}//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiLi9wbGF0Zm9ybS9jb3JlL2Rhc2hib2FyZC9yZXNvdXJjZXMvanMvY2hlY2stZm9yLXVwZGF0ZXMuanMiLCJtYXBwaW5ncyI6Ijs7QUFBOEQ7QUFFOUQsSUFBSSxPQUFPQyxNQUFNLEtBQUssV0FBVyxFQUFFO0VBQy9CQSxNQUFNLENBQUNDLE9BQU8sQ0FBQyxVQUFDQyxHQUFHLEVBQUs7SUFDcEJBLEdBQUcsQ0FBQ0MsU0FBUyxDQUFDLHFCQUFxQixFQUFFSix1RUFBZSxDQUFDO0VBQ3pELENBQUMsQ0FBQztBQUNOIiwic291cmNlcyI6WyJ3ZWJwYWNrOi8vLy4vcGxhdGZvcm0vY29yZS9kYXNoYm9hcmQvcmVzb3VyY2VzL2pzL2NoZWNrLWZvci11cGRhdGVzLmpzPzE0YTkiXSwic291cmNlc0NvbnRlbnQiOlsiaW1wb3J0IENoZWNrRm9yVXBkYXRlcyBmcm9tICcuL2NvbXBvbmVudHMvQ2hlY2tGb3JVcGRhdGVzLnZ1ZSdcblxuaWYgKHR5cGVvZiB2dWVBcHAgIT09ICd1bmRlZmluZWQnKSB7XG4gICAgdnVlQXBwLmJvb3RpbmcoKHZ1ZSkgPT4ge1xuICAgICAgICB2dWUuY29tcG9uZW50KCd2LWNoZWNrLWZvci11cGRhdGVzJywgQ2hlY2tGb3JVcGRhdGVzKVxuICAgIH0pXG59XG4iXSwibmFtZXMiOlsiQ2hlY2tGb3JVcGRhdGVzIiwidnVlQXBwIiwiYm9vdGluZyIsInZ1ZSIsImNvbXBvbmVudCJdLCJzb3VyY2VSb290IjoiIn0=\n//# sourceURL=webpack-internal:///./platform/core/dashboard/resources/js/check-for-updates.js\n");

/***/ }),

/***/ "./platform/core/dashboard/resources/js/components/CheckForUpdates.vue":
/*!*****************************************************************************!*\
  !*** ./platform/core/dashboard/resources/js/components/CheckForUpdates.vue ***!
  \*****************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   \"default\": () => (__WEBPACK_DEFAULT_EXPORT__)\n/* harmony export */ });\n/* harmony import */ var _CheckForUpdates_vue_vue_type_template_id_e8615b02__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./CheckForUpdates.vue?vue&type=template&id=e8615b02 */ \"./platform/core/dashboard/resources/js/components/CheckForUpdates.vue?vue&type=template&id=e8615b02\");\n/* harmony import */ var _CheckForUpdates_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./CheckForUpdates.vue?vue&type=script&lang=js */ \"./platform/core/dashboard/resources/js/components/CheckForUpdates.vue?vue&type=script&lang=js\");\n/* harmony import */ var _node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/dist/exportHelper.js */ \"./node_modules/vue-loader/dist/exportHelper.js\");\n\n\n\n\n;\nconst __exports__ = /*#__PURE__*/(0,_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(_CheckForUpdates_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__[\"default\"], [['render',_CheckForUpdates_vue_vue_type_template_id_e8615b02__WEBPACK_IMPORTED_MODULE_0__.render],['__file',\"platform/core/dashboard/resources/js/components/CheckForUpdates.vue\"]])\n/* hot reload */\nif (false) {}\n\n\n/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (__exports__);//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiLi9wbGF0Zm9ybS9jb3JlL2Rhc2hib2FyZC9yZXNvdXJjZXMvanMvY29tcG9uZW50cy9DaGVja0ZvclVwZGF0ZXMudnVlIiwibWFwcGluZ3MiOiI7Ozs7Ozs7QUFBNEU7QUFDVjtBQUNMOztBQUU3RCxDQUE0RjtBQUM1RixpQ0FBaUMseUZBQWUsQ0FBQyxvRkFBTSxhQUFhLHNGQUFNO0FBQzFFO0FBQ0EsSUFBSSxLQUFVLEVBQUUsRUFZZjs7O0FBR0QsaUVBQWUiLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9wbGF0Zm9ybS9jb3JlL2Rhc2hib2FyZC9yZXNvdXJjZXMvanMvY29tcG9uZW50cy9DaGVja0ZvclVwZGF0ZXMudnVlP2EzYTkiXSwic291cmNlc0NvbnRlbnQiOlsiaW1wb3J0IHsgcmVuZGVyIH0gZnJvbSBcIi4vQ2hlY2tGb3JVcGRhdGVzLnZ1ZT92dWUmdHlwZT10ZW1wbGF0ZSZpZD1lODYxNWIwMlwiXG5pbXBvcnQgc2NyaXB0IGZyb20gXCIuL0NoZWNrRm9yVXBkYXRlcy52dWU/dnVlJnR5cGU9c2NyaXB0Jmxhbmc9anNcIlxuZXhwb3J0ICogZnJvbSBcIi4vQ2hlY2tGb3JVcGRhdGVzLnZ1ZT92dWUmdHlwZT1zY3JpcHQmbGFuZz1qc1wiXG5cbmltcG9ydCBleHBvcnRDb21wb25lbnQgZnJvbSBcIi4uLy4uLy4uLy4uLy4uLy4uL25vZGVfbW9kdWxlcy92dWUtbG9hZGVyL2Rpc3QvZXhwb3J0SGVscGVyLmpzXCJcbmNvbnN0IF9fZXhwb3J0c19fID0gLyojX19QVVJFX18qL2V4cG9ydENvbXBvbmVudChzY3JpcHQsIFtbJ3JlbmRlcicscmVuZGVyXSxbJ19fZmlsZScsXCJwbGF0Zm9ybS9jb3JlL2Rhc2hib2FyZC9yZXNvdXJjZXMvanMvY29tcG9uZW50cy9DaGVja0ZvclVwZGF0ZXMudnVlXCJdXSlcbi8qIGhvdCByZWxvYWQgKi9cbmlmIChtb2R1bGUuaG90KSB7XG4gIF9fZXhwb3J0c19fLl9faG1ySWQgPSBcImU4NjE1YjAyXCJcbiAgY29uc3QgYXBpID0gX19WVUVfSE1SX1JVTlRJTUVfX1xuICBtb2R1bGUuaG90LmFjY2VwdCgpXG4gIGlmICghYXBpLmNyZWF0ZVJlY29yZCgnZTg2MTViMDInLCBfX2V4cG9ydHNfXykpIHtcbiAgICBhcGkucmVsb2FkKCdlODYxNWIwMicsIF9fZXhwb3J0c19fKVxuICB9XG4gIFxuICBtb2R1bGUuaG90LmFjY2VwdChcIi4vQ2hlY2tGb3JVcGRhdGVzLnZ1ZT92dWUmdHlwZT10ZW1wbGF0ZSZpZD1lODYxNWIwMlwiLCAoKSA9PiB7XG4gICAgYXBpLnJlcmVuZGVyKCdlODYxNWIwMicsIHJlbmRlcilcbiAgfSlcblxufVxuXG5cbmV4cG9ydCBkZWZhdWx0IF9fZXhwb3J0c19fIl0sIm5hbWVzIjpbXSwic291cmNlUm9vdCI6IiJ9\n//# sourceURL=webpack-internal:///./platform/core/dashboard/resources/js/components/CheckForUpdates.vue\n");

/***/ }),

/***/ "./platform/core/dashboard/resources/js/components/CheckForUpdates.vue?vue&type=script&lang=js":
/*!*****************************************************************************************************!*\
  !*** ./platform/core/dashboard/resources/js/components/CheckForUpdates.vue?vue&type=script&lang=js ***!
  \*****************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   \"default\": () => (/* reexport safe */ _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_CheckForUpdates_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__[\"default\"])\n/* harmony export */ });\n/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_CheckForUpdates_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../../../../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./CheckForUpdates.vue?vue&type=script&lang=js */ \"./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./platform/core/dashboard/resources/js/components/CheckForUpdates.vue?vue&type=script&lang=js\");\n //# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiLi9wbGF0Zm9ybS9jb3JlL2Rhc2hib2FyZC9yZXNvdXJjZXMvanMvY29tcG9uZW50cy9DaGVja0ZvclVwZGF0ZXMudnVlP3Z1ZSZ0eXBlPXNjcmlwdCZsYW5nPWpzIiwibWFwcGluZ3MiOiI7Ozs7O0FBQXNPIiwic291cmNlcyI6WyJ3ZWJwYWNrOi8vLy4vcGxhdGZvcm0vY29yZS9kYXNoYm9hcmQvcmVzb3VyY2VzL2pzL2NvbXBvbmVudHMvQ2hlY2tGb3JVcGRhdGVzLnZ1ZT8xOGRjIl0sInNvdXJjZXNDb250ZW50IjpbImV4cG9ydCB7IGRlZmF1bHQgfSBmcm9tIFwiLSEuLi8uLi8uLi8uLi8uLi8uLi9ub2RlX21vZHVsZXMvYmFiZWwtbG9hZGVyL2xpYi9pbmRleC5qcz8/Y2xvbmVkUnVsZVNldC01LnVzZVswXSEuLi8uLi8uLi8uLi8uLi8uLi9ub2RlX21vZHVsZXMvdnVlLWxvYWRlci9kaXN0L2luZGV4LmpzPz9ydWxlU2V0WzBdLnVzZVswXSEuL0NoZWNrRm9yVXBkYXRlcy52dWU/dnVlJnR5cGU9c2NyaXB0Jmxhbmc9anNcIjsgZXhwb3J0ICogZnJvbSBcIi0hLi4vLi4vLi4vLi4vLi4vLi4vbm9kZV9tb2R1bGVzL2JhYmVsLWxvYWRlci9saWIvaW5kZXguanM/P2Nsb25lZFJ1bGVTZXQtNS51c2VbMF0hLi4vLi4vLi4vLi4vLi4vLi4vbm9kZV9tb2R1bGVzL3Z1ZS1sb2FkZXIvZGlzdC9pbmRleC5qcz8/cnVsZVNldFswXS51c2VbMF0hLi9DaGVja0ZvclVwZGF0ZXMudnVlP3Z1ZSZ0eXBlPXNjcmlwdCZsYW5nPWpzXCIiXSwibmFtZXMiOltdLCJzb3VyY2VSb290IjoiIn0=\n//# sourceURL=webpack-internal:///./platform/core/dashboard/resources/js/components/CheckForUpdates.vue?vue&type=script&lang=js\n");

/***/ }),

/***/ "./platform/core/dashboard/resources/js/components/CheckForUpdates.vue?vue&type=template&id=e8615b02":
/*!***********************************************************************************************************!*\
  !*** ./platform/core/dashboard/resources/js/components/CheckForUpdates.vue?vue&type=template&id=e8615b02 ***!
  \***********************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   render: () => (/* reexport safe */ _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_CheckForUpdates_vue_vue_type_template_id_e8615b02__WEBPACK_IMPORTED_MODULE_0__.render)
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_CheckForUpdates_vue_vue_type_template_id_e8615b02__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../../../../../node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!../../../../../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./CheckForUpdates.vue?vue&type=template&id=e8615b02 */ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./platform/core/dashboard/resources/js/components/CheckForUpdates.vue?vue&type=template&id=e8615b02");


/***/ }),

/***/ "vue":
/*!**********************!*\
  !*** external "Vue" ***!
  \**********************/
/***/ ((module) => {

module.exports = Vue;

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module can't be inlined because the eval-source-map devtool is used.
/******/ 	var __webpack_exports__ = __webpack_require__("./platform/core/dashboard/resources/js/check-for-updates.js");
/******/ 	
/******/ })()
;