var UIPainter = new Class({
	
    ARBuilder: null,
	config: null,
    $settings: null,
	UIColorizer: null,
	UIListener: null,
    dateHelper: null,
    UITemplateRegistrator: null,
    $UIScroller: null,
    $UIStructurePainter: null,

	pager: null,
	
	rootElement: null,
	i18n: null,

	callbackStack: [],

	// sort attributes
	sortDuration: 750,
	morphDuration: 500,
	
	// dispose element
	disposeDuration: 750,
	
	initialize: function (ARBuilder, config, settings, i18n, UIColorizer, UIListener, dateHelper, UITemplateRegistrator, UIScroller, UIStructurePainter) {
		this.ARBuilder = ARBuilder;
        this.config = config;
        this.$settings = settings;
		this.rootElement = $(this.config.getRootElementID());
		this.i18n = i18n;
		this.UIColorizer = UIColorizer;
		this.UIListener = UIListener;
		this.dateHelper = dateHelper;
		this.UITemplateRegistrator = UITemplateRegistrator;
        this.$UIScroller = UIScroller;
        this.$UIStructurePainter = UIStructurePainter;
	},

	createUI: function () {
		this.renderNavigation();
		this.renderActiveRule();
	},

	renderNavigation: function () {
		// attributes
		this.renderAttributes();

        // data fields
        this.renderDataFields();
	},
	
	renderAttributes: function(navigation) {
		if (this.ARBuilder.getARManager().getAttributesByGroup() === true) {
			this.renderAttributesByGroup(navigation.getElement('ul'));
		} else {
			this.renderAttributesByList();
		}

        if (this.ARBuilder.getDD().hasHiddenAttributes()) {
            this.$UIStructurePainter.showHiddenAttributesButton();
        } else {
            this.$UIStructurePainter.hideHiddenAttributesButton();
        }
	},
	
	renderAttributesByGroup: function (elementParent) {
		elementParent = elementParent || $$('nav#navigation ul')[0];
		elementParent.setAttribute('id', 'attributes-by-group');
		if (elementParent.hasChildNodes()) {
			elementParent.empty();
		}

		elementParent.grab(this.initFieldGroup(this.ARBuilder.getFGC().getFieldGroupRootConfigID()));
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

		Object.each(this.ARBuilder.getDD().getAttributes(), function (attribute) {
			this.renderAttributeByList(attribute, elementParent);
		}.bind(this));
	},
	
	renderAttributeByList: function (attribute, elementParent) {
		if (elementParent) { // insert
            var elementAttribute = Mooml.render('attributeByListTemplate', {i18n: this.i18n, isUsed: this.ARBuilder.getARManager().getActiveRule().isAttributeUsed(attribute), attribute: attribute, showEditAttribute: this.$settings.isAttributeEditAllowed(), showRemoveAttribute: this.$settings.isAttributeDeleteAllowed()});
			elementParent.grab(elementAttribute);
			this.UIListener.registerAttributeEventHandler(attribute, this.$settings.isAttributeEditAllowed(), this.$settings.isAttributeDeleteAllowed());
		} else { // re-render
			var element = $(attribute.getCSSID());
			element.set('morph', {duration: this.morphDuration});
            element.removeAttribute('class');
            if (attribute.isRecommended()) {
                element.addClass('rec1');
			} else if (attribute.isPartiallyRecommended()) {
                element.addClass('rec2');
			} else if (this.ARBuilder.getARManager().getActiveRule().isAttributeUsed(attribute)) {
				element.morph({
					'color': '#AAA'});
			} else {
				element.morph({
					'color': '#434343'});
			}
		}
	},

    renderAddAttributeWindow: function(field) {
        var overlay = this.$UIStructurePainter.showOverlay();
        var url = this.config.getAddAttributeURL(field.getName());
        var window = Mooml.render('addAttributeTemplate', {i18n: this.i18n, url: url});
        overlay.grab(window);

        this.$UIScroller.scrollTo(0, 0);
    },

    renderEditAttributeWindow: function (attribute) {
        var overlay = this.$UIStructurePainter.showOverlay();
        var url = this.config.getEditAttributeURL(attribute.getName());
        var window = Mooml.render('editAttributeTemplate', {i18n: this.i18n, url: url});
        overlay.grab(window);

        this.$UIScroller.scrollTo(0, 0);
    },

    removeAttribute: function(attribute) {
        $(attribute.getCSSID()).getParent().destroy();
    },

    renderDataFields: function() {
        var dataFields = $$('#data-fields ul')[0];
        dataFields.empty();
        this.ARBuilder.getDD().getFields().each(function(field) {
            this.renderDataField(field, dataFields);
            this.UIListener.registerDataFieldEventHandler(field);
        }.bind(this));
    },

    renderDataField: function(field, elementParent) {
        var DF = Mooml.render('dataFieldTemplate', {field: field});
        elementParent.grab(Mooml.render('dataFieldTemplate', {field: field}));
    },
	
	sortAttributes: function (positions) {
		var sorter = new Fx.Sort($$('#attributes > div > ul > li'), {
			transition: Fx.Transitions.Cubic.easeInOut,
			duration: this.sortDuration
		});
		
		sorter.sort(positions).chain(function () {
			sorter.rearrangeDOM();
			
			Array.each(this.ARBuilder.getDD().getAttributes(), function (attribute) {
				this.renderAttributeByList(attribute);
			}.bind(this));
			
			this.renderAttributes.delay((this.sortDuration + this.morphDuration) * 1.5, this);
		}.bind(this));
	},
	
	renderMarkedRules: function (elementParent, markedRules) {
		if (!elementParent) { // re-render
			elementParent = $$('#marked-rules ul')[0];
			elementParent.empty();
		}

		var i = 0;
		Object.each(markedRules, function (FR) {
			FR.getRule().setId(++i);
			var elementRule = Mooml.render('markedRuleTemplate', {i18n: this.i18n, rule: FR.getRule()});
			elementParent.grab(elementRule);
			this.UIListener.registerMarkedRuleEventHandlers(FR);
		}.bind(this));

		var sortables = new Sortables(elementParent, {
			clone: true,
			revert: true,
			
			onComplete: function (element) {
				this.ARBuilder.getARManager().sortMarkedRules(sortables.serialize());
			}.bind(this)
		});
	},
	
	initFieldGroup: function (id) { // recursive
		var FG = this.ARBuilder.getFGC().getFieldGroup(id);

		var returnEl = new Element('li', {id: 'fg-' + id + '-name', 'class': 'field-group-drag', html: '<span>' + FG.getLocalizedName() + '</span>', title: FG.getExplanation()});
		var FGEl = new Element('ul', {id: 'fg-' + id, 'class': 'field-group'}).inject(returnEl);
	    this.callbackStack.push({func: this.UIListener.registerFieldGroupEventHandler, args: [FG]});

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
	
	renderActiveRule: function () {
        Mooml.render('activeRuleTemplate', {rules: this.ARBuilder.getARManager().display4ftTaskBox(), attributes: this.ARBuilder.getARManager().displayETreeTaskBox(), i18n: this.i18n, displayAddIM: this.ARBuilder.getARManager().hasPossibleIMs()}).replaces($('active-rule'));

        var elementParent = $('antecedent');
		this.renderCedent(this.ARBuilder.getARManager().getActiveRule().getAntecedent(), elementParent);
		
		Object.each(this.ARBuilder.getARManager().getActiveRule().getIMs(), function(IM, key) {
			this.renderIM(IM);
		}.bind(this));
		
		var elementParent = $('succedent');
		this.renderCedent(this.ARBuilder.getARManager().getActiveRule().getSuccedent(), elementParent);
		
		this.UIListener.registerActiveRuleEventHandlers(this.ARBuilder.getARManager().getActiveRule());
		
		this.$UIStructurePainter.resizeApplication();
	},
	
	renderCedent: function (cedent, elementParent) {
		var elementCedent = Mooml.render('cedentTemplate', {rule: this.ARBuilder.getARManager().getActiveRule(), cedent: cedent, i18n: this.i18n});
		if (elementParent !== null) { // new cedent
			elementParent.grab(elementCedent);
		} else { // re-render
			// re-render
			elementCedent.replaces($(cedent.getCSSID()));
		}

        var noRestriction = this.ARBuilder.getDefFL().hasCedentNoRestriction(cedent);
        if (noRestriction) {
            tips = new Tips('.no-restriction');
            tips.addEvent('show', function(tip, el){
                tip.addClass('tip-visible');
            });
            tips.addEvent('hide', function(tip, el){
                tip.removeClass('tip-visible');
            });

            var elementNoRestriction = Mooml.render('noRestrictionTemplate', {i18n: this.i18n});
            elementParent.grab(elementNoRestriction);
            tips.attach(elementNoRestriction);
        }

		var elementFields = elementCedent.getElement('div.fields');

        var index = 1;

        if (cedent.hasOpeningBracket(index)) {
            var elementLeftBracket = Mooml.render('bracketTemplate', {isLeft: true});
            elementFields.grab(elementLeftBracket);
        }

        var settings = this.ARBuilder.getARManager().getActiveRule().toSettings()[cedent.getScope()];
        var markFieldsAllowed = (cedent.getNumFields(cedent.getLevel()) > 2) &&
            (this.ARBuilder.getDefFL().isConnectiveAllowed('Conjunction', cedent.getScope(), settings, cedent.getNextLevel()) || this.ARBuilder.getDefFL().isConnectiveAllowed('Disjunction', cedent.getScope(), settings, cedent.getNextLevel()));

        cedent.getChildren().each(function(child) {
            if (instanceOf(child, Cedent)) { // Cedent
                this.renderCedent(child, elementFields);
            } else { // FieldAR
                this.renderField(child, elementFields, cedent, markFieldsAllowed);
            }

            if (index < cedent.getNumChildren()) { // Connective
                this.renderConnective(cedent.getConnective(), elementFields);
            }

            index++;
        }.bind(this));

        if (cedent.hasClosingBracket(index - 1)) {
            var rightBracket = Mooml.render('bracketTemplate', {isLeft: false});
            elementFields.grab(rightBracket);
        }

		this.UIListener.registerCedentEventHandlers(cedent, this.ARBuilder.getARManager().getActiveRule());
	},
	
	renderField: function (field, elementParent, cedent, markFielsdAllowed) {
        if (elementParent !== null) { // new field
			var elementField = Mooml.render('fieldTemplate', {field: field, i18n: this.i18n, markFieldAllowed: markFielsdAllowed});
			elementParent.grab(elementField);
		} else { // re-render
			var elementField = Mooml.render('fieldTemplate', {field: field, i18n: this.i18n, markFieldAllowed: markFielsdAllowed});
			elementField.replaces($(field.getCSSID()));
		}
		
		if (field.getType() !== null) {
			this.UIListener.registerEditCoefficientEventHandler(field);
			this.UIListener.registerFieldAREventHandlers(field, cedent);
		}
	},

    renderConnective: function (connective, elementParent) {
        var elementConnective = Mooml.render('connectiveTemplate', {connective: connective, i18n: this.i18n});
        elementParent.grab(elementConnective);
    },

	renderIM: function (IM) {
		var elementParent = $$('div#interest-measures > div')[0];
		elementParent.grab(Mooml.render('interestMeasureTemplate', {IM: IM, i18n: this.i18n}));
	    this.UIListener.registerIMEventHandler(IM);
	}, 
	
	renderAddIMWindow: function (IMs) {
		var overlay = this.$UIStructurePainter.showOverlay();
		overlay.grab(Mooml.render('addIMWindowTemplate', {i18n: this.i18n}));
		var selectedIM = IMs[Object.keys(IMs)[0]];
		Object.each(IMs, function (IM) {
			var isSelected = (IM.getName() === selectedIM.getName());
			$('add-im-select').grab(Mooml.render('IMWindowSelectOptionTemplate', {IM: IM, isSelected: isSelected}));
		}.bind(this));
		
		this.renderIMAutocomplete('add', selectedIM);

		this.UIListener.registerIMFormEventHandler('add');
	},
	
	renderEditIMWindow: function(IMs, selectedIM) {
		var overlay = this.$UIStructurePainter.showOverlay();
		overlay.grab(Mooml.render('editIMWindowTemplate', {i18n: this.i18n, IM: selectedIM}));
		Object.each(IMs, function (IM) {
			var isSelected = (IM.getName() === selectedIM.getName());
			$('edit-im-select').grab(Mooml.render('IMWindowSelectOptionTemplate', {IM: IM, isSelected: isSelected}));
		}.bind(this));
		
		this.renderIMAutocomplete('edit', selectedIM);

		this.UIListener.registerIMFormEventHandler('edit');
	},
	
	renderIMAutocomplete: function (action, selectedIM) {
		var elAutocomplete = $(action + '-im-form').getElement('.autocomplete').empty();
		Array.each(selectedIM.getFields(), function (f) {
			var IMSlider = new InterestMeasureSlider(elAutocomplete, f, action, selectedIM);
		}.bind(this));
	},
	
	renderAddCoefficientWindow: function (field) {
		var overlay = this.$UIStructurePainter.showOverlay();
		overlay.grab(Mooml.render('addCoefficientWindowTemplate', {i18n: this.i18n}));
		var selectedCoefficient = this.ARBuilder.getFL().getDefaultBBACoef();
		this.renderAddCoefficientAutocomplete(field, selectedCoefficient);
	},
	
	renderEditCoefficientWindow: function (field) {
		var overlay = this.$UIStructurePainter.showOverlay();
		overlay.grab(Mooml.render('editCoefficientWindowTemplate', {i18n: this.i18n}));
		var selectedCoefficient = this.ARBuilder.getFL().getBBACoefficient(field.getType());
		this.renderEditCoefficientAutocomplete(field, selectedCoefficient);
	},
	
	renderAddCoefficientAutocomplete: function(field, selectedCoefficient) { 
		Mooml.render('addCoefficientWindowAutocompleteTemplate', {i18n: this.i18n, selectedCoefficient: selectedCoefficient}).replaces($('add-coefficient-autocomplete'));
		
		Object.each(this.ARBuilder.getFL().getBBACoefficients(), function (BBACoefficient) {
			var isSelected = (BBACoefficient.getName() === selectedCoefficient.getName());
			$('add-coefficient-select').grab(Mooml.render('addCoefficientWindowSelectOptionTemplate', {coefficient: BBACoefficient, isSelected: isSelected}));
		}.bind(this));
		
		if (selectedCoefficient.getName() === 'One category') {
			var select = $('add-coefficient-category');
			Array.each(field.getRef().getChoices(), function (choice) {
				select.grab(Mooml.render('addCoefficientWindowSelectOption2Template', {choice: choice}));
			});
		} else {
			if (selectedCoefficient.fields.minLength.minValue < selectedCoefficient.fields.minLength.maxValue) {
				var coefficientSlider1 = new CoefficientAddSlider($('add-coefficient-minlength-slider'), $('add-coefficient-minlength'), selectedCoefficient.fields.minLength);
			} else {
				$('add-coefficient-minlength').set('value', selectedCoefficient.fields.minLength.minValue);
				$('add-coefficient-minlength-slider').setStyles({display: 'none'});
			}
			if (selectedCoefficient.fields.maxLength.minValue < selectedCoefficient.fields.maxLength.maxValue) {
				var coefficientSlider2 = new CoefficientAddSlider($('add-coefficient-maxlength-slider'), $('add-coefficient-maxlength'), selectedCoefficient.fields.maxLength);
			} else {
				$('add-coefficient-maxlength').set('value', selectedCoefficient.fields.maxLength.minValue);
				$('add-coefficient-maxlength-slider').setStyles({display: 'none'});
			}
		}
		
		this.UIListener.registerAddCoefficientFormEventHandler(field);
	},
	
	renderEditCoefficientAutocomplete: function(field, selectedCoefficient) { 
		Mooml.render('editCoefficientWindowAutocompleteTemplate', {field: field, i18n: this.i18n, selectedCoefficient: selectedCoefficient}).replaces($('edit-coefficient-autocomplete'));
		
		Object.each(this.ARBuilder.getFL().getBBACoefficients(), function (BBACoefficient) {
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
			if (selectedCoefficient.fields.minLength.minValue < selectedCoefficient.fields.minLength.maxValue) {
				var coefficientSlider1 = new CoefficientEditSlider($('edit-coefficient-minlength-slider'), $('edit-coefficient-minlength'), selectedCoefficient.fields.minLength);
			} else {
				$('edit-coefficient-minlength').set('value', selectedCoefficient.fields.minLength.minValue);
				$('edit-coefficient-minlength-slider').setStyles({display: 'none'});
			}
			if (selectedCoefficient.fields.maxLength.minValue < selectedCoefficient.fields.maxLength.maxValue) {
				var coefficientSlider2 = new CoefficientEditSlider($('edit-coefficient-maxlength-slider'), $('edit-coefficient-maxlength'), selectedCoefficient.fields.maxLength, coefficientSlider1);
			} else {
				$('edit-coefficient-maxlength').set('value', selectedCoefficient.fields.maxLength.minValue);
				$('edit-coefficient-maxlength-slider').setStyles({display: 'none'});
			}
		}
		
		this.UIListener.registerEditCoefficientFormEventHandler(field);
	},
	
	renderEditConnectiveWindow: function (cedent) {
		var overlay = this.$UIStructurePainter.showOverlay();
		overlay.grab(Mooml.render('editConnectiveWindowTemplate', {i18n: this.i18n}));

        if (this.ARBuilder.getFL().isConnectiveAllowed('Conjunction', cedent.getScope(), this.ARBuilder.getARManager().getActiveRule().toSettings()[cedent.getScope()], cedent.getLevel())) {
            $('edit-connective-select').grab(Mooml.render('editConnectiveWindowSelectOptionTemplate', {isSelected: cedent.getConnective().getName() === 'Conjunction', connective: 'Conjunction'}));
        }

        if (this.ARBuilder.getFL().isConnectiveAllowed('Disjunction', cedent.getScope(), this.ARBuilder.getARManager().getActiveRule().toSettings()[cedent.getScope()], cedent.getLevel())) {
            $('edit-connective-select').grab(Mooml.render('editConnectiveWindowSelectOptionTemplate', {isSelected: cedent.getConnective().getName() === 'Disjunction', connective: 'Disjunction'}));
        }

		this.UIListener.registerEditConnectiveFormEventHandler(cedent);
	},
	
	/* found rules */
	updateFoundRule: function (FR) {
		var elFR = $(FR.getCSSID());
		if (!elFR) { return; }
		
		if (FR.isInteresting() || FR.isException()) {
			elFR.set('morph', {duration: this.morphDuration});
			elFR.setStyle('cursor', 'help');
			
			var elFRInfo = elFR.getElement('.info');
            // TODO refactor all into external CSS style
			elFRInfo.setStyle('display', 'block');
			var elFRHelp = elFRInfo.getElement('.help');
			if (FR.isInteresting()) {
				if (FR.getInteresting()) { // marked as interesting
					elFR.setStyle('font-weight', 'bold');
					
					elFR.store('tip:text', this.i18n.translate('Association rule is novel.'));
				} else { // marked as not interesting
					Array.each(elFR.getChildren('*:not(span.info)'), function (child) {
						child.set('morph', {duration: this.morphDuration});
						child.morph({'opacity': '0.3'});
					}.bind(this));
					
					elFR.store('tip:text', this.i18n.translate('Association rule confirms an already known rule.'));
				}
			} else if (FR.isException()) {
				elFR.setStyle('font-weight', 'bold');
				elFR.morph({'color': '#C91E1D'});
				
				elFR.store('tip:text', this.i18n.translate('This rule is an exception to a rule stored in knowledge base.'));
			}
			
			if (!this.ARBuilder.getFL().getAutoSuggest()) {
				var elBK = elFR.getElement('.bk');
				elBK.morph({'display': 'none'});
			}
			
		}
		
		var elLoading = elFR.getElement('.loading');
		if (elLoading) {
			elLoading.set('morph', {duration: this.morphDuration});
			elLoading.morph({
				'opacity': '0.0'
			});
		}
	},
	
	showFRLoading: function (FR) {
		var elLoading = $(FR.getCSSID()).getElement('.loading');
		elLoading.set('morph', {duration: this.morphDuration});
		elLoading.morph({
			'opacity': '1',
			'visibility': 'visible'
		});
	},
		
	/* navigation */
	showETreeProgress: function () {
		$('etree-progress').fade('in');
    },
	
	hideETReeProgress: function () {
		$('etree-progress').fade('out');
	},
    
	morph: function (el, options, duration) {
		duration = duration || this.morphDuration;
		el.set('morph', {duration: duration});
		el.morph(options);
	},
	
    showStopMiningButton: function() {
        $('stop-mining').setStyle('visibility', 'visible');
    },

    hideStopMiningButton: function() {
        $('stop-mining').setStyle('visibility', 'hidden');
    },

    updateDownloadButtons: function(settingPath, resultPath) {
        $('view-task-setting').setStyle('visibility', 'visible');
        $('view-task-setting').set('href', settingPath);

        $('view-task-result').setStyle('visibility', 'visible');
        $('view-task-result').set('href', resultPath);
    },

    hideDownloadButtons: function() {
        $('view-task-setting').setStyle('visibility', 'hidden');
        $('view-task-result').setStyle('visibility', 'hidden');
    },

    hideOverlay: function() {
        this.$UIStructurePainter.hideOverlay();
    }

});