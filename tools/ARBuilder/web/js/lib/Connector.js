/*global Class: false, Request: false */ 

var Connector = new Class({

	dataGetURL: '',
	dataSetURL: '',
	rulesGetURL: '',

	initialize: function (dataGetURL, dataSetURL, rulesGetURL) {
		this.dataGetURL = dataGetURL;
		this.dataSetURL = dataSetURL;
		this.rulesGetURL = rulesGetURL;
	},
	
	getDataGetURL: function () {
		return this.dataGetURL;
	},
	
	getDataSetURL: function () {
		return this.dataSetURL;
	},
	
	getRulesGetURL: function () {
		return this.rulesGetURL;
	},

	getData: function (dataContainer) {
		new Request.JSON({
			url: this.dataGetURL,
			secure: true,
			async: false,
			
			onSuccess: function (responseJSON, responseText) {
				dataContainer.parseData(responseJSON);
			},
			
			onError: function (text, error) {
				// TODO - implement
			}
		
		}).get();
	},

	setData: function () {
		// TODO
	},

	getRules: function () {
		// TODO
	}

});