/**
 * Contact Form Handler
 * Handles form submission via AJAX with Cloudflare Turnstile integration
 */

(function($) {
    'use strict';

    // Store Turnstile token
    let turnstileToken = '';

    /**
     * Turnstile callback - called when user completes verification
     */
    window.ehsTurnstileCallback = function(token) {
        turnstileToken = token;
        // Also store in the hidden field if present
        $('.ehs-contact-form [name="turnstile_token"]').val(token);
    };

    /**
     * Show form message
     */
    function showMessage($form, message, type = 'error') {
        const $messages = $form.find('.ehs-form-messages');
        $messages.removeClass('success error').addClass(type);
        $messages.html('<p>' + message + '</p>');
        $messages.slideDown(200);

        // Scroll to message
        $('html, body').animate({
            scrollTop: $messages.offset().top - 100
        }, 300);

        // Auto-hide success messages
        if (type === 'success') {
            setTimeout(function() {
                $messages.slideUp(200);
            }, 5000);
        }
    }

    /**
     * Handle form submission
     */
    function handleFormSubmit(e) {
        e.preventDefault();

        const $form = $(this);
        const $submitBtn = $form.find('.ehs-submit-btn');
        const $btnText = $submitBtn.find('.btn-text');
        const $btnLoader = $submitBtn.find('.btn-loader');
        const nonce = $form.data('nonce');

        // Check honeypot field
        const honeypot = $form.find('.ehs-honeypot input').val();
        if (honeypot) {
            // Bot detected - silently fail
            console.log('[EHS Contact Form] Bot detected via honeypot');
            return false;
        }

        // Validate required fields
        let isValid = true;
        $form.find('[required]').each(function() {
            if (!$(this).val().trim()) {
                isValid = false;
                $(this).addClass('error');
            } else {
                $(this).removeClass('error');
            }
        });

        if (!isValid) {
            showMessage($form, 'Please fill in all required fields.', 'error');
            return false;
        }

        // Check Turnstile if widget is present
        const hasTurnstile = $form.find('.cf-turnstile').length > 0;
        const currentToken = $form.find('[name="turnstile_token"]').val() || turnstileToken;

        if (hasTurnstile && !currentToken) {
            showMessage($form, 'Please complete the verification challenge.', 'error');
            return false;
        }

        // Disable submit button
        $submitBtn.prop('disabled', true);
        $btnText.hide();
        $btnLoader.show();

        // Collect form data
        const formData = {
            action: 'ehs_submit_contact_form',
            nonce: nonce,
            turnstile_token: currentToken,
            name: $form.find('[name="name"]').val() || '',
            email: $form.find('[name="email"]').val() || '',
            phone: $form.find('[name="phone"]').val() || '',
            company: $form.find('[name="company"]').val() || '',
            subject: $form.find('[name="subject"]').val() || '',
            message: $form.find('[name="message"]').val() || '',
            website: honeypot, // Include honeypot value
        };

        // Submit via AJAX
        $.ajax({
            url: ehsContactForm.ajaxUrl,
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showMessage($form, response.data.message || 'Thank you! Your message has been sent.', 'success');
                    $form[0].reset();

                    // Reset Turnstile widget
                    if (hasTurnstile && typeof turnstile !== 'undefined') {
                        turnstile.reset();
                    }
                    turnstileToken = '';
                    $form.find('[name="turnstile_token"]').val('');
                } else {
                    showMessage($form, response.data.message || 'Sorry, there was an error sending your message. Please try again.', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('[EHS Contact Form] AJAX Error:', error);
                showMessage($form, 'Sorry, there was an error sending your message. Please try again.', 'error');
            },
            complete: function() {
                // Re-enable submit button
                $submitBtn.prop('disabled', false);
                $btnText.show();
                $btnLoader.hide();
            }
        });
    }

    /**
     * Initialize contact forms
     */
    function initContactForms() {
        // Bind submit handler to all contact forms
        $(document).on('submit', '.ehs-contact-form', handleFormSubmit);

        // Remove error class on input
        $(document).on('input', '.ehs-contact-form [required]', function() {
            $(this).removeClass('error');
        });
    }

    // Initialize on DOM ready
    $(document).ready(initContactForms);

    // Re-initialize for dynamically added forms (e.g., in modals)
    $(document).on('ehs:contactFormReady', function() {
        initContactForms();
    });

    // Modal integration helper
    window.ehsOpenContactModal = function(modalId) {
        const $modal = $('#' + modalId);
        if ($modal.length) {
            $modal.fadeIn(300);
            $('body').addClass('ehs-modal-open');

            // Trigger form ready event
            setTimeout(function() {
                $(document).trigger('ehs:contactFormReady');
            }, 100);
        }
    };

    window.ehsCloseContactModal = function(modalId) {
        const $modal = $('#' + modalId);
        if ($modal.length) {
            $modal.fadeOut(300);
            $('body').removeClass('ehs-modal-open');
        }
    };

    // Close modal on overlay click
    $(document).on('click', '.ehs-modal-overlay', function(e) {
        if ($(e.target).hasClass('ehs-modal-overlay')) {
            $(this).fadeOut(300);
            $('body').removeClass('ehs-modal-open');
        }
    });

    // Close modal on ESC key
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' && $('body').hasClass('ehs-modal-open')) {
            $('.ehs-modal-overlay').fadeOut(300);
            $('body').removeClass('ehs-modal-open');
        }
    });

})(jQuery);
