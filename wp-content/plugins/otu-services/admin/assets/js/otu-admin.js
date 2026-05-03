/**
 * OTU Services — Admin JavaScript
 * Handles: settings tab switching, booking status AJAX update, datepicker init.
 */
/* global otu_admin_params, jQuery */
(function ($) {
    'use strict';

    /* ============================================================
       Settings Tab Switching
       ============================================================ */
    function initSettingsTabs() {
        var $tabs    = $('.otu-settings-tabs .nav-tab');
        var $content = $('.otu-settings-form');

        if (!$tabs.length || !$content.length) {
            return;
        }

        $tabs.on('click', function (e) {
            var href = $(this).attr('href');
            // Let WordPress handle navigation — tabs are anchor links.
            // This is here for any JS-only override if needed in future.
        });
    }

    /* ============================================================
       Booking Status AJAX Update
       ============================================================ */
    function initBookingStatusUpdate() {
        $(document).on('change', '.otu-booking-status-select', function () {
            var $select    = $(this);
            var bookingId  = $select.data('booking-id');
            var newStatus  = $select.val();
            var nonce      = $select.data('nonce') || (typeof otu_admin_params !== 'undefined' ? otu_admin_params.nonce : '');
            var $row       = $select.closest('tr');
            var $notice    = $row.find('.otu-admin-notice');

            if (!$notice.length) {
                $row.find('td:last').append('<span class="otu-admin-notice"></span>');
                $notice = $row.find('.otu-admin-notice');
            }

            $notice.removeClass('success error').text('Saving…');

            $.ajax({
                url     : otu_admin_params.ajaxurl,
                type    : 'POST',
                dataType: 'json',
                data    : {
                    action    : 'otu_update_booking_status',
                    booking_id: bookingId,
                    status    : newStatus,
                    nonce     : nonce
                },
                success: function (response) {
                    if (response.success) {
                        $notice.addClass('success').text('✓ Saved');
                        $row.attr('data-status', newStatus);
                        setTimeout(function () {
                            $notice.fadeOut(400, function () {
                                $(this).text('').show();
                            });
                        }, 2000);
                    } else {
                        var msg = (response.data && response.data.message) ? response.data.message : 'Error';
                        $notice.addClass('error').text('✗ ' + msg);
                        $select.val($select.data('prev-status') || newStatus);
                    }
                },
                error: function () {
                    $notice.addClass('error').text('✗ Request failed');
                }
            });
        });

        // Cache current status so we can restore on failure.
        $(document).on('focus', '.otu-booking-status-select', function () {
            $(this).data('prev-status', $(this).val());
        });
    }

    /* ============================================================
       Certificate Regeneration AJAX
       ============================================================ */
    function initCertRegeneration() {
        $(document).on('click', '.otu-regen-cert', function (e) {
            e.preventDefault();

            var $btn   = $(this);
            var certId = $btn.data('cert-id');
            var nonce  = $btn.data('nonce') || (typeof otu_admin_params !== 'undefined' ? otu_admin_params.nonce : '');

            if (!confirm('Regenerate the PDF for this certificate? The current PDF (if any) will be replaced.')) {
                return;
            }

            $btn.prop('disabled', true).text('Regenerating…');

            $.ajax({
                url     : otu_admin_params.ajaxurl,
                type    : 'POST',
                dataType: 'json',
                data    : {
                    action : 'otu_regenerate_certificate',
                    cert_id: certId,
                    nonce  : nonce
                },
                success: function (response) {
                    if (response.success) {
                        $btn.text('✓ Done');
                        setTimeout(function () {
                            location.reload();
                        }, 1200);
                    } else {
                        var msg = (response.data && response.data.message) ? response.data.message : 'Error';
                        alert('Error: ' + msg);
                        $btn.prop('disabled', false).text('Regenerate PDF');
                    }
                },
                error: function () {
                    alert('Request failed. Please try again.');
                    $btn.prop('disabled', false).text('Regenerate PDF');
                }
            });
        });
    }

    /* ============================================================
       jQuery UI Datepicker for Booking Dates
       ============================================================ */
    function initDatepicker() {
        if (typeof $.fn.datepicker === 'undefined') {
            return;
        }

        $('input[type="date"].otu-datepicker, .otu-admin-datepicker').datepicker({
            dateFormat : 'yy-mm-dd',
            minDate    : '+1d',
            changeMonth: true,
            changeYear : true,
            showAnim   : 'fadeIn'
        });
    }

    /* ============================================================
       DOM Ready
       ============================================================ */
    $(function () {
        initSettingsTabs();
        initBookingStatusUpdate();
        initCertRegeneration();
        initDatepicker();
    });

}(jQuery));
