jQuery( document ).ready(function($) {
	$('.forms2db-add-row').delegate('.button', 'click', function() {
		$(".forms2db-field-container:last").clone().find("input, textarea, select").val("").end().appendTo("#forms2db-fields");

	});

	$(document).on('click', '.forms2db-field-toggle span', function() {
		$(this).toggleClass('active');
		$(this).closest('.forms2db-field-container').toggleClass('active');
	});

	$(document).on('focus', '.forms2db-fields-row input, .forms2db-fields-row select, .forms2db-fields-row textarea', function() {
		console.log($(this).parent().parent().parent().prev().find('.forms2db-field-toggle'));
		
		$(this).parent().parent().parent().prev().find('.forms2db-field-toggle span').addClass('active');
		$(this).closest('.forms2db-field-container').addClass('active');
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
			$( this ).closest('.forms2db-field-container').find('.options').removeClass('hidden');
		} else {
			$( this ).closest('.forms2db-field-container').find('.options').addClass('hidden');
		}
	});

	/**
	 * Sorting fields
	 */

	$(function() {
		$( "#forms2db-fields" ).sortable();
		$( "#forms2db-fields" ).disableSelection();
	});

	/**
	 * Copy shortcode
	 */

	 $("#form2db-form-shortcode-conteiner").click(function() {
		
		copyClipboard("form2db-form-shortcode", "Shortcode copied to clipboard");
		  
	 });

});

function inArray(needle, haystack) {
    var length = haystack.length;
    for(var i = 0; i < length; i++) {
        if(haystack[i] == needle) return true;
    }
    return false;
}

function copyClipboard(elem_id, message) {
	var elm = document.getElementById(elem_id);
	// for Internet Explorer
  
	if(document.body.createTextRange) {
	  var range = document.body.createTextRange();
	  range.moveToElementText(elm);
	  range.select();
	  document.execCommand("Copy");
	  alert(message);
	}
	else if(window.getSelection) {
	  // other browsers
  
	  var selection = window.getSelection();
	  var range = document.createRange();
	  range.selectNodeContents(elm);
	  selection.removeAllRanges();
	  selection.addRange(range);
	  document.execCommand("Copy");
	  alert(message);
	}
}
