/*global Class: false, Attribute: false, RulePattern: false, InterestMeasure: false, BBACoefficient: false, FieldGroup: false, Field: false */ 

var DataContainer = new Class({

	attributes: [],
	miningMode: true,
	rulePatterns: [], 
	IMTreshold: '',
	IMs: [],
	IMCombinations: [],
	BBACoefficient: '',
	BBACoefficients: [],
	DBAMaxLevels: null,
	DBAConstraints: [],
	fieldGroupRootConfigID: null,
	fieldGroups: {},
	takState: '',
	
	initialize: function () {
	
	},
	
	getRulePatterns: function () {
		return this.rulePatterns;
	},
	
	getFieldGroupRootConfigID: function () {
		return this.fieldGroupRootConfigID;
	},

	parseData: function (data) {
		this.parseAttributes(data.attributes);
		this.miningMode = data.miningMode;
		this.parseRulePatterns(data.rulePattern);
		this.parseInterestMeasures(data.interestMeasures);
		this.parseBBAs(data.BBA);
		this.parseDBAs(data.DBA);
		this.parseFieldGroups(data.fieldGroups);
		this.parseExistingRules(data.existingRules);
	},
	
	parseAttributes: function (attributes) {
		Object.each(attributes, function (value, key) {
			this.attributes.push(new Attribute(key, value.choices, new StringHelper(), 0));
		}.bind(this));
	},
	
	parseRulePatterns: function (rulePatterns) {
		Object.each(rulePatterns, function (value, key) {
			this.rulePatterns[key] = new RulePattern(key, value.minNumberOfBBAs, value.maxNumberOfBBAs);
		}.bind(this));
	},
	
	parseInterestMeasures: function (interestMeasures) {
		this.IMTreshold = interestMeasures.treshold;
		
		Object.each(interestMeasures.types, function (value, key) {
			this.IMs[key] = new InterestMeasure(key, value.localizedName, value.explanation, value.field, new StringHelper());
		}.bind(this));
		
		Object.each(interestMeasures.combinations, function (value, key) {
			this.IMCombinations[key] = value;
		}.bind(this));
	},
	
	parseBBAs: function (BBA) {
		this.BBACoefficient = BBA.coefficient;
		
		Object.each(BBA.coefficients, function (value, key) {
			var BBACoef = new BBACoefficient(key, value.localizedName, value.Explanation);
			
			Object.each(value.fields, function (value, key) {
				BBACoef.addField(key, value);
			}.bind(this));
			
			this.BBACoefficients[key] = BBACoef;
		}.bind(this));
	},
	
	parseDBAs: function (DBA) {
		this.DBAMaxLevels = DBA.maxLevels;
		
		Object.each(DBA.constraints, function (value, key) {
			var constraint = {'Conjunction': value.Conjunction.allowed,
			                  'Disjunction': value.Disjunction.allowed,
			                  'Any': value.Any.allowed,
			                  'Negation': value.Negation.allowed};
			this.DBAConstraints[key] = constraint;
		}.bind(this));
	},
	
	parseFieldGroups: function (fieldGroups) {
		this.fieldGroupRootConfigID = fieldGroups.rootConfigID;
		
		Object.each(fieldGroups.groups, function (value, key) {
			var FG = new FieldGroup(value.id, value.name, value.localizedName, value.explanation, value.childGroups,
									value.connective, (value.id === this.fieldGroupRootConfigID));
			
			Object.each(value.fieldConfig, function (value, key) { 
				var F = null;
				if (value.coefficient === null) {
					F = new Field(0, this.getAttributeByName(key), null, new StringHelper());
				} else if (value.coefficient.type === 'One category') {
					F = new Field(0, this.getAttributeByName(key), value.coefficient.type, new StringHelper(), value.coefficient.category);	
				} else {
					F = new Field(0, this.getAttributeByName(key), value.coefficient.type, new StringHelper(), value.coefficient.minimalLength, value.coefficient.maximalLength);
				}

				FG.addField(F.getRef().getName(), F);
			}.bind(this));

			this.fieldGroups[value.id] = FG;
		}.bind(this));
	},
	
	parseExistingRules: function (existingRules) {
		this.taskState = existingRules.taskState;
		
		// TODO parse rules
	},
	
	getAttributeByName: function (name) {
		var retval = null;
		Array.each(this.attributes, function(attr) {
			if (attr.getName() === name) {
				retval = attr;
			}
		}.bind(this));
		
		return retval;
	},
	
	getAttributeKey: function () {
		
	},
	
	getAttributes: function () {
		return this.attributes;
	},
	
	setAttributes: function(attributes) {
		this.attributes = attributes;
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
	
	getIM: function (name) {
		if (this.IMs.hasOwnProperty(name)) {
			return this.IMs[name];
		}
		
		return null;
	},
	
	getBBACoefficients: function () {
		return this.BBACoefficients;
	},
	
	getIMs: function () {
		return this.IMs;
	},
	
	getIMCombinations: function () {
		return this.IMCombinations;
	},
	
	getDBAConstraint: function (level) {
		return this.DBAConstraints['level' + level];
	},
	
	getBBACoefficient: function (coefficientName) {
		return this.BBACoefficients[coefficientName];
	}
	
});