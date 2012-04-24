var UIListener = new Class({
	
	ARManager: null,
	UIColorizer: null,
	UIPainter: null,
	
	initialize: function (ARManager, UIColorizer) {
		this.ARManager = ARManager;
		this.UIColorizer = UIColorizer;
	},
	
	setUIPainter: function (UIPainter) {
		this.UIPainter = UIPainter;
	},
	
	registerNavigationEventHandlers: function () {
		if (this.ARManager.getAttributesByGroup()) {
			// attributes by list
			$('attributes-by-list').addEvent('click', function (event) {
				this.ARManager.displayAttributesByList();
				event.stop();
			}.bind(this));
		} else {
			// attributes by group
			$('attributes-by-group').addEvent('click', function (event) {
				this.ARManager.displayAttributesByGroup();
				event.stop();
			}.bind(this));
		}
	},
	
	registerAttributeEventHandler: function (attribute) {
		$(attribute.getCSSID()).addEvent('mousedown', function (event) {
			event.stop();
			
			// disable right click drag & drop
			if (event.rightClick) {
				return false;
			}
			
			var draggedAttribute = $(attribute.getCSSID());
			var clone = draggedAttribute.clone().setStyles(draggedAttribute.getCoordinates()).setStyles({
					opacity: 0.7,
					position: 'absolute'
			    }).inject(document.body);
			
		    var drag = new Drag.Move(clone, {
		        droppables: $$('div.cedent'),

		        onDrop: function (dragging, cedent) {
		        	dragging.destroy();
		        	if (cedent !== null) {
		        		cedent.fireEvent('addAttribute', attribute);
		        		this.UIColorizer.cedentDragDrop(cedent);
		        	}
		        }.bind(this),
		        
		        onEnter: function (dragging, cedent) {
		        	this.UIColorizer.cedentDragEnter(cedent);
		        }.bind(this),
		        
		        onLeave: function (dragging, cedent) {
		        	this.UIColorizer.cedentDragLeave(cedent);
		        }.bind(this),
		        
		        onCancel: function (dragging) {
		        	dragging.destroy();
		        }
		    });
		    
		    drag.start(event);
		    
		}.bind(this));
	},
	
	registerFieldEventHandler: function (field) {
		$(field.getCSSID()).addEvent('mousedown', function (event) {
			event.stop();
			
			// disable right click drag & drop
			if (event.rightClick) {
				return false;
			}
			
			var draggedField = $(field.getCSSID());
			var clone = draggedField.clone().setStyles(draggedField.getCoordinates()).setStyles({
				opacity: 0.7,
				position: 'absolute'
		    }).inject(document.body);

		    var drag = new Drag.Move(clone, {
		        droppables: $$('div.cedent'),

		        onDrop: function (dragging, elementCedent) {
		        	dragging.destroy();
		        	if (elementCedent !== null) {
		        		elementCedent.fireEvent('addField', field);
		        		this.UIColorizer.cedentDragDrop(elementCedent);
		        	}
		        }.bind(this),
		        
		        onEnter: function (dragging, elementCedent) {
		        	this.UIColorizer.cedentDragEnter(elementCedent);
		        }.bind(this),
		        
		        onLeave: function (dragging, elementCedent) {
		        	this.UIColorizer.cedentDragLeave(elementCedent);
		        }.bind(this),
		        
		        onCancel: function (dragging) {
		        	dragging.destroy();
		        }
		    });
			
			drag.start(event);
			
		}.bind(this));
	},
	
	registerFieldGroupEventHandler: function (FG) {
		$(FG.getCSSNameID()).addEvent('mousedown', function (event) {
			event.stop();
			
			// disable right click drag & drop
			if (event.rightClick) {
				return false;
			}
			
			var draggedFG = $(FG.getCSSNameID());
			var clone = draggedFG.clone().setStyles(draggedFG.getCoordinates()).setStyles({
				opacity: 0.7,
				position: 'absolute'
		    }).inject(document.body);

		    var drag = new Drag.Move(clone, {
		        droppables: $$('div.cedent'),

		        onDrop: function (dragging, elementCedent) {
		        	dragging.destroy();
		        	if (elementCedent !== null) {
		        		elementCedent.fireEvent('addFieldGroup', FG);
		        		this.UIColorizer.cedentDragDrop(elementCedent);
		        	}
		        }.bind(this),
		        
		        onEnter: function (dragging, elementCedent) {
		        	this.UIColorizer.cedentDragEnter(elementCedent);
		        }.bind(this),
		        
		        onLeave: function (dragging, elementCedent) {
		        	this.UIColorizer.cedentDragLeave(elementCedent);
		        }.bind(this),
		        
		        onCancel: function (dragging) {
		        	dragging.destroy();
		        }
		    });
			
			drag.start(event);
			
		}.bind(this));
	},
	
	registerMarkedRuleEventHandlers: function (rule) {
		$(rule.getMarkedRuleCSSRemoveID()).addEvent('click', function (event) {
			this.ARManager.removeMarkedRule(rule);
			event.stop();
		}.bind(this));
	},

	registerActiveRuleEventHandlers: function (rule) {
		if (this.ARManager.hasPossibleIMs()) {
			// open add IM window
			$('add-im').addEvent('click', function (event) {
				event.stop();
				this.ARManager.openAddIMWindow();
			}.bind(this));
		}
		
		if (this.ARManager.display4ftTaskBox()) {
			$('mine-rules-confirm').addEvent('click', function (event) {
				event.stop();
				this.ARManager.mineRulesConfirm();
			}.bind(this));
		}
		
		if (this.ARManager.displayETreeTaskBox()) {
			$('recommend-attributes-confirm').addEvent('click', function (event) {
				event.stop();
				this.ARManager.recommendAttributesConfirm();
			}.bind(this));
		}
	},
	
	registerIMEventHandler: function (IM) {
		// change IM value
		$(IM.getCSSSliderID()).addEvent('change', function () {
			this.ARManager.editIM(IM);
		}.bind(this));
		
		// remove
		$(IM.getCSSRemoveID()).addEvent('click', function (event) {
			event.stop();
			this.ARManager.removeIM(IM);
		}.bind(this));
	},
	
	registerAddIMFormEventHandler: function () {
		// submit
		var elementSubmit = $('add-im-form').getElement('input[type=submit]');
		elementSubmit.addEvent('click', function (event) {
			var elementSelect = $('add-im-select');
			var IMName = elementSelect.options[elementSelect.selectedIndex].value;
			var IMValue = $('add-im-value').value;
			var IM = this.ARManager.addIM(IMName, IMValue);
			event.stop();
		}.bind(this));
		
		// change IM
		var elementSelect = $('add-im-select');
		elementSelect.addEvent('change', function (event) {
			var IMName = elementSelect.options[elementSelect.selectedIndex].value;
			var IM = this.ARManager.getIMPrototype(IMName);
			this.UIPainter.renderAddIMAutocomplete(this.ARManager.getPossibleIMs(), IM);
			event.stop();
		}.bind(this));
		
		// close
		var elementClose = $('add-im-close');
		elementClose.addEvent('click', function (event) {
			this.ARManager.closeAddIMWindow();
			event.stop();
		}.bind(this));
	},
	
	registerCedentEventHandlers: function (cedent, rule) {
		// add attribute (fired by drag & drop)
		$(cedent.getCSSID()).addEvent('addAttribute', function (attribute) {
			this.ARManager.addAttribute(cedent, attribute);
		}.bind(this));
		
		// add preset field (fired by drag & drop)
		$(cedent.getCSSID()).addEvent('addField', function (field) {
			this.ARManager.addField(field, cedent);
		}.bind(this));
		
		// add preset field group
		$(cedent.getCSSID()).addEvent('addFieldGroup', function (FG) {
			this.ARManager.addFieldGroup(FG, cedent);
		}.bind(this));
		
		// add field (fired by drag & drop)
		$(cedent.getCSSID()).addEvent('addFieldAR', function (field) {
			this.ARManager.addFieldAR(field, cedent);
		}.bind(this));
		
		// edit connective
		$(cedent.getCSSEditConnectiveID()).addEvent('click', function (event) {
			this.ARManager.openEditConnectiveWindow(cedent);
			event.stop();
		}.bind(this));
		
		// change cedent sign
		if (cedent.displayChangeSign()) {
			$(cedent.getCSSChangeSignID()).addEvent('click', function (event) {
				this.ARManager.changeCedentSign(cedent);
				event.stop();
			}.bind(this));
		}
		
		// group fields option
		if (rule.getGroupFields() && cedent.displayGroupButton()) {
			// group fields confirm
			$(cedent.getCSSGroupFieldsConfirmID()).addEvent('click', function (event) {
				this.ARManager.groupFields(cedent);
				event.stop();
			}.bind(this));
			
//			// group fields reject
//			$(cedent.getCSSGroupFieldsRejectID()).addEvent('click', function (event) {
//				this.ARManager.rejectGroupFields(cedent);
//				event.stop();
//			}.bind(this));
		}
	},
	
	registerAddCoefficientFormEventHandler: function (field) { 
		// submit
		var elementSubmit = $('add-coefficient-form').getElement('input[type=submit]');
		elementSubmit.addEvent('click', function (event) {
			var elementSelect = $('add-coefficient-select');
			var coefficientName = elementSelect.options[elementSelect.selectedIndex].value;
			if (coefficientName === 'One category') {
				var coefficientCategory = $('add-coefficient-category').value;
				this.ARManager.addCoefficient(field, coefficientName, coefficientCategory);
			} else {
				var coefficientMinlength = $('add-coefficient-minlength').value;
				var coefficientMaxlength = $('add-coefficient-maxlength').value;
				this.ARManager.addCoefficient(field, coefficientName, coefficientMinlength, coefficientMaxlength);
			}
			event.stop();
		}.bind(this));
		
		// change coefficient
		var elementSelect = $('add-coefficient-select');
		elementSelect.addEvent('change', function (event) {
			var coefficientName = elementSelect.options[elementSelect.selectedIndex].value;
			var coefficient = this.ARManager.getBBACoefficient(coefficientName);
			this.UIPainter.renderAddCoefficientAutocomplete(field, coefficient);
			event.stop();
		}.bind(this));
		
		// close
		var elementClose = $('add-coefficient-close');
		elementClose.addEvent('click', function (event) {
			this.ARManager.closeAddCoefficientWindow();
			this.ARManager.removeField(field);
			event.stop();
		}.bind(this));
	},
	
	registerEditCoefficientEventHandler: function (field) {
		$(field.getEditCoefficientCSSID()).addEvent('click', function (event) {
			this.ARManager.openEditCoefficientWindow(field);
			event.stop();
		}.bind(this));
	},
	
	registerEditCoefficientFormEventHandler: function (field) { 
		// submit
		var elementSubmit = $('edit-coefficient-form').getElement('input[type=submit]');
		elementSubmit.addEvent('click', function (event) {
			var elementSelect = $('edit-coefficient-select');
			var coefficientName = elementSelect.options[elementSelect.selectedIndex].value;
			if (coefficientName === 'One category') {
				var coefficientCategory = $('edit-coefficient-category').value;
				this.ARManager.editCoefficient(field, coefficientName, coefficientCategory);
			} else {
				var coefficientMinlength = $('edit-coefficient-minlength').value;
				var coefficientMaxlength = $('edit-coefficient-maxlength').value;
				this.ARManager.editCoefficient(field, coefficientName, coefficientMinlength, coefficientMaxlength);
			}
			event.stop();
		}.bind(this));
		
		// change coefficient
		var elementSelect = $('edit-coefficient-select');
		elementSelect.addEvent('change', function (event) {
			var coefficientName = elementSelect.options[elementSelect.selectedIndex].value;
			var coefficient = this.ARManager.getBBACoefficient(coefficientName);
			this.UIPainter.renderEditCoefficientAutocomplete(field, coefficient);
			event.stop();
		}.bind(this));
		
		// close
		var elementClose = $('edit-coefficient-close');
		elementClose.addEvent('click', function (event) {
			this.ARManager.closeEditCoefficientWindow();
			event.stop();
		}.bind(this));
	},
	
	registerEditConnectiveFormEventHandler: function(cedent) {
		// submit
		var elementSubmit = $('edit-connective-form').getElement('input[type=submit]');
		elementSubmit.addEvent('click', function (event) {
			var elementSelect = $('edit-connective-select');
			var connectiveName = elementSelect.options[elementSelect.selectedIndex].value;
			this.ARManager.editConnective(cedent, connectiveName);
			event.stop();
		}.bind(this));
		
		// close
		var elementClose = $('edit-connective-close');
		elementClose.addEvent('click', function (event) {
			this.ARManager.closeEditConnectiveWindow();
			event.stop();
		}.bind(this));
	},
	
	registerFieldAREventHandlers: function (field, cedent) {
		// remove field
		var elementField = $(field.getCSSRemoveID());
		elementField.addEvent('click', function (event) {
			this.ARManager.removeField(field);
			event.stop();
		}.bind(this));
		
		// change field sign
		$(field.getCSSChangeSignID()).addEvent('click', function (event) {
			this.ARManager.changeFieldSign(field);
			event.stop();
		}.bind(this));
		
		var elMark = $(field.getCSSMarkID());
		if (cedent.getNumLiteralRefs() > 1 && elMark) {
			// mark / unmark rule
			elMark.addEvent('click', function (event) {
				this.ARManager.changeMark(field);
				event.stop();
			}.bind(this));
		}
		
		// drag & drop
		$(field.getCSSDragID()).addEvent('mousedown', function (event) {
			event.stop();
			
			// disable right click drag & drop
			if (event.rightClick) {
				return false;
			}
			
			var draggedField = $(field.getCSSDragID());
			var clone = draggedField.clone().setStyles(draggedField.getCoordinates()).setStyles({
				opacity: 0.7,
				position: 'absolute'
			}).inject(document.body);
			
		    var drag = new Drag.Move(clone, {
		    	droppables: $$('div.cedent'),
		        //droppables: $$('div.cedent:not(div#' + cedent.getCSSID() + ')'),
	
		        onDrop: function (dragging, elementCedent) {
		        	dragging.destroy();
		        	if (elementCedent === $(cedent.getCSSID())) { return; };
		        	
		        	if (elementCedent !== null) {
		        		elementCedent.fireEvent('addFieldAR', field);
		        		this.UIColorizer.cedentDragDrop(elementCedent);
		        	}
		        }.bind(this),
		        
		        onEnter: function (dragging, elementCedent) {
		        	if (elementCedent === $(cedent.getCSSID())) { return; };
		        	this.UIColorizer.cedentDragEnter(elementCedent);
		        }.bind(this),
		        
		        onLeave: function (dragging, elementCedent) {
		        	if (elementCedent === $(cedent.getCSSID())) { return; };
		        	this.UIColorizer.cedentDragLeave(elementCedent);
		        }.bind(this),
		        
		        onCancel: function (dragging) {
		        	dragging.destroy();
		        }
		    });
			
			drag.start(event);
		}.bind(this));
	},
	
	registerFoundRuleEventHandlers: function(rule) {
		// mark
		$(rule.getFoundRuleCSSMarkID()).addEvent('click', function (event) {
			event.stop();
			this.ARManager.markFoundRule(rule);
		}.bind(this));
		
		// remove
		$(rule.getFoundRuleCSSRemoveID()).addEvent('click', function (event) {
			event.stop();
			this.ARManager.removeFoundRule(rule);
		}.bind(this));
	}
	
});