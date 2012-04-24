var URLHelper = new Class({
	
	initialize: function () {},
	
	getURL: function () {
		return window.location.href;
	},
	
	getURLParams: function (url) {
		url = url || this.getURL();
		
		if (url.indexOf('?') !== -1) {
			return url.slice(url.indexOf('?') + 1);
		}
		
		return null;
	},
	
	getImagePath: function (image) {
		var matches = this.getURL().match('http://[a-z]{1,}(/arbuilder)');
		var path = matches[1] + '/web/images/' + image;
		
		return path;
	}
	
});