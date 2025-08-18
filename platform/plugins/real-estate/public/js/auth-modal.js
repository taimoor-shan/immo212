/******/ (() => { // webpackBootstrap
/*!*****************************************************************!*\
  !*** ./platform/plugins/real-estate/resources/js/auth-modal.js ***!
  \*****************************************************************/
$(document).ready(function () {
  'use strict';

  // Handle authentication modal form validation
  function initAuthModalValidation() {
    // Handle login form submission
    $('#modalLogin form').on('submit', function (e) {
      e.preventDefault();
      var $form = $(this);
      var $submitBtn = $form.find('button[type="submit"]');
      var originalText = $submitBtn.text();

      // Clear previous errors
      $form.find('.invalid-feedback').remove();
      $form.find('.is-invalid').removeClass('is-invalid');

      // Show loading state
      $submitBtn.prop('disabled', true).text('Logging in...');
      $.ajax({
        url: $form.attr('action'),
        method: 'POST',
        data: $form.serialize(),
        success: function success(response) {
          if (response.error) {
            showFormErrors($form, response.message);
          } else {
            // Redirect on success
            window.location.href = response.redirect || window.location.href;
          }
        },
        error: function error(xhr) {
          if (xhr.status === 422) {
            // Validation errors
            var errors = xhr.responseJSON.errors;
            showValidationErrors($form, errors);
          } else if (xhr.responseJSON && xhr.responseJSON.message) {
            showFormErrors($form, xhr.responseJSON.message);
          } else {
            showFormErrors($form, 'An error occurred. Please try again.');
          }
        },
        complete: function complete() {
          $submitBtn.prop('disabled', false).text(originalText);
        }
      });
    });

    // Handle register form submission
    $('#modalRegister form').on('submit', function (e) {
      e.preventDefault();
      var $form = $(this);
      var $submitBtn = $form.find('button[type="submit"]');
      var originalText = $submitBtn.text();

      // Clear previous errors
      $form.find('.invalid-feedback').remove();
      $form.find('.is-invalid').removeClass('is-invalid');

      // Show loading state
      $submitBtn.prop('disabled', true).text('Creating account...');
      $.ajax({
        url: $form.attr('action'),
        method: 'POST',
        data: $form.serialize(),
        success: function success(response) {
          if (response.error) {
            showFormErrors($form, response.message);
          } else {
            // Show success message and redirect
            if (response.message) {
              Theme.showSuccess(response.message);
            }
            setTimeout(function () {
              window.location.href = response.redirect || window.location.href;
            }, 1500);
          }
        },
        error: function error(xhr) {
          if (xhr.status === 422) {
            // Validation errors
            var errors = xhr.responseJSON.errors;
            showValidationErrors($form, errors);
          } else if (xhr.responseJSON && xhr.responseJSON.message) {
            showFormErrors($form, xhr.responseJSON.message);
          } else {
            showFormErrors($form, 'An error occurred. Please try again.');
          }
        },
        complete: function complete() {
          $submitBtn.prop('disabled', false).text(originalText);
        }
      });
    });
  }

  // Show validation errors for specific fields
  function showValidationErrors($form, errors) {
    $.each(errors, function (field, messages) {
      var $field = $form.find("[name=\"".concat(field, "\"]"));
      if ($field.length) {
        $field.addClass('is-invalid');

        // Create error message element
        var errorHtml = "<div class=\"invalid-feedback\">".concat(messages[0], "</div>");

        // Insert error message after the field or its parent container
        if ($field.parent().hasClass('position-relative')) {
          $field.parent().after(errorHtml);
        } else {
          $field.after(errorHtml);
        }
      }
    });
  }

  // Show general form errors
  function showFormErrors($form, message) {
    // Remove existing error alerts
    $form.find('.alert-danger').remove();

    // Add error alert at the top of the form
    var errorHtml = "\n            <div class=\"alert alert-danger\" role=\"alert\">\n                <i class=\"ti ti-alert-circle me-2\"></i>\n                ".concat(message, "\n            </div>\n        ");
    $form.prepend(errorHtml);
  }

  // Enhanced email/username validation for login field
  function validateLoginField() {
    var $emailField = $('#modalLogin [name="email"]');
    $emailField.on('blur', function () {
      var value = $(this).val().trim();
      var $feedback = $(this).siblings('.invalid-feedback');

      // Remove existing validation state
      $(this).removeClass('is-invalid is-valid');
      $feedback.remove();
      if (value) {
        // Check if it's email format
        var isEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
        var isUsername = /^[a-zA-Z0-9_.-]+$/.test(value);
        if (!isEmail && !isUsername) {
          $(this).addClass('is-invalid');
          $(this).after('<div class="invalid-feedback">Please enter a valid email address or username.</div>');
        } else {
          $(this).addClass('is-valid');
        }
      }
    });
  }

  // Enhanced username validation for registration
  function validateUsernameField() {
    var $usernameField = $('#modalRegister [name="username"]');
    $usernameField.on('blur', function () {
      var value = $(this).val().trim();
      var $feedback = $(this).siblings('.invalid-feedback');

      // Remove existing validation state
      $(this).removeClass('is-invalid is-valid');
      $feedback.remove();
      if (value) {
        var isValid = /^[a-zA-Z0-9_.-]+$/.test(value) && value.length >= 2 && value.length <= 120;
        if (!isValid) {
          $(this).addClass('is-invalid');
          $(this).after('<div class="invalid-feedback">Username can only contain letters, numbers, dots, hyphens and underscores (2-120 characters).</div>');
        } else {
          $(this).addClass('is-valid');
        }
      }
    });
  }

  // Enhanced email validation for registration
  function validateEmailField() {
    var $emailField = $('#modalRegister [name="email"]');
    $emailField.on('blur', function () {
      var value = $(this).val().trim();
      var $feedback = $(this).siblings('.invalid-feedback');

      // Remove existing validation state
      $(this).removeClass('is-invalid is-valid');
      $feedback.remove();
      if (value) {
        var isValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
        if (!isValid) {
          $(this).addClass('is-invalid');
          $(this).after('<div class="invalid-feedback">Please enter a valid email address.</div>');
        } else {
          $(this).addClass('is-valid');
        }
      }
    });
  }

  // Initialize all validation when modals are shown
  $('#modalLogin, #modalRegister').on('shown.bs.modal', function () {
    initAuthModalValidation();
    validateLoginField();
    validateUsernameField();
    validateEmailField();
  });

  // Clear validation state when modals are hidden
  $('#modalLogin, #modalRegister').on('hidden.bs.modal', function () {
    var $form = $(this).find('form');
    $form.find('.invalid-feedback').remove();
    $form.find('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
    $form.find('.alert-danger').remove();
    $form[0].reset();
  });
});
/******/ })()
;
//# sourceMappingURL=auth-modal.js.map