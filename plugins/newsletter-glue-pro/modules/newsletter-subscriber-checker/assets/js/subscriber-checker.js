// Wait for the DOM to be fully loaded
jQuery(document).ready(function($) {
    console.log('Newsletter Subscriber Checker script loaded');
    
    // Find all cells in the Newsletter Check column
    var cells = document.querySelectorAll('td.column-ngl_newsletter_check');
    console.log('Found ' + cells.length + ' newsletter check cells');
    
    cells.forEach(function(cell) {
        var content = cell.textContent.trim();
        
        // Check if this is our subscriber check format
        if (content.startsWith('ngl_check_')) {
            console.log('Found subscriber check data:', content);
            
            // Extract the subscriber ID and email
            var parts = content.split('_');
            if (parts.length >= 3) {
                var subscriberId = parts[2];
                // The email is everything after the subscriber ID
                var email = content.substring(content.indexOf('_', content.indexOf('_') + 1) + 1);
                email = email.substring(email.indexOf('_') + 1);
                
                console.log('Extracted subscriber ID:', subscriberId, 'and email:', email);
                
                // Create a button element
                var button = document.createElement('button');
                button.className = 'button nglsc-check-subscriber-button';
                button.textContent = 'Check Subscriber';
                button.setAttribute('data-subscriber-id', subscriberId);
                button.setAttribute('data-email', email);
                button.onclick = function(e) {
                    e.preventDefault();
                    openSubscriberCheckModal(this);
                    return false;
                };
                
                // Clear the cell and append the button
                cell.innerHTML = '';
                cell.appendChild(button);
            }
        }
    });

    // Create modal container if it doesn't exist
    if (!document.getElementById('nglsc-subscriber-check-modal')) {
        var modalHtml = `
            <div id="nglsc-subscriber-check-modal" class="nglsc-subscriber-modal">
                <div class="nglsc-subscriber-modal-content">
                    <span class="nglsc-subscriber-modal-close">&times;</span>
                    <h2>Subscriber Check</h2>
                    <div id="nglsc-subscriber-check-result">
                        <p>Checking subscriber status...</p>
                    </div>
                </div>
            </div>
        `;
        $('body').append(modalHtml);

        // Close modal when clicking the X
        $('.nglsc-subscriber-modal-close').on('click', function() {
            $('#nglsc-subscriber-check-modal').hide();
        });

        // Close modal when clicking outside of it
        $(window).on('click', function(event) {
            if ($(event.target).is('#nglsc-subscriber-check-modal')) {
                $('#nglsc-subscriber-check-modal').hide();
            }
        });
    }

    // Global function to open the subscriber check modal
    window.openSubscriberCheckModal = function(buttonElement) {
        // Get data from the button
        var subscriberId = buttonElement.getAttribute('data-subscriber-id');
        var email = buttonElement.getAttribute('data-email');
        
        console.log('Opening modal for subscriber:', email);
        
        // Show the modal
        $('#nglsc-subscriber-check-modal').show();
        
        // Update content with loading message
        $('#nglsc-subscriber-check-result').html('<p>Checking subscriber status for ' + email + '...</p>');
        
        // Make AJAX request to check subscriber
        $.ajax({
            url: ngl_subscriber_checker.ajax_url,
            type: 'POST',
            data: {
                action: 'ngl_check_subscriber',
                email: email,
                nonce: ngl_subscriber_checker.nonce
            },
            success: function(response) {
                if (response.success) {
                    $('#nglsc-subscriber-check-result').html('<div class="nglsc-subscriber-result">' + response.data + '</div>');
                } else {
                    $('#nglsc-subscriber-check-result').html('<div class="nglsc-subscriber-error">Error: ' + response.data + '</div>');
                }
            },
            error: function() {
                $('#nglsc-subscriber-check-result').html('<div class="nglsc-subscriber-error">Error checking subscriber. Please try again.</div>');
            }
        });
    }
});
