/*global Class: false, $: false, Element: false */ 

var UITemplateRegistrator = new Class({
	
	initialize: function () {
		this.registerAll();
	},
	
	registerAll: function () {
		this.registerStructure();		
		this.registerNavigation();
		this.registerActiveRule();
		this.registerAddIMWindow();
		this.registerAddCoefficientWindow();
		this.registerEditConnectiveWindow();
		this.registerFoundRule();
		this.registerMarkedRules();
		this.registerSettingsWindow();
	},
	
	registerStructure: function () {
		Mooml.register('overlayTemplate', function () {
			section({id: 'overlay'});
		});
		
		Mooml.register('headerTemplate', function (data) {
			i18n = data.i18n;
			config = data.config;
			
			header(div({id: 'settings'},
				a({href: '#', id: 'settings-open'}, i18n.translate('Settings'))),	
				h1(config.getName() + '<sup>' + config.getVersion() + '</sup><span>' + config.getSlogan() + '</span>'))
		});
		
		Mooml.register('mainTemplate', function (data) {
			config = data.config,
			dateHelper = data.dateHelper,
			i18n = data.i18n;
			
			div({id: 'wrapper'}, 
				section({id: 'main'},
					section({id: 'content'},
						section({id: 'active-rule'}),
						section({id: 'found-rules'}, 
							h2(i18n.translate('Found rules')),
							div({id: 'pager-label'}),
							div({id: 'paging'}),
							div({id: 'pager'},
								ul({'class': 'scroller'})),
							a({id: 'pager-clear', href: '#'}, i18n.translate('Clear rules'))
						)),
					nav({id: 'navigation'}),
					div({'class': 'clearfix'})));
		});
		
		Mooml.register('footerTemplate', function (data) {
			i18n = data.i18n;
			config = data.config;
			
			footer('&copy; ' + dateHelper.getYear() + ' ' + config.getCopyright() + ', ' + i18n.translate('created by') + ' ' + config.getAuthor())
		});
	},
	
	registerNavigation: function () {
		Mooml.register('attributesStructureTemplate', function (data) {
			byGroup = data.byGroup;
			inProgress = data.inProgress;
			i18n = data.i18n;
			
			if (byGroup) {
				section({id: 'attributes'}, 
					h2(i18n.translate('Attributes'), a({href: '#', 'class': 'dropdown'}, '')),
					div(
						ul(),
						span({id: 'etree-progress', styles: {'visibility': inProgress ? 'visible' : 'hidden'}}, i18n.translate('Sort in progress.')),
						div(a({id: 'attributes-by-list', href: '#'}, i18n.translate('attributes')))));
			} else {
				section({id: 'attributes'}, 
					h2(i18n.translate('Attributes'), a({href: '#', 'class': 'dropdown'}, '')),
					div(
						ul(),
						span({id: 'etree-progress', styles: {'visibility': inProgress ? 'visible' : 'hidden'}}, i18n.translate('Sort in progress.')),
						div(a({id: 'attributes-by-group', href: '#'}, i18n.translate('predefined attributes')))));	
			}
		});
		
		Mooml.register('attributeByListTemplate', function (data) {
			attribute = data.attribute;
			isUsed = data.isUsed;
			
			var className = '';
			if (attribute.isRecommended()) {
				className = 'rec1';
			} else if (attribute.isPartiallyRecommended()) {
				className = 'rec2';
			} else if (isUsed) {
				className = 'used';
			}
			
			li({id: attribute.getCSSID(), 'class': className}, attribute.getName());
		});
	},
	
	registerActiveRule: function () {
		Mooml.register('activeRuleTemplate', function (data) {
			rules = data.rules;
			attributes = data.attributes;
			taskBox = rules || attributes;
			i18n = data.i18n;
			displayAddIM = data.displayAddIM;
			
			if (taskBox) {
				var taskText = i18n.translate('Do you want to');
				if (rules && attributes) {
					taskText += ' <a href="#" id="mine-rules-confirm">' + i18n.translate('mine rules') + '</a> ' + i18n.translate('or') + ' <a href="#" id="recommend-attributes-confirm">' + i18n.translate('recommend next attribute') + '</a>' + '?';
				} else if (rules) {
					taskText += ' <a href="#" id="mine-rules-confirm">' + i18n.translate('mine rules') + '</a>' + '?';
				} else if (attributes) {
					taskText += ' <a href="#" id="recommend-attributes-confirm">' + i18n.translate('recommend next attribute') + '</a>' + '?';
				}
			}
			
			section({id: 'active-rule'}, 
				h2(i18n.translate('Association rule pattern')),
				div({id: 'antecedent'}, h3(i18n.translate('Antecedent'))),
				div({id: 'interest-measures'}, 
						h3(i18n.translate('Interest measures')), 
						div(),
						displayAddIM ? a({href: '#', id: 'add-im'}, i18n.translate('Add')) : ''),
				div({id: 'succedent'}, h3(i18n.translate('Succedent'))),
				div({'class': 'clearfix'}),
				span({id: 'action-box', styles: {'visibility': taskBox ? 'visible' : 'hidden'}}, taskText));
		});
		
		Mooml.register('interestMeasureTemplate', function (data) {
			IM = data.IM;
			i18n = data.i18n;
			
			div({id: IM.getCSSID()},
				span({'class': 'name', 'title': IM.getField().localizedName}, 
						IM.getLocalizedName() + ': ', 
						span({id: IM.getCSSValueID(), 'class': 'im-slider-value'}),
						a({id: IM.getCSSRemoveID(), href: '#', 'class': 'remove-im', 'title': i18n.translate('Remove')})),
				div({id: IM.getCSSSliderID(), 'class': 'im-slider'},
						div({'class': 'knob'})));
		});
		
		Mooml.register('cedentTemplate', function (data) {
			rule = data.rule;
			cedent = data.cedent;
			i18n = data.i18n;
			
			div({id: cedent.getCSSID(), 'class': 'cedent'},
				div({id: cedent.getCSSFieldsID(), 'class': 'fields'}, !cedent.getNumLiterals() ? '<span class="info">Drag & Drop<br/>attribute here</span>' : ''),
				div({'class': 'controls'},
					//span(i18n.translate('Cedent ') + cedent.getId()),
					span({id: cedent.getCSSInfoID(), 'class': 'info'},
						 rule.getGroupFields() && cedent.displayGroupButton() ? '<a href="#" id="' + cedent.getCSSGroupFieldsConfirmID() + '" class="group-fields">' + i18n.translate('group fields') + '</a> | ' : ''),
					a({id: cedent.getCSSEditConnectiveID(), href: '#', 'class': 'edit-connective'}, i18n.translate('edit connective'))));
		});
		
		Mooml.register('cedentSignTemplate', function (data) {
			cedent = data.cedent;
			
			a({id: cedent.getCSSChangeSignID(), href: '#', 'class': 'change-cedent-sign ' + cedent.getSign()});
		});
		
		Mooml.register('fieldTemplate', function (data) {
			field = data.field;
			i18n = data.i18n;
			fieldSign = field.getSign().toLowerCase();
			cedent = data.cedent;
			
			if (field.getType() === null) {
				div({id: field.getCSSID(), 'class': 'field'},
					a({id: field.getCSSChangeSignID(), href: '#', 'class': 'change-sign ' + fieldSign}),
					span({id: field.getCSSDragID()}, field.toString()));
			} else {// if (field.getType() === 'One category') {
				div({id: field.getCSSID(), 'class': 'field'},
						a({id: field.getCSSChangeSignID(), href: '#', 'class': 'change-sign ' + fieldSign}),
						span({id: field.getCSSDragID(), 'class': 'field-drag'}, field.toStringAR()),
						div({'class': 'controls'},
							cedent.getNumLiteralRefs() > 1 ? a({id: field.getCSSMarkID(), href: '#', 'class': field.isMarked() === true ? 'marked-field': 'mark-field', 'title': field.isMarked() === true ? i18n.translate('Unmark') : i18n.translate('Mark')}) : '',
								a({id: field.getCSSRemoveID(), href: '#', 'class': 'remove-field', 'title': i18n.translate('Remove')}),
								a({id: field.getEditCoefficientCSSID(), href: '#', 'class': 'edit-coefficient', 'title': i18n.translate('Edit')})));
			}// else {
//				div({id: field.getCSSID(), 'class': 'field'},
//						a({id: field.getCSSChangeSignID(), href: '#', 'class': 'change-sign ' + fieldSign}),
//						span({id: field.getCSSDragID(), 'class': 'field-drag'}, field.toStringAR()),
//						div({'class': 'controls'},
//							a({id: field.getCSSMarkID(), href: '#', 'class': field.isMarked() === true ? 'marked-field': 'mark-field', 'title': field.isMarked() === true ? i18n.translate('Unmark') : i18n.translate('Mark')}),
//							   a({id: field.getEditCoefficientCSSID(), href: '#', 'class': 'edit-coefficient', 'title': i18n.translate('Edit')}),
//							   a({id: field.getCSSRemoveID(), href: '#', 'class': 'remove-field', 'title': i18n.translate('Remove')})));
//				div({id: field.getCSSID(), 'class': 'field'},
//					a({id: field.getCSSChangeSignID(), href: '#', 'class': 'change-sign ' + fieldSign}),	
//					span({id: field.getCSSDragID(), 'class': 'field-drag'}, field.toStringAR()),
//					div({'class': 'controls'},
//						ul(li(a({id: field.getCSSMarkID(), href: '#', 'class': field.isMarked() === true ? 'marked-field': 'mark-field'}, field.isMarked() === true ? i18n.translate('Unmark') : i18n.translate('Mark'))),
//						   li(a({id: field.getEditCoefficientCSSID(), href: '#', 'class': 'edit-coefficient'}, i18n.translate('Edit'))),
//						   li(a({id: field.getCSSRemoveID(), href: '#', 'class': 'remove-field'}, i18n.translate('Remove'))))));				
//			}
//		}
		});
		
		Mooml.register('connectiveTemplate', function (data) {
			connective = data.connective;
			i18n = data.i18n;
			
			div({id: connective.getCSSID(), 'class': 'connective'},
				span(connective.toString()));
		});
		
		Mooml.register('bracketTemplate', function (data) {
			isLeft = data.isLeft;
			
			if (isLeft === true) {
				span({'class': 'left-bracket'}, '(');
			} else {
				span({'class': 'right-bracket'}, ')');
			}
		});
	},
	
	registerAddIMWindow: function () {
		Mooml.register('addIMWindowTemplate', function (data) {
			i18n = data.i18n;
			
			div({id: 'add-im-window'},
				a({id: 'add-im-close', href: '#'}, i18n.translate('Close')),
				h2(i18n.translate('Add interest measure')),
				form({action: '#', method: 'POST', id: 'add-im-form'},
					label({'for': 'add-im-select'}, i18n.translate('Interest measure:')),
					span({id: 'add-im-autocomplete'})));
		});
		
		Mooml.register('addIMWindowAutocompleteTemplate', function (data) {
			i18n = data.i18n;
			
			span({id: 'add-im-autocomplete'},
				div(
					select({name: 'add-im-select', id: 'add-im-select'}),
					label({'for': 'add-im-value'}, i18n.translate('Threshold value:'))),
				div(
					input({type: 'text', name: 'add-im-value', id: 'add-im-value', 'readonly': 'readonly'}),
					div({id: 'add-im-slider'},
						div({'class': 'knob'}))),
				input({type: 'submit', value: i18n.translate('Add')}));
		});
		
		Mooml.register('addIMWindowSelectOptionTemplate', function (data) {
			IM = data.IM;
			isSelected = data.isSelected;

			if (isSelected === true) {
				option({'value': IM.name, 'selected': 'selected'}, IM.getLocalizedName());
			} else {
				option({'value': IM.name}, IM.getLocalizedName());
			}
		});
	},

	registerAddCoefficientWindow: function () {
		Mooml.register('addCoefficientWindowTemplate', function (data) {
			i18n = data.i18n;
			
			div({id: 'add-coefficient-window'},
				a({id: 'add-coefficient-close', href: '#'}, i18n.translate('Close')),
				h2(i18n.translate('Add coefficient')),
				form({action: '#', method: 'POST', id: 'add-coefficient-form'},
					label({'for': 'add-coefficient-select'}, i18n.translate('Coefficient:')),
					span({id: 'add-coefficient-autocomplete'})),
				div({'class': 'clearfix'}));
		});
		
		Mooml.register('editCoefficientWindowTemplate', function (data) {
			i18n = data.i18n;
			
			div({id: 'edit-coefficient-window'},
				a({id: 'edit-coefficient-close', href: '#'}, i18n.translate('Close')),
				h2(i18n.translate('Edit coefficient')),
				form({action: '#', method: 'POST', id: 'edit-coefficient-form'},
					label({'for': 'edit-coefficient-select'}, i18n.translate('Coefficient:')),
					span({id: 'edit-coefficient-autocomplete'})),
				div({'class': 'clearfix'}));
		});
		
		Mooml.register('addCoefficientWindowAutocompleteTemplate', function (data) {
			selectedCoefficient = data.selectedCoefficient;
			i18n = data.i18n;
			
			if (selectedCoefficient.getName() === 'One category') {
				span({id: 'add-coefficient-autocomplete'},
					select({name: 'add-coefficient-select', id: 'add-coefficient-select'}),
					label({'for': 'add-coefficient-category'}, selectedCoefficient.fields.category.localizedName + ':'),
					select({name: 'add-coefficient-category', id: 'add-coefficient-category'}),
					input({type: 'submit', value: i18n.translate('Add')}));
			} else {
				span({id: 'add-coefficient-autocomplete'},
					select({name: 'add-coefficient-select', id: 'add-coefficient-select'}),
						table(
							tr(
								td(label({'for': 'add-coefficient-minlength'}, selectedCoefficient.fields.minLength.localizedName + ':')),
								td(input({type: 'text', name: 'add-coefficient-minlength', id: 'add-coefficient-minlength', readonly: 'readonly'}))
							),
							tr(
								td({colspan: 2}, 
									div({id: 'add-coefficient-minlength-slider'},
										div({'class': 'knob'}))
								)
							),
							tr(td({colspan: 2}, '&nbsp;')),
							tr(
								td(label({'for': 'add-coefficient-maxlength'}, selectedCoefficient.fields.maxLength.localizedName + ':')),
								td(input({type: 'text', name: 'add-coefficient-maxlength', id: 'add-coefficient-maxlength', readonly: 'readonly'}))
							),
							tr(
								td({colspan: 2}, 
									div({id: 'add-coefficient-maxlength-slider'},
										div({'class': 'knob'}))
								)
							)
						),
						input({type: 'submit', value: i18n.translate('Add')}));
			}
		});
		
		Mooml.register('editCoefficientWindowAutocompleteTemplate', function (data) {
			field = data.field;
			selectedCoefficient = data.selectedCoefficient;
			i18n = data.i18n;
			
			if (selectedCoefficient.getName() === 'One category') {
				span({id: 'edit-coefficient-autocomplete'},
					select({name: 'edit-coefficient-select', id: 'edit-coefficient-select'}),
					label({'for': 'edit-coefficient-category'}, i18n.translate('Category')),
					select({name: 'edit-coefficient-category', id: 'edit-coefficient-category'}),
					input({type: 'submit', value: i18n.translate('Edit')}));
			} else {
				span({id: 'edit-coefficient-autocomplete'},
					select({name: 'edit-coefficient-select', id: 'edit-coefficient-select'}),
						table(
							tr(
								td(label({'for': 'edit-coefficient-minlength'}, selectedCoefficient.fields.minLength.localizedName + ':')),
								td(input({type: 'text', name: 'edit-coefficient-minlength', id: 'edit-coefficient-minlength', readonly: 'readonly', value: field.getMinimalLength()}))
							),
							tr(
								td({colspan: 2}, 
									div({id: 'edit-coefficient-minlength-slider'},
										div({'class': 'knob'}))
								)
							),
							tr(td({colspan: 2}, '&nbsp;')),
							tr(
								td(label({'for': 'edit-coefficient-maxlength'}, selectedCoefficient.fields.maxLength.localizedName + ':')),
								td(input({type: 'text', name: 'edit-coefficient-maxlength', id: 'edit-coefficient-maxlength', readonly: 'readonly', value: field.getMaximalLength()}))
							),
							tr(
								td({colspan: 2}, 
									div({id: 'edit-coefficient-maxlength-slider'},
										div({'class': 'knob'}))
								)
							)
						),
						input({type: 'submit', value: i18n.translate('Edit')}));
			}
		});
		
		Mooml.register('addCoefficientWindowSelectOptionTemplate', function (data) {
			coefficient = data.coefficient;
			isSelected = data.isSelected;

			if (isSelected === true) {
				option({'value': coefficient.getName(), 'selected': 'selected'}, coefficient.getName());
			} else {
				option({'value': coefficient.getName()}, coefficient.getName());
			}
		});
		
		Mooml.register('editCoefficientWindowSelectOptionTemplate', function (data) {
			coefficient = data.coefficient;
			isSelected = data.isSelected;

			if (isSelected === true) {
				option({'value': coefficient.getName(), 'selected': 'selected'}, coefficient.getName());
			} else {
				option({'value': coefficient.getName()}, coefficient.getName());
			}
		});
		
		Mooml.register('addCoefficientWindowSelectOption2Template', function (data) {
			choice = data.choice;

			option({'value': choice}, choice);
		});
		
		Mooml.register('editCoefficientWindowSelectOption2Template', function (data) {
			choice = data.choice;
			isSelected = data.isSelected;

			if (isSelected === true) {
				option({'value': choice, 'selected': 'selected'}, choice);
			} else {
				option({'value': choice}, choice);
			}
		});
		
	},
	
	registerEditConnectiveWindow: function() {
		Mooml.register('editConnectiveWindowTemplate', function (data) {
			i18n = data.i18n;
			
			div({id: 'edit-connective-window'},
				h2(i18n.translate('Edit connective')),
				span({id: 'edit-connective-close'},
						a({href: '#'}, i18n.translate('Close'))),
				form({action: '#', method: 'POST', id: 'edit-connective-form'},
					label({'for': 'edit-connective-select'}, i18n.translate('Connective')),
					select({name: 'edit-connective-select', id: 'edit-connective-select'}),
					input({type: 'submit', value: i18n.translate('Edit')})));
		});

		Mooml.register('editConnectiveWindowSelectOptionTemplate', function (data) {
			connective = data.connective;
			isSelected = data.isSelected;

			if (isSelected === true) {
				option({'value': connective, 'selected': 'selected'}, connective);
			} else {
				option({'value': connective}, connective);
			}
		});
	},
	
	registerFoundRule: function () {
		Mooml.register('foundRuleTemplate', function (data) {
			key = data.key;
			rule = data.rule;
			i18n = data.i18n;
			BK = data.BK;
			
			li({id: rule.getFoundRuleCSSID(), 'class': 'found-rule'}, 
				span({'class': 'rule'}, '<span class="id">' + key + '.</span>' + rule.getIdent()),
				!BK ? a({id: rule.getFoundRuleCSSBKID(), href: '#', 'class': 'bk', 'title': i18n.translate('Ask background knowledge')}) : '',
				a({id: rule.getFoundRuleCSSMarkID(), href: '#', 'class': 'mark', 'title': i18n.translate('Mark rule')}),
				a({id: rule.getFoundRuleCSSRemoveID(),href: '#', 'class': 'clear', 'title': i18n.translate('Clear rule')}),
				div({'class': 'loading'}, '')
			);
		});
	},
	
	registerMarkedRules: function () {
		Mooml.register('markedRulesStructureTemplate', function (data) {
			i18n = data.i18n;
			
			section({id: 'marked-rules'}, 
					h2(i18n.translate('Marked rules'), a({href: '#', 'class': 'dropdown'}, '')),
					div(
						ul(),
						div({'class': 'clearfix'})));
		});
		
		Mooml.register('markedRuleTemplate', function (data) {
			rule = data.rule;
			i18n = data.i18n;

			li({id: rule.getMarkedRuleCSSID()},
				span({'class': 'rule'}, rule.getIdent()), 
				a({id: rule.getMarkedRuleCSSRemoveID(), href: '#', 'class': 'clear', 'title': i18n.translate('Remove')}));
		});
	},
	
	registerSettingsWindow: function () {
		Mooml.register('settingsTemplate', function (data) {
			autoSuggestPossible = data.autoSuggestPossible;
			i18n = data.i18n;
			reset = data.reset;
			settings = data.settings;
			
			div({id: 'settings-window'},
				a({id: 'settings-close', href: '#'}, i18n.translate('Close')),
				h2(i18n.translate('Settings')),
				form({action: '#', method: 'POST', id: 'settings-form'},
					div(
						span({'class': 'category'}, i18n.translate('Association rule pattern restrictions'))),
					div({'class': 'autocomplete'},
						div(
							label({'for': 'fl-select'}, i18n.translate('Restrictions') + ':'),
							select({name: 'fl-select', id: 'fl-select'}),
							reset ? span({'class': 'tooltip warning'},
								span({'class': 'warning'},
									img({src: './images/icon-tooltip-warning.png'}),
									em(i18n.translate('Association rule pattern reset')),
									i18n.translate('Association rule pattern has to be reset due to new restrictions.'))) : '',	
									span({'class': 'tooltip info'},
										span({'class': 'help'},
											img({src: './images/icon-tooltip-help.png'}),
											em(i18n.translate('Restrictions')),
											i18n.translate('These are predefined association rule pattern restrictions, which do not depend on analysed data. The more expert the looser they are.')))),
											div(
												label({'for': 'as-select', 'class': 'thin'}, i18n.translate('Attribute<br>suggestion') + ':'),
												autoSuggestPossible ? a({id: 'as', 'href': '#', 'class': settings.getRecEnabled() ? 'autosuggest-on' : 'autosuggest-off'}, i18n.translate(settings.getRecEnabled() ? 'On': 'Off')) : span({'class': 'autosuggest-off'}, i18n.translate(settings.getRecEnabled() ? 'On': 'Off')),
												span({id: 'as-select'}))),
					div(
						span({'class': 'category'}, i18n.translate('Found rules'))),
					div(
						label({'for': 'rulesCnt'}, i18n.translate('Limit') + ':'),
						input({id: 'rules-cnt', 'type': 'text', 'class': 'shortnr', value: settings.getRulesCnt()}),
						span({'class': 'tooltip info'},
								span({'class': 'help'},
										img({src: './images/icon-tooltip-help.png'}),
										em(i18n.translate('Limit')),
										i18n.translate('Maximal number of association rules to be searched for. If the limit is reached and there are more rules to find, an option to search for the remaining rules pops up.')))),
					div(
						label({'for': 'as-select'}, i18n.translate('Auto filter') + ':'),
						a({id: 'autofilter', 'href': '#', 'class': settings.getBKAutoSearch() ? 'autofilter-on' : 'autofilter-off'}, i18n.translate(settings.getBKAutoSearch() ? 'On': 'Off')),
						span({'class': 'tooltip info'},
								span({'class': 'help'},
										img({src: './images/icon-tooltip-help.png'}),
										em(i18n.translate('Auto filter')),
										i18n.translate('Association rules are automaticaly filtered according to expert background knowledge. This guarantees that only interesting association rules are left.')))),
					br({'class': 'clearfix'}),
					input({type: 'submit', value: i18n.translate('Save')})));
		});
		
		Mooml.register('flOptionTemplate', function (data) {
			FL = data.FL;
			isSelected = data.isSelected;

			if (isSelected === true) {
				option({'value': FL.getName(), 'selected': 'selected'}, FL.getLocalizedName());
			} else {
				option({'value': FL.getName()}, FL.getLocalizedName());
			}
		});
	}
	
});