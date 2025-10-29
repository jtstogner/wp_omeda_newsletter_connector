( function( $ ) {
	"use strict";

	// Initialize dropdowns after the page has fully loaded
	$(document).ready(function() {
		// First run the standard dropdown initialization
		$('.ngl .ui.dropdown, .ngl-metabox .ui.dropdown').dropdown({ 
			onChange: function() { 
				if (typeof ngl_validate_form === 'function') {
					ngl_validate_form(); 
				}
			}
		});
		
		// Special handling for multiple select dropdowns
		$('.ui.dropdown.multiple').each(function() {
			var $dropdown = $(this);
			var inputName = $dropdown.find('input[type="hidden"]').attr('name');
			
			// Skip if no input name found
			if (!inputName) return;
			
			// Remove the brackets from the name for multiple selects
			var baseName = inputName.replace('[]', '');
			
			// Find all hidden inputs with this base name
			var values = [];
			$('input[type="hidden"][name="' + baseName + '[]"]').each(function() {
				var val = $(this).val();
				if (val && val !== '') {
					values.push(val);
				}
			});
			
			// If we have values, set them in the dropdown
			if (values.length > 0) {
				$dropdown.dropdown('set selected', values);
			}
		});
	});

} )( jQuery );
