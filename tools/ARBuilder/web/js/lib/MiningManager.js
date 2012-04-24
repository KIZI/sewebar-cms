var MiningManager = new Class({
	
	config: null,
	rulesParser: null,
	UIPainter: null,
	requests: [],
	limitRules: 10,
	foundRules: [],
	numFoundRules: 0,
	inProgress: false,
	finishedStates: ['Solved', 'Interrupted'],
	
	initialize: function (config, rulesParser) {
		this.config = config;
		this.rulesParser = rulesParser;
	},
	
	setUIPainter: function (UIPainter) {
		this.UIPainter = UIPainter;
	},
	
	getLimitHits: function () {
		return this.limitHits;
	},
	
	mineRules: function (rule) {
		this.UIPainter.showElement($$('#found-rules h2')[0]);
		this.UIPainter.showMiningProgress();
		this.UIPainter.disposeFoundRules();
		
		this.inProgress = true;
		var requestData = {
				limitHits: this.limitRules,
				rule0: rule.serialize(),
				rules: 1};
		
		this.makeRequest(JSON.encode(requestData));
	},
	
	makeRequest: function (data) {
		var request = new Request.JSON({
			url: this.config.getRulesGetURL(),
	        secure: true,
	            
	        onSuccess: function(responseJSON, responseText) {
	        	this.handleSuccessRequest(data, responseJSON);
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
	
	handleSuccessRequest: function (data, responseJSON) {
		this.UIPainter.hideMiningProgress();
		
		this.numFoundRules = responseJSON.hasOwnProperty('rules') ? Object.getLength(responseJSON.rules) : 0;
		if (this.numFoundRules) {
			this.foundRules = this.rulesParser.parse(responseJSON.rules);
			this.UIPainter.renderFoundRules(this.foundRules);
		}
		
		if (this.finishedStates.contains(responseJSON.taskState)) { // task is finished
			this.inProgress = false;
			this.UIPainter.renderActiveRule();
		} else { // task is still running
			this.makeRequest(data);
		}
	},
	
	handleErrorRequest: function () {
		this.UIPainter.hideMiningProgress();
		
		this.inProgress = false;
		this.UIPainter.renderActiveRule();
		
		// TODO handle AJAX request error
		console.log('AJAX request error!');
	},
	
	addRequest: function (request) {
		this.requests.push(request);
	},
	
	getInProgress: function () {
		return this.inProgress;
	}
	
});