/**                                                
 * Class: BasicStructureGUI
 * This class is responsible for creating nbasic HTML structure of the application
 * based on language, position where it should be created and three arrays representing
 * attribuets, booleans and interestMeasures
 */
var BasicStructureGUI = new Class({
    ID_BOOLEANS_DIV: "booleans",
    ID_ATTRIBUTES_DIV: "attributes",
    ID_INTEREST_MEASURES_DIV: "operators",

    COLS_ATTRIBUTES: 4,
    COLS_INTEREST_MEASURES: 2,
    COLS_BOOL: 1,

    /**
     * Function: initialize
     * Creates instance of this class and helds responsibility for creating HTML
     * structure
     *
     * Parameters:
     * idMainDiv     {String} Id of div in which should the application be created
     * booleans     {Array} Array of booleans to be created
     * attributes     {Array} Array of attributes to be created
     * interestMeasures     {Array} Array of interestMeasures to be created
     * lang         {String} Language in which should the application be created.
     * displayMode  {Bool} Display mode
     * hits      {Hits} Instance of hits class
     */
    initialize: function(booleans, attributes, interestMeasures, idMainDiv, lang, displayMode, hits){
        this.lang = lang;
        this.language = new LanguageSupport();
        this.displayMode = displayMode;

        // Create basic divs
        this.createMainDivs(idMainDiv, hits);

        // Create booleans
        this.createElements(this.COLS_BOOL, booleans, this.ID_BOOLEANS_DIV);
        // Create attributes
        this.createElements(this.COLS_ATTRIBUTES, attributes, this.ID_ATTRIBUTES_DIV);
        // Create interest measures
        this.createElements(this.COLS_INTEREST_MEASURES, interestMeasures, this.ID_INTEREST_MEASURES_DIV);
    },

    /**
     * Function: createMainDivs
     * It creates main Divs("main","left","right") and the base structure of applications.
     *
     * Parameters:
     * idMainDiv     {String} Id of div in which should the application be created
     * limitHits     {String} Max. number of hits to be retreived
     */
    createMainDivs: function(idMainDiv, hits, sources){
        var utils = new UtilsAR();
        // real main and necessary structure
        var mainDiv = utils.createDiv("main");
        var leftDiv = utils.createDiv("left");
        var rightDiv = utils.createDiv("right");
        var rightDivCreate = utils.createDiv("rightDivPlace");
        var rightDivButton = utils.createDiv("rightDivButton");
        var hitsHeader = utils.createDiv("hitsHeader");
        var booleansDiv = utils.createDivClas("booleans", "booleans");

        // places for booleans, operators and attributes
        var booleans = utils.createDivIdClas("booleans0", "booleansSmall");
        var attributes = utils.createDivIdClas("attributes", "attributes");
        var operators = utils.createDivIdClas("operators", "operators");

        //Headers of parts attributes, booleans and operators
        var booleansHeader = utils.createDivHtmlClas(this.language.getName(this.language.CONNECTIVES, this.lang), "headersLeft");
        var operatorsHeader = utils.createDivHtmlClas(this.language.getName(this.language.INTEREST_MEASURES, this.lang), "headersLeft");
        var attributesHeader = utils.createDivHtmlClas(this.language.getName(this.language.FIELDS, this.lang), "headersLeft");
        
        if (!this.displayMode) {
        	var rightDivHits = utils.createDiv("rightDivHits");
        	
	        // hits
	        var limitHitsDiv = utils.createDiv("limitHits");
	        var limitHitsLabel = utils.createLabel(this.language.getName(this.language.HITS_LIMIT, this.lang)); 
	        limitHitsLabel.inject(limitHitsDiv);
	        var limitHitsInput = utils.createInputText("limitHitsInput", hits.getMaxNumHits());
	        limitHitsInput.inject(limitHitsDiv);
	        var limitHitsSubmit = utils.createInputSubmit("limitHitsSubmit", this.language.getName(this.language.HITS_SEARCH_AGAIN, this.lang));
	        limitHitsSubmit.addEvent('click', function(event) {
	        	limitHitsSubmit.hide();
	        	$("getHits").fireEvent('click');
	        }.bind(this));
	        limitHitsSubmit.hide();
	        limitHitsSubmit.inject(limitHitsDiv);
	        var hitsLabel = utils.createDivIdHtml("hitsLabel", this.language.getName(this.language.HITS_LABEL, this.lang), "hitsLabel"); 
	        hitsLabel.inject(hitsHeader);
	        limitHitsDiv.inject(hitsHeader);
	        
	        // sources
	        for (i = 0; i < hits.getSourcesLength(); i++) {
	        	var source = hits.getSourceByPos(i);
	        	var sourceLabel = utils.createIdLabel('sourceLabel' + source["id"], this.language.getName(this.language.HITS_SOURCE, this.lang) + ': ' + source["id"]);
	        	sourceLabel.hide();
	        	sourceLabel.inject(rightDivHits);
	        	var sourceDiv = utils.createDivIdClas('sourceHits' + source["id"], 'sourceHits');
				sourceDiv.inject(rightDivHits);
			}
        }

        var buttonPlaceDown = utils.createDivIdClas("buttonPlaceDown", "buttonPlaceDown");

        // saving existing rules
        var save = utils.createDivClas("saveWhole");
        var saveInnerDiv = utils.createHtmlIdClick("saveRule", this.language.getName(this.language.SAVE, this.lang), "")

       
        mainDiv.inject($(idMainDiv));
        rightDiv.inject(mainDiv);
        leftDiv.inject(mainDiv);

        booleansDiv.inject(leftDiv);
        booleansHeader.inject(booleansDiv);
        booleans.inject(booleansDiv);
        operators.inject(leftDiv);
        operatorsHeader.inject(operators);
        attributes.inject(leftDiv);
        attributesHeader.inject(attributes);
        
        if (!this.displayMode) {
        	var ruleHeader = utils.createDivIdHtmlClas("ruleLabel", this.language.getName(this.language.MINING_SETTING_CREATE, this.lang), "ruleLabel");
            ruleHeader.inject(rightDiv);
        }
        
        rightDivCreate.inject(rightDiv);
        rightDivButton.inject(rightDiv);
        
        hitsHeader.inject(rightDiv);

        buttonPlaceDown.inject(rightDivButton);
        save.inject(buttonPlaceDown);

        if(this.displayMode){
        	// new rule button
            var nextRule = utils.createDivIdClas("newRule", "newRule");
            var nextRuleInnerDiv = utils.createHtmlIdClick("createRuleButton", this.language.getName(this.language.NEW_RULE, this.lang), "");
            nextRule.inject(buttonPlaceDown);
            nextRuleInnerDiv.inject(nextRule);
            
            // save button
            saveInnerDiv.inject(save);
        } else {
        	// get hits button (hidden)
            var hitsButton = utils.createHtmlIdClick("getHits", this.language.getName(this.language.SAVE, this.lang), "")
        	hitsButton.inject(save);
            
            rightDivHits.inject(rightDiv);
        }

        var elementSpecificCol;
        for(var i = 0; i < this.COLS_INTEREST_MEASURES; i++){
            elementSpecificCol = new Element('div', {
                id: this.ID_INTEREST_MEASURES_DIV+i,
                'class': "operatorsSmall"
            });
            elementSpecificCol.inject(operators);
        }
        for(i = 0; i < this.COLS_ATTRIBUTES; i++){
            elementSpecificCol = new Element('div', {
                id: this.ID_ATTRIBUTES_DIV+i,
                'class': "attributesSmall"
            });
            elementSpecificCol.inject(attributes);
        }
    },

    /**
     * Function: createElements
     * This function creates elements representing Attributes and InterestMeasures
     *
     * Parameters:
     * COLS     {Number} How many columns should elements place have
     * elements {Array} Array of elements to create
     * idWhere {String} Id of element where the elements should be injected
     */
    createElements: function(COLS, elements, idWhere){
        var column = 0;
        var elementToInject;
        for(var actualElement = 0; actualElement < elements.length; actualElement++){
            var idToBeUsed = idWhere+column;
            elementToInject = elements[actualElement].display(true);
            elementToInject.element = elements[actualElement];
            elementToInject.inject($(idToBeUsed));
            
            // This make it sures that when all columns in row are full we continue in new row
            column++;
            if(column == COLS){
                column = 0;
            }
        }
    },
    
    /**
     *  TODO func spec
     *  
     *  TODO param spec
     */
    setRuleLabel: function($html) {
    	$('ruleLabel').innerHTML = $html;
    },
    
    /**
     *  TODO func spec
     *  
     *  TODO param spec
     */
    setHitsStatusLabel: function($html) {
    	$('hitsLabel').innerHTML = $html;
    },
    
    /**
     *  TODO func spec
     *  
     *  TODO param spec
     */
    showLimitHitsSubmit: function() {
    	$('limitHitsSubmit').show('inline');
    },
    
    /**
     *  TODO func spec
     *  
     *  TODO param spec
     */
    hideLimitHitsSubmit: function() {
    	$('limitHitsSubmit').hide();
    },
    
    /**
     * Function: clearHits
     * This function is called to clear hits
     * 
     * TODO params doc
     */
    clearHits: function(id_source){
    	$('sourceHits' + id_source).empty();
    	$('sourceLabel' + id_source).hide();
    },
    
    /**
     *  TODO func spec
     *  
     *  TODO param spec
     */
    showSourceLabel: function(id_source) {
    	$('sourceLabel' + id_source).show('inline');
    },
    
    /**
     *  TODO func spec
     *  
     *  TODO param spec
     */
    displayHit: function(hits, hit, id_source) {
		hit.addEvent("display", function(){
			// TODO resolve draggability
            //this.setDraggability();
        }.bind(hits));
	    var newRuleDiv = hit.display();
	    newRuleDiv.inject($('sourceHits'+id_source));
    },
    
});
