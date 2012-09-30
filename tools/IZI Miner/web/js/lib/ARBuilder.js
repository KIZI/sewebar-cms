var ARBuilder = new Class({
    GetterSetter: ['DD', 'FGC', 'ETreeManager', 'config', 'ARManager', 'FRManager', 'miningManager'],
	Implements: Events,

	$config: null,
	settings: null,
	dataParser: null,
	$FRManager: null,
	$miningManager: null,
	$ETreeManager: null,
	$ARManager: null,
    $UIStructureListener: null,
    $UIStructurePainter: null,
	UIPainter: null,
	UIListener: null,
    $i18n: null,
	$DD: null,
	FLs: [],
	defFLIndex: 0,
	$FGC: null,
	$callbackDelay: 1000, // miliseconds

	// init basics
	initialize: function (config) {
		this.$config = config;
        this.$i18n = new i18n(this.$config.getLang());

        // Paint application structure
        this.$UIStructureListener = new UIStructureListener(this);
        this.$UIStructurePainter = new UIStructurePainter(this.$config, new DateHelper(), this.$i18n, this.$UIStructureListener, new UIStructureTemplater(), new UIScroller($(this.$config.getRootElementID())));
        this.$UIStructureListener.setUIStructurePainter(this.$UIStructurePainter);
        this.$UIStructurePainter.render();

        this.UIListener = new UIListener(this, new UIColorizer());
        this.UIPainter = new UIPainter(this, this.$config, this.$i18n, new UIColorizer(), this.UIListener, new DateHelper(), new UITemplateRegistrator(), new UIScroller($(this.$config.getRootElementID())), this.$UIStructurePainter);
        this.UIListener.setUIPainter(this.UIPainter);

        if (this.$config.getIdDm()) {
            this.loadData();
        } else {
            this.openNewTaskWindow();
        }
    },

    loadData: function() {
        this.handleLoadData();
        this.dataParser = new DataParser(this.$config, true);
        this.dataParser.getData(this.initApplication, this.handleLoadDataError, this, this.$config.getIdDm() != 'TEST' ? this.$callbackDelay : 0);
    },

    reloadData: function() {
        this.handleLoadData();
        this.dataParser = new DataParser(this.$config, true);
        this.dataParser.getData(this.repaintData, this.handleLoadDataError, this, this.$config.getIdDm() != 'TEST' ? this.$callbackDelay : 0);
    },

    repaintData: function() {
        this.$DD = this.dataParser.getDD();
        this.FLs = this.dataParser.getFLs();
        this.$FGC = this.dataParser.getFGC();

        this.reset();
        this.$UIStructurePainter.hideOverlay();
    },

    handleLoadData: function() {
        this.$UIStructurePainter.showLoadData();
    },

    handleLoadDataError: function() {
        this.$UIStructurePainter.showLoadDataError();
    },

    initApplication: function() {
        this.$UIStructurePainter.hideOverlay();

        this.$DD = this.dataParser.getDD();
        this.FLs = this.dataParser.getFLs();
        this.$FGC = this.dataParser.getFGC();

        this.settings = new Settings();
        this.$FRManager = new FRManager(this.$config, new RulesParser(this, this.$DD, this.getDefFL()), this.settings, this.UIPainter, this.UIListener, this.$i18n);
        this.$miningManager = new MiningManager(this.$config, this.settings, this.$FRManager, new DateHelper());
        this.$ETreeManager = new ETreeManager(this.$config, this.$DD, this.UIPainter);
        this.$ARManager = new ARManager(this, this.$DD, this.getDefFL(), this.$miningManager, this.$ETreeManager, this.settings, this.UIPainter);
        this.$ETreeManager.setARManager(this.$ARManager);

        this.UIPainter.createUI(); // TODO resize window after
        this.$FRManager.initPager();
    },

    getFL: function() {
        return this.getDefFL();  
    },
    
	getDefFL: function () {
		return this.FLs[this.defFLIndex];
	},
	
	setDefFL: function (FLName) {
		Array.each(this.FLs, function (FL, key) {
			if (FL.getName() === FLName) {
				this.defFLIndex = key;
			}
		}.bind(this));
	
		this.fireEvent('updateFL', this.getDefFL());
	},
	
	getFLByName: function (FLName) {
		var index = null;
		Array.each(this.FLs, function (FL, key) {
			if (FL.getName() === FLName) {
				index = key;
			}
		}.bind(this));
		
		return index !== null ? this.FLs[index] : null;
	},

    openNewTaskWindow: function () {
        this.$UIStructurePainter.renderNewTaskWindow();
    },

	openSettingsWindow: function () {
		this.$UIStructurePainter.renderSettingsWindow(this.FLs, this.getDefFL(), this.getDefFL().getAutoSuggest(), false, this.settings);
	},
	
	updateSettingsWindow: function (FLName) {
		var FL = this.getFLByName(FLName);
		var ARValidator = new AssociationRuleValidator(FL.getRulePattern(), FL.getIMCombinations());
		var reset = this.getDefFL().getName() !== FLName && !ARValidator.isValid(this.$ARManager.getActiveRule()) && this.$ARManager.getActiveRule().isChanged();
		this.$UIStructurePainter.renderSettingsWindow(this.FLs, FL, FL.getAutoSuggest(), reset, this.settings);
	},
	
	changeSettingsAutoFilter: function (el) {
		el.toggleClass('autofilter-on');
		el.toggleClass('autofilter-off');
		if (el.text === this.UIPainter.i18n.translate('On')) {
			el.text = this.UIPainter.i18n.translate('Off');
		} else {
			el.text = this.UIPainter.i18n.translate('On');
		}
	},
	
	changeSettingsAS: function (el) {
		el.toggleClass('autosuggest-on');
		el.toggleClass('autosuggest-off');
		if (el.hasClass('autosuggest-on')) {
			el.text = this.$i18n.translate('On');
		} else {
			el.text = this.$i18n.translate('Off');
		}
	},

    changeCaching: function(el) {
        el.toggleClass('cache-on');
        el.toggleClass('cache-off');
        if (el.hasClass('cache-on')) {
            el.text = this.$i18n.translate('On');
        } else {
            el.text = this.$i18n.translate('Off');
        }
    },

	closeSettingsWindow: function () {
		this.$UIStructurePainter.hideOverlay();
	},
	
	saveSettings: function(rulesCnt, FLName, autoSearch, autoSuggest, cache) {
		// settings
		this.settings.setRulesCnt(rulesCnt);
		this.settings.setBKAutoSearch(autoSearch);
		this.settings.setRecEnabled(autoSuggest);
        this.settings.setCaching(cache);
		
		// FL switch
		var FL = this.getFLByName(FLName);
		var ARValidator = new AssociationRuleValidator(FL.getRulePattern(), FL.getIMCombinations());
		if (this.getDefFL().getName() !== FLName && !ARValidator.isValid(this.$ARManager.getActiveRule())) { // switch FL
			this.setDefFL(FLName);
			this.$ARManager.initBlankAR();
			this.UIPainter.sortAttributes();
		} else {
			this.setDefFL(FLName);
		}
		this.UIPainter.renderActiveRule();
		
		this.$UIStructurePainter.hideOverlay();
	},

    openAddAttributeWindow: function(field) {
        this.UIPainter.renderAddAttributeWindow(field);
    },

    openEditAttributeWindow: function (attribute) {
        this.UIPainter.renderEditAttributeWindow(attribute);
    },

    // TODO handle load data error
    reloadAttributes: function() {
        this.reloadData();
    },

    closeOverlay: function () {
        this.$UIStructurePainter.hideOverlay();
    },

    removeAttribute: function(attribute) {
        // remove attribute
        this.$DD.removeAttribute(attribute);
        this.UIPainter.removeAttribute(attribute);

        // reset UI
        this.reset();
    },

    reset: function() {
        // navigation
        this.UIPainter.renderNavigation();

        // active rule
        // TODO reset only if necessary
        //this.$ARManager.initBlankAR();

        this.stopMining();

        // found rules
        // TODO reset only if necessary
//        this.$FRManager.reset();

        // marked rules
        // TODO reset only if necessary
//        this.$FRManager.removeMarkedRules();

        // ETree
        this.$ETreeManager.reset();
    },

    stopMining: function() {
        this.$miningManager.stopMining();
    }

});