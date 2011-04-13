/**
 * Class: ARElement
 * Interface which ensures that other classes have functions the app requires
 */
var ARElement = new Class({
    /**
     * Function: isBoolean
     * It simply returns if the ARElement is boolean.
     *
     * Returns:
     * {Boolean} whether the ARElement is boolean.
     */
    isElementBoolean: function(){
        return this.isBoolean;
    },
    
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
});

/**
 * Class: BooleanCl
 * It implements ARElement.
 */
var BooleanCl = new Class({
    Implements: [ARElement, Events],

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
        this.nameLang = name;
        this.type = type;
        this.isBoolean = true;

        this.BOOL_CLASS = "prvek boolean "+type;
    },

    /**
     * Function: display
     * Creates HTMLElement representing this Boolean.
     *
     * Returns:
     * {HTMLElement} element representing this Boolean
     */
    display: function(shouldBeCreated){
        var utils = new UtilsAR();
        var booleanElement = utils.createPrvek(this.BOOL_CLASS, this.nameLang);
        booleanElement.element = this;
        booleanElement.shouldBeCreated = shouldBeCreated;
        return booleanElement;
    },

    /**
     * Function: save
     * Here it does nothing.
     */
    save: function(){
        
    },

    /**
     * Function: onClickMy
     * It is called when its clicked on this element.
     *
     * Params:
     * event {Event} event
     */
    onClickMy: function(event){
        
    },

    /**
     * Function: toJSON
     * It returns JSON representation of object.
     *
     * Returns:
     * {JSONObject} JSON representation of object.
     */
    toJSON: function(){
        var jsonObject = new JSONObject();
        jsonObject.name = this.name;
        jsonObject.type = this.type;
        return jsonObject;
    }
});

/**
 * Class: Attribute
 * It implements ARElement and adds one category info.
 */
