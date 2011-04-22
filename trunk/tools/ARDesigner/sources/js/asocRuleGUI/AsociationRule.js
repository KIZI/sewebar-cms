/**
 * Class: AsociationRule
 * This class represents one Asociation Rule used in AsocRule GUI Application.
 * The rule is created by user during his work with the application. This class
 * enables adding elements, changing elements, removing elements and solving
 * validity of the rule.
 */
var AsociationRule = new Class({
    Implements: [Events],

    /**
     * Function: initialize
     * creates instance of class AsociationRule. As param it takes the main class
     * of application(AsociationRules).
     *
     * Parameters:
     * mainClass  {AsocationRules} Main Class of application.
     *
     * Returns:
     * {Void} Nothing
     */
    initialize: function(serverInfo){
        this.elements = new Array(); // It stores Attribute, InterestMeasure or Boolean
        this.elementsDisplayed = new Array();
        this.serverInfo = serverInfo;
        this.allowedCombinations = new Array();
        this.initAllowedCom();
        this.maxSize = 25;
        this.ruleDiv = new Element('div', {
            name: "ruleDiv",
            'class': "rule"
        });
    },

    /**
     * Function: setMaxSize
     * Simple setter for maxSize
     *
     * Parameters:
     * size  {Number} Maximal size of used element
     */
    setMaxSize: function(size){
        this.maxSize = size;
    },

    /**
     * Function: countElements
     * It counts how many elements are in the rule at the moment.
     *
     * Returns:
     * {Number} Amount of elements in the rule
     */
    countElements: function(){
        return this.elements.length;
    },

    /**
     * Function: solveAmounts
     * This function decides whether there are more elements in Antecedent,
     * Consequent or as a part of interest measure than allowed. How many elements
     * are allowed depends on the configuration XML on the server.
     *
     * Returns:
     * {Boolean} true If everything is ok(less or equal amount to allowed)
     */
    solveAmounts: function(){
        var antAttrs = this.countAttrsAnt();
        var conAttrs = this.countAttrsCon();
        var IMAttrs = this.countOpers();
        var arraySup = ["cons", "ant", "IM", "general"];
        var array2Sup = [conAttrs, antAttrs, IMAttrs, antAttrs+conAttrs];
        // This one solves max values
        for(var i = 0; i < arraySup.length; i++){
            if(this.serverInfo.getMaxValues(arraySup[i]) < array2Sup[i]){
                return false;
            }
        }
        // This one solves min values
        if(IMAttrs > 0){
            if(this.serverInfo.getMinValues("ant") > antAttrs){
                return false;
            }
        }
        if(conAttrs > 0){
            if(this.serverInfo.getMinValues("IM") > IMAttrs){
                return false;
            }
        }
        return true;
    },

    /**
     * Function: solveAmountsMin
     * This function decides whether there are enough attributes in antecedent,
     * IM and consequent.
     *
     * Returns:
     * {Boolean} true If everything is ok(less or equal amount to allowed)
     */
    solveAmountsMin: function(){
        var antAttrs = this.countAttrsAnt();
        var conAttrs = this.countAttrsCon();
        var IMAttrs = this.countOpers();
        var arraySup = ["cons", "ant", "IM", "general"];
        var array2Sup = [conAttrs, antAttrs, IMAttrs, antAttrs+conAttrs+IMAttrs];
        // This one solves min values
        for(var i = 0; i < arraySup.length; i++){
            if(this.serverInfo.getMinValues(arraySup[i]) > array2Sup[i]){
                return false;
            }
        }
        return true;
    },

    /**
     * Function: solveSupportedInterestMeasures
     * This function decides whether the combination of used interest measures is allowed
     * based on the configuration file on the server.
     *
     * Returns:
     * {Boolean} True if the combination is allowed.
     */
    solveSupportedInterestMeasures: function(){
        var existingOperators = this.getExistingInterestMeasures();
        if(existingOperators.length == 0){
            return true;
        }
        var supIMCom = this.serverInfo.getSupportedIMCombinations();
        var ok = 0;
        for(var l = 0; l < existingOperators.length; l++){
            for(var m = (l+1); m < existingOperators.length; m++){
                if(existingOperators[l] == existingOperators[m]){
                    return false;
                }
            }
        }
        if(supIMCom.length == 0){
            return true;
        }
        /*
        if(existingOperators.length == 1){
            return true;
        } */
        for(var j = 0; j < supIMCom.length; j++){
            ok = 0;
            for(var i = 0; i < existingOperators.length; i++){
                for(var k = 0; k < supIMCom[j].length; k++){
                    if(supIMCom[j][k] == existingOperators[i]){
                        ok++;
                    }
                }
            }
            if(ok == existingOperators.length){
                if(this.countAttrsBoolCon() != 0){
                    if(ok == supIMCom[j].length){
                        return true;
                    }
                }
                else{
                    return true;
                }
            }
        }
        return false;
    },

    /**
     * Function: solveSupportedInterestMeasures
     * This function decides whether the combination of used interest measures is allowed
     * based on the configuration file on the server.
     *
     * Returns:
     * {Boolean} True if the combination is allowed.
     */
    solveSupportedInterestMeasuresFinal: function(){
        var existingOperators = this.getExistingInterestMeasures();
        var supIMCom = this.serverInfo.getSupportedIMCombinations();
        var ok = 0;
        for(var l = 0; l < existingOperators.length; l++){
            for(var m = (l+1); m < existingOperators.length; m++){
                if(existingOperators[l] == existingOperators[m]){
                    return false;
                }
            }
        }
        if(supIMCom.length == 0){
            return true;
        }
        else{
            if(existingOperators.length == 0){
                return false;
            }
        }
        for(var j = 0; j < supIMCom.length; j++){
            ok = 0;
            for(var i = 0; i < existingOperators.length; i++){
                for(var k = 0; k < supIMCom[j].length; k++){
                    if(supIMCom[j][k] == existingOperators[i]){
                        ok++;
                    }
                }
            }
            if(ok == supIMCom[j].length){
                if(this.countAttrsBoolCon() != 0){
                    if(ok == supIMCom[j].length){
                        return true;
                    }
                }
                else{
                    return true;
                }
            }
        }
        return false;
    },

    /**
     * Function: getExistingInterestMeasures
     * Private function used to get operators which are already used in the rule.
     *
     * Returns:
     * {Array} Array of existing operators
     */
    getExistingInterestMeasures: function(){
        var operators = new Array();
        for(var i = 0; i < this.elements.length; i++){
            if(this.elements[i] == null){
                continue;
            }
            if(this.elements[i].getType() == "oper"){
                operators.push(this.elements[i].getName());
            }
        }
        return operators;
    },

    /**
     * Function: countAttrsAnt
     * This function count amount of attributes in antecedent. It is basically private
     *
     * Returns:
     * {int} Amount of attributes in Antecedent.
     */
    countAttrsAnt: function(){
        var attrCount = 0;
        for(var i = 0; i < this.elements.length; i++){
            if(this.elements[i] == null){
                continue;
            }
            if(this.elements[i].getType() == "attr"){
                attrCount++;
            }
            if(this.elements[i].getType() == "oper"){
                break;
            }
        }
        return attrCount;
    },

    /**
     * Function: countAttrsCon
     * This function counts attributes in consequent.
     *
     * Returns:
     * {int} Amount of attributes in consequent.
     */
    countAttrsCon: function(){
        var attrCount = 0;
        var isOperator = false;
        for(var i = 0; i < this.elements.length; i++){
            if(this.elements[i] == null){
                continue;
            }
            if(this.elements[i].getType() == "attr"){
                if(isOperator){
                    attrCount++;
                }
            }
            if(this.elements[i].getType() == "oper"){
                attrCount = 0;
                isOperator = true;
            }
        }
        return attrCount;
    },

    /**
     * Function: countAttrsBoolCon
     * This function counts attributes in consequent.
     *
     * Returns:
     * {int} Amount of attributes in consequent.
     */
    countAttrsBoolCon: function(){
        var attrCount = 0;
        var isOperator = false;
        for(var i = 0; i < this.elements.length; i++){
            if(this.elements[i] == null){
                continue;
            }
            if(this.elements[i].getType() != "oper"){
                if(isOperator){
                    attrCount++;
                }
            }
            if(this.elements[i].getType() == "oper"){
                attrCount = 0;
                isOperator = true;
            }
        }
        return attrCount;
    },

    /**
     * Function: countOpers
     * This function dounts operators in the rule.
     *
     * Returns:
     * {int} Amount of operators in the rule.
     */
    countOpers: function(position){
        var attrCount = 0;
        for(var i = 0; i < this.elements.length; i++){
            if(i-1 == position){
                break;
            }
            if(this.elements[i] == null){
                continue;
            }
            if(this.elements[i].getType() == "oper"){
                attrCount++;
            }
        }
        return attrCount;
    },

    /**
     * Function: initAllowedCom
     * One of the most important function. It initializes which combinations of types
     * are allowed and possibly what other function should be called to justify that.
     */
    initAllowedCom: function(){
        var supArray = this.initAddAttr();
        this.allowedCombinations.push(supArray);
        supArray = this.initAddOper();
        this.allowedCombinations.push(supArray);
        supArray = this.initAddAnd();
        this.allowedCombinations.push(supArray);
        supArray = this.initAddOr();
        this.allowedCombinations.push(supArray);
        supArray = this.initAddNeg();
        this.allowedCombinations.push(supArray);
        supArray = this.initAddLbrac();
        this.allowedCombinations.push(supArray);
        supArray = this.initAddRbrac();
        this.allowedCombinations.push(supArray);
    },

    /**
     * Function: initAddRbrac
     * Initialize what can follow right bracket.
     *
     * Returns:
     * {Array} What type is it, what can follows and additional functions to suply.
     */
    initAddRbrac: function(){
        var supArray = new Array();
        supArray.push("rbrac");
        supArray.push("rbrac");
        supArray.push(this.solveAttrRBrac);
        supArray.push("or");
        supArray.push(null);
        supArray.push("and");
        supArray.push(null);
        supArray.push("oper");
        supArray.push(this.solveAttrOper);
        return supArray;
    },

    /**
     * Function: initAddLbrac
     * Initialize what can follow left bracket.
     *
     * Returns:
     * {Array} What type is it, what can follows and additional functions to suply.
     */
    initAddLbrac: function(){
        var supArray = new Array();
        supArray.push("lbrac");
        supArray.push("attr");
        supArray.push(null);
        supArray.push("neg");
        supArray.push(null);
        supArray.push("lbrac");
        supArray.push(null);
        return supArray;
    },

    /**
     * Function: initAddNeg
     * Initialize what can follow negation.
     *
     * Returns:
     * {Array} What type is it, what can follows and additional functions to suply.
     */
    initAddNeg: function(){
        var supArray = new Array();
        supArray.push("neg");
        supArray.push("attr");
        supArray.push(null);
        return supArray;
    },

    /**
     * Function: initAddOr
     * Initialize what can follow Disjunction.
     *
     * Returns:
     * {Array} What type is it, what can follows and additional functions to suply.
     */
    initAddOr: function(){
        var supArray = new Array();
        supArray.push("or");
        supArray.push("lbrac");
        supArray.push(null);
        supArray.push("attr");
        supArray.push(null);
        supArray.push("neg");
        supArray.push(null);
        return supArray;
    },

    /**
     * Function: initAddAnd
     * Initialize what can follow conjunction.
     *
     * Returns:
     * {Array} What type is it, what can follows and additional functions to suply.
     */
    initAddAnd: function(){
        var supArray = new Array();
        supArray.push("and");
        supArray.push("lbrac");
        supArray.push(null);
        supArray.push("attr");
        supArray.push(null);
        supArray.push("neg");
        supArray.push(null);
        return supArray;
    },

    /**
     * Function: initAddOper
     * Initialize what can follow InterestMeasure(Operator).
     *
     * Returns:
     * {Array} What type is it, what can follows and additional functions to suply.
     */
    initAddOper: function(){
        var supArray = new Array();
        supArray.push("oper");
        supArray.push("attr");
        supArray.push(null);
        supArray.push("lbrac");
        supArray.push(null);
        supArray.push("oper");
        supArray.push(null);
        supArray.push("neg");
        supArray.push(null);
        return supArray;
    },

    /**
     * Function: initAddAttr
     * Initialize what can follow attribute.
     *
     * Returns:
     * {Array} What type is it, what can follows and additional functions to suply.
     */
    initAddAttr: function(){
        var supArray = new Array();
        supArray.push("attr");
        supArray.push("oper");
        supArray.push(this.solveAttrOper);
        supArray.push("and");
        supArray.push(null);
        supArray.push("or");
        supArray.push(null);
        supArray.push("rbrac");
        supArray.push(this.solveAttrRBrac);
        return supArray;
    },

    /**
     * Function: solveAttrRBrac
     * This solves whether I can add right bracket after attributes. I must have
     * at least one unfinished left bracket to allow this.
     *
     * Parameters:
     * position  {int} position I am on in the rule. The left bracket must be before position.
     *
     * Returns:
     * {Boolean}
     */
    solveAttrRBrac: function(position){
        if((this.getBracketDepth(position)) > 0){
            return true;
        }
        return false;
    },

    /**
     * Function: solveAttrOper
     * This function solves if operator can follow attribute. All left brackets
     * must be finished and there must not be operator already in the rule.
     *
     * Parameters:
     * position  {int} position I am on in rule
     *
     * Returns:
     * {Boolean}
     */
    solveAttrOper: function(position){
        if((this.getBracketDepth(position)) == 0 && this.countOpers(position) == 0){
            return true;
        }
        return false;
    },

    /**
     * Function: isRuleCorrect
     * One of the basic functions. This one decides whether existing rule is correct
     * and therefore whether there is no mistake like attribute after attribute.
     *
     * Returns:
     * {Boolean} True if the rule is correct False otherwise.
     */
    isRuleCorrect: function(){
        var element1, element2;
        if(this.elements.length == 1){
            if(this.elements[0].getType() == "lbrac" || this.elements[0].getType() == "attr"){
                return true
            }
            if(this.elements[0].getType() == "oper" && this.serverInfo.getMinValues("ant") < 1){
                return true
            }
            if(this.elements[0].getType() == "neg"){
                var depthstart = this.getBracketDepth(0);
                if(this.serverInfo.getDepthLevels().isAllowed(this.elements[0].getType(), depthstart) == "false"){
                    return false;
                }
                else{
                    return true;
                }
            }
            return false;
        }
        if(!this.solveAmounts()){
            return false;
        }
        if(!this.solveSupportedInterestMeasures()){
            return false;
        }
        for(var i = 0; i < this.elements.length-1; i++){
            element1 = this.elements[i];
            element2 = this.elements[i+1];
            if(element1 == null || element2 == null){
                continue;
            }
            if(this.isBoolean(element2.getType())){
                var depth = this.getBracketDepth(i);
                if(this.serverInfo.getDepthLevels().isAllowed(element2.getType(), depth) == "false"){
                    return false;
                }
            }
            if(!this.solveTwoElements(element1, element2, i)){
                return false;
            }
        }
        if(this.getDepth(this.elements.length) > this.serverInfo.getDepthNesting()){
            return false;
        }
        return true;
    },

    /**
     * Function: countNegation
     * This counts how many negations are there in the rule to the position.
     *
     * Parameters:
     * position  {Number} position to which we should count it.
     *
     * Returns:
     * {Number} amount of negations
     */
    countNegation: function(position){
        var negation = 0
        for(var i = 0; i < this.elements.length; i++){
            if(i-1 == position){
                break;
            }
            if(this.elements[i] == null){
                continue;
            }
            if(this.elements[i].getType() == "neg"){
                negation++;
            }
            if(this.elements[i].getType() == "oper"){
                negation = 0;
            }
        }
        return negation;
    },

    /**
     * Function: isBoolean
     * This function whether type it gets is boolean type of application
     *
     * Parameters:
     * element  {String} Type of element where I need to decide whether it is
     * boolean.
     *
     * Returns:
     * {Boolean} True if it is boolean.
     */
    isBoolean: function(element){
        if(element == "and" || element == "or" || element == "neg"){
            return true;
        }
        return false;
    },

    /**
     * Function: solveTwoElements
     * This solves two following elements whether they can be one after another.
     *
     * Parameters:
     * element1     {String} Type of the first element
     * element2     {String} Type of the second element.
     * position     {int}    position of the first element.(possibly second I am not sure)
     *
     * Returns:
     * {boolean} True if element2 can follow element1
     */
    solveTwoElements: function(element1, element2, position){
        for(var j = 0; j < this.allowedCombinations.length; j++){
            if(element1.getType() == this.allowedCombinations[j][0]){
                for(var k = 1; k < this.allowedCombinations[j].length; k += 2){
                    if(element2.getType() == this.allowedCombinations[j][k] && (this.allowedCombinations[j][k+1] == null || this.allowedCombinations[j][k+1].bind(this)(position))){
                        return true;
                    }
                }
            }
        }
        return false;
    },

    /**
     * Function: countBrackets
     * This function counts how deep the brackets are in themselves.
     * Ex: Attr and ( Attr ot Attr ) means bracket depth 1
     *     Attr or Attr              means bracket depth 0
     *     Attr and ( ( Attr and Attr ) or ( Attr and Attr ) )
     *                               means bracket depth 2
     *
     * Parameters:
     * position  {int} position on which we are at the moment.
     *
     * Returns:
     * {int} deep of brackets.
     */
    countBrackets: function(position){
        var brackNumber = 0;
        for(var i = 0; i < this.elements.length; i++){
            if(i-1 == position){
                break;
            }
            if(this.elements[i] == null){
                continue;
            }
            if(this.elements[i].getType() == "lbrac"){
                brackNumber++;
            }
            if(this.elements[i].getType() == "rbrac"){
                brackNumber--;
            }
            if(this.elements[i].getType() == "oper"){
                brackNumber = 0;
            }
        }
        return brackNumber;
    },

    /**
     * Function: getBracketDepth
     * This function counts how deep the brackets are in themselves.
     * Ex: Attr and ( Attr ot Attr ) means bracket depth 1
     *     Attr or Attr              means bracket depth 0
     *     Attr and ( ( Attr and Attr ) or ( Attr and Attr ) )
     *                               means bracket depth 2
     *
     * Parameters:
     * position  {int} position on which we are at the moment.
     *
     * Returns:
     * {int} deep of brackets.
     */
    getBracketDepth: function(position){
        var brackNumber = 0;
        for(var i = 0; i < this.elements.length; i++){
            if(i-1 == position){
                break;
            }
            if(this.elements[i] == null){
                continue;
            }
            if(this.elements[i].getType() == "lbrac"){
                brackNumber++;
            }
            /*if(this.elements[i].getType() == "neg"){
                brackNumber++;
            }*/
            if(this.elements[i].getType() == "rbrac"){
                brackNumber--;
            }
            if(this.elements[i].getType() == "oper"){
                brackNumber = 0;
            }
        }
        return brackNumber;
    },

    getDepth: function(position){
        var tree = new Tree();
        var elementsToSolve = this.elements.slice(0,position);
        var missingBrackets = this.countBrackets();
        var rbrac = new BooleanCl(")","rbrac");
        var attr = new Attribute("attr1","",new Array());
        if(elementsToSolve[elementsToSolve.length-1] != null){
            if(elementsToSolve[elementsToSolve.length-1].isElementBoolean()){
                elementsToSolve.push(attr);
            }
        }
        for(var bracket = 0; bracket < missingBrackets; bracket++){
            elementsToSolve.push(rbrac);
        }
        // I need to have correct rule. Therefore fill in attrs and brackets.
        if(tree.solveRule(elementsToSolve) == null){
            return 9999999;
        }
        return tree.getLevels();
    },

    /**
     * Function: copyElements
     * This function create new array as a copy of existing one.
     *
     * Returns:
     * {Array} Array of existing elements(ARElement).
     */
    copyElements: function(){
        var newElement = new Array();
        for(var i = 0; i < this.elements.length; i++){
            newElement.push(this.elements[i]);
        }
        return newElement;
    },


    /**
     * Function: insertItemWithoutDisplay
     * Try to insert item on position position and not display it
     *
     * Parameters:
     * elementAR    {ARElement} Type of new element
     * position     {Number}    Position
     *
     * Returns:
     * {Boolean} true if it was possible to insert item
     */
    insertItemWithoutDisplay: function(elementAR, position){
        var novaPozice = position * 1 + 1;
        if(novaPozice > this.elements.length){
            var elementsSup = this.copyElements();
            this.elements.push(elementAR);
            if(this.isRuleCorrect()){
                this.lastElement = elementAR;
                return true;
            }
            else{
                this.elements = elementsSup;
                return false;
            }
        }
        else{
            return this.changeItemWithoutDisplay(elementAR, position);
        }
    },

    /**
     * Function: changeItemWithoutDisplay
     * It tries to change element on position position and not display it
     *
     * Parameters:
     * position     {int} position of the item
     * elementAR    {ARElement} New Element.
     *
     * Returns:
     * {Boolean} true if it was possible to change item
     */
    changeItemWithoutDisplay: function(elementAR, position){
        var elementsBackup = this.copyElements();
        this.elements.splice(position, 1, elementAR);
        if(this.isRuleCorrect()){
            this.lastElement = elementAR;
            return true;
        }
        else{
            this.elements = elementsBackup;
            return false;
        }
    },

    /**
     * Function: insertItemNew
     * Try to insert item on new position
     *
     * Parameters:
     * elementAR    {ARElement} Type of new element
     *
     * Returns:
     * {Boolean} true if it was possible to insert item
     */
    insertItemNew: function(elementAR){
        var novaPozice = this.elements.length;
        return this.insertItemWithoutDisplay(elementAR, novaPozice);
    },

    /**
     * Function: insertItem
     * Try to insert item on position position
     *
     * Parameters:
     * elementAR    {ARElement} Type of new element
     * position     {Number}    Position
     *
     * Returns:
     * {Boolean} true if it was possible to insert item
     */
    insertItem: function(elementAR, position){
        var novaPozice = position * 1 + 1;
        if(novaPozice > this.elements.length){
            var elementsSup = this.copyElements();
            this.elements.push(elementAR);
            if(this.isRuleCorrect()){
                var actualDiv = this.ruleDiv
                this.ruleDiv.empty();
                this.display().replaces(actualDiv);
                this.fireEvent("display");
                this.lastElement = elementAR;
                return true;
            }
            else{
                this.elements = elementsSup;
                return false;
            }
        }
        else{
            return this.changeItem(elementAR, position);
        }
    },

    /**
     * Function: changeItem
     * It tries to change element on position position
     *
     * Parameters:
     * position     {int} position of the item
     * elementAR    {ARElement} New Element.
     *
     * Returns:
     * {Boolean} true if it was possible to change item
     */
    changeItem: function(elementAR, position){
        var elementsBackup = this.copyElements();
        this.elements.splice(position, 1, elementAR);
        if(this.isRuleCorrect()){
            var actualDiv = this.ruleDiv
            this.ruleDiv.empty();
            this.display().replaces(actualDiv);
            this.fireEvent("display");
            this.lastElement = elementAR;
            return true;
        }
        else{
            this.elements = elementsBackup;
            return false;
        }
    },

    /**
     * Function: removeItem
     * Removes Item on position position
     *
     * Parameters:
     * position     {int} position of the item
     */
    removeItem: function(element){
        var mainElement = element.element;
        for(var i = 0; i < this.elements.length; i++){
            if(this.elements[i] === mainElement){
                this.elements[i] = null;
            }
        }
        for(var j = this.elements.length-1; j >= 0; j--){
            if(this.elements[j] == null){
                this.elements.splice(j,1);
            }
            else{
                break;
            }
        }
        var actualDiv = this.ruleDiv
        this.ruleDiv.empty();
        this.display().replaces(actualDiv);
        this.fireEvent("display");
        return true;
    },

    /**
     * Function: display
     * Creates HTMLElement representing this AsociationRule.
     *
     * Returns:
     * {HTMLElement} element representing this AsociationRule
     */
    display: function(){
        this.ruleDiv = new Element('div', {
            name: "ruleDiv",
            'class': "rule"
        });
        this.ruleDiv.setStyle("height",2*this.maxSize+40);
        this.ruleDiv.asociationRule = this;
        var placeForEl = new PlaceForARElement();
        this.placeHTML = placeForEl.display();

        var lastElement = null;
        for(var actualElement = 0; actualElement < this.elements.length; actualElement++){
            if(this.elements[actualElement] != null){
                lastElement = this.elements[actualElement];
            }
            this.createNewElement(placeForEl, this.elements[actualElement], actualElement);
        }
        this.placeHTML.set("name","rule"+actualElement);
        this.placeHTML.setStyle("height",this.maxSize+5);
        this.placeHTML.inject(this.ruleDiv);

        if(lastElement != null){
        //this.lastElement = lastElement;
        }

        return this.ruleDiv;
    },

    /**
     * Function: showAsking
     * This function is called when user clicks on element or dip it in the rule.
     */
    showAsking: function(){
        this.lastElement.onClickMy();
    },

    /**
     * Function: createNewElement
     * Creates HTMLElement representing Element in this rule and place for it and
     * injects these two things in this rule.
     *
     * Params:
     * placeForElement {HTMLElement} element representing place for this element.
     * actualElement   {ARElement}   element which shall be displayed
     */
    createNewElement: function(placeForElement, actualElement, elementNumber){
        if(actualElement != null){
            var elementToBeDisplayed = actualElement.display(false);
            elementToBeDisplayed.addEvent('click', function(event){
                actualElement.onClickMy();
            }.bind(this));
            actualElement.addEvent("save", function(event){
                
                }.bind(this))
            actualElement.correctPlace = false;
            actualElement.shouldBeCreated = false;
        }
        var placeForElHTML = placeForElement.display();
        
        if(actualElement != null){
            elementToBeDisplayed.setStyle("height",this.maxSize);
            elementToBeDisplayed.inject(placeForElHTML);
        }
        placeForElHTML.inject(this.ruleDiv);
        placeForElHTML.set("name","rule"+elementNumber);
        placeForElHTML.setStyle("height",this.maxSize+5);

        // Possibly wrong
        this.placeHTML.dispose();
        this.placeHTML = placeForElement.display();
        this.placeHTML.inject(this.ruleDiv, "bottom");
    },

    /**
     * Function: save
     * It saves all elements data at this moment.
     */
    save: function(){
        // It is necessary to test rule for min amounts.
        for(var actualElement = 0; actualElement < this.elements.length; actualElement++){
            this.elements[actualElement].save();
        }
    },

    /**
     * Function: toJSON
     * It makes JSON of whole rule
     *
     * Returns:
     * {JSONObject} JSON object representing this rule.
     */
    toJSON: function(){
        var jsonObject = new Array();
        if(this.getDepth(this.elements.length) == -1){
            return null;
        }
        if(!this.isRuleCorrect()){
            return null;
        }
        if(!this.solveSupportedInterestMeasuresFinal()){
            return null;
        }
        if(!this.solveAmountsMin()){
            return null;
        }
        if(this.countBrackets(this.elements.length) > 0){
            return null;
        }
        var type = this.elements[this.elements.length-1].getType();
        if(type == "and" || type == "or" || type == "neg"){
            return null;
        }
        for(var actualElement = 0; actualElement < this.elements.length; actualElement++){
            if(this.elements[actualElement] != null){
                jsonObject.push(this.elements[actualElement].toJSON());
            }
        }
        return jsonObject;
    }
});

/**
 * Class: PlaceForARElement
 * It represents place for ARElement
 */
var PlaceForARElement = new Class({
    initialize: function(){

    },

    /**
     * Function: display
     * It creates HTMLElement representing PlaceForARElement.
     *
     * Returns:
     * {HTMLElement} HTMLElement representing PlaceForARElement.
     */
    display: function(){
        var placeForElement = new Element('div',{
            'class': "ARElement"
        })
        return placeForElement;
    }
})

