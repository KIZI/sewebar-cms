var InterestMeasure = new Class({

    GetterSetter: ['default'],

	name: '',
	localizedName: '',
	explanation: '',
	thresholdType: '',
	compareType: '',
	fields: [],
	stringHelper: null,
    $default: false,

	initialize: function (name, localizedName, explanation, thresholdType, compareType, fields, stringHelper, def) {
		this.name = name;
		this.localizedName = localizedName;
		this.explanation = explanation;
		this.thresholdType = thresholdType;
		this.compareType = compareType;
		this.fields = fields;
		this.stringHelper = stringHelper;
        this.$default = def || false;
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
	
	getNormalizedName: function () {
		return this.stringHelper.normalizeString(this.name);
	},
	
	getExplanation: function () {
		return this.explanation;
	},
	
	getThresholdType: function () {
		return this.thresholdType;
	},
	
	getCompareType: function () {
		return this.compareType;
	},
	
	getFields: function () {
		return this.fields;
	},
	
	getStringHelper: function () {
		return this.stringHelper;
	},
	
	hasThreshold: function() {
		var has = false;
		Array.each(this.fields, function (f) {
			if (f.name === 'threshold') {
				has = true;
			}
		}.bind(this));
		
		return has;
	},
	
	hasAlpha: function() {
		var has = false;
		Array.each(this.fields, function (f) {
			if (f.name === 'alpha') {
				has = true;
			}
		}.bind(this));
		
		return has;
	},
	
	getCSSID: function () {
		return 'im-' + this.getNormalizedName();
	},
	
	getCSSEditID: function () {
		return 'im-' + this.getNormalizedName() + '-edit';
	},
	
	getCSSRemoveID: function () {
		return 'im-' + this.getNormalizedName() + '-remove';
	},
	
	getCSSSliderID: function () {
		return 'im-' + this.getNormalizedName() + '-slider';
	},
	
	getCSSValueID: function () {
		return 'im-' + this.getNormalizedName() + '-value';
	},

    initIMAR: function(IM) {
        var threshold = 0;
        var alpha = 0;
        IM.fields.each(function(field) {
            if (field.name === 'threshold') { // threshold
                threshold = field.defaultValue;
            } else { // alpha
                alpha = field.defaultValue;
            }
        });

        return new InterestMeasureAR(IM.getName(), IM.getLocalizedName(), IM.getExplanation(), IM.getThresholdType(), IM.getCompareType(), IM.getFields(), IM.getStringHelper(), IM.getDefault(), threshold, alpha);
    }

});