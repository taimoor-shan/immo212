/******/ (() => { // webpackBootstrap
/*!*******************************************************************!*\
  !*** ./platform/plugins/real-estate/resources/js/front-review.js ***!
  \*******************************************************************/
$(function () {
  new StarRating('.star-rating');
  var $reviewList = $(document).find('.reviews-list');
  var fetchReviews = function fetchReviews(url) {
    $reviewList.append('<div class="loading-spinner"></div>');
    $.get(url || "".concat($reviewList.data('url')), function (_ref) {
      var data = _ref.data;
      $reviewList.html(data);
      if (typeof Theme.lazyLoadInstance !== 'undefined') {
        Theme.lazyLoadInstance.update();
      }
    });
  };
  fetchReviews();
  $(document).on('submit', '.review-form', function (e) {
    e.preventDefault();
    var $form = $(e.currentTarget);
    var $button = $form.find('button[type="submit"]');
    $.ajax({
      method: 'POST',
      url: $form.prop('action'),
      data: $form.serialize(),
      beforeSend: function beforeSend() {
        return $button.prop('disabled', true).addClass('btn-loading');
      },
      success: function success(_ref2) {
        var data = _ref2.data;
        $form.get(0).reset();
        $form.find('textarea').prop('disabled', true).val('');
        Theme.showSuccess(data.message);
        fetchReviews();
      },
      error: function error(response) {
        Theme.handleError(response);
        $button.prop('disabled', false);
      },
      complete: function complete() {
        if (typeof refreshRecaptcha !== 'undefined') {
          refreshRecaptcha();
        }
        $button.removeClass('btn-loading');
      }
    });
  }).on('click', '.pagination ul li a', function (e) {
    e.preventDefault();
    fetchReviews(e.target.href);
    $('html, body').animate({
      scrollTop: $reviewList.offset().top - 220
    }, 0);
  });
});
/******/ })()
;
//# sourceMappingURL=front-review.js.map