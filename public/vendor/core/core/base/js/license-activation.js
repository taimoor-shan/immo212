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

/***/ "./platform/core/base/resources/js/license-activation.js":
/*!***************************************************************!*\
  !*** ./platform/core/base/resources/js/license-activation.js ***!
  \***************************************************************/
/***/ (() => {

eval("\n\n$(function () {\n  $(document).on('submit', '[data-bb-toggle=\"activate-license\"]', function (e) {\n    e.preventDefault();\n    var $form = $(this);\n    var formData = new FormData(e.currentTarget);\n    Botble.showLoading($form[0]);\n    $httpClient.make().postForm($form.prop('action'), formData).then(function (_ref) {\n      var data = _ref.data;\n      Botble.showSuccess(data.message);\n      if ($form.data('reload')) {\n        setTimeout(function () {\n          window.location.reload();\n        }, 1000);\n        return;\n      }\n      var redirect = $form.data('redirect');\n      if (redirect) {\n        window.location.assign(redirect);\n      }\n    })[\"finally\"](function () {\n      Botble.hideLoading($form[0]);\n    });\n  });\n});//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiLi9wbGF0Zm9ybS9jb3JlL2Jhc2UvcmVzb3VyY2VzL2pzL2xpY2Vuc2UtYWN0aXZhdGlvbi5qcyIsIm1hcHBpbmdzIjoiQUFBWTs7QUFFWkEsQ0FBQyxDQUFDLFlBQU07RUFDSkEsQ0FBQyxDQUFDQyxRQUFRLENBQUMsQ0FBQ0MsRUFBRSxDQUFDLFFBQVEsRUFBRSxxQ0FBcUMsRUFBRSxVQUFVQyxDQUFDLEVBQUU7SUFDekVBLENBQUMsQ0FBQ0MsY0FBYyxDQUFDLENBQUM7SUFFbEIsSUFBTUMsS0FBSyxHQUFHTCxDQUFDLENBQUMsSUFBSSxDQUFDO0lBQ3JCLElBQU1NLFFBQVEsR0FBRyxJQUFJQyxRQUFRLENBQUNKLENBQUMsQ0FBQ0ssYUFBYSxDQUFDO0lBRTlDQyxNQUFNLENBQUNDLFdBQVcsQ0FBQ0wsS0FBSyxDQUFDLENBQUMsQ0FBQyxDQUFDO0lBRTVCTSxXQUFXLENBQ05DLElBQUksQ0FBQyxDQUFDLENBQ05DLFFBQVEsQ0FBQ1IsS0FBSyxDQUFDUyxJQUFJLENBQUMsUUFBUSxDQUFDLEVBQUVSLFFBQVEsQ0FBQyxDQUN4Q1MsSUFBSSxDQUFDLFVBQUFDLElBQUEsRUFBYztNQUFBLElBQVhDLElBQUksR0FBQUQsSUFBQSxDQUFKQyxJQUFJO01BQ1RSLE1BQU0sQ0FBQ1MsV0FBVyxDQUFDRCxJQUFJLENBQUNFLE9BQU8sQ0FBQztNQUVoQyxJQUFJZCxLQUFLLENBQUNZLElBQUksQ0FBQyxRQUFRLENBQUMsRUFBRTtRQUN0QkcsVUFBVSxDQUFDLFlBQU07VUFDYkMsTUFBTSxDQUFDQyxRQUFRLENBQUNDLE1BQU0sQ0FBQyxDQUFDO1FBQzVCLENBQUMsRUFBRSxJQUFJLENBQUM7UUFFUjtNQUNKO01BRUEsSUFBSUMsUUFBUSxHQUFHbkIsS0FBSyxDQUFDWSxJQUFJLENBQUMsVUFBVSxDQUFDO01BRXJDLElBQUlPLFFBQVEsRUFBRTtRQUNWSCxNQUFNLENBQUNDLFFBQVEsQ0FBQ0csTUFBTSxDQUFDRCxRQUFRLENBQUM7TUFDcEM7SUFDSixDQUFDLENBQUMsV0FDTSxDQUFDLFlBQU07TUFDWGYsTUFBTSxDQUFDaUIsV0FBVyxDQUFDckIsS0FBSyxDQUFDLENBQUMsQ0FBQyxDQUFDO0lBQ2hDLENBQUMsQ0FBQztFQUNWLENBQUMsQ0FBQztBQUNOLENBQUMsQ0FBQyIsInNvdXJjZXMiOlsid2VicGFjazovLy8uL3BsYXRmb3JtL2NvcmUvYmFzZS9yZXNvdXJjZXMvanMvbGljZW5zZS1hY3RpdmF0aW9uLmpzPzU5OWMiXSwic291cmNlc0NvbnRlbnQiOlsiJ3VzZSBzdHJpY3QnXG5cbiQoKCkgPT4ge1xuICAgICQoZG9jdW1lbnQpLm9uKCdzdWJtaXQnLCAnW2RhdGEtYmItdG9nZ2xlPVwiYWN0aXZhdGUtbGljZW5zZVwiXScsIGZ1bmN0aW9uIChlKSB7XG4gICAgICAgIGUucHJldmVudERlZmF1bHQoKVxuXG4gICAgICAgIGNvbnN0ICRmb3JtID0gJCh0aGlzKVxuICAgICAgICBjb25zdCBmb3JtRGF0YSA9IG5ldyBGb3JtRGF0YShlLmN1cnJlbnRUYXJnZXQpXG5cbiAgICAgICAgQm90YmxlLnNob3dMb2FkaW5nKCRmb3JtWzBdKVxuXG4gICAgICAgICRodHRwQ2xpZW50XG4gICAgICAgICAgICAubWFrZSgpXG4gICAgICAgICAgICAucG9zdEZvcm0oJGZvcm0ucHJvcCgnYWN0aW9uJyksIGZvcm1EYXRhKVxuICAgICAgICAgICAgLnRoZW4oKHsgZGF0YSB9KSA9PiB7XG4gICAgICAgICAgICAgICAgQm90YmxlLnNob3dTdWNjZXNzKGRhdGEubWVzc2FnZSlcblxuICAgICAgICAgICAgICAgIGlmICgkZm9ybS5kYXRhKCdyZWxvYWQnKSkge1xuICAgICAgICAgICAgICAgICAgICBzZXRUaW1lb3V0KCgpID0+IHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHdpbmRvdy5sb2NhdGlvbi5yZWxvYWQoKVxuICAgICAgICAgICAgICAgICAgICB9LCAxMDAwKVxuXG4gICAgICAgICAgICAgICAgICAgIHJldHVyblxuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIGxldCByZWRpcmVjdCA9ICRmb3JtLmRhdGEoJ3JlZGlyZWN0JylcblxuICAgICAgICAgICAgICAgIGlmIChyZWRpcmVjdCkge1xuICAgICAgICAgICAgICAgICAgICB3aW5kb3cubG9jYXRpb24uYXNzaWduKHJlZGlyZWN0KVxuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0pXG4gICAgICAgICAgICAuZmluYWxseSgoKSA9PiB7XG4gICAgICAgICAgICAgICAgQm90YmxlLmhpZGVMb2FkaW5nKCRmb3JtWzBdKVxuICAgICAgICAgICAgfSlcbiAgICB9KVxufSlcbiJdLCJuYW1lcyI6WyIkIiwiZG9jdW1lbnQiLCJvbiIsImUiLCJwcmV2ZW50RGVmYXVsdCIsIiRmb3JtIiwiZm9ybURhdGEiLCJGb3JtRGF0YSIsImN1cnJlbnRUYXJnZXQiLCJCb3RibGUiLCJzaG93TG9hZGluZyIsIiRodHRwQ2xpZW50IiwibWFrZSIsInBvc3RGb3JtIiwicHJvcCIsInRoZW4iLCJfcmVmIiwiZGF0YSIsInNob3dTdWNjZXNzIiwibWVzc2FnZSIsInNldFRpbWVvdXQiLCJ3aW5kb3ciLCJsb2NhdGlvbiIsInJlbG9hZCIsInJlZGlyZWN0IiwiYXNzaWduIiwiaGlkZUxvYWRpbmciXSwic291cmNlUm9vdCI6IiJ9\n//# sourceURL=webpack-internal:///./platform/core/base/resources/js/license-activation.js\n");

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module can't be inlined because the eval-source-map devtool is used.
/******/ 	var __webpack_exports__ = {};
/******/ 	__webpack_modules__["./platform/core/base/resources/js/license-activation.js"]();
/******/ 	
/******/ })()
;