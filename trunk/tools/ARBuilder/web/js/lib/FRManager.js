var FRManager = new Class({
	
	config: null,
	rulesParser: null,
	settings: null,
	AJAXBalancer: null,
	UIPainter: null,
	rules: {},
	maxId: 0,
	
	initialize: function (config, rulesParser, settings) {
		this.config = config;
		this.rulesParser = rulesParser;
		this.settings = settings;
		this.AJAXBalancer = new AJAXBalancer();
	},

	setUIPainter: function (UIPainter) {
		this.UIPainter = UIPainter;
	},
	
	handleInProgress: function () {
		this.reset();
		
		this.UIPainter.renderActiveRule();
		this.UIPainter.resetFoundRules();
		this.UIPainter.showMiningProgress();
		
	},
	
	renderRules: function (rules, numRules, inProgress) {
		// filter new rules
		rules = this.filterRules(rules, this.maxId);
		var parsedRules = this.rulesParser.parse(rules);
		Array.each(parsedRules, function (r) {
			var rule = new FoundRule(r);
			this.rules[r.getId()] = rule;
			if (this.settings.getBKAutoSearch()) {
				this.buildRequest(rule);
			}
		}.bind(this));
		
		this.UIPainter.renderFoundRules(parsedRules);
		
		if (!inProgress) {
			this.UIPainter.hideMiningProgress();
			this.UIPainter.showClearRules();
		}
		
		if (this.settings.getBKAutoSearch()) {
			this.AJAXBalancer.run.delay(500, this.AJAXBalancer);
		}
	},
	
	filterRules: function (rules, maxId) {
		rules = Object.cleanValues(rules, function(value) {
			if (!value.hasOwnProperty('value')) { return true; } // if one rule is returned, it does not have id
			return value.id > maxId;
		}.bind(this));
		
		return rules;
	},
	
	buildRequest: function (FR) {
		var reqData = {
				data: {
					limitHits: 1,
					rule0: FR.getRule().serialize(),
					rules: 1}};
		
		var options = {
			url: this.config.getBKGetURL(),
	        secure: true,
	        data: JSON.encode(reqData),
	            
	        onRequest: function () {
				this.UIPainter.showFRLoading(FR);
			}.bind(this),
	        
	        onSuccess: function(responseJSON, responseText) {
	        	this.handleSuccessRequest(FR, responseJSON);
	        }.bind(this),
	            
	        onError: function () {
	        	this.handleErrorRequest(FR);
	        }.bind(this),
	        
	        onCancel: function () {
	        	this.handleErrorRequest(FR);
	        }.bind(this),
	        
	        onFailure: function () {
	        	this.handleErrorRequest(FR);
	        }.bind(this),
	        
	        onException: function () {
	        	this.handleErrorRequest(FR);
	        }.bind(this),
	        
	        onTimeout: function () {
	        	this.handleErrorRequest(FR);
	        }.bind(this)
		};
		
		this.AJAXBalancer.addRequest(options);
	},
	
	handleSuccessRequest: function (FR, responseJSON) {
		FR.setInteresting(responseJSON);
		this.UIPainter.updateFoundRule(FR);
	},
	
	handleErrorRequest: function (FR) {
		FR.setInteresting(true);
	},
	
	handleError: function () {
		this.UIPainter.hideMiningProgress();
		this.UIPainter.renderActiveRule();
	},

	reset: function () {
		this.rules = {};
		this.maxId = 0;
		this.AJAXBalancer.stopAllRequests();
	}
	
});