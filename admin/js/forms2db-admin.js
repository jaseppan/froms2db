jQuery( document ).ready(function($) {
	$('.forms2db-add-row').delegate('.button', 'click', function() {
		$(".forms2db-field-container:last").clone().find("input, textarea, select").val("").end().appendTo("#forms2db-fields");

	});

	$(document).on('click', '.forms2db-field-toggle span', function() {
		$(this).toggleClass('active');
		$(this).closest('.forms2db-field-container').toggleClass('active');
	});

	$(document).on('click', '.forms2db-field-delete span', function() {
		var r = confirm("Are sure!");
		if (r == true) {
			$(this).closest('.forms2db-field-container').remove();
		} 
	});

	$(document).on('change', '.type', function() {
		var checkboxes = ['checkbox', 'radio', 'select']; 

		if( inArray($(this).val(), checkboxes) ) {
			$('.options').removeClass('hidden');
		} else {
			$('.options').addClass('hidden');
		}
	});

	$(function() {
		$( "#forms2db-fields" ).sortable();
		$( "#forms2db-fields" ).disableSelection();
	});
});

function inArray(needle, haystack) {
    var length = haystack.length;
    for(var i = 0; i < length; i++) {
        if(haystack[i] == needle) return true;
    }
    return false;
}


