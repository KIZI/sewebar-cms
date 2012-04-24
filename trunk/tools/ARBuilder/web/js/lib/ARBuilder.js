var ARBuilder = new Class({
	Implements: Events,

	config: null,
	connector: null,
	dataContainer: null,
	ARManager: null,
	ETreeManager: null,
	miningManager: null,
	UIColorizer: null,
	UIPainter: null,
	UIListener: null,

	// init basics
	initialize: function (config) {
		this.config = config;
		this.connector = new Connector(this.config.getDataGetURL(), this.config.getDataSetURL(), this.config.getRulesGetURL());
		this.dataContainer = new DataContainer();
		this.miningManager = new MiningManager(this.config, new RulesParser(this.dataContainer));
		this.ETreeManager = new ETreeManager(this.config, this.dataContainer);
		this.ARManager = new ARManager(this.dataContainer, new StringHelper(), this.miningManager, this.ETreeManager);
		this.ETreeManager.setARManager(this.ARManager);
		this.UIColorizer = new UIColorizer();
		this.UIListener = new UIListener(this.ARManager, this.UIColorizer);
		this.UIPainter = new UIPainter(this.config, this.dataContainer, this.ARManager, this.miningManager, this.UIColorizer, this.UIListener);
		this.UIListener.setUIPainter(this.UIPainter);
		this.ARManager.setUIPainter(this.UIPainter);
		this.ETreeManager.setUIPainter(this.UIPainter);
		this.miningManager.setUIPainter(this.UIPainter);
	},
	
	// run ARB
	run: function () {
		this.connector.getData(this.dataContainer); 
		this.ARManager.initARValidator();
		this.ARManager.initBlankAR();
		this.UIPainter.createUI();
	}

});