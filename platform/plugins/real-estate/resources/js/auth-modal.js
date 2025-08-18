$(document).ready(function() {
    'use strict';

    // Handle authentication modal form validation
    function initAuthModalValidation() {
        // Handle login form submission
        $('#modalLogin form').on('submit', function(e) {
            e.preventDefault();
            
            const $form = $(this);
            const $submitBtn = $form.find('button[type="submit"]');
            const originalText = $submitBtn.text();
            
            // Clear previous errors
            $form.find('.invalid-feedback').remove();
            $form.find('.is-invalid').removeClass('is-invalid');
            
            // Show loading state
            $submitBtn.prop('disabled', true).text('Logging in...');
            
            $.ajax({
                url: $form.attr('action'),
                method: 'POST',
                data: $form.serialize(),
                success: function(response) {
                    if (response.error) {
                        showFormErrors($form, response.message);
                    } else {
                        // Redirect on success
                        window.location.href = response.redirect || window.location.href;
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        // Validation errors
                        const errors = xhr.responseJSON.errors;
                        showValidationErrors($form, errors);
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        showFormErrors($form, xhr.responseJSON.message);
                    } else {
                        showFormErrors($form, 'An error occurred. Please try again.');
                    }
                },
                complete: function() {
                    $submitBtn.prop('disabled', false).text(originalText);
                }
            });
        });

        // Handle register form submission
        $('#modalRegister form').on('submit', function(e) {
            e.preventDefault();
            
            const $form = $(this);
            const $submitBtn = $form.find('button[type="submit"]');
            const originalText = $submitBtn.text();
            
            // Clear previous errors
            $form.find('.invalid-feedback').remove();
            $form.find('.is-invalid').removeClass('is-invalid');
            
            // Show loading state
            $submitBtn.prop('disabled', true).text('Creating account...');
            
            $.ajax({
                url: $form.attr('action'),
                method: 'POST',
                data: $form.serialize(),
                success: function(response) {
                    if (response.error) {
                        showFormErrors($form, response.message);
                    } else {
                        // Show success message and redirect
                        if (response.message) {
                            Theme.showSuccess(response.message);
                        }
                        setTimeout(function() {
                            window.location.href = response.redirect || window.location.href;
                        }, 1500);
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        // Validation errors
                        const errors = xhr.responseJSON.errors;
                        showValidationErrors($form, errors);
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        showFormErrors($form, xhr.responseJSON.message);
                    } else {
                        showFormErrors($form, 'An error occurred. Please try again.');
                    }
                },
                complete: function() {
                    $submitBtn.prop('disabled', false).text(originalText);
                }
            });
        });
    }

    // Show validation errors for specific fields
    function showValidationErrors($form, errors) {
        $.each(errors, function(field, messages) {
            const $field = $form.find(`[name="${field}"]`);
            if ($field.length) {
                $field.addClass('is-invalid');
                
                // Create error message element
                const errorHtml = `<div class="invalid-feedback">${messages[0]}</div>`;
                
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
        const errorHtml = `
            <div class="alert alert-danger" role="alert">
                <i class="ti ti-alert-circle me-2"></i>
                ${message}
            </div>
        `;
        
        $form.prepend(errorHtml);
    }

    // Enhanced email/username validation for login field
    function validateLoginField() {
        const $emailField = $('#modalLogin [name="email"]');
        
        $emailField.on('blur', function() {
            const value = $(this).val().trim();
            const $feedback = $(this).siblings('.invalid-feedback');
            
            // Remove existing validation state
            $(this).removeClass('is-invalid is-valid');
            $feedback.remove();
            
            if (value) {
                // Check if it's email format
                const isEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
                const isUsername = /^[a-zA-Z0-9_.-]+$/.test(value);
                
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
        const $usernameField = $('#modalRegister [name="username"]');
        
        $usernameField.on('blur', function() {
            const value = $(this).val().trim();
            const $feedback = $(this).siblings('.invalid-feedback');
            
            // Remove existing validation state
            $(this).removeClass('is-invalid is-valid');
            $feedback.remove();
            
            if (value) {
                const isValid = /^[a-zA-Z0-9_.-]+$/.test(value) && value.length >= 2 && value.length <= 120;
                
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
        const $emailField = $('#modalRegister [name="email"]');
        
        $emailField.on('blur', function() {
            const value = $(this).val().trim();
            const $feedback = $(this).siblings('.invalid-feedback');
            
            // Remove existing validation state
            $(this).removeClass('is-invalid is-valid');
            $feedback.remove();
            
            if (value) {
                const isValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
                
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
    $('#modalLogin, #modalRegister').on('shown.bs.modal', function() {
        initAuthModalValidation();
        validateLoginField();
        validateUsernameField();
        validateEmailField();
    });

    // Clear validation state when modals are hidden
    $('#modalLogin, #modalRegister').on('hidden.bs.modal', function() {
        const $form = $(this).find('form');
        $form.find('.invalid-feedback').remove();
        $form.find('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
        $form.find('.alert-danger').remove();
        $form[0].reset();
    });
});
