var InterestMeasureAddSlider = new Class({
	Extends: Slider,
	
	numSteps: 100,
	precision: 0,
	inversePrecision: 2,
	initialStep: 0.75,
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
	        initialStep: this.dataType !== 'enum' ? this.numberNormalizer.normalize(this.initialStep) : 0,
	        
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
	
	    	$('add-im-value').value = string;
		} else {
			var number = this.IM.field.values[value];
			$('add-im-value').value = number;
		}
	}

});