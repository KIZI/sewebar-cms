var ETreeManager = new Class({
	
	ARManager: null,
	config: null,
	dataContainer: null,
	inProgress: false,
	requests: [],
	UIPainter: null,
	
	initialize: function (config, dataContainer) {
		this.config = config;
		this.dataContainer = dataContainer;
	},
	
	setARManager: function (ARManager) {
		this.ARManager = ARManager;
	},
	
	setUIPainter: function (UIPainter) {
		this.UIPainter = UIPainter;
	},
	
	recommendAttributes: function (rule) {
		this.setInProgress(true);
		var attributes = this.getRemainingAttributes(rule.getLiterals());
		var requestData = {
				attributes: attributes,
				rule0: rule.serialize(),
				rules: 1};
		this.makeRequest(JSON.encode(requestData));
	},
	
	setInProgress: function (value) {
		this.inProgress = value;
		if (this.inProgress) {
			this.UIPainter.showETreeProgress();
		} else {
			this.UIPainter.hideETReeProgress();
		}
	},
	
	getRemainingAttributes: function (usedLiterals) {
		var attributes = [];
		Object.each(this.dataContainer.getAttributes(), function(attribute) {
			attributes.include(attribute.getName());
		}.bind(this));
		
		Array.each(usedLiterals, function(usedLiteral) {
			if (attributes.contains(usedLiteral.getAttributeName())) {
				attributes.erase(usedLiteral.getAttributeName());
			}
		}.bind(this));
		
		return attributes;
	},
	
	makeRequest: function (data) {
		var request = new Request.JSON({
			url: this.config.getETreeGetURL(),
	        secure: true,
	            
	        onSuccess: function(responseJSON, responseText) {
	        	this.sortAttributes(data, responseJSON);
	        }.bind(this),
	            
	        onError: function () {
	        	this.handleErrorRequest();
	        }.bind(this),
	        
	        onCancel: function () {
	        	this.handleErrorRequest();
	        }.bind(this),
	        
	        onFailure: function () {
	        	this.handleErrorRequest();
	        }.bind(this),
	        
	        onException: function () {
	        	this.handleErrorRequest();
	        }.bind(this),
	        
	        onTimeout: function () {
	        	this.handleErrorRequest();
	        }.bind(this),

		}).post({'data': data});
	        
		this.addRequest(request);
	},
	
	sortAttributes: function (data, responseJSON) {
		this.setInProgress(false);
		var attributes = this.dataContainer.getAttributes();
		var attributeSorter = new AttributeSorter(this.ARManager, this.UIPainter);
		attributeSorter.sort(attributes, responseJSON);
	},
	
	handleErrorRequest: function () {
		this.setInProgress(false);
		
		console.log('AJAX request error!');
	},
	
	addRequest: function (request) {
		this.requests.push(request);
	},
	
	getInProgress: function() {
		return this.inProgress;
	}
	
});