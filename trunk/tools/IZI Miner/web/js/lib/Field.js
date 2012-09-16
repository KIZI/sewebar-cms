var Field = new Class({

	id: 0,
	ref: null,
	type: null,
	minimalLength: null,
	maximalLength: null,
	category: null,
	stringHelper: null,
    sign: true,
	
	initialize: function () {
		this.id = arguments[0];
		this.ref = arguments[1];
		this.type = arguments[2];
		this.stringHelper = arguments[3];
		if (arguments.length === 5) { // One category
			this.category = arguments[4];
		} else if (arguments.length === 6) { // Interval, Cut, ...
			this.minimalLength = arguments[4];
			this.maximalLength = arguments[5];
		}
	},
	
	getId: function () {
		return this.id;
	},
	
	getRef: function () {
		return this.ref;
	},
	
	getType: function () {
		return this.type;
	},
	
	getMinimalLength: function () {
		return this.minimalLength;
	},
	
	getMaximalLength: function () {
		return this.maximalLength;
	},
	
	getCategory: function () {
		return this.category;
	},

    changeSign: function () {
        this.sign = !this.sign;
    },

    hasPositiveSign: function () {
        return this.sign;
    },
	
	getCSSID: function () {
		return 'field-nav-' + this.getNormalizedName();
	},
	
	getNormalizedName: function () {
		return this.stringHelper.normalizeString(this.getAttributeName());
	},
	
	getCSSDragID: function () {
		return 'field-nav-drag-' + this.id;
	},
	
	getAttributeName: function () {
		return this.ref.getName();
	},
	
	setCoefficient: function () {
		if (arguments.length === 2) { // One category
			this.type = arguments[0];
			this.minimalLength = null;
			this.maximalLength = null;
			this.category = arguments[1];
		} else {
			this.type = arguments[0];
			this.minimalLength = arguments[1];
			this.maximalLength = arguments[2];
			this.category = null;
		}
	},

    serialize: function () {
        var serialized = {};
        serialized.name = this.getAttributeName();
        serialized.category = this.type;
        if (this.type === 'One category') {
            serialized.fields = [{name: 'category', value: this.category}];
        } else if (this.type) {
            serialized.fields = [{name: 'minLength', value: this.minimalLength},
                {name: 'maxLength', value: this.maximalLength}];
        }
        serialized.sign = this.hasPositiveSign() ? 'positive' : 'negative';

        return serialized;
    },
	
	toString: function () {
		if (!this.type) {
			return this.getAttributeName();
		}

        var string = '';
		if (this.type === 'One category') {
            if (this.category.contains('<') || this.category.contains('>')) {
                string += this.getAttributeName() + '<span class="coefficient">' + this.category + '</span>';
            } else {
			    string += this.getAttributeName() + '<span class="coefficient">(' + this.category + ')</span>';
            }
        } else if (this.type == 'Subset' && this.minimalLength == 1 && this.maximalLength == 1) {
            string += this.getAttributeName() + '<span class="coefficient">(*)</span>';
        } else {
            string += this.getAttributeName() + '<span class="coefficient">(*' + this.type + ' ' + this.minimalLength + '-' + this.maximalLength + ')</span>';
		}

        return string;
	}

});