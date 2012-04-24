/*global Class: false */ 

var InterestMeasure = new Class({

	name: '',
	localizedName: '',
	explanation: '',
	field: [],
	stringHelper: null,

	initialize: function (name, localizedName, explanation, field, stringHelper) {
		this.name = name;
		this.localizedName = localizedName;
		this.explanation = explanation;
		this.field = field;
		this.stringHelper = stringHelper;
	},
	
	getName: function () {
		return this.name;
	},
	
	getLocalizedName: function () {
		if (this.localizedName.length !== 0) {
			return this.localizedName;
		}
		
		return this.name;
	},
	
	getNormalizedName: function () {
		return this.stringHelper.normalizeString(this.name);
	},
	
	getExplanation: function () {
		return this.explanation;
	},
	
	getField: function () {
		return this.field;
	},
	
	getStringHelper: function () {
		return this.stringHelper;
	},
	
	getCSSID: function () {
		return 'im-' + this.getNormalizedName();
	},
	
	getCSSRemoveID: function () {
		return 'im-' + this.getNormalizedName() + '-remove';
	},
	
	getCSSSliderID: function () {
		return 'im-' + this.getNormalizedName() + '-slider';
	},
	
	getCSSValueID: function () {
		return 'im-' + this.getNormalizedName() + '-value';
	}

});