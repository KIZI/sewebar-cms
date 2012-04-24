var NativeTypeExtender = new Class({
	
	initialize: function () {},
	
	extendAll: function () {
		this.extendNativeElement();
		this.extendNativeString();
		this.extendNativeArray();
	},
	
	extendNativeElement: function () {
		Element.implement('hasChildren', function () {
			return (this.getChildren().length > 0);
		});
	},
	
	extendNativeString: function () {
		String.implement('setCharAt', function (char, index) {
			if (index > this.length - 1) {
				return this.toString();
			}
			
			return this.substr(0, index) + char + this.substr(index + 1);
		});
		
		String.implement('strTr', function (from, to) {
			var string = this.toString();
			for(var i = 0; i < string.length; i++) {
				var char = string[i];
				if (from.contains(char) === true) {
					var index = from.indexOf(char);
					string = string.setCharAt(to[index], i);
				}
			}
			
			return string.toString();
		});
	},
	
	extendNativeArray: function () {
		Array.implement('remove', function (keyToRemove) {
			var array = [];
			Array.each(this, function(value, key) {
				if (keyToRemove !== key) { 
					array.push(this[key]); 
				}
			}.bind(this));
			
			return array;
		});
		
		Array.implement('indexOfObject', function (value, fn) {
			var index = undefined;
			Array.each(this, function(object, key) {
				if (fn.call(object) === value) {
					index = key;
				}
			}.bind(this));
			
			return index;
		});
		
		Array.implement('equalsTo', function (arr) {
			if (this === arr) { 
				return true;
			}
			
			if (this.length !== arr.length) { 
				return false; 
			}
			
			for(var i = this.length - 1; i >= 0; i--){
	            if (!arr.contains(this[i])){
	                return false;
	            }
	        }
			
	        return true;
		});
	}
	
});