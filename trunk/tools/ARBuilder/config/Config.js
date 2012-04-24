var Config = new Class({
	
	// app info
	author: 'Radek Skrabal (<a href="mailto:radek@skrabal.me">radek@skrabal.me</a>)',
	name: 'AR Builder',
	version: '1.0-alpha',
	slogan: 'the beauty of data mining',
	copyright: '<a href="http://kizi.vse.cz" title="Department of Information and Knowledge Engineering - University of Economics Prague">DIKE UEP</a>',
	
	// language
	lang: 'en',
	
	// URL settings
	params: '',
	dataGetURL: 'getData.php',
	dataSetURL: 'setData.php',
	rulesGetURL: 'getRules.php',
	ETreeGetURL: 'getEtree.php',
		
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
	
	getDataGetURL: function () {
		return this.dataGetURL;
	},
	
	getDataSetURL: function () {
		return this.dataSetURL;
	},
	
	getRulesGetURL: function () {
		return this.params ? this.rulesGetURL + "?" + this.params : this.rulesGetURL;
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