var Attribute = new Class({
    Implements: [ARElement, Events],

    /**
     * Function: initialize
     * It creates instance of class
     *
     * Parameters:
     * name     {String}
     * oneCategoryInfo    {Array} array of possible options for one category.
     * attributeFields  {Array} Array of possible attribute fields
     */
    initialize: function(name, oneCategoryInfo, attributeFields){
        ATTTRIBUTE_TYPE = "attr";
        this.attributeFields = attributeFields;
        this.name = name;
        this.nameLang = name;
        this.type = ATTTRIBUTE_TYPE;

        // This sets one category info to all attrfFields, so they know it if they need it.
        for(var actualAttrFields = 0; actualAttrFields < attributeFields.length; actualAttrFields++){
            attributeFields[actualAttrFields].setOneCategoryInfo(oneCategoryInfo);
        }
        // If at least one attributeField exists.
        if(attributeFields.length > 0){
            this.actualAttrField = attributeFields[0]
        }
        else{
            this.actualAttrField = null;
        }
        this.isBoolean = false;

        this.ATTR_CLASS = "prvek attribute "+ATTTRIBUTE_TYPE;
    },

    /**
     * Function: display
     * Creates HTMLElement representing this attribute.
     *
     * Returns:
     * {HTMLElement} element representing this Attribute
     */
    display: function(shouldBeCreated){
        var utils = new UtilsAR();
        // Tim co ma byt zobrazeno je jmeno<br><i>field1 : value1</i><br> ...
        var textToDisplay;
        if(this.actualAttrField != null){
            textToDisplay = this.nameLang+"<br>"+this.actualAttrField.getFieldNameValues();
        }
        else{
            textToDisplay = this.nameLang;
        }
        var attributeElement = utils.createPrvek(this.ATTR_CLASS, textToDisplay);
        attributeElement.element = this;
        attributeElement.shouldBeCreated = shouldBeCreated;
        this.specificId = "attr"+Counter.counter;
        attributeElement.set('id',this.specificId);
        Counter.counter++;
        return attributeElement;
    },

    /**
     * Function: setValue
     * It sets value to specified attributeField
     *
     * Parameters:
     * attrFieldName     {String} name of attribute field to set value
     * fieldName         {String} name of the field
     * value             {String} value of the field
     */
    setValue: function(attrFieldName, fieldName, value){
        for(var actualAttrField = 0; actualAttrField < this.attributeFields.length; actualAttrField++){
            if(this.attributeFields[actualAttrField].getName() == attrFieldName){
                this.actualAttrField = this.attributeFields[actualAttrField];
                this.actualAttrField.setValue(fieldName, value);
            }
        }
    },

    /**
     * Function: displayAskingWindow
     * It creates and displays window, asking user for additional informations
     * about this attribute
     *
     * Returns:
     * {HTMLElement}  askingWindowDiv  Div which is created. I return it mainly for testing.
     */
    displayAskingWindow: function(){
        // Create asking window div
        this.podklad = new Element('div',{
            id: "podklad"
        });
        this.askingWindowDiv = new Element('div',{
            id: "askingWindow",
            'class': "askingWindow"
        });
        // Create topPart div
        var topPartDiv = new Element('div',{
            id: "topPartAW",
            'class': "topPartAW"
        });
        this.createTopDiv(topPartDiv);
        topPartDiv.inject(this.askingWindowDiv);
        // Create field part
        this.fieldDiv = this.actualAttrField.display();
        this.fieldDiv.inject(this.askingWindowDiv)
        // Create button div
        var buttonDiv = new Element('div',{
            id: "buttonDivAW",
            'class': "buttonDivAW"
        });
        // Create button
        this.createButton(buttonDiv);
        buttonDiv.inject(this.askingWindowDiv);
        return this.askingWindowDiv;
    },

    /**
     * Function: onClickMy
     * It is called when its clicked on this element. It creates asking window.
     *
     * Params:
     * event {Event} event
     */
    onClickMy: function(event){
        if(AsociationRules.attrCoef == "prohibited"){
            return;
        }
        var askingWindow = this.displayAskingWindow();
        this.podklad.inject($('mainDiv'));
        askingWindow.inject($('mainDiv'));
    },

    /**
     * Function: recreateFields
     * It removes field part of asking window and creates it again with new
     * content.
     */
    recreateFields: function(){
        var newDiv = this.actualAttrField.display();
        newDiv.replaces(this.fieldDiv);
        this.fieldDiv = newDiv;
    },

    /**
     * Function: createOption
     * It creates option and returns it back
     *
     * Parameters:
     * attrField     {AttributeField} atributeField which belongs to this option
     *
     * Returns:
     * {HTMLElement} element representing this option
     */
    createOption: function(attrField){
        var option = new Element('option', {
            html: attrField.getLocalizedName()
        });
        option.attributeField = attrField
        return option;
    },

    /**
     * Function: createTopDiv
     * It creates top part of asking window. This part contains select which
     * contains all possible AttributeFields.
     *
     * Parameters:
     * topPart     {HTMLElement}  This is parts in which the topDiv should be created.
     */
    createTopDiv: function(topPart){
        var topSelect = new Element('select',{
            id: "topDivAW",
            'class': "askingWindowSelect"
        });
        var option;
        var attributeFields = this.attributeFields;
        // Create option if already selected before.
        if(this.actualAttrField != null){
            option = this.createOption(this.actualAttrField);
            option.inject(topSelect);
        }
        // Create all reamining options
        for(var actualAttributeChoice = 0; actualAttributeChoice < attributeFields.length; actualAttributeChoice++){
            // Create options
            if(this.actualAttrField == attributeFields[actualAttributeChoice]){
                continue;
            }
            option = this.createOption(attributeFields[actualAttributeChoice]);
            option.inject(topSelect);
        }
        topSelect.addEvent('change', function(event){
            var actualSelect = event.target;
            var optionActualPos = actualSelect.selectedIndex;
            this.actualAttrField = actualSelect.options[optionActualPos].attributeField;
            this.recreateFields();
        }.bind(this));

        topSelect.inject(topPart);
    },

    /**
     * Function: createButton
     * It creates save button and add event to it.
     *
     * Parameters:
     * buttonPart     {HTMLElement}  This is parts in which the button should be created.
     */
    createButton: function(buttonPart){
        var saveLang = "save";
        var button = new Element('input', {
            type: "button",
            id: "saveButton",
            'class': "buttonSave",
            value: saveLang
        });
        button.addEvent('click', function(event){
            this.save();
        }.bind(this));
        button.inject(buttonPart);
    },

    /**
     * Function: save
     * When user clicks on the save button, this function saves data he filled in.
     */
    save: function(){
        this.actualAttrField.save();
        if(this.actualAttrField.getValue() == ""){
            if(AsociationRules.attrCoef == "required"){
                var language = new LanguageSupport();
                new HlaseniAbove(language.getName(language.INCORRECT_FIELD_VALUE, LanguageSupport.actualLang));
                return;
            }
        }
        this.askingWindowDiv.dispose();
        this.podklad.dispose();
        var textToDisplay = "";
        if(this.actualAttrField != null){
            textToDisplay = this.nameLang+"<br>"+this.actualAttrField.getFieldNameValues();
        }
        else{
            textToDisplay = this.nameLang;
        }
        $(this.specificId).set('html',textToDisplay);
    //this.fireEvent("save");
    },

    /**
     * Function: toJSON
     * It returns JSON representation of object.
     *
     * Returns:
     * {JSONObject} JSON representation of object.
     */
    toJSON: function(){
        var jsonObject = new JSONObject();
        jsonObject.name = this.name;
        jsonObject.type = this.type;
        jsonObject.category = this.actualAttrField.getName();
        jsonObject.fields = this.actualAttrField.toJSON();
        return jsonObject;
    }
});

