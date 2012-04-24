var AttributeSorter = new Class({
	
	ARManager: null,
	UIPainter: null,
	
	initialize: function(ARManager, UIPainter) {
		this.ARManager = ARManager;
		this.UIPainter = UIPainter;
	},
	
	sort: function(attributes, recommendation) {
		var positions = [];
		
		// sort recommended attributes
		Object.each(recommendation, function(value, name) {
			var index = attributes.indexOfObject(name, Attribute.prototype.getName);
			positions.push(index);
			attributes[index].setValue(value);
		}.bind(this));
		
		// sort remaining attributes
		var usedPositions = [];
		Array.each(attributes, function (value, key) {
			if (this.ARManager.isAttributeUsed(value)) {
				usedPositions.push(key);
				attributes[key].setValue(0);
			} else if (!positions.contains(key)) { 
				positions.push(key);
				attributes[key].setValue(0);
			}
		}.bind(this));
		positions.append(usedPositions);
		
		// internal sort according to positions
		attributes.sort(function(a, b, array) {
			var indexA = attributes.indexOfObject(a.getName(), Attribute.prototype.getName);
			var indexB = attributes.indexOfObject(b.getName(), Attribute.prototype.getName);
			return positions.indexOf(indexA) > positions.indexOf(indexB);
		}.bind(this));
		
		this.ARManager.dataContainer.setAttributes(attributes);
		
		// repaint attributes
		this.UIPainter.sortAttributes(attributes, positions);
	}
	
});