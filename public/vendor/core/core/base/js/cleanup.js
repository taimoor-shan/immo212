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

/***/ "./platform/core/base/resources/js/cleanup.js":
/*!****************************************************!*\
  !*** ./platform/core/base/resources/js/cleanup.js ***!
  \****************************************************/
/***/ (() => {

eval("\n\n$(function () {\n  $(document).on('click', '.btn-trigger-cleanup', function (event) {\n    event.preventDefault();\n    $('#cleanup-modal').modal('show');\n  });\n  $(document).on('click', '#cleanup-submit-action', function (event) {\n    event.preventDefault();\n    event.stopPropagation();\n    var _self = $(event.currentTarget);\n    Botble.showButtonLoading(_self);\n    var $form = $('#form-cleanup-database');\n    var $modal = $('#cleanup-modal');\n    $httpClient.make().post($form.prop('action'), new FormData($form[0])).then(function (_ref) {\n      var data = _ref.data;\n      return Botble.showSuccess(data.message);\n    })[\"finally\"](function () {\n      Botble.hideButtonLoading(_self);\n      $modal.modal('hide');\n    });\n  });\n});//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiLi9wbGF0Zm9ybS9jb3JlL2Jhc2UvcmVzb3VyY2VzL2pzL2NsZWFudXAuanMiLCJtYXBwaW5ncyI6IkFBQVk7O0FBQ1pBLENBQUMsQ0FBQyxZQUFNO0VBQ0pBLENBQUMsQ0FBQ0MsUUFBUSxDQUFDLENBQUNDLEVBQUUsQ0FBQyxPQUFPLEVBQUUsc0JBQXNCLEVBQUUsVUFBQ0MsS0FBSyxFQUFLO0lBQ3ZEQSxLQUFLLENBQUNDLGNBQWMsQ0FBQyxDQUFDO0lBQ3RCSixDQUFDLENBQUMsZ0JBQWdCLENBQUMsQ0FBQ0ssS0FBSyxDQUFDLE1BQU0sQ0FBQztFQUNyQyxDQUFDLENBQUM7RUFFRkwsQ0FBQyxDQUFDQyxRQUFRLENBQUMsQ0FBQ0MsRUFBRSxDQUFDLE9BQU8sRUFBRSx3QkFBd0IsRUFBRSxVQUFDQyxLQUFLLEVBQUs7SUFDekRBLEtBQUssQ0FBQ0MsY0FBYyxDQUFDLENBQUM7SUFDdEJELEtBQUssQ0FBQ0csZUFBZSxDQUFDLENBQUM7SUFDdkIsSUFBTUMsS0FBSyxHQUFHUCxDQUFDLENBQUNHLEtBQUssQ0FBQ0ssYUFBYSxDQUFDO0lBRXBDQyxNQUFNLENBQUNDLGlCQUFpQixDQUFDSCxLQUFLLENBQUM7SUFFL0IsSUFBTUksS0FBSyxHQUFHWCxDQUFDLENBQUMsd0JBQXdCLENBQUM7SUFDekMsSUFBTVksTUFBTSxHQUFHWixDQUFDLENBQUMsZ0JBQWdCLENBQUM7SUFFbENhLFdBQVcsQ0FDTkMsSUFBSSxDQUFDLENBQUMsQ0FDTkMsSUFBSSxDQUFDSixLQUFLLENBQUNLLElBQUksQ0FBQyxRQUFRLENBQUMsRUFBRSxJQUFJQyxRQUFRLENBQUNOLEtBQUssQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQ2xETyxJQUFJLENBQUMsVUFBQUMsSUFBQTtNQUFBLElBQUdDLElBQUksR0FBQUQsSUFBQSxDQUFKQyxJQUFJO01BQUEsT0FBT1gsTUFBTSxDQUFDWSxXQUFXLENBQUNELElBQUksQ0FBQ0UsT0FBTyxDQUFDO0lBQUEsRUFBQyxXQUM3QyxDQUFDLFlBQU07TUFDWGIsTUFBTSxDQUFDYyxpQkFBaUIsQ0FBQ2hCLEtBQUssQ0FBQztNQUMvQkssTUFBTSxDQUFDUCxLQUFLLENBQUMsTUFBTSxDQUFDO0lBQ3hCLENBQUMsQ0FBQztFQUNWLENBQUMsQ0FBQztBQUNOLENBQUMsQ0FBQyIsInNvdXJjZXMiOlsid2VicGFjazovLy8uL3BsYXRmb3JtL2NvcmUvYmFzZS9yZXNvdXJjZXMvanMvY2xlYW51cC5qcz80M2UwIl0sInNvdXJjZXNDb250ZW50IjpbIid1c2Ugc3RyaWN0J1xuJCgoKSA9PiB7XG4gICAgJChkb2N1bWVudCkub24oJ2NsaWNrJywgJy5idG4tdHJpZ2dlci1jbGVhbnVwJywgKGV2ZW50KSA9PiB7XG4gICAgICAgIGV2ZW50LnByZXZlbnREZWZhdWx0KClcbiAgICAgICAgJCgnI2NsZWFudXAtbW9kYWwnKS5tb2RhbCgnc2hvdycpXG4gICAgfSlcblxuICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsICcjY2xlYW51cC1zdWJtaXQtYWN0aW9uJywgKGV2ZW50KSA9PiB7XG4gICAgICAgIGV2ZW50LnByZXZlbnREZWZhdWx0KClcbiAgICAgICAgZXZlbnQuc3RvcFByb3BhZ2F0aW9uKClcbiAgICAgICAgY29uc3QgX3NlbGYgPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpXG5cbiAgICAgICAgQm90YmxlLnNob3dCdXR0b25Mb2FkaW5nKF9zZWxmKVxuXG4gICAgICAgIGNvbnN0ICRmb3JtID0gJCgnI2Zvcm0tY2xlYW51cC1kYXRhYmFzZScpXG4gICAgICAgIGNvbnN0ICRtb2RhbCA9ICQoJyNjbGVhbnVwLW1vZGFsJylcblxuICAgICAgICAkaHR0cENsaWVudFxuICAgICAgICAgICAgLm1ha2UoKVxuICAgICAgICAgICAgLnBvc3QoJGZvcm0ucHJvcCgnYWN0aW9uJyksIG5ldyBGb3JtRGF0YSgkZm9ybVswXSkpXG4gICAgICAgICAgICAudGhlbigoeyBkYXRhIH0pID0+IEJvdGJsZS5zaG93U3VjY2VzcyhkYXRhLm1lc3NhZ2UpKVxuICAgICAgICAgICAgLmZpbmFsbHkoKCkgPT4ge1xuICAgICAgICAgICAgICAgIEJvdGJsZS5oaWRlQnV0dG9uTG9hZGluZyhfc2VsZilcbiAgICAgICAgICAgICAgICAkbW9kYWwubW9kYWwoJ2hpZGUnKVxuICAgICAgICAgICAgfSlcbiAgICB9KVxufSlcbiJdLCJuYW1lcyI6WyIkIiwiZG9jdW1lbnQiLCJvbiIsImV2ZW50IiwicHJldmVudERlZmF1bHQiLCJtb2RhbCIsInN0b3BQcm9wYWdhdGlvbiIsIl9zZWxmIiwiY3VycmVudFRhcmdldCIsIkJvdGJsZSIsInNob3dCdXR0b25Mb2FkaW5nIiwiJGZvcm0iLCIkbW9kYWwiLCIkaHR0cENsaWVudCIsIm1ha2UiLCJwb3N0IiwicHJvcCIsIkZvcm1EYXRhIiwidGhlbiIsIl9yZWYiLCJkYXRhIiwic2hvd1N1Y2Nlc3MiLCJtZXNzYWdlIiwiaGlkZUJ1dHRvbkxvYWRpbmciXSwic291cmNlUm9vdCI6IiJ9\n//# sourceURL=webpack-internal:///./platform/core/base/resources/js/cleanup.js\n");

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module can't be inlined because the eval-source-map devtool is used.
/******/ 	var __webpack_exports__ = {};
/******/ 	__webpack_modules__["./platform/core/base/resources/js/cleanup.js"]();
/******/ 	
/******/ })()
;