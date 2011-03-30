function getRules(data) {
	var url = '/administrator/index.php?option=com_kbi&controller=selector&task=params&format=raw';
	var params = data;
	var form = document.forms['adminForm'];

	if(form) {
		var qid = form.elements['id'].value;
		var query = $$('#query');
		var loader = $$('#query');
	} else {
		var qid = $$('#query').getValue();
		var query = $$('#parameters');
		var loader = $$('#parameters');
		var params_raw = $$('#parameter_raw div.text');

		params_raw.empty().appendText(data);
	}

	query.appendText(data);

	/*
	Transformace se vykonava az pri vykonavani Query.
	loader.empty().addClass('ajax-loading');

	new Ajax(url + '&id_query=' + qid, {
		method: 'post',
		//update: $('someelement'),
		data: {data: params},
		onComplete: function() {
			loader.removeClass('ajax-loading');
			query.appendText(this.response.text);
		}
	}).request();
	*/
}