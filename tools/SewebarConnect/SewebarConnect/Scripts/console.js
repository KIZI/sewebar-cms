$(document).ready(function () {
	// registration
	$('button[name="reg"]').click(function () {
		$.ajax({
			type: "POST",
			url: "Application/Register",
			data: {
				type: $('#reg_type').val(),
				metabase: $('#reg_metabase').val(),
				server: $('#reg_server').val(),
				database: $('#reg_database').val(),
				username: $('#reg_username').val(),
				password: $('#reg_password').val()
			},
			dataType: 'text'
		}).done(function (data) {
			var xml = jQuery.parseXML(data),
				id = $(xml).contents().attr('id'),
				textarea = $('textarea[name="reg_response"]'),
				status = $(xml).contents().attr('status');

			textarea.removeClass();
			textarea.addClass(status);
			textarea.val(data);

			if (id) {
				$('#import_id').val(id);
				$('#task_id').val(id);
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

	// task 4ft
	$('button[name="task_4ft"]').click(function () {
		$.ajax({
			type: "POST",
			url: "Task/Run",
			data: {
				guid: $('#task_id').val(),
				content: $('#task_request').val()
			},
			dataType: 'text'
		}).done(function (data) {
			$('#task_response').val(data);
		});

		return false;
	});

	// task pooler
	$('button[name="task_pool"]').click(function () {
		$.ajax({
			type: "POST",
			url: "Task/Pool",
			data: {
				guid: $('#task_id').val(),
				content: $('#task_request').val()
			},
			dataType: 'text'
		}).done(function (data) {
			$('#task_response').val(data);
		});

		return false;
	});
});