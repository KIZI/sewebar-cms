var FieldFR = new Class({
	Extends: Field,
	
	marked: false,

	initialize: function () {
		this.parent(arguments[0], arguments[1], arguments[2], arguments[3], arguments[4]);
	},
	
	hasPositiveSign: function () {
		return this.sign;
	},
	
	serialize: function () {
		var serialized = {};
		serialized.name = this.getAttributeName();
		serialized.type = 'attr';
		serialized.category = this.type;
		serialized.catref = this.category;

		return serialized;
	},

    toString: function () {
        var string = '';
        if (!this.hasPositiveSign()) {
            string += '<span class="field-sign negative"></span>';
        }

        if (this.category.contains('<') || this.category.contains('>')) {
            string += this.getAttributeName() + '<span class="coefficient">' + this.category + '</span>';
        } else {
            string += this.getAttributeName() + '<span class="coefficient">(' + this.category + ')</span>';
        }

        return string;
    }
	
});