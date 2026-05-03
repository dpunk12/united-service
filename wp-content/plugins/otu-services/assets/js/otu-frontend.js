/**
 * OTU Services — Frontend JavaScript
 * Handles: dashboard tab navigation, booking form validation,
 *          certificate download, service form visibility, AJAX submissions.
 */
/* global otu_params, jQuery */
(function ($) {
    'use strict';

    /* ============================================================
       Utility helpers
       ============================================================ */

    /**
     * Show an inline error on a field.
     * @param {jQuery} $field - The input/select element.
     * @param {string} msg    - Error message.
     */
    function showFieldError($field, msg) {
        $field.addClass('otu-input-error').css('border-color', '#c62828');
        var $err = $field.closest('.otu-form-row').find('.otu-field-error');
        if ($err.length) {
            $err.text(msg);
        }
    }

    /**
     * Clear an inline error on a field.
     * @param {jQuery} $field - The input/select element.
     */
    function clearFieldError($field) {
        $field.removeClass('otu-input-error').css('border-color', '');
        $field.closest('.otu-form-row').find('.otu-field-error').text('');
    }

    /**
     * Show a top-level message box inside a wrapper.
     * @param {jQuery} $wrap  - The container element.
     * @param {string} msg    - Message string.
     * @param {string} type   - 'success' or 'error'.
     */
    function showMessage($wrap, msg, type) {
        var cssClass = 'otu-' + (type || 'success') + '-message';
        var $box = $wrap.find('.otu-booking-messages, #otu-booking-messages, #otu-profile-messages');
        if (!$box.length) {
            $box = $('<div id="otu-messages"></div>');
            $wrap.prepend($box);
        }
        $box.html('<div class="' + cssClass + '">' + $('<span>').text(msg).html() + '</div>');
        $('html, body').animate({ scrollTop: $box.offset().top - 80 }, 300);
    }

    /**
     * Simple email format check.
     * @param  {string} email
     * @return {boolean}
     */
    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    /* ============================================================
       Dashboard Tab Navigation (no page reload)
       ============================================================ */
    function initDashboardTabs() {
        var $dashboard = $('#otu-dashboard');
        if (!$dashboard.length) { return; }

        $dashboard.on('click', '.otu-tab', function (e) {
            e.preventDefault();

            var tabId = $(this).data('tab');

            // Update button states.
            $dashboard.find('.otu-tab').removeClass('otu-tab-active').attr('aria-selected', 'false');
            $(this).addClass('otu-tab-active').attr('aria-selected', 'true');

            // Show corresponding panel.
            $dashboard.find('.otu-tab-content').removeClass('otu-tab-content-active');
            $('#otu-tab-' + tabId).addClass('otu-tab-content-active');

            // Update URL without reload (History API).
            if (window.history && window.history.pushState) {
                var url = new URL(window.location.href);
                url.searchParams.set('otu_tab', tabId);
                window.history.pushState({ otuTab: tabId }, '', url.toString());
            }

            // Animate progress bars when switching to the courses tab.
            if ('courses' === tabId) {
                animateProgressBars();
            }
        });

        // Handle browser back/forward.
        window.addEventListener('popstate', function (e) {
            if (e.state && e.state.otuTab) {
                $dashboard.find('.otu-tab[data-tab="' + e.state.otuTab + '"]').trigger('click');
            }
        });
    }

    /* ============================================================
       Progress Bar Animation
       ============================================================ */
    function animateProgressBars() {
        $('.otu-progress-bar').each(function () {
            var $bar     = $(this);
            var progress = $bar.data('progress') || 0;
            $bar.css('width', 0).animate({ width: progress + '%' }, 600);
        });
    }

    /* ============================================================
       Booking Form: Validation & AJAX Submission
       ============================================================ */
    function initBookingForm() {
        var $form = $('#otu-booking-form');
        if (!$form.length) { return; }

        // Prevent past dates in native date picker (belt-and-suspenders alongside min attr).
        var $dateField = $form.find('#otu_booking_date');
        if ($dateField.length) {
            var tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            var minDate = tomorrow.toISOString().split('T')[0];
            $dateField.attr('min', minDate);

            $dateField.on('change', function () {
                var selectedDate = new Date($(this).val());
                var today        = new Date();
                today.setHours(0, 0, 0, 0);
                if (selectedDate <= today) {
                    showFieldError($dateField, 'Please select a future date for your appointment.');
                    $(this).val('');
                } else {
                    clearFieldError($dateField);
                }
            });
        }

        // Inline validation on blur.
        $form.find('input, select, textarea').on('blur', function () {
            var $field = $(this);
            if ($field.prop('required') && !$field.val().trim()) {
                var label = $field.closest('.otu-form-row').find('label').text().replace('*', '').trim();
                showFieldError($field, label + ' is required.');
            } else {
                clearFieldError($field);
            }
        });

        $form.on('submit', function (e) {
            e.preventDefault();

            var isValid   = true;
            var $required = $form.find('[required]');

            $required.each(function () {
                if (!$(this).val().trim()) {
                    var label = $(this).closest('.otu-form-row').find('label').text().replace('*', '').trim();
                    showFieldError($(this), label + ' is required.');
                    isValid = false;
                } else {
                    clearFieldError($(this));
                }
            });

            var $emailField = $form.find('#otu_booking_email');
            if ($emailField.length && $emailField.val() && !isValidEmail($emailField.val())) {
                showFieldError($emailField, 'Please enter a valid email address.');
                isValid = false;
            }

            if (!isValid) { return; }

            var $submit  = $form.find('.otu-booking-submit');
            var $spinner = $form.find('.otu-spinner');

            $submit.prop('disabled', true);
            $spinner.show();

            var formData     = $form.serialize();
            var ajaxurl      = (typeof otu_params !== 'undefined') ? otu_params.ajaxurl : '/wp-admin/admin-ajax.php';

            $.ajax({
                url     : ajaxurl,
                type    : 'POST',
                dataType: 'json',
                data    : formData + '&action=otu_submit_booking',
                success : function (response) {
                    $submit.prop('disabled', false);
                    $spinner.hide();

                    if (response.success) {
                        $form.find('input:not([type=hidden]), select, textarea').val('');
                        showMessage($form.closest('.otu-booking-form-wrap'), response.data.message, 'success');
                        $form.slideUp(400);
                    } else {
                        var messages = (response.data && response.data.messages) ? response.data.messages : ['An error occurred.'];
                        showMessage($form.closest('.otu-booking-form-wrap'), messages.join('<br>'), 'error');
                    }
                },
                error: function () {
                    $submit.prop('disabled', false);
                    $spinner.hide();
                    showMessage($form.closest('.otu-booking-form-wrap'), 'A network error occurred. Please try again.', 'error');
                }
            });
        });
    }

    /* ============================================================
       Certificate Download Handler
       ============================================================ */
    function initCertificateDownload() {
        $(document).on('click', '.otu-download-certificate', function (e) {
            // These are regular anchor links — allow default navigation.
            // This handler adds a brief visual feedback.
            var $btn = $(this);
            var originalText = $btn.text();
            $btn.text('Preparing download…');
            setTimeout(function () {
                $btn.text(originalText);
            }, 3000);
        });
    }

    /* ============================================================
       Profile Form: AJAX Update
       ============================================================ */
    function initProfileForm() {
        var $form = $('#otu-profile-form');
        if (!$form.length) { return; }

        $form.on('submit', function (e) {
            e.preventDefault();

            var $submit  = $form.find('[type=submit]');
            var $spinner = $form.find('.otu-spinner');
            var $msgs    = $('#otu-profile-messages');

            $submit.prop('disabled', true);
            $spinner.show();
            $msgs.html('');

            var ajaxurl = (typeof otu_params !== 'undefined') ? otu_params.ajaxurl : '/wp-admin/admin-ajax.php';

            $.ajax({
                url     : ajaxurl,
                type    : 'POST',
                dataType: 'json',
                data    : $form.serialize(),
                success : function (response) {
                    $submit.prop('disabled', false);
                    $spinner.hide();

                    if (response.success) {
                        $msgs.html('<div class="otu-success-message">' + $('<span>').text(response.data.message).html() + '</div>');
                    } else {
                        var msg = (response.data && response.data.message) ? response.data.message : 'Update failed.';
                        $msgs.html('<div class="otu-error-message">' + $('<span>').text(msg).html() + '</div>');
                    }
                },
                error: function () {
                    $submit.prop('disabled', false);
                    $spinner.hide();
                    $msgs.html('<div class="otu-error-message">A network error occurred. Please try again.</div>');
                }
            });
        });
    }

    /* ============================================================
       Service Form Show/Hide based on service type meta
       (For dynamic service-type switching on admin or custom pages)
       ============================================================ */
    function initServiceFormVisibility() {
        // This is mainly handled server-side, but we provide JS hooks
        // for any themes that dynamically set a service type selector.
        var $typeSelect = $('#otu-service-type-selector');
        if (!$typeSelect.length) { return; }

        function showForm(type) {
            $('.otu-service-form').hide();
            if (type) {
                $('.otu-' + type + '-form').show();
            }
        }

        $typeSelect.on('change', function () {
            showForm($(this).val());
        });

        // Run on load.
        showForm($typeSelect.val());
    }

    /* ============================================================
       Inline Form Validation for Service Forms
       ============================================================ */
    function initServiceFormValidation() {
        $('.otu-service-form').each(function () {
            var $form = $(this).closest('form');
            if (!$form.length) { return; }

            $form.on('blur', '.otu-service-form [required]', function () {
                var $field = $(this);
                if (!$field.val().trim()) {
                    var label = $field.closest('.otu-form-row').find('label').text().replace('*', '').trim();
                    showFieldError($field, label + ' is required.');
                } else {
                    clearFieldError($field);
                }
            });

            $form.on('change', '.otu-service-form [required]', function () {
                clearFieldError($(this));
            });
        });
    }

    /* ============================================================
       Animate progress bars on initial page load
       ============================================================ */
    function initProgressBars() {
        // Only animate bars that are visible immediately.
        $('.otu-tab-content-active .otu-progress-bar').each(function () {
            var $bar     = $(this);
            var progress = $bar.data('progress') || 0;
            var current  = parseInt($bar.css('width'), 10);
            // Don't re-animate if already set.
            if (current > 0) { return; }
            $bar.css('width', 0).delay(200).animate({ width: progress + '%' }, 700);
        });
    }

    /* ============================================================
       DOM Ready
       ============================================================ */
    $(function () {
        initDashboardTabs();
        initProgressBars();
        initBookingForm();
        initCertificateDownload();
        initProfileForm();
        initServiceFormVisibility();
        initServiceFormValidation();
    });

}(jQuery));
