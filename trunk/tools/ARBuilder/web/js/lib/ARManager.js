var ARManager = new Class({
	
	dataContainer: null,
	stringHelper: null,
	miningManager: null,
	ETreeManager: null,
	UIPainter: null,
	
	activeRule: null,
	ETreeValidator: null,
	markedRules: [],
	maxCedentID: 0,
	maxFieldID: 0,
	maxConnectiveID: 0,
	attributesByGroup: false,
	defaultConnectiveName: 'Conjunction',
	
	initialize: function (dataContaner, stringHelper, miningManager, ETreeManager) {
		this.dataContainer = dataContaner;
		this.stringHelper = stringHelper;
		this.miningManager = miningManager;
		this.ETreeManager = ETreeManager;
		
		this.ETreeValidator = new ETreeValidator();
	},
	
	setUIPainter: function (UIPainter) {
		this.UIPainter = UIPainter;
	},
	
	initBlankAR: function () {
		var AR = new AssociationRule(this.initARValidator());

		// antecedent
		var antecedent = this.initCedent(1);
		AR.addAntecedent(antecedent);
		
		// succedent
		var succedent = this.initCedent(1);
		AR.addSuccedent(succedent);
		
		this.activeRule = AR;
	},
	
	initARValidator: function () {
		return new AssociationRuleValidator(this.dataContainer.getRulePatterns(), this.dataContainer.getIMCombinations());
	},
	
	initCedent: function (level) {
		return new Cedent(this.generateCedentID(), level, this.dataContainer.getDBAConstraint(level), this.getDefaultConnective(), [], []);
	},
	
	getUsedIMs: function () {
		return this.activeRule.getIMs();
	},
	
	hasPossibleIMs: function () {
		return Object.getLength(this.getPossibleIMs()) > 0;
	},
	
	getPossibleIMs: function () {
		var usedIMs = this.getUsedIMs();
		var possibleIMs = [];
		
		if (Object.getLength(usedIMs) === 0) {
			possibleIMs = this.dataContainer.getIMs();
		} else {
			Array.each(this.dataContainer.getIMCombinations(), function (IMCombination) {
				var applicableCombination = true;
				Object.each(usedIMs, function (usedIM) {
					if (!IMCombination.contains(usedIM.getName()) || IMCombination.length === 1) {
						applicableCombination = false;
					}
				}.bind(this));
				
				if (applicableCombination === true) {
					var applicableIMCombination = Array.clone(IMCombination);
					Object.each(usedIMs, function (usedIM) {
						applicableIMCombination.erase(usedIM.getName());
					}.bind(this));
					
					Array.each(applicableIMCombination, function (IMName) {
						var IM = this.getIMPrototype(IMName);
						possibleIMs[IM.getName()] = IM;
					}.bind(this));
				}
			}.bind(this));
		}
		
		return possibleIMs;
	},
	
	getActiveRule: function () {
		return this.activeRule;
	},
	
	openAddIMWindow: function () {
		var possibleIMs = this.getPossibleIMs();
		this.UIPainter.renderAddIMWindow(possibleIMs);
	},
	
	closeAddIMWindow: function () {
		this.UIPainter.hideOverlay();
	},
	
	addAntecedent: function (antecedent) {
		this.activeRule.addAntecedent(antecedent);
	},
	
	addIM: function (name, value) {
		var IMPrototype = this.getIMPrototype(name);
		var IM = new InterestMeasureAR(name, IMPrototype.getLocalizedName(), IMPrototype.getExplanation(), IMPrototype.getField(), IMPrototype.getStringHelper(), value);
		this.activeRule.addIM(IM);
		
		this.UIPainter.hideOverlay();
		this.UIPainter.renderActiveRule();
	},
	
	editIM: function (IM) {
		this.activeRule.editIM(IM, $(IM.getCSSValueID()).get('text'));
		this.UIPainter.renderActiveRule();
	},
	
	removeIM: function (IM) {
		this.activeRule.removeIM(IM.getName());
		this.UIPainter.renderActiveRule();
	},
	
	isAttributeUsed: function(attribute) {
		return this.activeRule.isAttributeUsed(attribute);
	},
	
	addAttribute: function (cedent, attribute) {
		var field = new FieldAR(this.generateFieldID(), attribute, null, new StringHelper());
		cedent.addLiteralRef(field);

		this.UIPainter.renderActiveRule();
		this.openAddCoefficientWindow(field);
	},
	
	openAddCoefficientWindow: function(field) {
		this.UIPainter.renderAddCoefficientWindow(field);
	},
	
	addCoefficient: function() {
		var field = arguments[0];
		if (arguments.length === 3) { // One category
			field.setCoefficient(arguments[1], arguments[2]);
		} else {
			field.setCoefficient(arguments[1], arguments[2], arguments[3]);
		}

		this.sortAttributes();
		this.setActiveRuleChanged();
		this.UIPainter.renderActiveRule();
		this.closeAddCoefficientWindow();
	},
	
	editCoefficient: function() {
		var field = arguments[0];
		if (arguments.length === 3) { // One category
			field.setCoefficient(arguments[1], arguments[2]);
		} else {
			field.setCoefficient(arguments[1], arguments[2], arguments[3]);
		}
		
		this.setActiveRuleChanged();
		this.UIPainter.renderActiveRule();
		this.closeEditCoefficientWindow();
	},
	
	getBBACoefficient: function(coefficientName) {
		return this.dataContainer.getBBACoefficient(coefficientName);
	},
	
	closeAddCoefficientWindow: function () {
		this.UIPainter.hideOverlay();
	},
	
	openEditCoefficientWindow: function(field) {
		this.UIPainter.renderEditCoefficientWindow(field);
	},
	
	closeEditCoefficientWindow: function () {
		this.UIPainter.hideOverlay();
	},
	
	openEditConnectiveWindow: function (cedent) {
		this.UIPainter.renderEditConnectiveWindow(cedent);
	},
	
	editConnective: function(cedent, connectiveName) {
		cedent.setConnective(connectiveName);
		
		this.UIPainter.renderCedent(cedent, null);
		this.setActiveRuleChanged();
		this.closeEditConnectiveWindow();
	},
	
	closeEditConnectiveWindow: function () {
		this.UIPainter.hideOverlay();
	},
	
	addField: function (field, cedent) {
		if (field.getType() === 'One category') {
			var fieldAR = new FieldAR(this.generateFieldID(), field.getRef(), field.getType(), new StringHelper(), field.getCategory());
		} else {
			var fieldAR = new FieldAR(this.generateFieldID(), field.getRef(), field.getType(), new StringHelper(), field.getMinimalLength(), field.getMaximalLength());
		}
		this.activeRule.addField(fieldAR, cedent);
		this.UIPainter.renderActiveRule();
	},
	
	addFieldAR: function (field, cedent) {
		this.activeRule.removeField(field);
		this.activeRule.addField(field, cedent);
		this.UIPainter.renderActiveRule();
	},
	
	removeField: function (field) {
		this.activeRule.removeField(field);
		if (!this.attributesByGroup) {
			this.sortAttributes();
		}
		this.UIPainter.renderActiveRule();
	},
	
	changeFieldSign: function(field) {
		this.activeRule.changeFieldSign(field);
		this.UIPainter.renderActiveRule();
	},

	addFieldGroup: function (FG, cedent) {
		cedent.setConnective(FG.getConnective());
		Object.each(FG.getFields(), function (field) {
			if (field.getType() === 'One category') {
				var fieldAR = new FieldAR(this.generateFieldID(), field.getRef(), field.getType(), new StringHelper(), field.getCategory());
			} else {
				var fieldAR = new FieldAR(this.generateFieldID(), field.getRef(), field.getType(), new StringHelper(), field.getMinimalLength(), field.getMaximalLength());
			}
			this.activeRule.addField(fieldAR, cedent);
		}.bind(this));
		
		this.UIPainter.renderActiveRule();
		this.setActiveRuleChanged();
	},
	
	groupFields: function (cedent) {
		if (cedent.getNumLiteralRefs() !== cedent.getNumMarkedFields()) {
			var newCedent = new Cedent(this.generateCedentID(), cedent.getNextLevel(), this.dataContainer.getDBAConstraint(cedent.getNextLevel()), this.getDefaultConnective(), [], []);
			cedent.groupLiteralRefs(newCedent);
		} else {
			cedent.unmarkLiteralRefs();
		}
		this.UIPainter.renderActiveRule();
		this.setActiveRuleChanged();
	},
	
	rejectGroupFields: function (cedent) {
		this.activeRule.setGroupFields(false);
		this.UIPainter.clearCedentInfo(cedent);
	},
	
	changeMark: function(field) {
		field.changeMark();
		this.activeRule.setGroupFields(true);
		this.UIPainter.renderActiveRule();
	},
	
	addCedent: function (cedent) {
		var childCedent = new Cedent(this.generateCedentID(), cedent.getNextLevel(), this.dataContainer.getDBAConstraint(cedent.getNextLevel()), this.getDefaultConnective(), [], []);
		cedent.addChildCedent(childCedent);
		this.UIPainter.renderCedent(cedent, null);
		this.setActiveRuleChanged();
	},
	
	removeCedent: function (cedent) {
		if (cedent.getLevel() === 1) {
			var blankCedent = this.initCedent(cedent.getLevel());
			this.activeRule.setCedent(cedent, blankCedent);
		} else {
			this.activeRule.removeCedent(cedent);
			this.setActiveRuleChanged();
		}
		
		this.UIPainter.renderActiveRule();
	},
	
	changeCedentSign: function(cedent) {
		cedent.changeSign();
		this.setActiveRuleChanged();
		this.UIPainter.renderCedent(cedent, null);
	},

	getMarkedRule: function(id) {
		var rule = null;
		Object.each(this.markedRules, function (markedRule) {
			if (id === markedRule.getId()) {
				rule = markedRule;
			}
		}.bind(this));
		
		return rule;
	},
	
	removeMarkedRule: function(rule) {
		Object.each(this.markedRules, function (markedRule, key) {
			if (rule.getId() === markedRule.getId()) {
				delete this.markedRules[key];
			}
		}.bind(this));

		this.UIPainter.renderMarkedRules(null);
	},

	sortMarkedRules: function (order) {
		var markedRules = [];
		Array.each(order, function (CSSID) {
			if (CSSID !== null) {
				var ruleId = this.stringHelper.getId(CSSID);
				var rule = this.getMarkedRule(ruleId);
				markedRules.push(rule);
			}
		}.bind(this));
		
		this.markedRules = markedRules;
		this.UIPainter.renderMarkedRules(null);
	},
	
	setActiveRuleChanged: function () {
		this.activeRule.setChanged(true);
	},
	
	getIMPrototype: function (name) {
		return this.dataContainer.getIM(name);
	},
	
	getMarkedRules: function () {
		return this.markedRules;
	},
	
	displayAttributesByGroup: function () {
		this.attributesByGroup = true;
		this.UIPainter.renderAttributes();
	},
	
	displayAttributesByList: function () {
		this.attributesByGroup = false;
		this.UIPainter.renderAttributes();
		this.sortAttributes();
	},
	
	generateCedentID: function () {
		return ++this.maxCedentID;
	},
	
	generateFieldID: function () {
		return ++this.maxFieldID;
	},
	
	getDefaultConnective: function () {
		return new Connective(this.generateConnectiveID(), this.defaultConnectiveName);
	},
	
	generateConnectiveID: function () {
		return ++this.maxConnectiveID;
	},
	
	getAttributesByGroup: function () {
		return this.attributesByGroup;
	},
	
	/* attribute sort */
	sortAttributes: function () {
		var attributes = this.dataContainer.getAttributes();
		var attributeSorter = new AttributeSorter(this, this.UIPainter);
		attributeSorter.sort(attributes, []);
	},
	
	/* mining */
	display4ftTaskBox: function () {
		return (this.activeRule.isValid() && this.activeRule.isChanged() && !this.miningManager.getInProgress());
	},
	
	displayETreeTaskBox: function () {
		return (this.activeRule.isChanged() && !this.ETreeManager.getInProgress() && this.ETreeValidator.isValid(this.activeRule));
	},
	
	mineRulesConfirm: function () {
		this.activeRule.setChanged(false);
		this.miningManager.mineRules(this.activeRule);
		this.UIPainter.renderActiveRule();
	},

	recommendAttributesConfirm: function () {
		this.activeRule.setChanged(false);
		this.ETreeManager.recommendAttributes(this.activeRule);
		this.UIPainter.renderActiveRule();
	},
	
	/* found rules */
	markFoundRule: function (rule) {
		this.markedRules.push(rule);
		this.UIPainter.disposeElement($(rule.getFoundRuleCSSID()));
		this.UIPainter.renderMarkedRules();
	},
	
	removeFoundRule: function (rule) {
		this.UIPainter.disposeElement($(rule.getFoundRuleCSSID()));
	}
	
});