/**
 * UCUA Safety Management System - JavaScript Utilities
 * Handles common button functionality, form submissions, and modal operations
 */

(function() {
    'use strict';

    // Ensure jQuery is available
    if (typeof $ === 'undefined') {
        console.error('jQuery is required for UCUA utilities');
        return;
    }

    // Global UCUA utilities namespace
    window.UCUA = window.UCUA || {};

    /**
     * Button Management
     */
    UCUA.Button = {
        // Add loading state to button
        setLoading: function(button, loadingText = 'Processing...') {
            const $btn = $(button);
            if ($btn.data('loading')) return; // Already loading
            
            $btn.data('original-text', $btn.html());
            $btn.data('loading', true);
            $btn.prop('disabled', true);
            $btn.html('<i class="fas fa-spinner fa-spin"></i> ' + loadingText);
        },

        // Remove loading state from button
        removeLoading: function(button) {
            const $btn = $(button);
            if (!$btn.data('loading')) return; // Not loading
            
            $btn.prop('disabled', false);
            $btn.html($btn.data('original-text'));
            $btn.removeData('loading original-text');
        },

        // Handle form submission with loading state
        handleFormSubmit: function(form, options = {}) {
            const $form = $(form);
            const $submitBtn = $form.find('button[type="submit"]');
            
            options = $.extend({
                loadingText: 'Submitting...',
                beforeSubmit: null,
                afterSubmit: null,
                onError: null
            }, options);

            $form.on('submit', function(e) {
                // Prevent double submission
                if ($submitBtn.data('loading')) {
                    e.preventDefault();
                    return false;
                }

                // Run before submit callback
                if (options.beforeSubmit && typeof options.beforeSubmit === 'function') {
                    if (options.beforeSubmit($form) === false) {
                        e.preventDefault();
                        return false;
                    }
                }

                // Set loading state
                UCUA.Button.setLoading($submitBtn, options.loadingText);

                // Handle AJAX forms
                if ($form.data('ajax') || options.ajax) {
                    e.preventDefault();
                    UCUA.Form.submitAjax($form, options);
                }
            });
        }
    };

    /**
     * Form Management
     */
    UCUA.Form = {
        // Submit form via AJAX
        submitAjax: function(form, options = {}) {
            const $form = $(form);
            const $submitBtn = $form.find('button[type="submit"]');
            
            $.ajax({
                url: $form.attr('action') || window.location.href,
                method: $form.attr('method') || 'POST',
                data: $form.serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .done(function(response) {
                UCUA.Button.removeLoading($submitBtn);
                
                if (options.onSuccess && typeof options.onSuccess === 'function') {
                    options.onSuccess(response, $form);
                } else {
                    // Default success handling
                    if (response.success) {
                        UCUA.Alert.show('success', response.message || 'Operation completed successfully');
                        if (response.redirect) {
                            setTimeout(() => window.location.href = response.redirect, 1500);
                        }
                    }
                }
            })
            .fail(function(xhr) {
                UCUA.Button.removeLoading($submitBtn);
                
                if (options.onError && typeof options.onError === 'function') {
                    options.onError(xhr, $form);
                } else {
                    // Default error handling
                    let errorMessage = 'An error occurred. Please try again.';
                    
                    if (xhr.status === 422) {
                        // Validation errors
                        const errors = xhr.responseJSON?.errors;
                        if (errors) {
                            errorMessage = Object.values(errors).flat().join('<br>');
                        }
                    } else if (xhr.status === 419) {
                        errorMessage = 'Your session has expired. Please refresh the page.';
                    }
                    
                    UCUA.Alert.show('error', errorMessage);
                }
            });
        },

        // Validate form before submission
        validate: function(form) {
            const $form = $(form);
            let isValid = true;
            
            // Clear previous validation states
            $form.find('.is-invalid').removeClass('is-invalid');
            $form.find('.invalid-feedback').remove();
            
            // Check required fields
            $form.find('[required]').each(function() {
                const $field = $(this);
                if (!$field.val() || $field.val().trim() === '') {
                    $field.addClass('is-invalid');
                    $field.after('<div class="invalid-feedback">This field is required.</div>');
                    isValid = false;
                }
            });
            
            // Check email fields
            $form.find('input[type="email"]').each(function() {
                const $field = $(this);
                const email = $field.val();
                if (email && !UCUA.Validation.isValidEmail(email)) {
                    $field.addClass('is-invalid');
                    $field.after('<div class="invalid-feedback">Please enter a valid email address.</div>');
                    isValid = false;
                }
            });
            
            return isValid;
        }
    };

    /**
     * Modal Management
     */
    UCUA.Modal = {
        show: function(modalId) {
            $('#' + modalId).modal('show');
        },

        hide: function(modalId) {
            $('#' + modalId).modal('hide');
        },

        // Show confirmation modal
        confirm: function(message, callback, options = {}) {
            options = $.extend({
                title: 'Confirm Action',
                confirmText: 'Confirm',
                cancelText: 'Cancel',
                confirmClass: 'btn-primary'
            }, options);

            const modalHtml = `
                <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">${options.title}</h5>
                                <button type="button" class="close" data-dismiss="modal">
                                    <span>&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <p>${message}</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">${options.cancelText}</button>
                                <button type="button" class="btn ${options.confirmClass}" id="confirmBtn">${options.confirmText}</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // Remove existing modal
            $('#confirmModal').remove();
            
            // Add new modal
            $('body').append(modalHtml);
            
            // Handle confirm button
            $('#confirmBtn').on('click', function() {
                $('#confirmModal').modal('hide');
                if (callback && typeof callback === 'function') {
                    callback();
                }
            });
            
            // Show modal
            $('#confirmModal').modal('show');
            
            // Clean up after modal is hidden
            $('#confirmModal').on('hidden.bs.modal', function() {
                $(this).remove();
            });
        }
    };

    /**
     * Alert Management
     */
    UCUA.Alert = {
        show: function(type, message, duration = 5000) {
            const alertClass = type === 'error' ? 'alert-danger' : `alert-${type}`;
            const alertHtml = `
                <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            `;
            
            // Remove existing alerts
            $('.alert').remove();
            
            // Add new alert at the top of the main content
            $('main, .main-content, .container').first().prepend(alertHtml);
            
            // Auto-hide after duration
            if (duration > 0) {
                setTimeout(() => {
                    $('.alert').fadeOut();
                }, duration);
            }
        }
    };

    /**
     * Validation Utilities
     */
    UCUA.Validation = {
        isValidEmail: function(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }
    };

    /**
     * Initialize utilities when DOM is ready
     */
    $(document).ready(function() {
        // Auto-initialize forms with data-ucua-form attribute
        $('form[data-ucua-form]').each(function() {
            const options = $(this).data('ucua-options') || {};
            UCUA.Button.handleFormSubmit(this, options);
        });

        // Ensure Bootstrap modal dismiss buttons work properly
        $(document).on('click', '[data-dismiss="modal"], [data-bs-dismiss="modal"]', function(e) {
            // Let Bootstrap handle these naturally - don't interfere
            const modalId = $(this).closest('.modal').attr('id');
            if (modalId) {
                // Use a small delay to ensure Bootstrap processes first
                setTimeout(() => {
                    $('#' + modalId).modal('hide');
                }, 10);
            }
        });

        // Auto-initialize buttons with data-ucua-confirm attribute (exclude cancel buttons)
        $(document).on('click', '[data-ucua-confirm]', function(e) {
            // Skip if this is a modal dismiss button
            if ($(this).attr('data-dismiss') === 'modal' || $(this).attr('data-bs-dismiss') === 'modal') {
                return true;
            }

            // Skip if this is a cancel/close button
            if ($(this).hasClass('btn-secondary') || $(this).hasClass('btn-cancel') ||
                $(this).text().toLowerCase().includes('cancel') ||
                $(this).text().toLowerCase().includes('close')) {
                return true;
            }

            e.preventDefault();
            const message = $(this).data('ucua-confirm');
            const href = $(this).attr('href');
            const form = $(this).closest('form');

            UCUA.Modal.confirm(message, function() {
                if (href) {
                    window.location.href = href;
                } else if (form.length) {
                    form.submit();
                }
            });
        });

        // Global error handling for submit buttons only (not cancel buttons)
        $(document).on('click', 'button[type="submit"]', function(e) {
            const $btn = $(this);

            // Skip if this is a modal dismiss button
            if ($btn.attr('data-dismiss') === 'modal' || $btn.attr('data-bs-dismiss') === 'modal') {
                return true;
            }

            // Skip if this is a cancel/close button
            if ($btn.hasClass('btn-secondary') || $btn.hasClass('btn-cancel') ||
                $btn.text().toLowerCase().includes('cancel') ||
                $btn.text().toLowerCase().includes('close')) {
                return true;
            }

            const $form = $btn.closest('form');

            // Validate form if not already handled
            if (!$form.data('ucua-form') && !$form.data('ajax')) {
                if (!UCUA.Form.validate($form)) {
                    e.preventDefault();
                    return false;
                }
            }
        });
    });

})();
