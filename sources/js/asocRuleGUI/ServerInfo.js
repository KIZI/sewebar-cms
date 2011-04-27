/**
 * Class: ServerInfo
 * It parses JSON from server and based on it it creates appropriate DataTypes.
 */
var ServerInfo = new Class({
    /**
     * Function: initialize
     * Creates instance of class ServerInfo
     */
    initialize: function(item){
        this.depthNesting = 1;
        this.depthLevels = new DepthNesting();

        this.attributes = new Array();
        this.attributes2 = new Array();
        this.attributesFields = new Array(); //categories

        this.interestMeasures = new Array(); // obsahuje Interest Measures(Operator)
        
        this.booleans = new Array();
        this.initializeBooleans();
        
        this.minValues = new Array();
        this.maxValues = new Array();
        this.CONS = 0;
        this.ANT = 1;
        this.IM = 2;
        this.GENERAL = 3;

        this.supportedIMCombinations = new Array();

        this.moreRules = true;
        this.rules = new Array();

        this.solveInterestMeasures(item);
        this.solvePossibleCoefficients(item);
        this.solveAttributes(item);
        this.solveSupIM(item);
        this.solveMinMax(item);
        this.solveDepth(item);

        this.existingRules = new Array();
        this.solveRules(item);
        
        this.hits = new Array();
    },

    /**
     * Function: getExistingRules
     * Just a simple getter, returning array of existing rules.
     */
    getExistingRules: function(){
        return this.existingRules;
    },
    
    /**
     * Function: getHits
     * Just a simple getter, returning array of hits.
     */
    getHits: function(){
        return this.hits;
    },

    /**
     * Function: initializeBooleans
     * This initializes booleans in the array this.booleans. Booleans are instances of
     * BooleanCl
     */
    initializeBooleans: function(){
        this.booleans.push(new BooleanCl("AND","and"));
        this.booleans.push(new BooleanCl("OR","or"));
        this.booleans.push(new BooleanCl("NEG","neg"));
        this.booleans.push(new BooleanCl("(","lbrac"));
        this.booleans.push(new BooleanCl(")","rbrac"));
    },

    /**
     * Function: solveInterestMeasures
     * It gets item.operators from JSON and based on it it creates Operators
     *
     * Parameters:
     * item     {JSONItem} JSON item
     */
    solveInterestMeasures: function(item){
        var actualIM = null;
        var name, nameLang, explanation, fieldNames, fieldLangs, fieldMinValues, fieldMaxValues, fieldDatatypes, fieldExplanations, fieldMin, fieldMax;
        var fields = new Array();
        for(var i = 0; i < item.interestMeasures.length; i++){
            actualIM = item.interestMeasures[i];
            // Info about interestMeasure itself
            name = actualIM.name;
            nameLang = actualIM.localizedName;
            explanation = actualIM.explanation;

            fields = actualIM.fields;
            // fields
            fieldNames = fields.fieldNames;
            if(fieldNames == undefined){
                fieldNames = new Array();
            }
            fieldLangs = fields.fieldNamesLocalized;
            if(fieldLangs == undefined){
                fieldLangs = new Array();
            }
            fieldMinValues = fields.fieldMinValues;
            if(fieldMinValues == undefined){
                fieldMinValues = new Array();
            }
            fieldMaxValues = fields.fieldMaxValues;
            if(fieldMaxValues == undefined){
                fieldMaxValues = new Array();
            }
            fieldDatatypes = fields.fieldDatatypes;
            if(fieldDatatypes == undefined){
                fieldDatatypes = new Array();
            }
            fieldExplanations = fields.fieldExplanations;
            if(fieldExplanations == undefined){
                fieldExplanations = new Array();
            }
            fieldMin = fields.fieldInclusivesMin;
            if(fieldMin == undefined){
                fieldMin = new Array();
            }
            fieldMax = fields.fieldInclusivesMax;
            if(fieldMax == undefined){
                fieldMax = new Array();
            }
            
            fields = this.solveFields(fieldNames,fieldLangs,fieldMinValues,fieldMaxValues,fieldDatatypes,fieldExplanations, "", fieldMin, fieldMax);
            
            this.interestMeasures.push(new InterestMeasure(name, nameLang, explanation, fields));
        }
    },

    /**
     * Function: getMinValues
     * getter it gets min values depending on param
     *
     * Parameters:
     * fieldNames     {Array} names
     * fieldLangs     {Array} localized names
     * fieldMinValues     {Array} min values
     * fieldMaxValues     {Array} max values
     * fieldDatatypes     {Array} datatypes
     * fieldExplanations     {Array} explanations
     * fNameTest     {String} Name of category
     *
     * Returns:
     * {Array} Array of fields
     */
    solveFields: function(fieldNames,fieldLangs,fieldMinValues,fieldMaxValues,fieldDatatypes,fieldExplanations, fNameTest, fieldMinInc, fieldMaxInc){
        var fName,fLang,fMinValue,fMaxValue,fDatatype,fExplanation, newField, fieldMin, fieldMax;
        var fields = new Array();
        for(var actualField = 0; actualField < fieldNames.length; actualField++){
            fName = fieldNames[actualField]
            if(fieldLangs.length < actualField){
                fLang = ""
            }
            else{
                fLang = fieldLangs[actualField]
            }

            if(fieldMinValues.length < actualField){
                fMinValue = ""
            }
            else{
                fMinValue = fieldMinValues[actualField]
            }

            if(fieldMaxValues.length < actualField){
                fMaxValue = ""
            }
            else{
                fMaxValue = fieldMaxValues[actualField]
            }

            if(fieldDatatypes.length < actualField){
                fDatatype = ""
            }
            else{
                fDatatype = fieldDatatypes[actualField]
            }

            if(fieldExplanations.length < actualField){
                fExplanation = ""
            }
            else{
                fExplanation = fieldExplanations[actualField]
            }

            if(fieldMinInc.length < actualField){
                fieldMin = true;
            }
            else{
                if(fieldMinInc[actualField] == "false"){
                    fieldMin = false;
                }
                else{
                    fieldMin = true;
                }
            }

            if(fieldMaxInc.length < actualField){
                fieldMax = true;
            }
            else{
                if(fieldMaxInc[actualField] == "false"){
                    fieldMax = false;
                }
                else{
                    fieldMax = true;
                }
            }

            if(fNameTest.toLowerCase() != "one category"){
                newField = new FieldInput(fName,fLang,fMinValue,fMaxValue,fDatatype,fExplanation, fieldMin, fieldMax);
            }
            else{
                newField = new FieldSelect(fName,fLang,fExplanation);
            }
            fields.push(newField);
        }
        return fields;
    },

    /**
     * Function: solveAttributes
     * It gets item.attributes from JSON and based on it it creates Attributes
     *
     * Parameters:
     * item     {JSONItem} JSON item
     */
    solveAttributes: function(item){
        var attrName, oneCategoryInfo, attribute;
        for(var actualAttribute = 0; actualAttribute < item.attributes.length; actualAttribute++){
            attribute = item.attributes[actualAttribute];

            attrName = attribute.name;
            if(attrName == undefined){
                attrName = "";
            }
            oneCategoryInfo = attribute.choices
            if(oneCategoryInfo == undefined){
                oneCategoryInfo = new Array();
            }

            this.attributes.push(new Attribute(attrName, oneCategoryInfo, clone_obj(this.attributesFields)));
            this.attributes2.push(new Attribute(attrName, oneCategoryInfo, clone_obj(this.attributesFields)));
        }
    },

    /**
     * Function: solvePossibleCoefficients
     * It gets item.posCoef from JSON and based on it it creates AttributesFields
     *
     * Parameters:
     * item     {JSONItem} JSON item
     */
    solvePossibleCoefficients: function(item){
        var coef, category, localizedCategory;
        var fieldNames, fieldLangs, fieldMinValues, fieldMaxValues, fieldDatatypes, fieldExplanations, fieldMin, fieldMax;
        var fields = new Array();
        var fieldsData;
        for(var actualCoef = 0; actualCoef < item.possibleCoef.length; actualCoef++){
            coef = item.possibleCoef[actualCoef];

            category = coef.name;
            localizedCategory = coef.localizedName;
            if(localizedCategory == "" || localizedCategory == undefined){
                localizedCategory = category;
            }
            // fields
            fieldsData = coef.fields;
            fieldNames = fieldsData.fieldNames;
            if(fieldNames == undefined){
                fieldNames = new Array();
            }
            fieldLangs = fieldsData.fieldNamesLocalized;
            if(fieldLangs == undefined){
                fieldLangs = new Array();
            }
            fieldMinValues = fieldsData.fieldMinValues;
            if(fieldMinValues == undefined){
                fieldMinValues = new Array();
            }
            fieldMaxValues = fieldsData.fieldMaxValues;
            if(fieldMaxValues == undefined){
                fieldMaxValues = new Array();
            }
            fieldDatatypes = fieldsData.fieldDatatypes;
            if(fieldDatatypes == undefined){
                fieldDatatypes = new Array();
            }
            fieldExplanations = fieldsData.fieldExplanations;
            if(fieldExplanations == undefined){
                fieldExplanations = new Array();
            }
            fieldMin = fields.fieldInclusivesMin;
            if(fieldMin == undefined){
                fieldMin = new Array();
            }
            fieldMax = fields.fieldInclusivesMax;
            if(fieldMax == undefined){
                fieldMax = new Array();
            }

            fields = this.solveFields(fieldNames,fieldLangs,fieldMinValues,fieldMaxValues,fieldDatatypes,fieldExplanations, category, fieldMin, fieldMax)

            this.attributesFields.push(new AttributeFields(category, localizedCategory, fields));
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

    /**
     * Function: solveRules
     * It solves rules included in item from server and creates array of rules which
     * will be shown later in process.
     */
    solveRules: function(item){
        this.moreRules = item.moreRules;
        var amountOfRules = item.rules;
        if(this.moreRules == "false"){
            if(amountOfRules > 0){
                this.solveRule(item["rule0"]);
            }
            else{
            	var asociationRule = this.solveRule(new Array(), this.attributes); 
            	this.existingRules.push(asociationRule);
            }
        }
        else{
            for(var actualRule = 0; actualRule < amountOfRules; actualRule++){
            	var asociationRule = this.solveRule(item["rule"+actualRule], this.attributes);
            	this.existingRules.push(asociationRule);
            }
        }
    },

    /**
     * Function: solveRule
     * It creates an AsociationRule from given values.
     *
     * Parameters:
     * rule     {Array} array of ARElements
     * 
     * Returns:
     * {AssocitaionRule} asociationRule 
     */
    solveRule: function(rule, attributes){
        var ruleElement, actualField, type, name, category, fields, oldElement, fieldName, fieldValue;
        var positionInRule = 0;
        var asociationRule = new AsociationRule(this);
        for(var actualElement = 0; actualElement < rule.length; actualElement++){
            ruleElement = rule[actualElement];
            type = ruleElement.type;
            name = ruleElement.name;
            if(this.isBoolean(type)){
                oldElement = clone_obj(this.getElement(name, this.booleans));
            }
            else if(type == "attr"){
                // Podle category vybrat attributeField
                oldElement = clone_obj(this.getElement(name, attributes));
                if(oldElement == null){
                    // TOdo asi fatalni chyba
                    continue;
                }
                category = ruleElement.category;
                fields = ruleElement.fields;
                for(actualField=0; actualField < fields.length; actualField++){
                    fieldName = fields[actualField].name
                    fieldValue = fields[actualField].value
                    oldElement.setValue(category, fieldName, fieldValue);
                }
            }
            else if(type == "oper"){
                fields.actualValue = ""
                oldElement = clone_obj(this.getElement(name, this.interestMeasures));
                if(oldElement == null){
                    continue;
                }
                fields = ruleElement.fields;
                // Z operatoru vybrat podle name odpovídající field a nastavit mu hodnotu.
                for(actualField=0; actualField < fields.length; actualField++){
                    fieldName = fields[actualField].name
                    fieldValue = fields[actualField].value
                    oldElement.setValue(fieldName, fieldValue);
                }
            }
            asociationRule.insertItemWithoutDisplay(oldElement, positionInRule);
            positionInRule++;
        }
        
        return asociationRule;
    },
    
    /**
     * Function: initHits
     * Just a simple initialization of hits
     */
    initHits: function() {
    	this.hits = new Array();	
    },
    
    /**
     * Function: solveHits
     * It solves hits received from the server which are then shown.
     */
    solveHits: function(item) {
    	this.initHits();
    	
    	var amountOfRules = item.rules;
    	for (var actualRule = 0; actualRule < amountOfRules; actualRule++) {
    		var asociationRule = this.solveRule(item["rule" + actualRule], this.attributes2);
    		this.hits.push(asociationRule);
    	}
    },

    /**
     * Function: getElement
     * It finds element by name in given array
     *
     * Parameters:
     * name     {String} name of element
     * elements {Array} array of elements to look in
     *
     * Returns:
     * {ARElement} ARElement belonging to name or null
     */
    getElement: function(name, elements){
        for(var actualElement = 0; actualElement < elements.length; actualElement++){
            if(elements[actualElement].getName() == name){
                return clone_obj(elements[actualElement]);
            }
        }
        return null;
    },

    /**
     * Function: isBoolean
     * Decides whether param is boolean
     *
     * Parameters:
     * type     {String} type of element
     *
     * Returns:
     * {Boolean}
     */
    isBoolean: function(type){
        if(type == "bool" || type == "lbrac" || type == "rbrac" ||
            type == "and" || type == "or" || type == "neg"){
            return true;
        }
        return false;
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
        return this.interestMeasures;
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
    },

    /**
     * Function: getBooleans
     * getter it gets booleans
     *
     * Returns:
     * {Array} booleans
     */
    getBooleans : function(){
        return this.booleans;
    }
});