/**
 * Class: AttributeFields
 * This class only stores data about which possble fields belongs to which attribute
 * choice(like one category for example)
 */
var AttributeFields = new Class({
    Implements: [Events],

    /**
     * Function: initialize
     * This function creates an instance of class AttributeFields
     *
     * Parameters:
     * category     {String} Category(One category and so on)
     * localizedCategory {String} Localized name of category
     * fields    {Array} possible fields
     */
    initialize: function(name, localizedName, fields){
        this.name = name;
        this.localizedName = localizedName;
        this.fields = fields;
        this.oneCategoryInfo = new Array();
    },

    /**
     * Function: setValue
     * It sets value to specified field
     *
     * Parameters:
     * fieldName         {String} name of the field
     * value             {String} value of the field
     */
    setValue: function(fieldName, value){
        for(var actualField = 0; actualField < this.fields.length; actualField++){
            if(fieldName == this.fields[actualField].getName()){
                this.fields[actualField].setValue(value);
                return;
            }
        }
    },

    /**
     * Function: getName
     * This is a simple getter returning value of Name
     *
     * Returns:
     * {String} name of this attributeField
     */
    getName: function(){
        return this.name;
    },

    /**
     * Function: getFieldNameValues
     * It returns as a String info about all fields and their actual values.
     *
     * Returns:
     * {String}  Info about fields
     */
    getFieldNameValues: function(){
        var text = "<span class='additionalInfo'>";
        var name = "";
        var value = "";
        for(var actualField = 0; actualField < this.fields.length; actualField++){
            name = this.fields[actualField].getName();
            value = this.fields[actualField].getValue();
            text += name+": "+value;
            if(actualField != this.fields.length-1){
                text += "<br>"
            }
        }
        text += "</span>";
        return text;
    },

    /**
     * Function: getLocalizedName
     * This is a simple getter returning value of getLocalizedName
     *
     * Returns:
     * {String} localized name of this attributeField
     */
    getLocalizedName: function(){
        return this.localizedName;
    },

    /**
     * Function: setOneCategoryInfo
     * This function loops over all fields and set one category info to them.
     *
     * Parameters:
     * oneCategoryInfo    {Array} possible choices for one category.
     */
    setOneCategoryInfo: function(oneCategoryInfo){
        for(var actualField = 0; actualField < this.fields.length; actualField++){
            this.fields[actualField].setOneCategoryInfo(oneCategoryInfo);
        }
    } ,

    /**
     * Function: display
     * This function creates an Element representing it.
     *
     * Returns:
     * {HTMLElement} fieldsDiv It is div representing all possible fields belonging to this AttributeFields
     */
    display: function(){
        var FIELD_DIV_ID = "fieldPartAW";
        var fieldsDiv = new Element('div',{
            id: FIELD_DIV_ID
        });
        var field;
        for(var actualField = 0; actualField < this.fields.length; actualField++){
            field = this.fields[actualField].display();
            field.inject(fieldsDiv);
        }
        return fieldsDiv;
    },

    /**
     * Function: save
     * It loops over all fields and make them save the data user filled in them.
     */
    save: function(){
        for(var actualField = 0; actualField < this.fields.length; actualField++){
            this.fields[actualField].save();
        }
    },

    /**
     * Function: toJSON
     * It returns JSON representation of object.
     *
     * Returns:
     * {JSONObject} JSON representation of object.
     */
    toJSON: function(){
        var jsonObject = new Array();
        for(var actualField = 0; actualField < this.fields.length; actualField++){
            jsonObject.push(this.fields[actualField].toJSON());
        }
        return jsonObject;
    }
});

