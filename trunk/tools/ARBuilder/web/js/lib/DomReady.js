var DomReady = new Class({

	initialize: function () {
		window.addEvent('domready', function () {
			this.ready();
		}.bind(this));
	},
		
	ready: function () {
		var nativeTypeExtender = new NativeTypeExtender();
		nativeTypeExtender.extendAll();
		
		var config = new Config();
		
		var URLHelperObj = new URLHelper();
		config.setParams(URLHelperObj.getURLParams());
		
		var ARB = new ARBuilder(config);
		ARB.run();
	}

});

var DomReady = new DomReady();