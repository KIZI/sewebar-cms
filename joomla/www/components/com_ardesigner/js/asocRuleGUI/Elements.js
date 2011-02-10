/**
 * Class: ARElement
 * Interface which ensures that other classes have functions the app requires
 */
var ARElement = new Class({
    /**
     * Function: getType
     * getter it gets type
     *
     * Returns:
     * {String} type
     */
    getType: function(){
        return this.type;
    },

    /**
     * Function: getName
     * getter it gets name
     *
     * Returns:
     * {String} name
     */
    getName: function(){
        return this.name;
    }
})

/**
 * Class: BooleanCl
 * It implements ARElement.
 */
var BooleanCl = new Class({
    Implements: [ARElement],

    /**
     * Function: initialize
     * It creates instance of class
     *
     * Parameters:
     * name     {String} name
     * type    {String} type
     */
    initialize: function(name, type){
        this.name = name;
        this.type = type;
    }
})

/**
 * Class: Attribute
 * It implements ARElement and adds one category info.
 */
var Attribute = new Class({
    Implements: [ARElement],
    
    /**
     * Function: initialize
     * It creates instance of class
     *
     * Parameters:
     * name     {String}
     * oneCategoryInfo    {mixed}
     */
    initialize: function(name, oneCategoryInfo, explanation){
        this.name = name;
        this.oneCategoryInfo = oneCategoryInfo;
        this.type = "attr";
        this.explanation = explanation;
    },

    /**
     * Function: getOneCategoryInfo
     * getter it gets oneCategoryInfo
     *
     * Returns:
     * {mixed} oneCategoryInfo
     */
    getOneCategoryInfo: function(){
        return this.oneCategoryInfo;
    },

    getExplanation: function(){
        return this.explanation;
    }
})

/**
 * Class: Operator
 * It implements ARElenet and adds function necessary to retrieve data about fields.
 */
var Operator = new Class({
    Implements: [ARElement],

    /**
     * Function: initialize
     * It creates instance of class
     *
     * Parameters:
     * name     {String} name
     * nameLang    {String} nameLang
     * fieldNames           {Array} fieldNames
     * fieldLangs    {Array} fieldLangs
     * fieldMinValues   {Array} fieldMinValues
     * fieldMaxValues {Array} fieldMaxValues
     * fieldDatatypes  {Array} fieldDatatypes
     */
    initialize: function(name, nameLang, fieldNames, fieldLangs, fieldMinValues, fieldMaxValues, fieldDatatypes, explanation){
        this.name = name;
        this.nameLang = nameLang;
        this.explanation = explanation;

        this.fieldNames = fieldNames;
        this.fieldLangs = fieldLangs;
        this.fieldMinValues = fieldMinValues;
        this.fieldMaxValues = fieldMaxValues;
        this.fieldDatatypes = fieldDatatypes;

        this.type = "oper";
    },

    /**
     * Function: getNameLang
     * getter it gets nameLang
     *
     * Returns:
     * {mixed} nameLang
     */
    getNameLang: function(){
        return this.nameLang;
    },

    /**
     * Function: getFieldNames
     * getter it gets fieldNames
     *
     * Returns:
     * {Array} fieldNames
     */
    getFieldNames: function(){
        return this.fieldNames;
    },

    /**
     * Function: getFieldLangs
     * getter it gets fieldLangs
     *
     * Returns:
     * {Array} fieldLangs
     */
    getFieldLangs: function(){
        return this.fieldLangs;
    },

    /**
     * Function: getFieldMinValues
     * getter it gets fieldMinValues
     *
     * Returns:
     * {Array} fieldMinValues
     */
    getFieldMinValues: function(){
        return this.fieldMinValues;
    },

    /**
     * Function: getFieldMaxValues
     * getter it gets fieldMaxValues
     *
     * Returns:
     * {Array} fieldMaxValues
     */
    getFieldMaxValues: function(){
        return this.fieldMaxValues;
    },

    /**
     * Function: getFieldDatatype
     * getter it gets fieldDatatypes
     *
     * Returns:
     * {Array} fieldDatatypes
     */
    getFieldDatatype: function(){
        return this.fieldDatatypes;
    },

    /**
     * Function: getExplanation
     * getter returns explanation
     *
     * Returns:
     * {String} explanation
     */
    getExplanation: function(){
        return this.explanation;
    }
})


