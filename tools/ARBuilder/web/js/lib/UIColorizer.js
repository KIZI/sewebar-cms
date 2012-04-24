/*global Class: false */ 

var UIColorizer = new Class({

	initialize: function () {},
	
	cedentDragEnter: function (cedent) {
		cedent.tween('background-color', '#98B5C1');
	},
	
	cedentDragLeave: function (cedent) {
		cedent.tween('background-color', '#EEE');
	},
	
	cedentDragDrop: function (cedent) {
		cedent.highlight('#7389AE');
	}
	
});