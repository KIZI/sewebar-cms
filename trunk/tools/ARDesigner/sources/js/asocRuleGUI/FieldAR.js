/**
 * Class: FieldBase
 * This class is just the container for information about Field
 */
var FieldBase = new Class({
    /**
     * Function: display
     * This part creates Element which needs to be injected to create this Field.
     * It needs to be overriden by those who implements this.
     *
     * Returns:
     * {HTMLElement} HTMLElement representing Field.
     */
    display: function(){

    },

    /**
     * Function: setOneCategoryInfo
     * This sets one category info necessary for one category.
     *
     * Params:
     * oneCategoryInfo {Array} Array of one category info.
     */
    setOneCategoryInfo: function(oneCategoryInfo){
        this.oneCategoryInfo = oneCategoryInfo;
    },

    /**
     * Function: toJSON
     * This creates JSON representation of object.
     *
     * Returns:
     * {JSON} JSON representing this field.
     */
    toJSON: function(){
        var jsonObject = new JSONObject();
        jsonObject.name = this.name;
        jsonObject.value = this.actualValue;
        return jsonObject;
    },

    /**
     * Function: save
     * This saves proposed Value to actualValue
     */
    save: function(){ 
        this.actualValue = this.proposedValue;
    },

    /**
     * Function: setValue
     * Simple setter that sets value
     *
     * Params:
     * value {String} Value to be set
     */
    setValue: function(value){
        this.proposedValue = value;
    },

    /**
     * Function: getName
     * Simple getter. Returns name
     *
     * Returns:
     * {String} name
     */
    getName: function(){
        return this.name;
    },

    /**
     * Function: getValue
     * Simple getter. Returns name
     *
     * Returns:
     * {String} value
     */
    getValue: function(){
        return this.proposedValue;
    }
});

/**
 * Class: FieldInput
 * This class is just the container for information about Field
 */
var FieldInput = new Class({
    Implements: [FieldBase, Events],

    /**
     * Function: initialize
     * It creates instance of class
     *
     * Parameters:
     * name     {String} name
     * nameLang    {String} nameLang
     * minValue {Number} minValue
     * maxValue {Number} maxValue
     * datatype  {String} datatype
     * explanation {String} explanation
     */
    initialize: function(name, nameLang, minValue, maxValue, datatype, explanation){
        this.name = name;
        this.nameLang = nameLang;
        this.minValue = minValue;
        this.maxValue = maxValue;
        this.datatype = datatype;
        this.explanation = explanation;
        this.actualValue = "";
        this.proposedValue = "";
        this.control = new Control();
    },

    /**
     * Function: display
     * This part creates Element which needs to be injected to create this Field.
     * It overrides method of FieldBase class.
     *
     * Returns:
     * {HTMLElement} HTMLElement representing Field.
     */
    display: function(){
        var mainDiv = new Element('div',{
            name: "field",
            'class': "field"
        });
        var fieldExpression = new Element('div',{
            name: "fieldExpression",
            html: this.explanation,
            'class': "fieldExpression"
        });
        var fieldInput = new Element('input',{
            type: "text",
            name: "fieldInput",
            value: this.actualValue,
            'class': "fieldInput"
        });
        this.fieldInputt = fieldInput;
        fieldInput.addEvent('change', function(event){
            if(this.control.control(this.datatype, this.minValue, this.maxValue, event.target.get("value"))){
                this.proposedValue = event.target.get("value");
            }
            else{
                var language = new LanguageSupport();
                new HlaseniAbove(language.getName(language.INCORRECT_FIELD_VALUE, LanguageSupport.actualLang));
                event.target.set("value","");
                this.proposedValue = "";
            }
        }.bind(this))
        fieldExpression.inject(mainDiv);
        fieldInput.inject(mainDiv);
        mainDiv.field = this;

        return mainDiv;
    }
});

/**
 * Class: FieldSelect
 * This class is just the container for information about Field
 */
var FieldSelect = new Class({
    Implements: [FieldBase, Events],

    /**
     * Function: initialize
     * It creates instance of class
     *
     * Parameters:
     * name     {String} name
     * nameLang    {String} nameLang
     * explanation {String} explanation
     * choices   {Array} possibleChoices
     */
    initialize: function(name, nameLang, explanation){
        this.name = name;
        this.nameLang = nameLang;
        this.oneCategoryInfo = new Array();

        this.explanation = explanation;
        this.actualValue = "";
        this.proposedValue = "";

    },
    
    /**
     * Function: display
     * This part creates Element which needs to be injected to create this Field.
     * It overrides method of FieldBase class.
     *
     * Returns:
     * {HTMLElement} HTMLElement representing Field.
     */
    display: function(){
        var mainDiv = new Element('div',{
            name: "field",
            'class': "field"
        });
        var fieldExpression = new Element('div',{
            name: "fieldExpression",
            html: this.explanation,
            'class': "fieldExpression"
        });
        var fieldSelect = new Element('select',{
            name: "fieldInput",
            value: this.getActualValue,
            'class': "fieldInput"
        });
        fieldSelect.addEvent('change', function(event){
            var choosedOption = event.target.selectedIndex;
            this.proposedValue = event.target.options[choosedOption].get('html');
        }.bind(this))

        var option = null;
        var copyProposedValue = "";
        if(this.proposedValue != ""){
            copyProposedValue = this.proposedValue;
            option = new Element('option', {
                html: this.proposedValue
            });
            option.inject(fieldSelect);
            copyProposedValue = copyProposedValue.replace("&gt;",">");
            copyProposedValue = copyProposedValue.replace("&lt;","<");
        }
        for(var actualOption = 0; actualOption < this.oneCategoryInfo.length; actualOption++){
            if(this.oneCategoryInfo[actualOption].trim() == copyProposedValue.trim()){
                continue;
            }
            option = new Element('option', {
                html: this.oneCategoryInfo[actualOption]
            });
            option.inject(fieldSelect);
        }
        if(this.proposedValue == ""){
            this.proposedValue = this.oneCategoryInfo[0];
        }

        fieldExpression.inject(mainDiv);
        fieldSelect.inject(mainDiv);

        mainDiv.field = this;
        return mainDiv;
    }
});