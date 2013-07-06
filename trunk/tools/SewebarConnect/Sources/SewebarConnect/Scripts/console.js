$(document).ready(function () {
	function utf8ToB64(str) {
		return window.btoa(unescape(encodeURIComponent(str)));
	}

	function b64ToUtf8(str) {
		return decodeURIComponent(escape(window.atob(str)));
	}

	function encodeCredentials(user, password) {
		var tok = user + ':' + password,
			hash = utf8ToB64(tok);
		
		return "Basic " + hash;
	}

	function createHeaders() {
		var username = $('user_username').val(),
			password = $('user_password').val();

		if (username) {
			return {
				"Authorization": encodeCredentials(username, password)
			};
		}

		return null;
	}

	function xml2string(xml) {
		return (new XMLSerializer()).serializeToString(xml);
	}
	
	function appendXmlElement(root, parent, name, value) {
		var tag = root.createElement(name);

		tag.appendChild(root.createTextNode(value));

		parent.appendChild(tag);

		return tag;
	}

	function setStatusClass(textarea, status) {
		textarea.removeClass();
		textarea.addClass(status);
	}
	
	$('h2').click(function () {
		var cls = $(this).attr('class'),
			form = 'form.' + cls;

		$(form).toggle(500);
	});

	// registration
	$('button[name="reg"]').click(function () {
		var data = $.parseXML("<RegistrationRequest/>"),
			database = data.createElement('Connection'),
			databaseType = $('#reg_type').val(),
			metabase = $('#reg_metabase').val(),
			metabaseType = $('#reg_metabase_type').val(),
			m;
		
		data.firstChild.appendChild(database);

		database.setAttribute('type', databaseType);
		
		if (databaseType == 'MySQL') {
			appendXmlElement(data, database, 'Server', $('#reg_server').val());
			appendXmlElement(data, database, 'Database', $('#reg_database').val());
			appendXmlElement(data, database, 'Username', $('#reg_username').val());
			appendXmlElement(data, database, 'Password', $('#reg_password').val());
		} else {
			appendXmlElement(data, database, 'File', $('#reg_server').val());
		}
		
		if (metabase) {
			m = data.createElement('Metabase');
			
			m.setAttribute('type', metabaseType);
			appendXmlElement(data, m, 'File', metabase);

			data.firstChild.appendChild(m);
		}

		$.ajax({
			type: 'POST',
			url: 'miners',
			data: xml2string(data),
			contentType: 'text/xml',
			dataType: 'xml',
			cache: false,
			headers: createHeaders(),
			complete: function (response) {
				var textarea = $('#reg_response');
				setStatusClass(textarea, response.status == 200 ? 'success' : 'failure');
				textarea.val(response.responseText);
			}
		}).done(function (response) {
			var xml = response, // jQuery.parseXML(response),
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
			type: 'PUT',
			url: ['miners/', $('#import_id').val(), '/DataDictionary'].join(''),
			data: $('#import_request').val(),
			contentType: 'text/xml',
			dataType: 'xml',
			cache: false,
			headers: createHeaders(),
			complete: function (data) {
				var textarea = $('#import_response');
				setStatusClass(textarea, data.status == 200 ? 'success' : 'failure');
				textarea.val(data.responseText);
			}
		});

		return false;
	});

	// task pooler
	$('button[name="task_pool"]').click(function () {
		$.ajax({
			type: 'POST',
			url: [
				'miners/',
				$('#task_id').val(),
				'/tasks/task',
				'?alias=',
				$('#task_alias').val(),
				'&template=',
				$('#task_template').val()
			].join(''),
			data: $('#task_request').val(),
			contentType: 'text/xml',
			dataType: 'xml',
			cache: false,
			headers: createHeaders(),
			complete: function (data) {
				var textarea = $('#task_response');
				setStatusClass(textarea, data.status == 200 ? 'success' : 'failure');
				textarea.val(data.responseText);
			}
		});

		return false;
	});

	// proc pooler
	$('button[name="proc_pool"]').click(function () {
		$.ajax({
			type: 'POST',
			url: [
				'miners/',
				$('#task_id').val(),
				'/tasks/proc',
				'?alias=',
				$('#task_alias').val(),
				'&template=',
				$('#task_template').val()
			].join(''),
			data: $('#task_request').val(),
			contentType: 'text/xml',
			dataType: 'xml',
			cache: false,
			headers: createHeaders(),
			complete: function(data) {
				var textarea = $('#task_response');
				setStatusClass(textarea, data.status == 200 ? 'success' : 'failure');
				textarea.val(data.responseText);
			}
		});

		return false;
	});

	// grid pooler
	$('button[name="grid_pool"]').click(function () {
		$.ajax({
			type: 'POST',
			url: [
				'miners/',
				$('#task_id').val(),
				'/tasks/grid',
				'?alias=',
				$('#task_alias').val(),
				'&template=',
				$('#task_template').val()
			].join(''),
			data: $('#task_request').val(),
			contentType: 'text/xml',
			dataType: 'xml',
			cache: false,
			headers: createHeaders(),
			complete: function (data) {
				var textarea = $('#task_response');
				setStatusClass(textarea, data.status == 200 ? 'success' : 'failure');
				textarea.val(data.responseText);
			}
		});

		return false;
	});

	// task_pool_cancel
	$('button[name="task_pool_cancel"]').click(function () {
		var data = $.parseXML('<CancelationRequest></CancelationRequest>'),
			task = $('#cancel_task').val();

		$.ajax({
			type: 'PUT',
			url: [
				'miners/',
				$('#cancel_id').val(),
				'/tasks/task/',
				task
			].join(''),
			data: xml2string(data),
			contentType: 'text/xml',
			dataType: 'xml',
			cache: false,
			headers: createHeaders(),
			complete: function (response) {
				var textarea = $('#cancel_response');
				setStatusClass(textarea, response.status == 200 ? 'success' : 'failure');
				textarea.val(response.responseText);
			}
		});

		return false;
	});

	// proc_pool_cancel
	$('button[name="proc_pool_cancel"]').click(function () {
		var data = $.parseXML('<CancelationRequest></CancelationRequest>'),
			task = $('#cancel_task').val();

		$.ajax({
			type: 'PUT',
			url: [
				'miners/',
				$('#cancel_id').val(),
				'/tasks/proc/',
				task
			].join(''),
			contentType: 'text/xml',
			dataType: 'xml',
			data: xml2string(data),
			cache: false,
			headers: createHeaders(),
			complete: function (response) {
				var textarea = $('#cancel_response');
				setStatusClass(textarea, response.status == 200 ? 'success' : 'failure');
				textarea.val(response.responseText);
			}
		});

		return false;
	});

	// grid_pool_cancel
	$('button[name="grid_pool_cancel"]').click(function () {
		var data = $.parseXML('<CancelationRequest></CancelationRequest>'),
			task = $('#cancel_task').val();

		$.ajax({
			type: 'PUT',
			url: [
				'miners/',
				$('#cancel_id').val(),
				'/tasks/grid/',
				task
			].join(''),
			contentType: 'text/xml',
			dataType: 'xml',
			cache: false,
			data: xml2string(data),
			headers: createHeaders(),
			complete: function(response) {
				var textarea = $('#cancel_response');
				setStatusClass(textarea, response.status == 200 ? 'success' : 'failure');
				textarea.val(response.responseText);
			}
		});

		return false;
	});

	// export task
	$('button[name="b_export_task"]').click(function () {
		$.ajax({
			type: 'GET',
			url: [
				'miners/',
				$('#export_id').val(),
				'/tasks/',
				$('#export_task').val()
			].join(''),
			data: {
				alias: $('#export_alias').val(),
				template: $('#export_template').val()
			},
			contentType: 'text/xml',
			dataType: 'xml',
			cache: false,
			headers: createHeaders(),
			complete: function (data) {
				var textarea = $('#export_response');
				setStatusClass(textarea, data.status == 200 ? 'success' : 'failure');
				textarea.val(data.responseText);
			}
		});

		return false;
	});

	// export dd
	$('button[name="b_export_dd"]').click(function () {
		$.ajax({
			type: 'GET',
			url: 'miners/' + $('#export_id').val() + '/DataDictionary',
			data: {
				matrix: $('#export_matrix').val(),
				template: $('#export_template').val()
			},
			contentType: 'text/xml',
			dataType: 'xml',
			cache: false,
			headers: createHeaders(),
			complete: function (data) {
				var textarea = $('#export_response');
				setStatusClass(textarea, data.status == 200 ? 'success' : 'failure');
				textarea.val(data.responseText);
			}
		});

		return false;
	});
});