/*global Class: false, $: false, Element: false */ 

var UIPainter = new Class({
	
	config: null,
	dataContainer: null,
	ARManager: null,
	ETreeManager: null,
	miningManager: null,
	UIColorizer: null,
	UIListener: null,
	
	rootElement: null,
	i18n: null,
	dateHelper: null,
	UITemplateRegistrator: null,
	
	callbackStack: [],

	// sort attributes
	sortDuration: 1000,
	morphDuration: 500,
	
	// dispose element
	disposeDuration: 750,
	
	initialize: function (config, dataContainer, ARManager, miningManager, UIColorizer, UIListener) {
		this.config = config;
		this.rootElement = $(this.config.getRootElementID());
		this.i18n = new i18n(this.config.getLang());
		this.dataContainer = dataContainer;
		this.ARManager = ARManager;
		this.miningManager = miningManager;
		this.UIColorizer = UIColorizer;
		this.UIListener = UIListener;
		this.dateHelper = new DateHelper();
		this.UITemplateRegistrator = new UITemplateRegistrator();
	},
	
	createUI: function () {
		this.UITemplateRegistrator.registerAll();
		
		this.renderAll();
	},
	
	renderAll: function () {
		this.renderStructure();
		this.renderNavigation();
		this.renderContent();
	},
	
	renderStructure: function () {
		this.rootElement.grab(Mooml.render('overlayTemplate'));
		this.rootElement.grab(Mooml.render('headerTemplate', {config: this.config, i18n: this.i18n}));
		this.rootElement.grab(Mooml.render('mainTemplate', {config: this.config, dateHelper: this.dateHelper, i18n: this.i18n}));
		this.rootElement.grab(Mooml.render('footerTemplate', {config: this.config, i18n: this.i18n}));
	},
	
	renderNavigation: function () {
		var navigation = $('navigation');
		
		// attributes
		this.renderAttributes(navigation);
	},
	
	renderAttributes: function(navigation) {
		navigation = navigation || $('navigation');
		
		var attributes = $('attributes');
		if (attributes) {
			Mooml.render('attributesStructureTemplate', {i18n: this.i18n, byGroup: this.ARManager.getAttributesByGroup()}).replaces(attributes);
		} else {
			navigation.grab(Mooml.render('attributesStructureTemplate', {i18n: this.i18n, byGroup: this.ARManager.getAttributesByGroup()}));
		}
		
		if (this.ARManager.getAttributesByGroup() === true) {
			this.renderAttributesByGroup(navigation.getElement('ul'));
		} else {
			this.renderAttributesByList();
		}
		
		this.UIListener.registerNavigationEventHandlers();
	},
	
	renderAttributesByGroup: function (elementParent) {
		elementParent = elementParent || $$('nav#navigation ul')[0];
		elementParent.setAttribute('id', 'attributes-by-group');
		if (elementParent.hasChildNodes()) {
			elementParent.empty();
		}

		elementParent.grab(this.initFieldGroup(this.dataContainer.getFieldGroupRootConfigID()));
		while (callback = this.callbackStack.pop()) {
			callback.func.apply(this.UIListener , callback.args);
		}
	},
	
	renderAttributesByList: function() {
		var elementParent = $$('nav#navigation ul')[0];
		elementParent.setAttribute('id', 'attributes-by-list');
		if (elementParent.hasChildNodes()) {
			elementParent.empty();
		}
		
		Object.each(this.dataContainer.getAttributes(), function (attribute) {
			this.renderAttributeByList(attribute, elementParent);
		}.bind(this));
	},
	
	renderAttributeByList: function (attribute, elementParent) {
		if (elementParent) { // insert
			elementParent.grab(Mooml.render('attributeByListTemplate', {ARManager: this.ARManager, attribute: attribute}));
			this.UIListener.registerAttributeEventHandler(attribute);
		} else { // re-render
			var element = $(attribute.getCSSID());
			element.set('morph', {duration: this.morphDuration});
			if (attribute.isRecommended()) {
				element.morph({
					'background-image': 'url(images/icon-rec1.png',
					'background-repeat': 'no-repeat',
					'color': '#434343'});
			} else if (attribute.isPartiallyRecommended()) {
				element.morph({
					'background-image': 'url(images/icon-rec2.png',
					'background-repeat': 'no-repeat',
					'color': '#434343'});
			} else if (this.ARManager.isAttributeUsed(attribute)) {
				element.morph({
					'background-image': 'none',
					'color': '#AAA'});
			} else {
				element.morph({
					'background-image': 'none',
					'color': '#434343'});
			}
		}
	},
	
	sortAttributes: function (attributes, positions) {
		var sorter = new Fx.Sort($$('#attributes > ul > li'), {
			transition: Fx.Transitions.Cubic.easeInOut,
			duration: this.sortDuration
		});
		sorter.sort(positions); //a specific order
		
		Array.each(attributes, function (attribute) {
			this.renderAttributeByList.delay(this.sortDuration + this.morphDuration, this, attribute);
		}.bind(this));
		
		//sorter.rearrangeDOM.delay((this.sortDuration + this.morphDuration) * 2.1, sorter);
		this.renderAttributes.delay((this.sortDuration + this.morphDuration) * 2.2, this);
	},
	
	renderMarkedRulesBox: function () {
		var main = $('main');
		
		// marked rules
		var elementMarkedRules = Mooml.render('markedRulesStructureTemplate', {i18n: this.i18n});
		main.grab(elementMarkedRules);
		this.renderMarkedRules(elementMarkedRules.getElement('ul'));
	},
	
	renderMarkedRules: function (elementParent) {
		if (!elementParent) { // re-render
			elementParent = $$('#marked-rules ul')[0];
			elementParent.empty();
		}
		
		var i = 0;
		Object.each(this.ARManager.getMarkedRules(), function (rule) {
			rule.setId(++i);
			var elementRule = Mooml.render('markedRuleTemplate', {i18n: this.i18n, rule: rule});
			elementParent.grab(elementRule);
			this.UIListener.registerMarkedRuleEventHandlers(rule);
		}.bind(this));
		
		var sortables = new Sortables(elementParent, {
			clone: true,
			revert: true,
			
			onComplete: function (element) {
				this.ARManager.sortMarkedRules(sortables.serialize());
			}.bind(this)
		});
	},
	
	initFieldGroup: function (id) { // recursive
		var FG = this.dataContainer.fieldGroups[id];

		var returnEl = new Element('li', {id: 'fg-' + id + '-name', 'class': 'field-group-drag', html: '<span>' + FG.getLocalizedName() + '</span>', title: FG.getExplanation()});
		var FGEl = new Element('ul', {id: 'fg-' + id, 'class': 'field-group'}).inject(returnEl);
		//if (FG.getId() !== 1) {
			this.callbackStack.push({func: this.UIListener.registerFieldGroupEventHandler, args: [FG]});
		//}

		if (Object.getLength(FG.getFields()) > 0) {
			Object.each(FG.getFields(), function (field, key) {
				new Element('li', {id: field.getCSSID(), html: field.toString()}).inject(FGEl);
				this.callbackStack.push({func: this.UIListener.registerFieldEventHandler, args: [field]});
			}.bind(this));
		}
		
		Array.each(FG.getChildGroups(), function (value, key) {
			this.initFieldGroup(value).inject(FGEl); // call recursion
		}.bind(this));
		
		return returnEl;
	},
	
	renderContent: function () {
		this.renderActiveRule();
		this.renderMarkedRulesBox();
	},
	
	renderActiveRule: function () {
		Mooml.render('activeRuleTemplate', {rules: this.ARManager.display4ftTaskBox(), attributes: this.ARManager.displayETreeTaskBox(), i18n: this.i18n, displayAddIM: this.ARManager.hasPossibleIMs()}).replaces($('active-rule'));
		
		var elementParent = $('antecedent');
		this.renderCedent(this.ARManager.getActiveRule().getAntecedent(), elementParent);
		
		Object.each(this.ARManager.getActiveRule().getIMs(), function(IM, key) {
			this.renderIM(IM);
		}.bind(this));
		
		var elementParent = $('succedent');
		this.renderCedent(this.ARManager.getActiveRule().getSuccedent(), elementParent);
		
		this.UIListener.registerActiveRuleEventHandlers(this.ARManager.getActiveRule());
	},
	
	renderCedent: function (cedent, elementParent) {
		var elementCedent = Mooml.render('cedentTemplate', {rule: this.ARManager.getActiveRule(), cedent: cedent, i18n: this.i18n});
		if (elementParent !== null) { // new cedent
			elementParent.grab(elementCedent);
		} else { // re-render
			// re-render
			elementCedent.replaces($(cedent.getCSSID()));
		}

		if (cedent.getNumLiterals() <= 1) {
			$(cedent.getCSSEditConnectiveID()).hide();
		}
		
		var elementFields = elementCedent.getElement('div.fields');
		if (cedent.displayChangeSign()) {
			var elementCedentSign = Mooml.render('cedentSignTemplate', {cedent: cedent});
			elementFields.grab(elementCedentSign);
		}
		
		if (cedent.hasBrackets()) {
			var elementLeftBracket = Mooml.render('bracketTemplate', {isLeft: true});
			elementFields.grab(elementLeftBracket);
		}

		var numChildren = cedent.getNumChildren();
		var i = 0;
		Object.each(cedent.getLiteralRefs(), function (field) {
			this.renderField(field, elementFields, cedent);
			
			if (++i !== numChildren && i !== cedent.getNumLiteralRefs()) {
				var connective = Mooml.render('connectiveTemplate', {connective: cedent.getConnective(), i18n: this.i18n});
				elementFields.grab(connective);
			}
		}.bind(this));
		
		if (cedent.hasBrackets()) {
			var rightBracket = Mooml.render('bracketTemplate', {isLeft: false});
			elementFields.grab(rightBracket);
		}

		if (i > 0 && i < numChildren) {
			var connective = Mooml.render('connectiveTemplate', {connective: cedent.getConnective(), i18n: this.i18n});
			elementFields.grab(connective);
		}

		Object.each(cedent.getChildCedents(), function (childCedent) {
			this.renderCedent(childCedent, $(cedent.getCSSFieldsID()));
			
			if (++i !== numChildren) {
				var connective = Mooml.render('connectiveTemplate', {connective: cedent.getConnective(), i18n: this.i18n});
				elementFields.grab(connective);
			}
		}.bind(this));
		
		this.UIListener.registerCedentEventHandlers(cedent, this.ARManager.getActiveRule());
	},
	
	renderField: function (field, elementParent, cedent) {
		if (elementParent !== null) { // new field
			var elementField = Mooml.render('fieldTemplate', {field: field, i18n: this.i18n, cedent: cedent});
			elementParent.grab(elementField);
		} else { // re-render
			var elementField = Mooml.render('fieldTemplate', {field: field, i18n: this.i18n, cedent: cedent});
			elementField.replaces($(field.getCSSID()));
		}
		
		if (field.getType() !== null) {
			this.UIListener.registerEditCoefficientEventHandler(field);
			this.UIListener.registerFieldAREventHandlers(field, cedent);
		}
	},
	
	renderIM: function (IM) {
		var elementParent = $$('div#interest-measures > div')[0];
		elementParent.grab(Mooml.render('interestMeasureTemplate', {IM: IM, i18n: this.i18n}));
		
	    var elementSlider = $(IM.getCSSSliderID());
	    var IMSlider = new InterestMeasureARSlider(elementSlider, IM);	
	    
	    this.UIListener.registerIMEventHandler(IM);
	}, 
	
	renderAddIMWindow: function (IMs) {
		var overlay = this.showOverlay();
		overlay.grab(Mooml.render('addIMWindowTemplate', {i18n: this.i18n}));
		var selectedIM = IMs[Object.keys(IMs)[0]];
		this.renderAddIMAutocomplete(IMs, selectedIM);
	},
	
	renderAddIMAutocomplete: function (IMs, selectedIM) {
		Mooml.render('addIMWindowAutocompleteTemplate', {i18n: this.i18n}).replaces($('add-im-autocomplete'));
		Object.each(IMs, function (IM) {
			var isSelected = (IM.getName() === selectedIM.getName());
			$('add-im-select').grab(Mooml.render('addIMWindowSelectOptionTemplate', {IM: IM, isSelected: isSelected}));
			if (isSelected === true) {
				var IMSlider = new InterestMeasureAddSlider($('add-im-slider'), IM);
			}
		}.bind(this));
		
		this.UIListener.registerAddIMFormEventHandler();
	},
	
	renderAddCoefficientWindow: function (field) {
		var overlay = this.showOverlay();
		overlay.grab(Mooml.render('addCoefficientWindowTemplate', {i18n: this.i18n}));
		var selectedCoefficient = this.dataContainer.getBBACoefficients()[Object.keys(this.dataContainer.getBBACoefficients())[0]];
		this.renderAddCoefficientAutocomplete(field, selectedCoefficient);
	},
	
	renderEditCoefficientWindow: function (field) {
		var overlay = this.showOverlay();
		overlay.grab(Mooml.render('editCoefficientWindowTemplate', {i18n: this.i18n}));
		var selectedCoefficient = this.dataContainer.getBBACoefficient(field.getType());
		this.renderEditCoefficientAutocomplete(field, selectedCoefficient);
	},
	
	renderAddCoefficientAutocomplete: function(field, selectedCoefficient) { 
		Mooml.render('addCoefficientWindowAutocompleteTemplate', {i18n: this.i18n, selectedCoefficient: selectedCoefficient}).replaces($('add-coefficient-autocomplete'));
		
		Object.each(this.dataContainer.getBBACoefficients(), function (BBACoefficient) {
			var isSelected = (BBACoefficient.getName() === selectedCoefficient.getName());
			$('add-coefficient-select').grab(Mooml.render('addCoefficientWindowSelectOptionTemplate', {coefficient: BBACoefficient, isSelected: isSelected}));
		}.bind(this));
		
		if (selectedCoefficient.getName() === 'One category') {
			var select = $('add-coefficient-category');
			Array.each(field.getRef().getChoices(), function (choice) {
				select.grab(Mooml.render('addCoefficientWindowSelectOption2Template', {choice: choice}));
			});
		} else {
			var coefficientSlider1 = new CoefficientAddSlider($('add-coefficient-minlength-slider'), $('add-coefficient-minlength'), selectedCoefficient.fields.minLength);
			var coefficientSlider2 = new CoefficientAddSlider($('add-coefficient-maxlength-slider'), $('add-coefficient-maxlength'), selectedCoefficient.fields.maxLength);
		}
		
		this.UIListener.registerAddCoefficientFormEventHandler(field);
	},
	
	renderEditCoefficientAutocomplete: function(field, selectedCoefficient) { 
		Mooml.render('editCoefficientWindowAutocompleteTemplate', {field: field, i18n: this.i18n, selectedCoefficient: selectedCoefficient}).replaces($('edit-coefficient-autocomplete'));
		
		Object.each(this.dataContainer.getBBACoefficients(), function (BBACoefficient) {
			var isSelected = (BBACoefficient.getName() === selectedCoefficient.getName());
			$('edit-coefficient-select').grab(Mooml.render('editCoefficientWindowSelectOptionTemplate', {coefficient: BBACoefficient, isSelected: isSelected}));
		}.bind(this));
		
		if (selectedCoefficient.getName() === 'One category') {
			var select = $('edit-coefficient-category');
			Array.each(field.getRef().getChoices(), function (choice) {
				var isSelected = (choice === field.getCategory());
				select.grab(Mooml.render('editCoefficientWindowSelectOption2Template', {choice: choice, isSelected: isSelected}));
			});
		} else {
			var coefficientSlider1 = new CoefficientEditSlider($('edit-coefficient-minlength-slider'), $('edit-coefficient-minlength'), selectedCoefficient.fields.minLength);
			var coefficientSlider2 = new CoefficientEditSlider($('edit-coefficient-maxlength-slider'), $('edit-coefficient-maxlength'), selectedCoefficient.fields.maxLength, coefficientSlider1);
		}
		
		this.UIListener.registerEditCoefficientFormEventHandler(field);
	},
	
	renderEditConnectiveWindow: function (cedent) {
		var overlay = this.showOverlay();
		overlay.grab(Mooml.render('editConnectiveWindowTemplate', {i18n: this.i18n}));
		
		Object.each(cedent.constraint, function(allowed, connective) {
			if (allowed === true && connective !== 'Negation') {
				var isSelected = (connective === cedent.getConnective().getName());
				$('edit-connective-select').grab(Mooml.render('editConnectiveWindowSelectOptionTemplate', {isSelected: isSelected, connective: connective}));
			}
		}.bind(this));
		
		this.UIListener.registerEditConnectiveFormEventHandler(cedent);
	},
	
	/* found rules */
	renderFoundRules: function (rules) {
		elementFoundRules = $$('#found-rules ul')[0];
		elementFoundRules.empty();
		Array.each(rules, function (rule) {
			elementFoundRules.grab(Mooml.render('foundRuleTemplate', {rule: rule, i18n: this.i18n}));
			this.UIListener.registerFoundRuleEventHandlers(rule);
		}.bind(this));
	},
	
	clearCedentInfo: function (cedent) {
		$(cedent.getCSSInfoID()).empty();
	},
	
	/* navigation */
	showETreeProgress: function () {
		$('etree-progress').fade('in');
    },
	
	hideETReeProgress: function () {
		$('etree-progress').fade('out');
	},
    
    /* overlay */
    showOverlay: function () {
		var elementOverlay = $('overlay');
		elementOverlay.fade('in');
		
		return elementOverlay;
	},
	
	hideOverlay: function () {
		var elementOverlay = $('overlay');
		elementOverlay.fade('out');
		elementOverlay.empty();
		
		return elementOverlay;
	},
	
	/* misc */
	showElement: function (el, cls) {
		el.set('morph', {duration: this.disposeDuration});
		if (!cls) {
			el.morph({
				'opacity': '1',
				'display': 'block'
			});	
		} else {
			el.morph(cls);
		}
	},
	
	showMiningProgress: function () {
		var el = $('mining-in-progress');
		el.toggle();
		el.set('morph', {duration: this.disposeDuration});
		el.morph({
			'opacity': '1'
		});
	},
	
	hideMiningProgress: function () {
		var el = $('mining-in-progress');
		el.set('morph', {duration: this.disposeDuration});
		el.morph({
			'opacity': '0'
		});
		el.toggle.delay(this.disposeDuration, el);
	},
	
	disposeFoundRules: function () {
		var delay = 0;
		var delayTime = 100;
		Array.each($$('#found-rules li'), function (el) {
			delay += delayTime;
			this.disposeElement.delay(delay, this, el);
		}.bind(this));
	},
	
	disposeElement: function (el) {
		el.set('morph', {duration: this.disposeDuration});
		el.morph({
			'opacity': '0',
			'height': '0'
		});

		el.destroy.delay(this.disposeDuration, el);
	}
	
});