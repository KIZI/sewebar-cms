var InterestMeasureARSlider = new Class({
	Extends: Slider,
	
	numSteps: 100,
	precision: 0,
	inversePrecision: 2,
	dataType: 'double',
	
	elementSlider: null,
	IM: null,
	numberNormalizer: null,
	inverseNumberNormalizer: null,
	
	initialize: function (elementSlider, IM) {
		this.dataType = IM.field.dataType;
		this.elementSlider = elementSlider;
		this.IM = IM;
		
		if (this.dataType !== 'enum') {
			this.numberNormalizer = new NumberNormalizer(IM.field.minValue, IM.field.maxValue, this.inversePrecision, 0, 100, this.precision, this.numSteps, IM.field.minValueInclusive, IM.field.maxValueInclusive);
			this.inverseNumberNormalizer = new NumberNormalizer(0, 100, this.precision, IM.field.minValue, IM.field.maxValue, this.inversePrecision, this.numSteps, IM.field.minValueInclusive, IM.field.maxValueInclusive);
		}
		
		this.parent(this.elementSlider, this.elementSlider.getElement('.knob'), {
			range: [0, (this.dataType !== 'enum' ? this.numSteps : (IM.field.values.length - 1))],
	        initialStep: this.dataType !== 'enum' ? this.numberNormalizer.normalize(IM.getValue()) : IM.field.values.indexOf(IM.getValue()),
	        
	        onChange: function(value) {
	        	this.handleChange(value);
	        }
	    });
	},
	
	handleChange: function (value) {
		if (this.dataType !== 'enum') {
	    	var number = this.inverseNumberNormalizer.validate(value);
	    	number = this.inverseNumberNormalizer.normalize(number);
	    	var string = this.inverseNumberNormalizer.format(number);
	    	$(this.IM.getCSSValueID()).set('text', string);
		} else {
			var number = IM.field.values[value];
			$(this.IM.getCSSValueID()).set('text', number);
		}

    	this.elementSlider.fireEvent('change');
	}

});