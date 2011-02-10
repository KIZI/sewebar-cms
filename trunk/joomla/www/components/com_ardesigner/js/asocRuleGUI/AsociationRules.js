/**
 * Class: AsociationRules
 * This is the main class of the Application AsociationRulesGUI. This class creates the
 * structure and manage communication between the other parts.
 */
var AsociationRules = new Class({
    Implements: Events,

    CATEGORY : "category",
    COLS_ATTRIBUTE: 4,
    COLS_OPERATOR: 2,

    /**
     * Function: initialize
     * This function creates instance of this class. It sets lenguage of application
     * and locations of necessary resources on the server. 
     *
     * Parameters:
     * lang         {String} Language of the application
     * urlGet       {String} Url where the app gets Info
     * urlSet       {String} Url where the app serializes Info
     */
    initialize: function(lang, urlGet, urlSet){
        this.asociationRules = new Array();
        this.drag = new Dragability(this, ".ARElement",".prvek");
        this.createHTML = new HTMLCreation(this);
        this.serverInfo = new ServerInfo();
        this.booleans = new Array();
        this.urlSet = urlSet;
        this.lang = lang;
        this.language = new LanguageSupport();

        this.getInfo(urlGet);
    },

    /**
     * Function: getServerInfo
     * getter of property serverInfo
     *
     * Returns:
     * {ServerInfo} return this.serverInfo
     */
    getServerInfo: function(){
        return this.serverInfo;
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
     * Function: create
     * This function ensures creating of the application(Mainly the part which user see).
     */
    create: function(){
        this.createHTML.createBaseStructure($('mainDiv'),this.COLS_OPERATOR, this.COLS_ATTRIBUTE);
        this.createHTML.createAttributes();
        this.createHTML.createOperators();
    },

    /**
     * Function: getRulesAmount
     * This function return length of this.asociationRules array which keeps all
     * existing rules.
     */
    getRulesAmount: function(){
        return this.asociationRules.length;
    },

    /**
     * Function: initArrayElements
     * This function initializes array of elements. It is used by function canAdd
     * and it prepares array of possible lements to add from which the correct element
     * is chosen.
     *
     * Returns:
     * {Array} Array of elements ith apropriate functions.
     */
    initArrayElements: function(){
        var arrayElements = new Array();
        arrayElements.push("attr");
        arrayElements.push(this.createAttributeEl);
        arrayElements.push("oper");
        arrayElements.push(this.createOperatorEl);
        arrayElements.push("and");
        arrayElements.push(this.createBoolEl);
        arrayElements.push("or");
        arrayElements.push(this.createBoolEl);
        arrayElements.push("neg");
        arrayElements.push(this.createBoolEl);
        arrayElements.push("lbrac");
        arrayElements.push(this.createBoolEl);
        arrayElements.push("rbrac");
        arrayElements.push(this.createBoolEl);
        return arrayElements;
    },

    /**
     * Function: createAttributeEl
     * It finds Attribute which name is element.
     *
     * Parameters:
     * element     {String} Name of attribute to be chosen.
     *
     * Returns:
     * {Attribute} null or Attribute with right name.
     */
    createAttributeEl: function(element){
        var attrs = this.getServerInfo().getAttributes();
        for(var i = 0; i < attrs.length; i++){
            if(attrs[i].getName() == element.get("nameb")){
                return attrs[i];
            }
        }
        return null;
    },

    /**
     * Function: createOperatorEl
     * It finds operator which belongs to the name element.
     *
     * Parameters:
     * element     {String} Name of operator(Interest Measure)
     *
     * Returns:
     * {Operator} null or attribute belonging to the name element
     */
    createOperatorEl: function(element){
        var opers = this.getServerInfo().getOperators();
        for(var i = 0; i < opers.length; i++){
            if(opers[i].getName() == element.get("nameb")){
                return opers[i];
            }
        }
        return null;
    },

    /**
     * Function: createBoolEl
     * It gets BooleanCl belonging to the name element
     *
     * Parameters:
     * element     {String} name of the boolean.
     *
     * Returns:
     * {BooleanCl} null or BooleanCl belonging to the name element.
     */
    createBoolEl: function(element){
        for(var i = 0; i < this.booleans.length; i++){
            if(this.booleans[i].getName() == element.get("nameb")){
                return this.booleans[i];
            }
        }
        return null;
    },

    /**
     * Function: canAdd
     * This function solves whether attribute can be add to the rule.
     *
     * Parameters:
     * element   {HTMLElement} This is the element user wants to drop
     * dropedOn  {HTMLElement} Element on which is the element beng dropped.
     *
     * Returns:
     * {boolean} true if user can add the element otherwise false.
     */
    canAdd: function(element, dropedOn){
        var arrayElements = this.initArrayElements();
        var element2;
        if(dropedOn.get("type") == "free"){
            element2 = this.createElementGeneral(arrayElements, element);
            if(this.asociationRules[dropedOn.get('ruleposition')].insertItem(element2)){
                element.set("ruleposition", dropedOn.get("ruleposition"));
                element.set("elementposition", dropedOn.get("elementposition"));
                return true;
            }
        }
        else{
            element2 = this.createElementGeneral(arrayElements, element);
            if(this.asociationRules[dropedOn.get('ruleposition')].changeItem(element2, dropedOn.get('elementposition'))){
                element.set("ruleposition", dropedOn.get("ruleposition"));
                element.set("elementposition", dropedOn.get("elementposition"));
                return true;
            }
        }
        return false;
    },

    /**
     * Function: createElementGeneral
     * This function gets Element eblonging to the element. The Element can be BooleanCl,
     * Operator or Attribute.
     *
     * Parameters:
     * arrayElement     {Array} Array of the possible elements.
     * element          {HTMLElement} element to which should the Element belong.
     *
     * Returns:
     * {Element} Element belonging to the element.
     */
    createElementGeneral: function(arrayElements, element){
        var element2;
        for(var i = 0; i < arrayElements.length - 1 ; i += 2){
            if(arrayElements[i] == element.get("type")){
                element2 = arrayElements[i+1].bind(this)(element);
            }
        }
        return element2;
    },

    /**
     * Function: getInfo
     * This function gets Data and Configuration from server and solve the JSON
     * it gets.
     *
     * Parameters:
     * url     {String} url on the web where the app gets info.
     */
    getInfo: function(url){
        new Request.JSON({
            url: url,
            onComplete: function(item){
                this.lang = item.lang;

                this.serverInfo.solveRules(item);

                this.initializeBooleans();
                this.create();
                this.setDraggability();
                this.actualElement = null;

                this.serverInfo.solveOperators(item);
                this.serverInfo.solveAttributes(item);
                this.serverInfo.solveSupIM(item);
                this.serverInfo.solveMinMax(item);
                this.serverInfo.solveDepth(item);
                this.serverInfo.solveRules(item);

                this.createHTML.createAttributes(this.COLS_ATTRIBUTE);
                this.createHTML.createOperators(this.COLS_OPERATOR);

                if(this.serverInfo.getMoreRules() == "false"){
                    this.createNewRule();
                }

            }.bind(this)
        }).get();
    },

    /**
     * Function: removeItem
     * This function tries to remove element from the rule.
     *
     * Parameters:
     * element     {HTMLElement} the element which should be removed.
     */
    removeItem: function(element){
        var ruleNumber = element.get("ruleposition");
        var elementNumber = element.get("elementposition");
        this.asociationRules[ruleNumber].removeItem(elementNumber);
    },

    /**
     * Function: setDraggability
     * This function sets draggability of elements which should be dragged.
     */
    setDraggability: function(){
        this.drag.removeDragability();
        this.drag.createDragability();
    },

    /**
     * Function: createAskingWindow
     * This function creates windwo asking user for additional data by operators
     * and attributes.
     */
    createAskingWindow: function(attr, names, nameLang, minValues, maxValues, datatypes, options, operName, valuesb, attrName, explanation){
        this.createHTML.createAskingWindow(attr, names, nameLang, minValues, maxValues, datatypes, options, operName, valuesb, attrName, explanation);
    },

    /**
     * Function: solveAskingWindow
     * This solves what should be in the windows which asks user for additional info.
     *
     * Parameters:
     * element  {HTMLElement} element which asks for additional info.
     */
    solveAskingWindow: function(element){
        this.actualElement = element;
        var name = element.get("nameb");
        var attrs = this.serverInfo.getAttributes();
        var attrFields = this.serverInfo.getAttributesFields();
        var opers = this.serverInfo.getOperators();
        var attr = false;
        var names, minValues, maxValues, datatypes, operName, nameLang;
        var options = new Array();
        var explanation = null;
        if(element.get("type") == "attr"){
            attr = true;
            operName = null;
            names = attrFields[0].getfName();
            nameLang = attrFields[0].getfNameLang();
            minValues = attrFields[0].getMinValue();
            maxValues = attrFields[0].getMaxValue();
            datatypes = attrFields[0].getDatatype();
            for(var j = 0; j < attrFields.length; j++){
                options.push(attrFields[j].getCategory());
            }
            explanation = attrFields[0].getExplanation();
        }
        else if(element.get("type") == "oper"){
            for(var i = 0; i < opers.length; i++){
                if(opers[i].getName() == name){
                    operName = opers[i].getNameLang();
                    options = null;
                    names = opers[i].getFieldNames();
                    nameLang = opers[i].getFieldLangs();
                    minValues = opers[i].getFieldMinValues();
                    maxValues = opers[i].getFieldMaxValues();
                    datatypes = opers[i].getFieldDatatype();
                    explanation = opers[i].getExplanation();
                }
            }
        }
        else{
            return;
        }
        this.createAskingWindow(attr, names, nameLang, minValues, maxValues, datatypes, options, operName, element.get("valuesb"), name, explanation);
    },

    /**
     * Function: getOper
     * This function gets Operator belonging to the name from data the app got
     * from server.
     *
     * Parameters:
     * name  {String} name of the attribute
     *
     * Returns:
     * {Operator} null or Operator belonging to the name.
     */
    getOper: function(name){
        var opers = this.serverInfo.getOperators();
        for(var i = 0; i < opers.length; i++){
            if(opers[i].getName() == name){
                return opers[i];
            }
        }
        return null;
    },

    /**
     * Function: getAttr
     * This function gets Attribute belongin to the nama from data the app got
     * from server
     *
     * Parameters:
     * name  {String} name of the operator
     *
     * Returns:
     * {Attribute} null or Attribute belonging to the name.
     */
    getAttr: function(name){
        var attrs = this.serverInfo.getAttributes();
        for(var i = 0; i < attrs.length; i++){
            if(attrs[i].getName() == name){
                return attrs[i];
            }
        }
        return null;
    },

    /**
     * Function: getAttrFields
     * Get fields for solveAskingWindow belongin to the attribute with name.
     *
     * Parameters:
     * name     {String} name of the attribute
     *
     * Returns:
     * {AttributeFields} null or AttributeFields belonging to the name
     */
    getAttrFields: function(name){
        var attrField = this.serverInfo.getAttributesFields();
        for(var i = 0; i < attrField.length; i++){
            if(attrField[i].getCategory() == name){
                return attrField[i];
            }
        }
        return null;
    },

    /**
     * Function: solveSaveMinValues
     * This function solves whether the rules contains minimal amount of elements
     * in antecedent, consequent and as operators(interest measures)
     *
     * Parameters:
     * prvek     {HTMLElement} This element contains elements creating AsociationRule.
     *
     * Returns:
     * {Boolean} true if it is ok otherwise false.
     */
    solveSaveMinValues: function(prvek){
        var antBBA = this.asociationRules[prvek.get("ruleposition")].countAttrsAnt();
        var consBBA = this.asociationRules[prvek.get("ruleposition")].countAttrsCon();
        var IMBBA = this.asociationRules[prvek.get("ruleposition")].countOpers();
        var minVal = ["IM", "ant", "cons", "general"];
        var valuesa = [IMBBA, antBBA, consBBA, IMBBA+antBBA+consBBA];
        for(var i = 0; i < minVal.length; i++){
            if(valuesa[i] < this.getServerInfo().getMinValues(minVal[i])){
                new Hlaseni(this.language.getName(this.language.NEED_MORE_ELEMENTS, this.lang));
                return false;
            }
        }
        return true;
    },

    /**
     * Function: solveFieldsSave
     * This function takes additional values to attribute and operator and returns
     * them.
     *
     * Parameters:
     * element  {HTMLElement} Element from which it takes the field values.
     *
     * Returns:
     * {String} Empty String if there are no additional infos otherwise String
     *          containing field values.
     */
    solveFieldsSave: function(element){
        var nameValues = new Array();
        var supNames = "";  //Attr names
        var supValues = "";
        if(element != null){
            for(var i = 0; i < element.fields; i++){
                if(element["field"+i][1] == undefined){
                    element["field"+i][1] = "";
                }
                supNames += element["field"+i][0] + ";";
                supValues += element["field"+i][1]  + ";";
            }
            supNames = supNames.substring(0, supNames.length - 1);
            supValues = supValues.substring(0, supValues.length - 1);
            nameValues[0] = supNames;
            nameValues[1] = supValues;
            nameValues[2] = element.attrType;
            return nameValues;
        }
        else{
            return nameValues;
        }
    },

    /**
     * Function: save
     * This function solves JSON that should be sent to the server.
     */
    save: function(){
        this.i = 1;
        this.objectForServer = new JSONHelp();
        this.haveEnoughValues = false;
        var jsonEl;
        
        $$('.assocRule').each(function(prvek){
            if(this.solveSaveMinValues(prvek)){
                this.haveEnoughValues = true
            }
            this.objectForServer["rule"+this.i] = new Array();
            this.jSup = 0;
            prvek.getElements(".prvek").each(function(element){
                jsonEl = JSON.decode(element.get('valuesb'));
                var typ = element.get('type');
                if(typ == "and" || typ == "or"){
                    typ = "bool";
                }
                this.objectForServer["rule"+this.i][this.jSup * 5] = typ;
                this.objectForServer["rule"+this.i][this.jSup * 5 +1] = element.get('nameb');
                var fieldsArray = this.solveFieldsSave(jsonEl);
                if(fieldsArray.length == 0){
                    fieldsArray[0] = "";
                    fieldsArray[1] = "";
                    fieldsArray[2] = "";
                }
                this.objectForServer["rule"+this.i][this.jSup * 5 +2] = fieldsArray[0];
                this.objectForServer["rule"+this.i][this.jSup * 5 +3] = fieldsArray[1];
                this.objectForServer["rule"+this.i][this.jSup * 5 +4] = fieldsArray[2];
                this.jSup++;
            }.bind(this));
            this.i++;
        }.bind(this));

        this.objectForServer.rules = this.i - 1;
        var result = JSON.encode(this.objectForServer);
        //console.log(result);
        if(this.haveEnoughValues){
            $$('.assocRule').each(function(prvek){
                prvek.dispose();
            });
            this.asociationRules = new Array();
            this.saveServer(JSON.encode(this.objectForServer));
        }
    },

    /**
     * Function: saveServer
     * This function is called by save() and it actually sends the data on the
     * server in variable data.
     *
     * Parameters:
     * which  {String} Data that should be sent to the server.
     */
    saveServer: function(which){
        new Request({
            url: this.urlSet,
            onComplete: function(item){
                var hlaseni = new Hlaseni(this.language.getName(this.language.EVERYTHING_OK, this.lang));
                hlaseni.addEvent('closehlaseni', function() {
                    this.fireEvent('saved', item);
                }.bind(this));
            }.bind(this)
        }).post({
            'data': which
        });
    },

    /**
     * Function: createNewRule
     * This function creates new rule and position for it.
     */
    createNewRule: function(element){
        this.createHTML.createNewRule($('rightDivPlace'));
        this.asociationRules.push(new AsociationRule(this));
    },

    /**
     * Function: saveAskingWindow
     * This function saves data from window asking user for additional informations.
     */
    saveAskingWindow: function(){
        //get AllFields and somehow serialize them.
        var ONE_CATEGORY = "One category";
        var jsonStart = new JSONHelp();
        var els;
        var fields = 0;

        $$(".field").each(function(prvek){
            this.helpArray = new Array();
            els = prvek.getElements(".fieldname").each(function(prvek){
                this.helpArray.push(prvek.get('html'));
            }.bind(this));
            els = prvek.getElements(".fieldinput").each(function(prvek){
                this.helpArray.push(prvek.get('value'));
            }.bind(this));
            jsonStart["field"+fields] = this.helpArray;
            fields++;
        }.bind(this))
        jsonStart.fields = fields;
        var originalName = this.actualElement.nameLang;
        var attrType = "";
        var selectAttr = $('selectAttr')
        if(selectAttr != null){
            attrType = selectAttr.options[selectAttr.selectedIndex].get('html');
        }
        jsonStart.attrType = attrType;
        var fieldsToShow = "";
        for(var i = 0; i < fields; i++){
            if(attrType == ONE_CATEGORY){
                fieldsToShow += jsonStart["field"+i] + "<br>";
            }
            else{
                fieldsToShow += jsonStart["field"+i][0] + "-" + jsonStart["field"+i][1] + "<br>";
            }
        }
        fieldsToShow = fieldsToShow.substr(0, fieldsToShow.length - 1);
        this.actualElement.set('html', originalName + "<br><i>" + fieldsToShow + "</i>");
        
        this.actualElement.set("valuesb", JSON.encode(jsonStart));
        this.actualElement.setStyle("height", "auto");

        // Solve the size of ARElement based on the biggest size.
        var maxSize = 0;
        $$(".ARElement").each(function(el){
            var elDescendant = el.getElement("div");
            if(elDescendant != null){
                var actualSize = elDescendant.getSize().y;
                if(actualSize > maxSize){
                    maxSize = actualSize;
                }
            }
        });

        $$(".ARElement").each(function(el){
            var elDescendant = el.getElement("div");
            if(elDescendant != null){
                elDescendant.setStyle("height",maxSize);
            }
            var sizeBigger = maxSize + 5;
            el.setStyle("height",sizeBigger);
        });
    }
});

/**
 * Class: JSONHelp
 * This is supportive class for serialization into JSON. Basically it does nothing.
 */
var JSONHelp = new Class({

    });


