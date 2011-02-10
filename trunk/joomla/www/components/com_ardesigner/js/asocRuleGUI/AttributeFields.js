/**
 * Class: AttributeFields
 * This class only stores data about which possble fields belongs to which attribute
 * choice(like one category for example)
 */
var AttributeFields = new Class({
    /**
     * Function: initialize
     * This function creates an instance of class AttributeFields
     *
     * Parameters:
     * category     {String} Category(One category and so on)
     * fieldName    {String} Name of the field
     * fieldNameLang           {String} Nme of the field in users language
     * minValue    {String} minimal Value of the field
     * maxValue     {String} maximal Value of the field
     * datatype {String} datatype of the field.
     */
    initialize: function(category, fieldName, fieldNameLang, minValue, maxValue, datatype, explanation){
        this.category = category;
        this.fieldName = fieldName;
        this.fieldNameLang = fieldNameLang;
        this.minValue = minValue;
        this.maxValue = maxValue;
        this.datatype = datatype;
        this.explanation = explanation;
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
    },

    /**
     * Function: getCategory
     * getter returns category
     *
     * Returns:
     * {String} category
     */
    getCategory: function(){
        return this.category;
    },

    /**
     * Function: getfName
     * getter returns fieldName
     *
     * Returns:
     * {String} fieldName
     */
    getfName: function(){
        return this.fieldName;
    },

    /**
     * Function: getfNameLang
     * getter returns fieldNameLang
     *
     * Returns:
     * {String} fieldNameLang
     */
    getfNameLang: function(){
        return this.fieldNameLang;
    },

    /**
     * Function: getMinValue
     * getter returns minimal value
     *
     * Returns:
     * {String} minValue
     */
    getMinValue: function(){
        return this.minValue;
    },

    /**
     * Function: getMaxValue
     * getter returns maxValue
     *
     * Returns:
     * {String} maxValue
     */
    getMaxValue: function(){
        return this.maxValue;
    },

    /**
     * Function: getDatatype
     * getter returns datatype
     *
     * Returns:
     * {String} datatype
     */
    getDatatype: function(){
        return this.datatype;
    }
})