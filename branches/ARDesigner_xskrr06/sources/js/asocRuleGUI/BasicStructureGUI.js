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
     * lang         {String}  Language in which should the application be created.
     */
    initialize: function(booleans, attributes, interestMeasures, idMainDiv, lang, moreRules){
        this.lang = lang;
        this.language = new LanguageSupport();
        this.moreRules = moreRules;

        // Create basic divs
        this.createMainDivs(idMainDiv);

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
     */
    createMainDivs: function(idMainDiv){
        var utils = new UtilsAR();
        // real main and necessary structure
        var mainDiv = utils.createDiv("main");
        var leftDiv = utils.createDiv("left");
        var rightDiv = utils.createDiv("right");
        var rightDivCreate = utils.createDiv("rightDivPlace");
        var rightDivButton = utils.createDiv("rightDivButton");
        var booleansDiv = utils.createDivClas("booleans", "booleans");

        // places for booleans, operators and attributes
        var booleans = utils.createDivIdClas("booleans0", "booleansSmall");
        var attributes = utils.createDivIdClas("attributes", "attributes");
        var operators = utils.createDivIdClas("operators", "operators");

        //Headers of parts attributes, booleans and operators
        var booleansHeader = utils.createDivHtmlClas(this.language.getName(this.language.CONNECTIVES, this.lang), "headersLeft");
        var operatorsHeader = utils.createDivHtmlClas(this.language.getName(this.language.INTEREST_MEASURES, this.lang), "headersLeft");
        var attributesHeader = utils.createDivHtmlClas(this.language.getName(this.language.FIELDS, this.lang), "headersLeft");

        // Adding another rule
        if(this.moreRules){
            var nextRule = utils.createDivIdClas("newRule","newRule");
            var nextRuleInnerDiv = utils.createHtmlIdClick("createRuleButton", this.language.getName(this.language.NEW_RULE, this.lang), "");
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

        rightDivCreate.inject(rightDiv);
        rightDivButton.inject(rightDiv);

        buttonPlaceDown.inject(rightDivButton);

        if(this.moreRules){
            nextRule.inject(buttonPlaceDown);
            nextRuleInnerDiv.inject(nextRule);
        }

        save.inject(buttonPlaceDown);
        saveInnerDiv.inject(save);


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
    }
});
