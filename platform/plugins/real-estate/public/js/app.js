/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./platform/plugins/real-estate/resources/js/form.js":
/*!***********************************************************!*\
  !*** ./platform/plugins/real-estate/resources/js/form.js ***!
  \***********************************************************/
/***/ (() => {

$(document).ready(function () {
  $('.custom-select-image').on('click', function (event) {
    event.preventDefault();
    $(this).closest('.image-box').find('.image_input').trigger('click');
  });
  function readURL(input) {
    if (input.files && input.files[0]) {
      var reader = new FileReader();
      reader.onload = function (e) {
        $(input).closest('.image-box').find('.preview_image').prop('src', e.target.result);
      };
      reader.readAsDataURL(input.files[0]);
    }
  }
  $('.image_input').on('change', function () {
    readURL(this);
  });
});

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
// This entry needs to be wrapped in an IIFE because it needs to be isolated against other modules in the chunk.
(() => {
/*!**********************************************************!*\
  !*** ./platform/plugins/real-estate/resources/js/app.js ***!
  \**********************************************************/
__webpack_require__(/*! ./form */ "./platform/plugins/real-estate/resources/js/form.js");
$(function () {
  if (window.noticeMessages) {
    window.noticeMessages.forEach(function (message) {
      Botble.showNotice(message.type, message.message, message.type === 'error' ? window.trans && window.trans.error ? window.trans.error : 'Error!' : window.trans && window.trans.success ? window.trans.success : 'Success!');
    });
  }
  $(document).on('click', '[data-bb-toggle="property-renew-modal"]', function (event) {
    event.preventDefault();
    var $currentTarget = $(event.currentTarget);
    $('.button-confirm-renew').data('section', $currentTarget.prop('href')).data('parent-table', $currentTarget.closest('.table').prop('id'));
    $('.modal-confirm-renew').modal('show');
  });
  $('.button-confirm-renew').on('click', function (event) {
    event.preventDefault();
    var $currentTarget = $(event.currentTarget);
    var url = $currentTarget.data('section');
    $currentTarget.addClass('button-loading');
    $httpClient.make().withButtonLoading($currentTarget).post(url).then(function (_ref) {
      var data = _ref.data;
      window.LaravelDataTables[$currentTarget.data('parent-table')].row($("a[data-section=\"".concat(url, "\"]")).closest('tr')).remove().draw();
      Botble.showSuccess(data.message);
    })["finally"](function () {
      return $currentTarget.closest('.modal').modal('hide');
    });
  });
  $(document).on('click', '.btn_remove_image', function (event) {
    event.preventDefault();
    $(event.currentTarget).closest('.image-box').find('.preview-image-wrapper').hide();
    $(event.currentTarget).closest('.image-box').find('.image-data').val('');
  });
  var refreshCoupon = function refreshCoupon(url) {
    $httpClient.make().get(url).then(function (_ref2) {
      var data = _ref2.data;
      $('.order-detail-box').html(data.data);
    });
  };
  $(document).on('click', '.toggle-coupon-form', function () {
    return $(document).find('.coupon-form').toggle('fast');
  }).on('click', '.apply-coupon-code', function (e) {
    e.preventDefault();
    var $button = $(e.currentTarget);
    $httpClient.make().withButtonLoading($button).post($button.data('url'), {
      coupon_code: $button.closest('form').find('input[name="coupon_code"]').val()
    }).then(function (_ref3) {
      var data = _ref3.data;
      Botble.showSuccess(data.message);
      var refreshUrl = $('.order-detail-box').data('refresh-url');
      refreshCoupon(refreshUrl);
    });
  }).on('click', '.remove-coupon-code', function (e) {
    e.preventDefault();
    var $button = $(e.currentTarget);
    $httpClient.make().post($button.data('url')).then(function (_ref4) {
      var data = _ref4.data;
      Botble.showSuccess(data.message);
      var refreshUrl = $('.order-detail-box').data('refresh-url');
      refreshCoupon(refreshUrl);
    });
  });
  function handleToggleDrawer() {
    $('.navbar-toggler').on('click', function () {
      $('.ps-drawer--mobile').addClass('active');
      $('.ps-site-overlay').addClass('active');
    });
    $('.ps-drawer__close').on('click', function () {
      $('.ps-drawer--mobile').removeClass('active');
      $('.ps-site-overlay').removeClass('active');
    });
    $('body').on('click', function (e) {
      if ($(e.target).siblings('.ps-drawer--mobile').hasClass('active')) {
        $('.ps-drawer--mobile').removeClass('active');
        $('.ps-site-overlay').removeClass('active');
      }
    });
  }
  handleToggleDrawer();
});
})();

/******/ })()
;
//# sourceMappingURL=app.js.map