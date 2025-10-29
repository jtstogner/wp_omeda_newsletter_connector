jQuery(document).ready(function($) {
    'use strict';

    // Enable schedule button when checkbox is checked
    $('#omeda-schedule-confirm').on('change', function() {
        $('#omeda-schedule-btn').prop('disabled', !$(this).is(':checked'));
    });

    // Send Test Email
    $('#omeda-send-test-btn').on('click', function(e) {
        e.preventDefault();
        var $btn = $(this);
        var originalText = $btn.html();
        
        $btn.prop('disabled', true).html('<span class="dashicons dashicons-update spin"></span> Sending...');

        $.ajax({
            url: omedaAdmin.ajax_url,
            type: 'POST',
            data: {
                action: 'omeda_send_test',
                post_id: omedaAdmin.post_id,
                nonce: omedaAdmin.nonce
            },
            success: function(response) {
                if (response.success) {
                    alert('Test email sent successfully!');
                    location.reload(); // Reload to update UI
                } else {
                    alert('Error: ' + (response.data.message || 'Unknown error'));
                    $btn.prop('disabled', false).html(originalText);
                }
            },
            error: function() {
                alert('Request failed. Please try again.');
                $btn.prop('disabled', false).html(originalText);
            }
        });
    });

    // Schedule Deployment
    $('#omeda-schedule-btn').on('click', function(e) {
        e.preventDefault();
        var $btn = $(this);
        var originalText = $btn.html();
        var scheduleDate = $('#omeda-schedule-date').val();

        if (!scheduleDate) {
            alert('Please select a deployment date and time.');
            return;
        }

        // Convert local datetime to UTC
        var localDate = new Date(scheduleDate);
        var utcDate = new Date(localDate.getTime() + (localDate.getTimezoneOffset() * 60000));
        var utcString = utcDate.getUTCFullYear() + '-' +
            String(utcDate.getUTCMonth() + 1).padStart(2, '0') + '-' +
            String(utcDate.getUTCDate()).padStart(2, '0') + ' ' +
            String(utcDate.getUTCHours()).padStart(2, '0') + ':' +
            String(utcDate.getUTCMinutes()).padStart(2, '0');

        if (confirm('Schedule deployment for ' + utcString + ' UTC?')) {
            $btn.prop('disabled', true).html('<span class="dashicons dashicons-update spin"></span> Scheduling...');

            $.ajax({
                url: omedaAdmin.ajax_url,
                type: 'POST',
                data: {
                    action: 'omeda_schedule_deployment',
                    post_id: omedaAdmin.post_id,
                    schedule_date: utcString,
                    nonce: omedaAdmin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        alert('Deployment scheduled successfully!');
                        location.reload();
                    } else {
                        alert('Error: ' + (response.data.message || 'Unknown error'));
                        $btn.prop('disabled', false).html(originalText);
                    }
                },
                error: function() {
                    alert('Request failed. Please try again.');
                    $btn.prop('disabled', false).html(originalText);
                }
            });
        }
    });

    // Unschedule Deployment
    $('#omeda-unschedule-btn').on('click', function(e) {
        e.preventDefault();
        
        if (!confirm('Are you sure you want to unschedule this deployment?')) {
            return;
        }

        var $btn = $(this);
        var originalText = $btn.html();
        
        $btn.prop('disabled', true).html('<span class="dashicons dashicons-update spin"></span> Unscheduling...');

        $.ajax({
            url: omedaAdmin.ajax_url,
            type: 'POST',
            data: {
                action: 'omeda_unschedule_deployment',
                post_id: omedaAdmin.post_id,
                nonce: omedaAdmin.nonce
            },
            success: function(response) {
                if (response.success) {
                    alert('Deployment unscheduled successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + (response.data.message || 'Unknown error'));
                    $btn.prop('disabled', false).html(originalText);
                }
            },
            error: function() {
                alert('Request failed. Please try again.');
                $btn.prop('disabled', false).html(originalText);
            }
        });
    });

    // Add CSS for spinning animation
    $('<style>')
        .prop('type', 'text/css')
        .html('@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } } .spin { animation: spin 1s linear infinite; display: inline-block; }')
        .appendTo('head');
});
