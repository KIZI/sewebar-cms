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

			setStatusClass(textarea, status);

			textarea.val(data);

			if (id) {
				$('#import_id').val(id);
				$('#task_id').val(id);
				$('#cancel_id').val(id);
				$('#export_id').val(id);
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
			dataType: 'text',
			complete: function (data) {
				var textarea = $('#import_response');
				setStatusClass(textarea, data.status == 500 ? 'failure' : 'success');
				textarea.val(data.responseText);
			}
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
				content: $('#task_request').val(),
				alias: $('#task_alias').val(),
				template: $('#task_template').val()
			},
			dataType: 'text',
			complete: function (data) {
				var textarea = $('#task_response');
				setStatusClass(textarea, data.status == 500 ? 'failure' : 'success');
				textarea.val(data.responseText);
			}
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
				content: $('#task_request').val(),
				alias: $('#task_alias').val(),
				template: $('#task_template').val()
			},
			dataType: 'text',
			complete: function (data) {
				var textarea = $('#task_response');
				setStatusClass(textarea, data.status == 500 ? 'failure' : 'success');
				textarea.val(data.responseText);
			}
		});

		return false;
	});

	// cancel
	$('button[name="cancel"]').click(function () {
		$.ajax({
			type: "POST",
			url: "Task/Cancel",
			data: {
				guid: $('#cancel_id').val(),
				taskName: $('#cancel_task').val()
			},
			dataType: 'text',
			complete: function (data) {
				var textarea = $('#cancel_response');
				setStatusClass(textarea, data.status == 500 ? 'failure' : 'success');
				textarea.val(data.responseText);
			}
		});

		return false;
	});

	// Exporter
	$('button[name="export"]').click(function () {
		$.ajax({
			type: "POST",
			url: "DataDictionary/Export",
			data: {
				guid: $('#export_id').val(),
				//content: $('#export_request').val(),
				matrix: $('#export_matrix').val(),
				template: $('#export_template').val()
			},
			dataType: 'text',
			complete: function (data) {
				var textarea = $('#export_response');
				setStatusClass(textarea, data.status == 500 ? 'failure' : 'success');
				textarea.val(data.responseText);
			}
		});

		return false;
	});
});

function setStatusClass(textarea, status) {
	textarea.removeClass();
	textarea.addClass(status);
}