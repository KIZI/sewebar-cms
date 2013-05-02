$(document).ready(function () {
	function encodeCredentials(user, password) {
		var tok = user + ':' + password,
			hash = btoa(tok);
		
		return "Basic " + hash;
	}

	// registration
	$('button[name="reg"]').click(function () {
		$.ajax({
			type: "POST",
			url: 'miners',
			data: {
				type: $('#reg_type').val(),
				metabase: $('#reg_metabase').val(),
				server: $('#reg_server').val(),
				database: $('#reg_database').val(),
				username: $('#reg_username').val(),
				password: $('#reg_password').val()
			},
			dataType: 'text',
			headers: { "Authorization": encodeCredentials($('user_username'), $('user_password')) },
			complete: function (data) {
				var textarea = $('#reg_response');
				setStatusClass(textarea, data.status == 500 ? 'failure' : 'success');
				textarea.val(data.responseText);
			}
		}).done(function (data) {
			var xml = jQuery.parseXML(data),
				id = $(xml).contents().attr('id');

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
			type: "PATCH",
			url: 'miners/' + $('#import_id').val(),
			data: $('#import_request').val(),
			dataType: 'text',
			complete: function (data) {
				var textarea = $('#import_response');
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
			url: 'miners/' + $('#import_id').val() + '/tasks/task' + '?alias=' + $('#task_alias').val() + '&template=' + $('#task_template').val(),
			data: $('#task_request').val(),
			dataType: 'text',
			complete: function (data) {
				var textarea = $('#task_response');
				setStatusClass(textarea, data.status == 500 ? 'failure' : 'success');
				textarea.val(data.responseText);
			}
		});

		return false;
	});

	// proc pooler
	$('button[name="proc_pool"]').click(function () {
		$.ajax({
			type: "POST",
			url: 'miners/' + $('#import_id').val() + '/tasks/proc' + '?alias=' + $('#task_alias').val() + '&template=' + $('#task_template').val(),
			data: $('#task_request').val(),
			dataType: 'text',
			complete: function (data) {
				var textarea = $('#task_response');
				setStatusClass(textarea, data.status == 500 ? 'failure' : 'success');
				textarea.val(data.responseText);
			}
		});

		return false;
	});

	// grid pooler
	$('button[name="grid_pool"]').click(function () {
		$.ajax({
			type: "POST",
			url: 'miners/' + $('#import_id').val() + '/tasks/grid' + '?alias=' + $('#task_alias').val() + '&template=' + $('#task_template').val(),
			data: $('#task_request').val(),
			dataType: 'text',
			complete: function (data) {
				var textarea = $('#task_response');
				setStatusClass(textarea, data.status == 500 ? 'failure' : 'success');
				textarea.val(data.responseText);
			}
		});

		return false;
	});

	// task_pool_cancel
	$('button[name="task_pool_cancel"]').click(function () {
		$.ajax({
			type: "POST",
			url: 'miners/' + $('#cancel_id').val() + '/tasks/task/' + $('#cancel_task').val() + '/cancel',
			dataType: 'text',
			complete: function (data) {
				var textarea = $('#cancel_response');
				setStatusClass(textarea, data.status == 500 ? 'failure' : 'success');
				textarea.val(data.responseText);
			}
		});

		return false;
	});

	// proc_pool_cancel
	$('button[name="proc_pool_cancel"]').click(function () {
		$.ajax({
			type: "POST",
			url: 'miners/' + $('#cancel_id').val() + '/tasks/proc/' + $('#cancel_task').val() + '/cancel',
			dataType: 'text',
			complete: function (data) {
				var textarea = $('#cancel_response');
				setStatusClass(textarea, data.status == 500 ? 'failure' : 'success');
				textarea.val(data.responseText);
			}
		});

		return false;
	});

	// grid_pool_cancel
	$('button[name="grid_pool_cancel"]').click(function () {
		$.ajax({
			type: "POST",
			url: 'miners/' + $('#cancel_id').val() + '/tasks/grid/' + $('#cancel_task').val() + '/cancel',
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
			type: "GET",
			url: 'miners/' + $('#cancel_id').val() + '/dictionary',
			data: {
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

	$('h2').click(function () {
		var cls = $(this).attr('class'),
			form = 'form.' + cls;

		$(form).toggle(500);
	});
});

function setStatusClass(textarea, status) {
	textarea.removeClass();
	textarea.addClass(status);
}