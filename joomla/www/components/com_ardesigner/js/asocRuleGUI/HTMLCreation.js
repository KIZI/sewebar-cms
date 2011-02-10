/**
 * Class: HTMLCreation
 * This class is responsible for creating necessary HTMLElements needed by the
 * application
 */
var HTMLCreation = new Class({
    /**
     * Function: initialize
     * It creates instance of class HTMLCreation
     *
     * Parameters:
     * mainClass     {AsociationRules} mainClass of the application
     */
    initialize: function(mainClass){
        this.asociationRules = mainClass;
        this.language = new LanguageSupport();
    },

    /**
     * Function: createDiv
     * It creates HTMLElement and returns it
     *
     * Parameters:
     * id           {String} Id
     *
     * Returns:
     * {HTMLElement} created HTMLElement
     */
    createDiv: function(id){
        return new Element('div',{
            id: id
        });
    },

    /**
     * Function: createDivClass
     * It creates HTMLElement and returns it
     *
     * Parameters:
     * clas     {String} class
     *
     * Returns:
     * {HTMLElement} created HTMLElement
     */
    createDivClas: function(clas){
        return new Element('div',{
            'class': clas
        });
    },

    /**
     * Function: createDivIdClas
     * It creates HTMLElement and returns it
     *
     * Parameters:
     * id           {String} id
     * clas        {String} class
     *
     * Returns:
     * {HTMLElement} created HTMLElement
     */
    createDivIdClas: function(id, clas){
        return new Element('div',{
            'class': clas,
            id: id
        });
    },

    /**
     * Function: createDivHtmlClass
     * It creates HTMLElement and returns it
     *
     * Parameters:
     * html     {String} html
     * clas        {String} class
     *
     * Returns:
     * {HTMLElement} created HTMLElement
     */
    createDivHtmlClas: function(html, clas){
        return new Element('div',{
            'class': clas,
            html: html
        });
    },

    /**
     * Function: createHtmlIdClick
     * It creates HTMLElement and returns it
     *
     * Parameters:
     * id     {String} id
     * html    {String} html
     * click           {String} click
     *
     * Returns:
     * {HTMLElement} created HTMLElement
     */
    createHtmlIdClick: function(id, html, click){
        return new Element('div',{
            id: id,
            html: html,
            onclick: click
        });
    },

    /**
     * Function: createAttributes
     * This function creates elements representing Attributes.
     *
     * Parameters:
     * COLS_ATTRIBUTES     {Number} How many columns should attributes have
     */
    createAttributes: function(COLS_ATTRIBUTES){
        var attr = this.asociationRules.getServerInfo().getAttributes();
        var clas = "prvek attribute attr";
        var type = "attr";
        var state = 0;
        var prvek;
        var column = 0;
        for(var i = 0; i < attr.length; i++){
            var html = attr[i].getName();
            var nameb = attr[i].getName();
            prvek = this.createPrvek(clas, type, html, state, nameb);
            prvek.inject($('attributes'+column));
            column++;
            if(column == COLS_ATTRIBUTES){
                column = 0;
            }
        }
    },

    /**
     * Function: createOperators
     * This function creates elements representing Operators.
     *
     * Parameters:
     * COLS_OPERATORS     {Number} How many columns should operators have
     */
    createOperators: function(COLS_OPERATORS){
        var oper = this.asociationRules.getServerInfo().getOperators();
        var clas = "prvek operator oper";
        var type = "oper";
        var state = 0;
        var prvek;
        var column = 0;
        for(var i = 0; i < oper.length; i++){
            var html = oper[i].getNameLang();
            var nameb = oper[i].getName();
            prvek = this.createPrvek(clas, type, html, state, nameb);
            prvek.inject($('operators'+column));
            column++;
            if(column == COLS_OPERATORS){
                column = 0;
            }
        }
    },

    /**
     * Function: createMainDivs
     * It creates main Divs("main","left","right") and the base structur of applications.
     *
     * Parameters:
     * idMainDiv     {String} Latitude bodu na mape(V-Z u nas cca 14-16)
     * COLS_OPERATOR    {String} Longitude bodu na mape(S-J u nas cca 49-51)
     * COLS_ATTRIBUTES           {String} Id pridavaneho bodu
     */
    createMainDivs: function(idMainDiv, COLS_OPERATOR, COLS_ATTRIBUTE){
        // real main and necessary structure
        var mainDiv = this.createDiv("main");
        var leftDiv = this.createDiv("left");
        var rightDiv = this.createDiv("right");
        var rightDivCreate = this.createDiv("rightDivPlace");
        var rightDivButton = this.createDiv("rightDivButton");
        var booleansDiv = this.createDivClas("booleans", "booleans");

        // places for booleans, operators and attributes
        var booleans = this.createDivIdClas("booleans", "booleansSmall");
        var attributes = this.createDivIdClas("attributes", "attributes");
        var operators = this.createDivIdClas("operators", "operators");

        //Headers of parts attributes, booleans and operators
        var booleansHeader = this.createDivHtmlClas(this.language.getName(this.language.CONNECTIVES, this.asociationRules.lang), "headersLeft");
        var operatorsHeader = this.createDivHtmlClas(this.language.getName(this.language.INTEREST_MEASURES, this.asociationRules.lang), "headersLeft");
        var attributesHeader = this.createDivHtmlClas(this.language.getName(this.language.FIELDS, this.asociationRules.lang), "headersLeft");

        // Adding another rule
        if(this.asociationRules.getServerInfo().getMoreRules() != "false"){
            var nextRule = this.createDivClas("newRule");
            var nextRuleInnerDiv = this.createHtmlIdClick("createRuleButton", this.language.getName(this.language.NEW_RULE, this.asociationRules.lang), "asocRule.createNewRule(this);");
        }

        var buttonPlaceDown = this.createDivIdClas("buttonPlaceDown", "buttonPlaceDown");
        
        // saving existing rules
        var save = this.createDivClas("saveWhole");
        var saveInnerDiv = this.createHtmlIdClick("createRuleButton", this.language.getName(this.language.SAVE, this.asociationRules.lang), "asocRule.save();")

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

        buttonPlaceDown.inject(leftDiv);

        if(this.asociationRules.getServerInfo().getMoreRules() != "false"){
            nextRule.inject(rightDivButton);
            nextRuleInnerDiv.inject(nextRule);
        }

        save.inject(buttonPlaceDown);
        saveInnerDiv.inject(save);

        
        var operatorSpecific;
        for(var i = 0; i < COLS_OPERATOR; i++){
            operatorSpecific = new Element('div', {
                id: "operators"+i,
                'class': "operatorsSmall"
            });
            operatorSpecific.inject(operators);
        }
        for(i = 0; i < COLS_ATTRIBUTE; i++){
            operatorSpecific = new Element('div', {
                id: "attributes"+i,
                'class': "attributesSmall"
            });
            operatorSpecific.inject(attributes);
        }
    },

    /**
     * Function: createARElement
     * It creates new Element that should be droppable on.
     *
     * Parameters:
     * classs     {String} class
     * html    {String} html
     * ruleNumber           {String} ruleNumebr
     * elementNumber    {String} elementNumber
     * type     {String} type
     *
     * Returns:
     * {HTMLElement} ARElement
     */
    createARElement: function(classs,html,ruleNumber, elementNumber, type){
        return new Element('div',{
            'class': classs,
            html: html,
            ruleposition: ruleNumber,
            elementposition: elementNumber,
            type: type
        })
    },

    /**
     * Function: createPrvek
     * It creates new Element that should be draggable.
     *
     * Parameters:
     * classs     {String} class
     * type           {String} type
     * html    {String} html
     * state    {String} state(0 or 1 and it it is necessary ofr snapping function)
     * nameb     {String} nameb(Basic name necessary for serialization)
     *
     * Returns:
     * {HTMLElement} ARElement
     */
    createPrvek: function(classs, type, html, state, nameb){
        return new Element('div', {
            'class': classs,
            type: type,
            html: html,
            state: state,
            nameb: nameb,
            correctplace: "no"
        });
    },

    /**
     * Function: createBaseStructure
     * Creates basic structure. At the end you have columns attributes, operators,
     * booleans and the booleans column is fullfilled. It also creates two buttons
     * Save and Next rule
     * 
     * Parameters:
     * idMainDiv     {String} The place where the whole thing should be posted.
     */
    createBaseStructure: function(idMainDiv, COLS_OPERATOR, COLS_ATTRIBUTE){
        this.createMainDivs(idMainDiv, COLS_OPERATOR, COLS_ATTRIBUTE);

        var boolean1 = this.createPrvek("boolean prvek and", "and", "AND", 0, "AND");
        var boolean2 = this.createPrvek("boolean prvek or", "or", "OR", 0, "OR");
        var boolean3 = this.createPrvek("boolean prvek neg", "neg", "NEG", 0, "NEG");
        var boolean4 = this.createPrvek("boolean prvek lbrac", "lbrac", "(", 0, "(");
        var boolean5 = this.createPrvek("boolean prvek rbrac", "rbrac", ")", 0, ")");
        
        var booleans = $('booleans');
        boolean1.inject(booleans);
        boolean2.inject(booleans);
        boolean3.inject(booleans);
        boolean4.inject(booleans);
        boolean5.inject(booleans);
        
    },

    /**
     * Function: createNewRule
     * It creates a new rule with one basic place for inserting element
     *
     * Parameters:
     * mainDiv     {HTMLElement} Div here the new RUle should be created.
     */
    createNewRule: function(mainDiv){
        //this.asocRules.push(new AsociationRule(this.test, this.depthNesting, this.dn, this));

        var newRuleDiv = new Element('div', {
            name: "assocRule",
            'class': 'assocRule',
            ruleposition: this.asociationRules.getRulesAmount()
        });
        var ruleDiv = newRuleDiv.inject($('rightDivPlace'));
        var amount = this.asociationRules.getRulesAmount()+1;
        var newNumberDiv = new Element('div', {
            'class': 'beginningNumber',
            html: amount+":"
        });

        newNumberDiv.inject(ruleDiv);
        newRuleDiv.inject(mainDiv);
        this.createNewPlace(this.asociationRules.getRulesAmount(), 1);

        this.asociationRules.setDraggability();
    },

    /**
     * Function: createNewPlace
     * It creates new place in existing rule.
     *
     * Parameters:
     * ruleNumber     {Numebr} which rule is this.
     * elementNumber    {Number} number of last element in the rule.
     */
    createNewPlace: function(ruleNumber, elementNumber){
        var newElementDiv = this.createARElement("ARElement","",ruleNumber, elementNumber, "free");
        $$(".assocRule").each(function(prvek){
            if(prvek.get("ruleposition") == ruleNumber){
                newElementDiv.inject(prvek);
            }
        }.bind(this));
    },
    
    /**
     * Function: createAskingWindow
     * It creates window where can user type additional informations about either
     * attributes or operators
     *
     * Parameters:
     * attr     {String} attr
     * names    {Array} names
     * nameLang           {Array} names in chosen language
     * minValues    {Array}  minimal values
     * maxValues     {Array} maximal values
     * datatypes {Array}  datatypes
     * options  {Array}  options
     * operName  {String} If it is operator then operName otherwise undefined
     * valuesb  {String} JSON Object(It represents either nothing or choices chosen last time)
     * attrName  {String} If it is Attribute then its name otherwise undefined
     */
    createAskingWindow: function(attr, names, nameLang, minValues, maxValues, datatypes, options, operName, valuesb, attrName, explanation){
        if(!$chk(nameLang)){
            nameLang = names;
        }
        this.tvorba = new TvorbaElementu();
        this.tvorba.vytvorMeziVrstvu();
        var mainDiv = $('main');
        var divAsking = new Element('div', {
            id: "askingWindow",
            'class': 'askingWindow'
        });
        divAsking.inject(mainDiv);

        if(attr){
            var select = this.createSelectAttributes(divAsking, options, attrName, valuesb);
        }
        else{
            var topDiv = this.createTopPart(divAsking, operName);
        }
        var info = new Element('div', {
            html: explanation,
            style: "border-bottom: solid 1px black"
        });
        info.inject(divAsking);

        var showFirst = JSON.decode(valuesb);
        if(showFirst != null && $chk(showFirst.attrType)){
            if(showFirst.attrType == "One category"){
                var attrFields = this.asociationRules.getAttr(attrName);
                var possibleFields = attrFields.getOneCategoryInfo();
                fields = this.createFieldsCategory(possibleFields, showFirst.field0);
                this.createFieldPart(divAsking, fields);
                this.createBottomPart(divAsking);

                return;
            }
        }
        var fields = this.createFields(names, nameLang, minValues, maxValues, datatypes, valuesb);
        var fieldDiv = this.createFieldPart(divAsking, fields);

        this.createBottomPart(divAsking);
    },

    /**
     * Function: createFields
     * It creates fields in asking window.
     *
     * Parameters:
     * names    {Array} names
     * nameLang           {Array} names in chosen language
     * minValues    {Array}  minimal values
     * maxValues     {Array} maximal values
     * datatypes {Array}  datatypes
     * valuesb  {String} JSON Object(It represents either nothing or choices chosen last time)
     *
     * Returns:
     * {Array} fields
     */
    createFields: function(names, nameLang, minValues, maxValues, datatypes, valuesb){
        if(!$chk(nameLang)){
            nameLang = names;
        }
        var valuesPrevious = JSON.decode(valuesb);
        var valueS = "";
        var fields = new Array();
        var field, fieldName, fieldInput;
        for(var i = 0; i < names.length; i++){
            field = new Element('div',{
                id: "field"+i,
                name: "field",
                'class': "field"
            })
            valueS = "";
            if(valuesb != undefined){
                for(var k = 0; k < valuesPrevious.fields; k++){
                    if(valuesPrevious["field"+k][0] == nameLang[i]){
                        valueS = valuesPrevious["field"+k][1]
                    }
                }
            }
            fieldName = new Element('div',{
                id: "fieldname"+i,
                name: "fieldname",
                'class': "fieldname",
                html: nameLang[i],
                nameb: names[i]
            })
            fieldInput = new Element('input', {
                id: "fieldinput"+i,
                name: "fieldinput",
                'class': "fieldinput",
                value: valueS
            })
            fieldInput.datatype = datatypes[i];
            fieldInput.minValue = minValues[i];
            fieldInput.maxValue = maxValues[i];
            fieldInput.addEvent('change', function(){
                var contr = new Control();
                if(!contr.control(this.datatype, this.minValue, this.maxValue, this.get('value'))){
                    this.value = "";
                //Možná by zde mìlo pøibýt nìjaké hlášení.
                }
            })
            
            fieldName.inject(field);
            fieldInput.inject(field);
            fields.push(field);
        }
        return fields;
    },

    /**
     * Function: createTopCategory
     * Creates top part in case of One Category
     *
     * Parameters:
     * mainDiv     {String} Div where it should be created
     * html    {String} html
     *
     * Returns:
     * {HTMLElement} topDiv
     */
    createTopPart: function(mainDiv, html){
        var topDiv = new Element('div',{
            id: "infoAskingChoices",
            'class': "topDivAsking",
            html: html
        })
        topDiv.inject(mainDiv);
        return topDiv;
    },

    /**
     * Function: createFieldsCategory
     * Creates fields in case of One Category
     *
     * Parameters:
     * fields     {String} fields
     * choice    {String} choice
     *
     * Returns:
     * {Array} array of one field
     */
    createFieldsCategory: function(fields, choice){
        var array = new Array();
        var field = new Element('div',{
            id: "field"+i,
            name: "field",
            'class': "field"
        })
        var fieldName = new Element('select',{
            id: "fieldinput0",
            name: "fieldinput",
            'class': "fieldinput"
        })
        for(var i = 0; i < fields.length; i++){
            var option = new Element('option',{
                html: fields[i]
            })
            option.inject(fieldName);
        }
        fieldName.set("value", choice);
        fieldName.inject(field);
        array.push(field);
        return array;
    },

    /**
     * Function: createSelectAttributes
     * It creates Select in case of Attributes with chosen option as first
     *
     * Parameters:
     * mainDiv     {HTMLElement} div to be injected in
     * options    {Array} options
     * attrName           {String} name of attribuet
     *
     * Returns:
     * {HTMLElement} finalSelect
     */
    createSelectAttributes: function(mainDiv, options, attrName, valuesb){
        var valuesPrevious = JSON.decode(valuesb);
        var select = new Element('select', {
            id: "selectAttr",
            'class': "selectAsking"
        })
        select.attribute = attrName;
        var attrFields;
        select.addEvent('change', function(){
            $$(".field").each(function(element){
                element.dispose();
            })
            $('downDiv').dispose();
            var create = new HTMLCreation();
            var fields;
            var divAsking = $('askingWindow');
            if(this.options[this.selectedIndex].get('html') == "One category"){
                attrFields = asocRule.getAttr(this.attribute);
                fields = create.createFieldsCategory(attrFields.getOneCategoryInfo(), this.options[this.selectedIndex].get('html'));
                create.createFieldPart(divAsking, fields);
                create.createBottomPart(divAsking);
            }
            else{
                attrFields = asocRule.getAttrFields(this.options[this.selectedIndex].get('html'));
                var names = attrFields.getfName();
                var nameLang = attrFields.getfNameLang();
                var minValues = attrFields.getMinValue();
                var maxValues = attrFields.getMaxValue();
                var datatypes = attrFields.getDatatype();
                fields = create.createFields(names, nameLang, minValues, maxValues, datatypes, undefined);
                create.createFieldPart(divAsking, fields);
                create.createBottomPart(divAsking);
            }
        });
        var option;
        if(valuesPrevious != null && $chk(valuesPrevious.attrType)){
            option = new Element('option', {
                html: valuesPrevious.attrType
            })
            option.inject(select);
        }
        for(var i = 0; i < options.length; i++){
            option = new Element('option', {
                html: options[i]
            })
            option.inject(select);
        }
        select.inject(mainDiv);
        return select;
    },

    /**
     * Function: createFieldPart
     * It creates field part in other cases than One Category
     *
     * Parameters:
     * mainDiv     {HTMLElement} Div to be injected in
     * fields    {Array} fields
     *
     * Returns:
     * {HTMLElement} field Div
     */
    createFieldPart: function(mainDiv, fields){
        var fieldDiv = new Element('div',{
            id: "fieldDiv",
            'class': "fieldDivAsking"
        })
        fieldDiv.inject(mainDiv);
        for(var i = 0; i < fields.length; i++){
            fields[i].inject(fieldDiv);
        }
        return fieldDiv;
    },

    /**
     * Function: createBottomPart
     * It creates bottom part of asking window.
     *
     * Parameters:
     * mainDiv     {HTMLElement} Div to be injected in
     */
    createBottomPart: function(mainDiv){
        var downDiv = new Element('div',{
            id: "downDiv",
            'class': "fieldDivAsking"
        });
        downDiv.inject(mainDiv);
        var saveButton = new Element('input', {
            id: "saveButton",
            type: "button",
            value: "Save"
        })
        saveButton.addEvent('click', function(){
            asocRule.saveAskingWindow();
            $('askingWindow').dispose();
            $('podklad').dispose();
        })
        saveButton.inject(downDiv);
    }
})


