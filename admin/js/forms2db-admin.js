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
		if( $('.forms2db-field-container').length > 1 ) {
			var r = confirm("Are sure!");
			if (r == true) {
				$(this).closest('.forms2db-field-container').remove();
			} 
		} else {
			alert('You can not delete the last field.')
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

	 formFieldList();

	 function formFieldList() {
		$('.forms2db-field-name-list')
			.html( $('.field-name-field').map(function() {
				return formFieldShorcode( $( this ).val(), $( '#_forms2db_admin_message' ).text() );
			})
			.get()
			.join( " " ) );
	 }

	 function formFieldShorcode(text, textarea) {

		var shortcode = '[' + text + ']';
		if( textarea.indexOf( shortcode ) > -1 ) {
			var shortcodeClass = "added-field-shortcode";
		} else {
			var shortcodeClass = "add-field-shortcode";
		}
		var shortcodeButton = '<span class="' + shortcodeClass + '">' + shortcode + '</span>';
		return shortcodeButton;
	 }

	 $('.add-field-shortcode').click(function() {
		var shortcode = $( this ).html();
		includeShortcode('_forms2db_admin_message', shortcode);
		$( this ).addClass('added-field-shortcode').removeClass('added-field-shortcode');
	 });

	function includeShortcode(id, text) {
		var originalContent = $( '#' + id).val();
		var cursorPosition = forms2fbCursorPosition(id);
		if( cursorPosition == false ) {
			var updatedText = originalContent + text;
		} else {
			var textStart = originalContent.substr( 0, cursorPosition );
			var textEnd = originalContent.substr( cursorPosition );	
			var updatedText = textStart + text + textEnd;
		}
		$('#' + id).val(updatedText);
	}

	

});

function forms2fbCursorPosition(id) {
	var content = document.getElementById(id);
	if((content.selectionStart != null) && (content.selectionStart != undefined) && (content.selectionStart > 0)){
		var position = content.selectionStart;
		return position;
	}
	else {
		return false;
	}
}

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