/**
 * Class: InterestMeasure
 * It implements ARElement and adds function necessary to retrieve data about fields.
 */
var InterestMeasure = new Class({
    Implements: [ARElement, Events],

    /**
     * Function: initialize
     * It creates instance of class
     *
     * Parameters:
     * name     {String} name
     * nameLang    {String} nameLang
     * explanation {String} explanation of this specific Interest measure.
     * fields   {Array} fields
     */
    initialize: function(name, nameLang, explanation, fields){
        IM_TYPE = "oper";
        this.name = name;
        this.nameLang = nameLang;
        this.explanation = explanation;

        this.fields = fields;

        this.type = IM_TYPE;
        this.isBoolean = false;
        this.IM_CLASS = "prvek operator "+IM_TYPE;
    },

    /**
     * Function: display
     * Creates HTMLElement representing this interest measure.
     *
     * Returns:
     * {HTMLElement} element representing this InterestMeasure
     */
    display: function(shouldBeCreated){
        var utils = new UtilsAR();
        var textToDisplay = this.nameLang+"<br>"+this.getFieldNameValues();
        var interestMeasureElement = utils.createPrvek(this.IM_CLASS, textToDisplay);
        interestMeasureElement.element = this;
        interestMeasureElement.shouldBeCreated = shouldBeCreated;
        this.specificId = "im"+Counter.counter;
        interestMeasureElement.set('id',this.specificId);
        Counter.counter++;
        return interestMeasureElement;
    },

    /**
     * Function: getFieldNameValues
     * It returns as a String info about all fields and their actual values.
     *
     * Returns:
     * {String}  Info about fields
     */
    getFieldNameValues: function(){
        var text = "<span class='additionalInfo'>";
        var name = "";
        var value = "";
        for(var actualField = 0; actualField < this.fields.length; actualField++){
            name = this.fields[actualField].getName();
            value = this.fields[actualField].getValue();
            text += name+": "+value;
            if(actualField != this.fields.length-1){
                text += "<br>"
            }
        }
        text += "</span>";
        return text;
    },

    /**
     * Function: onClickMy
     * It is called when its clicked on this element.
     *
     * Params:
     * event {Event} event
     */
    onClickMy: function(event){
        if(AsociationRules.imThreshold == "prohibited"){
            return;
        }
        var askingWindow = this.displayAskingWindow();
        this.podklad.inject($('mainDiv'));
        askingWindow.inject($('mainDiv'));
    },

    /**
     * Function: displayAskingWindow
     * It creates and displays window, asking user for additional informations
     * about this InterestMeasure
     *
     * Returns:
     * {HTMLElement}  askingWindowDiv  Div which is created. I return it mainly for testing.
     */
    displayAskingWindow: function(){
        // Create asking window div
        this.podklad = new Element('div',{
            id: "podklad"
        });
        this.askingWindowDiv = new Element('div',{
            id: "askingWindow",
            'class': "askingWindow"
        });
        // Create topPart div
        var topPartDiv = new Element('div',{
            id: "topPartAW",
            'class': "topPartAW"
        });
        this.createTopDiv(topPartDiv);
        topPartDiv.inject(this.askingWindowDiv);
        // Create field part
        var FIELD_DIV_ID = "fieldPartAW";
        this.fieldDiv = new Element('div',{
            id: FIELD_DIV_ID
        });
        var field;
        for(var actualField = 0; actualField < this.fields.length; actualField++){
            field = this.fields[actualField].display();
            field.inject(this.fieldDiv);
        }
        this.fieldDiv.inject(this.askingWindowDiv)
        // Create button div
        var buttonDiv = new Element('div',{
            id: "buttonDivAW",
            'class': "buttonDivAW"
        });
        // Create button
        this.createButton(buttonDiv);
        buttonDiv.inject(this.askingWindowDiv);
        return this.askingWindowDiv;
    },

    /**
     * Function: recreateFields
     * It removes field part of asking window and creates it again with new
     * content.
     */
    recreateFields: function(){
        this.fieldDiv.dispose();
        this.fieldDiv = this.actualAttrField.display();
        this.fieldDiv.inject(this.askingWindowDiv)
    },

    /**
     * Function: createTopDiv
     * It creates top part of asking window. This part contains explanation
     * for this Interest Measure.
     *
     * Parameters:
     * topPart     {HTMLElement}  This is parts in which the topDiv should be created.
     */
    createTopDiv: function(topPart){
        var explanationDiv = new Element('div',{
            id: "topDivExplanationAW",
            'class': "topDivAW",
            html: this.explanation
        });
        explanationDiv.inject(topPart);
    },

    /**
     * Function: createButton
     * It creates save button and add event to it.
     *
     * Parameters:
     * buttonPart     {HTMLElement}  This is parts in which the button should be created.
     */
    createButton: function(buttonPart){
        var saveLang = "save";
        var button = new Element('input', {
            type: "button",
            id: "saveButton",
            'class': "buttonSave",
            value: saveLang
        });
        button.addEvent('click', function(event){
            this.save();
        }.bind(this));
        button.inject(buttonPart);
    },

    /**
     * Function: save
     * It loops over all fields and make them save the data user filled in them.
     */
    save: function(){
        for(var actualField = 0; actualField < this.fields.length; actualField++){
            this.fields[actualField].save();
            if(this.fields[actualField].getValue() == ""){
                if(AsociationRules.imThreshold == "required"){
                    var language = new LanguageSupport();
                    new HlaseniAbove(language.getName(language.INCORRECT_FIELD_VALUE, LanguageSupport.actualLang));
                    return;
                }
            }
        }
        this.askingWindowDiv.dispose();
        this.podklad.dispose();
        var textToDisplay = this.nameLang+"<br>"+this.getFieldNameValues();
        $(this.specificId).set('html',textToDisplay);
    //this.fireEvent("save");
    },

    /**
     * Function: toJSON
     * It returns JSON representation of object.
     *
     * Returns:
     * {JSONObject} JSON representation of object.
     */
    toJSON: function(){
        var jsonObject = new JSONObject();
        jsonObject.name = this.name;
        jsonObject.type = this.type;
        jsonObject.fields = new Array();
        for(var actualField = 0; actualField < this.fields.length; actualField++){
            jsonObject.fields.push(this.fields[actualField].toJSON());
        }
        return jsonObject;
    },

    /**
     * Function: setValue
     * It sets value to specified field
     *
     * Parameters:
     * fieldName         {String} name of the field
     * value             {String} value of the field
     */
    setValue: function(fieldName, value){
        for(var actualField = 0; actualField < this.fields.length; actualField++){
            if(this.fields[actualField].getName() == fieldName){
                this.fields[actualField].setValue(value);
            }
        }
    }
});