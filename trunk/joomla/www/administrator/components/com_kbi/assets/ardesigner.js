/**
 * selector.php
 */
function getRulesSelector(data) {
	var url = '/administrator/index.php?option=com_kbi&controller=selector&task=params&format=raw';
	var params = data;
	var query = $$('#query');
	var params_raw = $$('#parameter_raw div.text');
	params_raw.empty();
	params_raw.appendText(data);

	$$('#parameters').empty().addClass('ajax-loading');

	new Ajax(url + '&id_query=' + query.getValue(), {
		method: 'post',
		//update: $('someelement'),
		data: {data: params},
		onComplete: function() {
			var params = $$('#parameters');
			params.removeClass('ajax-loading');
			params.appendText(this.response.text);
		}
	}).request();
}

/**
 * queries.php
 */
function getRulesQueries(data) {
	var url = '/administrator/index.php?option=com_kbi&controller=selector&task=params&format=raw';
	var params = data;
	var form = document.forms['adminForm'];
	var qid = form.elements['id'];
	var query = $$('#query');

	query.empty().addClass('ajax-loading');

	new Ajax(url + '&id_query=' + qid.value, {
		method: 'post',
		//update: $('someelement'),
		data: {data: params},
		onComplete: function() {
			var query = $$('#query');
			query.removeClass('ajax-loading');
			query.appendText(this.response.text);
		}
	}).request();
}

function getRules(data) {
	alert(data);
}