/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/*!*************************************************************************!*\
  !*** ./platform/plugins/real-estate/resources/js/duplicate-property.js ***!
  \*************************************************************************/


$(function () {
  $(document).on('click', '[data-bb-toggle="duplicate-property"]', function (event) {
    event.preventDefault();
    $httpClient.make().withButtonLoading($(this)).post($(this).data('url')).then(function (_ref) {
      var data = _ref.data;
      Botble.showSuccess(data.message);
      setTimeout(function () {
        window.location.href = data.data.url;
      }, 500);
    });
  });
});
/******/ })()
;
//# sourceMappingURL=duplicate-property.js.map