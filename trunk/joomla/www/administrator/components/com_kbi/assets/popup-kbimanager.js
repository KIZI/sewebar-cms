var KbiManager = {
	initialize: function()
	{
		o = this._getUriObject(window.self.location.href);
		//console.log(o);
		q = $H(this._getQueryObject(o.query));
		this.editor = decodeURIComponent(q.get('e_name'));

		//frame
		//this.frame		= window.frames['imageframe'];
		//this.frameurl	= this.frame.location.href;
	},

	onOk: function()
	{
		var dynamic = document.getElementById("dynamic1").checked;
		var source = document.getElementById("sources").value; // Get selected source
		var query = document.getElementById("query").value; // Get selected query
		var xslt = document.getElementById("xslt").value; // Get selected xslt
		var parameters = document.getElementById("parameters").value;

		if(dynamic)	{
			// TODO: semidynamic - dynamicka data, ale staticky dotaz (zdroj v JSON...)
			var url = '/administrator/index.php?option=com_kbi&controller=selector&task=serialize&format=raw';
		} else {
			var url = '/index.php?option=com_kbi&task=query&format=raw';
		}

		var myAjax = new Ajax(url,
			{
				method: 'post',
				data:
				{
					source: source,
					query: query,
					xslt: xslt
				},
				onSuccess: function(response){
					window.parent.jInsertEditorText(response, 'text');
				}
			}).request();

		/*url += '&source=' + source;
		url += '&query=' + query;
		url += '&xslt=' + xslt;
		url += '&parameters=' + escape(parameters); */

		return false;
	},

	refreshFrame: function()
	{
		this._setFrameUrl();
	},

	_setFrameUrl: function(url)
	{
		if ($chk(url)) {
			this.frameurl = url;
		}
		this.frame.location.href = this.frameurl;
	},

	_getQueryObject: function(q) {
		var vars = q.split(/[&;]/);
		var rs = {};
		if (vars.length) vars.each(function(val) {
			var keys = val.split('=');
			if (keys.length && keys.length == 2) rs[encodeURIComponent(keys[0])] = encodeURIComponent(keys[1]);
		});
		return rs;
	},

	_getUriObject: function(u){
		var bits = u.match(/^(?:([^:\/?#.]+):)?(?:\/\/)?(([^:\/?#]*)(?::(\d*))?)((\/(?:[^?#](?![^?#\/]*\.[^?#\/.]+(?:[\?#]|$)))*\/?)?([^?#\/]*))?(?:\?([^#]*))?(?:#(.*))?/);
		return (bits)
			? bits.associate(['uri', 'scheme', 'authority', 'domain', 'port', 'path', 'directory', 'file', 'query', 'fragment'])
			: null;
	}
}

window.addEvent('domready', function(){
	KbiManager.initialize();
});