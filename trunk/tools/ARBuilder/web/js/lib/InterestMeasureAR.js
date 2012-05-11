/*global Class: false, InterestMeasure: false */ 

var InterestMeasureAR = new Class({
	Extends: InterestMeasure,

	value: 0,
	displayPrecision: 3,
	
	initialize: function (name, localizedName, explanation, thresholdType, compareType, field, stringHelper, value) {
		this.parent(name, localizedName, explanation, thresholdType, compareType, field, stringHelper);
		this.value = value;
	},
	
	getValue: function () {
		return this.value;
	},
	
	setValue: function (value) {
		this.value = value;
	},
	
	serialize: function () {
		return {name: this.name, 
				type: 'oper',
				thresholdType: this.thresholdType,
				compareType: this.compareType,
				fields: [{
					name: 'prahovaHodnota',
					value: this.value}]};
	},
	
	toString: function () {
		return this.getLocalizedName() + ':<span class="im-value">' + this.value.format({decimals: this.displayPrecision}) + '</span>';
	}

});