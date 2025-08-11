/******/ (() => { // webpackBootstrap
/*!********************************************************************!*\
  !*** ./platform/plugins/real-estate/resources/js/account-admin.js ***!
  \********************************************************************/
$(document).ready(function () {
  $(document).on('click', '#is_change_password', function (event) {
    if ($(event.currentTarget).is(':checked')) {
      $('input[type=password]').closest('.form-group').removeClass('hidden').fadeIn();
    } else {
      $('input[type=password]').closest('.form-group').addClass('hidden').fadeOut();
    }
  });
  $(document).on('click', '.btn-trigger-add-credit', function (event) {
    event.preventDefault();
    $('#add-credit-modal').modal('show');
  });
  $(document).on('click', '#confirm-add-credit-button', function (event) {
    event.preventDefault();
    var _self = $(event.currentTarget);
    _self.addClass('button-loading');
    $.ajax({
      type: 'POST',
      cache: false,
      url: _self.closest('.modal-content').find('form').prop('action'),
      data: _self.closest('.modal-content').find('form').serialize(),
      success: function success(res) {
        if (!res.error) {
          Botble.showNotice('success', res.message);
          $('#add-credit-modal').modal('hide');
          $('#credit-histories').load("".concat($('.page-body form').prop('action'), " #credit-histories > *"));
        } else {
          Botble.showNotice('error', res.message);
        }
        _self.removeClass('button-loading');
      },
      error: function error(res) {
        Botble.handleError(res);
        _self.removeClass('button-loading');
      }
    });
  });
  $(document).on('click', '.show-timeline-dropdown', function (event) {
    event.preventDefault();
    $($(event.currentTarget).data('target')).slideToggle();
    $(event.currentTarget).closest('.comment-log-item').toggleClass('bg-white');
  });
  $(document).on('click', '.verify-account-email-button', function (event) {
    event.preventDefault();
    $('#confirm-verify-account-email-button').data('action', $(event.currentTarget).prop('href'));
    $('#verify-account-email-modal').modal('show');
  });
  $(document).on('click', '#confirm-verify-account-email-button', function (event) {
    event.preventDefault();
    var _self = $(event.currentTarget);
    _self.addClass('button-loading');
    $.ajax({
      type: 'POST',
      cache: false,
      url: _self.data('action'),
      success: function success(res) {
        if (!res.error) {
          Botble.showSuccess(res.message);
          setTimeout(function () {
            window.location.reload();
          }, 2000);
        } else {
          Botble.showError(res.message);
        }
        _self.removeClass('button-loading');
        _self.closest('.modal').modal('hide');
      },
      error: function error(res) {
        Botble.handleError(res);
        _self.removeClass('button-loading');
      }
    });
  });
});
/******/ })()
;
//# sourceMappingURL=account-admin.js.map