jQuery( document ).ready(function($) {
	$('.forms2db-add-row').click(function() {
		$(".forms2db-field-container:last").clone().find("input, textarea, select").val("").end().appendTo("#forms2db-fields");
	});
});
