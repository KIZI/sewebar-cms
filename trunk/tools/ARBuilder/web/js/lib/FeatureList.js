var FeatureList = new Class({
	
	// user interface
	priority: 0,
	miningMode: true,
	name: '',
	localizedName: '',
	explanation: '',
	autoSuggest: [],
	
	// rule pattern
	rulePattern: [],
	
	// building blocks
	// - interest measures
	IMTreshold: '',
	IMs: [],
	IMCombinations: [],
	
	// - basic boolean attribute
	BBACoefficient: '',
	BBACoefficients: [],
	
	// - derived boolean attribute
	DBAMaxLevels: null,
	DBAConstraints: [],
	
	// custom properties
	defaultConnectiveName: 'Conjunction',
	maxConnectiveID: 0,
	
	initialize: function (data) {
		this.parseUserInterface(data);
		this.parseRulePattern(data);
		this.parseBuildingBlocks(data);
	},
	
	parseUserInterface: function (data) {
		this.priority = data.priority;
		this.miningMode = data.miningMode;
		this.name = data.name;
		this.localizedName = data.localizedName;
		this.explanation = data.explanation;
		this.autoSuggest = data.autoSuggest;
	},
	
	parseRulePattern: function (data) {
		Object.each(data.rulePattern, function (value, key) {
			this.rulePattern[key] = new RulePattern(key, value.minNumberOfBBAs, value.maxNumberOfBBAs);
		}.bind(this));
	},
	
	parseBuildingBlocks: function (data) {
		// interest measures
		this.IMTreshold = data.interestMeasures.treshold;
		
		Object.each(data.interestMeasures.types, function (value, key) {
			this.IMs[key] = new InterestMeasure(key, value.localizedName, value.explanation, value.thresholdType, value.compareType, value.field, new StringHelper());
		}.bind(this));
		
		Object.each(data.interestMeasures.combinations, function (value, key) {
			this.IMCombinations[key] = value;
		}.bind(this));
		
		// basic boolean attribute
		this.BBACoefficient = data.BBA.coefficient;
		
		Object.each(data.BBA.coefficients, function (value, key) {
			var BBACoef = new BBACoefficient(key, value.localizedName, value.Explanation);
			
			Object.each(value.fields, function (value, key) {
				BBACoef.addField(key, value);
			}.bind(this));
			
			this.BBACoefficients[key] = BBACoef;
		}.bind(this));
		
		// derived boolean attribute
		this.DBAMaxLevels = data.DBA.maxLevels;
		
		Object.each(data.DBA.constraints, function (value, key) {
			var constraint = {'Conjunction': value.Conjunction.allowed,
			                  'Disjunction': value.Disjunction.allowed,
			                  'Any': value.Any.allowed,
			                  'Negation': value.Negation.allowed};
			this.DBAConstraints[key] = constraint;
		}.bind(this));
	},
	
	getName: function () {
		return this.name;
	},
	
	getLocalizedName: function () {
		if (this.localizedName) {
			return this.localizedName;
		}
		
		return this.name;
	},
	
	getExplanation: function () {
		return this.explanation;
	},
	
	getAutoSuggest: function () {
		return this.autoSuggest;
	},
	
	getRulePattern: function () {
		return this.rulePattern;
	},
	
	getIM: function (name) {
		if (this.IMs.hasOwnProperty(name)) {
			return this.IMs[name];
		}
		
		return null;
	},
	
	getIMs: function () {
		return this.IMs;
	},
	
	getRemainingIMs: function (usedIMs) {
		var remainingIMs = [];
		Object.each(this.IMs, function (IM, name) {
			var found = false;
			Object.each(usedIMs, function (usedIM) {
				if (IM.getName() === usedIM.getName()) {
					found = true;
				}
			}.bind(this));
			if (found === false) {
				remainingIMs.push(IM);
			}
		}.bind(this));
		
		return remainingIMs;
	},
	
	getPossibleIMs: function (usedIMs) {
		var possibleIMs = [];
		
		if (Object.getLength(usedIMs) === 0) {
			possibleIMs = this.getIMs();
		} else if (this.getIMCombinations().length === 0) {
      possibleIMs = this.getRemainingIMs(usedIMs);
    } else {
			Array.each(this.getIMCombinations(), function (IMCombination) {
				var applicableCombination = true;
				Object.each(usedIMs, function (usedIM) {
					if (!IMCombination.contains(usedIM.getName()) || IMCombination.length === 1) {
						applicableCombination = false;
					}
				}.bind(this));
				
				if (applicableCombination === true) {
					var applicableIMCombination = Array.clone(IMCombination);
					Object.each(usedIMs, function (usedIM) {
						applicableIMCombination.erase(usedIM.getName());
					}.bind(this));
					
					Array.each(applicableIMCombination, function (IMName) {
						var IM = this.getIM(IMName);
						possibleIMs[IM.getName()] = IM;
					}.bind(this));
				}
			}.bind(this));
		}
		
		return possibleIMs;
	},
	
	getIMCombinations: function () {
		return this.IMCombinations;
	},
	
	getBBACoefficient: function (name) {
		return this.BBACoefficients[name];
	},
	
	getBBACoefficients: function () {
		return this.BBACoefficients;
	},
	
	getDefaultBBACoef: function () {
		return this.getBBACoefficients()[Object.keys(this.getBBACoefficients())[0]];
	},
	
	getDBAConstraint: function (level) {
		return this.DBAConstraints['level' + level];
	},
	
	getDefaultConnective: function () {
		return new Connective(this.generateConnectiveID(), this.defaultConnectiveName);
	},
	
	generateConnectiveID: function () {
		return ++this.maxConnectiveID;
	}
	
});