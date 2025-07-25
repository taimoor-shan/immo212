/******/ (() => { // webpackBootstrap
/*!*************************************************************!*\
  !*** ./platform/plugins/real-estate/resources/js/export.js ***!
  \*************************************************************/
$(function () {
  var isExporting = false;
  $(document).on('click', '[data-bb-toggle="export-data"]', function (event) {
    event.preventDefault();
    if (isExporting) {
      return;
    }
    var $currenTarget = $(event.currentTarget);
    isExporting = true;
    $httpClient.make().withButtonLoading($currenTarget).withResponseType('blob').post($currenTarget.prop('href')).then(function (_ref) {
      var data = _ref.data;
      var a = document.createElement('a');
      var url = window.URL.createObjectURL(data);
      a.href = url;
      a.download = $currenTarget.data('filename');
      document.body.append(a);
      a.click();
      a.remove();
      window.URL.revokeObjectURL(url);
    })["finally"](function () {
      return isExporting = false;
    });
  });
});
/******/ })()
;
//# sourceMappingURL=export.js.map