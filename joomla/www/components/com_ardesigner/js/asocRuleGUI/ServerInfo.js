/**
 * Class: ServerInfo
 * It parses JSON from server and based on it it creates appropriate DataTypes.
 */
var ServerInfo = new Class({
    /**
     * Function: initialize
     * Creates instance of class ServerInfo
     */
    initialize: function(){
        this.depthNesting = 1;
        this.depthLevels = new DepthNesting();

        this.attributes = new Array();
        this.attributesFields = new Array(); //categories

        this.operators = new Array(); // obsahuje Operator
        
        this.minValues = new Array();
        this.maxValues = new Array();
        this.CONS = 0;
        this.ANT = 1;
        this.IM = 2;
        this.GENERAL = 3;

        this.supportedIMCombinations = new Array();

        this.moreRules = true;
        this.rules = new Array();
    },

    /**
     * Function: solveOperators
     * It gets item.operators from JSON and based on it it creates Operators
     *
     * Parameters:
     * item     {JSONItem} JSON item
     */
    solveOperators: function(item){
        for(var i = 0; i < item.operators.length; i++){
            var name = item.operators[i];
            var nameLang = item.operatorsLang[i];
            var fieldNames = item[name.replace(/ /g,"")+"F"];
            if(fieldNames == undefined){
                fieldNames = new Array();
            }
            var fieldLangs = item[name.replace(/ /g,"")+"FLang"];
            if(fieldLangs == undefined){
                fieldLangs = new Array();
            }
            var fieldMinValues = item[name.replace(/ /g,"")+"MinValue"];
            if(fieldMinValues == undefined){
                fieldMinValues = new Array();
            }
            var fieldMaxValues = item[name.replace(/ /g,"")+"MaxValue"];
            if(fieldMaxValues == undefined){
                fieldMaxValues = new Array();
            }
            var fieldDatatypes = item[name.replace(/ /g,"")+"Datatype"];
            if(fieldDatatypes == undefined){
                fieldDatatypes = new Array();
            }
            var fieldExplanations = item[name.replace(/ /g,"")+"Expl"];
            if(fieldExplanations == undefined){
                fieldExplanations = new Array();
            }
            this.operators.push(new Operator(name, nameLang, fieldNames, fieldLangs, fieldMinValues, fieldMaxValues, fieldDatatypes, fieldExplanations));
        }
    },

    /**
     * Function: solveAttributes
     * It gets item.attributes and item.posCoef from JSON and based on it it creates
     * Attributes and AttributesFields
     *
     * Parameters:
     * item     {JSONItem} JSON item
     */
    solveAttributes: function(item){
        for(var i = 0; i < item.attributes.length; i++){
            this.attributes.push(new Attribute(item.attributes[i], item[item.attributes[i].replace(" ","")]));
        }
        for(i = 0; i < item.posCoef.length; i++){
            var category = item.posCoef[i];
            var fieldName = item[item.posCoef[i].replace(/ /g,"")+"F"];
            var fieldNameLang = item[item.posCoef[i].replace(/ /g,"")+"FLang"];
            var minValue = item[item.posCoef[i].replace(/ /g,"")+"MinValue"];
            var maxValue = item[item.posCoef[i].replace(/ /g,"")+"MaxValue"];
            var datatype = item[item.posCoef[i].replace(/ /g,"")+"Datatype"];
            var explanation = item[item.posCoef[i].replace(/ /g,"")+"Expl"];
            //console.log(category+" "+fieldName+" "+fieldNameLang+" "+minValue+" "+maxValue+" "+datatype+" "+explanation);
            this.attributesFields.push(new AttributeFields(category, fieldName, fieldNameLang, minValue, maxValue, datatype, explanation));
        }
    },

    /**
     * Function: solveSupIM
     * It gets item.supIMCombinations from JSON and based on it it creates
     * this.supportedIMCombinations
     *
     * Parameters:
     * item     {JSONItem} JSON item
     */
    solveSupIM: function(item){
        var howMany = item.supIMCombinations;
        for(var i = 1 ; i <= howMany; i++){
            this.supportedIMCombinations.push(item["supIMCom"+i]);
        }
    },

    /**
     * Function: solveMinMax
     * It gets item.(...)MinNumberBBA from JSON and based on it it creates
     * this.minValues and this.maxValues
     *
     * Parameters:
     * item     {JSONItem} JSON item
     */
    solveMinMax: function(item){
        this.minValues[this.CONS] = item.consMinNumberBBA;
        this.maxValues[this.CONS] = item.consMaxNumberBBA;

        this.minValues[this.ANT] = item.antMinNumberBBA;
        this.maxValues[this.ANT] = item.antMaxNumberBBA;

        this.minValues[this.IM] = item.IMMinNumberBBA;
        this.maxValues[this.IM] = item.IMMaxNumberBBA;

        this.minValues[this.GENERAL] = item.minNumberBBA;
        this.maxValues[this.GENERAL] = item.maxNumberBBA;
    },

    /**
     * Function: solveDepth
     * It gets item.depthNesting and item.depthLevels from JSON and based on it
     * it creates DepthNesting
     *
     * Parameters:
     * item     {JSONItem} JSON item
     */
    solveDepth: function(item){
        this.depthNesting = item.depthNesting;
        for(var i = 1; i <= this.depthNesting; i++){
            this.depthLevels.add(item["depth"+i][0], item["depth"+i][1], item["depth"+i][2]);
        }
    },

    /**
     * Function: getMinValues
     * getter it gets min values depending on param
     *
     * Parameters:
     * which     {String} "cons", "ant", "IM", "general"
     *
     * Returns:
     * {String} min Value or ""
     */
    getMinValues: function(which){
        if(which == "cons"){
            return this.minValues[this.CONS];
        }
        if(which == "ant"){
            return this.minValues[this.ANT];
        }
        if(which == "IM"){
            return this.minValues[this.IM];
        }
        if(which == "general"){
            return this.minValues[this.GENERAL];
        }
        return "";
    },

    /**
     * Function: getMaxValues
     * getter it gets max values depending on param
     *
     * Parameters:
     * which     {String} "cons", "ant", "IM", "general"
     *
     * Returns:
     * {String} maxValue or ""
     */
    getMaxValues: function(which){
        if(which == "cons"){
            return this.maxValues[this.CONS];
        }
        if(which == "ant"){
            return this.maxValues[this.ANT];
        }
        if(which == "IM"){
            return this.maxValues[this.IM];
        }
        if(which == "general"){
            return this.maxValues[this.GENERAL];
        }
        return "";
    },

    solveRules: function(item){
        this.moreRules = item.moreRules;
        // To be implemented sometime later.
    },

    /**
     * Function: getRules
     * getter it gets rules
     *
     * Returns:
     * {Array} rules
     */
    getRules: function(){
        return this.rules;
    },
    
    /**
     * Function: getSupportedIMCombination
     * getter it gets supportedIMCombinations
     *
     * Returns:
     * {Array} supportedIMCombinations
     */
    getSupportedIMCombinations: function(){
        return this.supportedIMCombinations;
    },

    /**
     * Function: getMoreRules
     * getter it gets moreRules
     *
     * Returns:
     * {mixed} moreRUles
     */
    getMoreRules: function(){
        return this.moreRules
    },
    
    /**
     * Function: getOperators
     * getter it gets operators
     *
     * Returns:
     * {Array} operators
     */
    getOperators: function(){
        return this.operators;
    },
    
    /**
     * Function: getAttributes
     * getter it gets attributes
     *
     * Returns:
     * {Array} attributes
     */
    getAttributes : function(){
        return this.attributes;
    },
    
    /**
     * Function: getAttributeFields
     * getter it gets attributesFields
     *
     * Returns:
     * {Array} attributesFields
     */
    getAttributesFields: function(){
        return this.attributesFields;
    },
    
    /**
     * Function: getDepthNesting
     * getter it gets depthNesting
     *
     * Returns:
     * {Number} depthNesting
     */
    getDepthNesting: function(){
        return this.depthNesting;
    },

    /**
     * Function: getDepthLevels
     * getter it gets depthLevels
     *
     * Returns:
     * {DepthNesting} depthLevels
     */
    getDepthLevels: function(){
        return this.depthLevels;
    }
})
