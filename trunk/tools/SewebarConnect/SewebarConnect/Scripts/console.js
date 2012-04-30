$(document).ready(function () {
	// registration
	$('button[name="reg"]').click(function () {
		$.ajax({
			type: "POST",
			url: "Application/Register",
			data: jQuery.parseJSON($('textarea[name="reg_request"]').val()),
			dataType: 'text'
		}).done(function (data) {
			var xml = jQuery.parseXML(data),
				id = $(xml).contents().attr('id');

			$('textarea[name="reg_response"]').val(data);

			if (id) {
				$('#import_id').val(id);
			}
		});

		return false;
	});

	// import
	$('button[name="import"]').click(function () {
		$.ajax({
			type: "POST",
			url: "DataDictionary/Import",
			data: {
				guid: $('#import_id').val(),
				content: $('#import_request').val()
			},
			dataType: 'text'
		}).done(function (data) {
			$('#import_response').val(data);
		});

		return false;
	});

	// ajax links
	$('.ajax').click(function () {
		$.ajax({
			url: $(this).attr('href')
		});

		return false;
	});
});