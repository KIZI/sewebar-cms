var Config = new Class({
	
	// app info
	author: 'Radek Skrabal (<a href="mailto:radek@skrabal.me">radek@skrabal.me</a>)',
	name: 'I<em>:</em>ZI Miner',
	version: '1.0-beta',
	slogan: 'the beauty of data mining',
	copyright: '<a href="http://kizi.vse.cz" title="Department of Information and Knowledge Engineering - University of Economics Prague">DIKE UEP</a>',
	
	// language
	lang: 'en',
	
	// URL settings
	params: {},
	BKGetURL: 'getBK.php',
	dataGetURL: 'getData.php',
	dataSetURL: 'setData.php',
	ETreeGetURL: 'getEtree.php',
	rulesGetURL: 'getRules.php',
		
	// root element
	rootElementID: 'ARBuilder',
	
	initialize: function () {},

	getAuthor: function () {
		return this.author;
	},
	
	getName: function () {
		return this.name;
	},
	
	getVersion: function () {
		return this.version;
	},
	
	getSlogan: function () {
		return this.slogan;
	},
	
	getCopyright: function () {
		return this.copyright;
	},
	
	getLang: function () {
		return this.lang;
	},
	
	setParams: function (params) {
		this.params = params;
	},
	
	getBKGetURL: function () {
		return Object.getLength(this.params) ? this.BKGetURL + "?id_kb=" + this.params.id_kb : this.BKGetURL;
	},
	
	getDataGetURL: function () {
		return this.dataGetURL;
	},
	
	setDataGetURL: function (url) {
		this.dataGetURL = url;
	},
	
	getDataSetURL: function () {
		return this.dataSetURL;
	},
	
	getRulesGetURL: function () {
		return Object.getLength(this.params) ? this.rulesGetURL + "?id_dm=" + this.params.id_dm : this.rulesGetURL;
	},
	
	setRulesGetURL: function (URL) {
		this.rulesGetURL = URL;
	},
	
	getETreeGetURL: function () {
		return this.ETreeGetURL;
	},
	
	getRootElementID: function () {
		return this.rootElementID;
	}
	
});