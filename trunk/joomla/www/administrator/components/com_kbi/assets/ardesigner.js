function getRules(data) {
	var url = '/administrator/index.php?option=com_kbi&controller=selector&task=params&format=raw';
	var params = data;
	var form = document.forms['adminForm'];

	if(form) {
		var query = $$('#query');
		var qid = form.elements['id'].value;
		var loader = $$('#query');
	} else {
		var query = $$('#query');
		var qid = query.getValue();
		var loader = $$('#parameters');
		var params_raw = $$('#parameter_raw div.text');

		params_raw.empty().appendText(data);
	}

	loader.empty().addClass('ajax-loading');

	new Ajax(url + '&id_query=' + qid, {
		method: 'post',
		//update: $('someelement'),
		data: {data: params},
		onComplete: function() {
			var query = $$('#query');
			loader.removeClass('ajax-loading');
			query.appendText(this.response.text);
		}
	}).request();
}