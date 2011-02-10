/**
 * Class: AsociationRule
 * This class represents one Asociation Rule used in AsocRule GUI Application. 
 * The rule is created by user during his work with the application. This class
 * enables adding elements, changing elements, removing elements and solving 
 * validity of the rule.
 */
var AsociationRule = new Class({
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
    initialize: function(mainClass){
        this.elements = new Array(); // tady budu skladovat Attribute, Operator nebo Boolean
        this.asociationRules = mainClass;
        this.serverInfo = mainClass.getServerInfo();
        this.allowedCombinations = new Array();
        this.initAllowedCom();
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
        var array2Sup = [conAttrs, antAttrs, IMAttrs, antAttrs+conAttrs+IMAttrs];
        for(var i = 0; i < arraySup.length; i++){
            /*if(this.serverInfo.getMinValues(arraySup[i]) > array2Sup[i]){
                console.log("Strange");
                return false;
            }*/
            if(this.serverInfo.getMaxValues(arraySup[i]) < array2Sup[i]){
                return false;
            }
        }
        return true;
    },

   /**
     * Function: solveSupportedOperators
     * This function decides whether the combination of used interest measures is allowed
     * based on the configuration file on the server.
     *
     * Returns:
     * {Boolean} True if the combination is allowed.
     */
    solveSupportedOperators: function(){
        var existingOperators = this.getExistingOperators();
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
        
        if(existingOperators.length == 1){
            return true;
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
            if(ok == existingOperators.length){
                if(this.countAttrsCon() != 0){
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
     * Function: getExistingOperators
     * Private function used to get operators which are already used in the rule.
     *
     * Returns:
     * {Array} Array of existing operators
     */
    getExistingOperators: function(){
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
        for(var i = 0; i < this.elements.length; i++){
            if(this.elements[i] == null){
                continue;
            }
            if(this.elements[i].getType() == "attr"){
                attrCount++;
            }
            if(this.elements[i].getType() == "oper"){
                attrCount = 0;
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
        if(this.getBracketDepth(position) >= 0){
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
        if(this.getBracketDepth(position) == 0 && this.countOpers(position) == 0){
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
            if(this.elements[0].getType() == "lbrac" || this.elements[0].getType() == "attr"|| this.elements[0].getType() == "neg"){
                return true
            }
            return false;
        }
        if(!this.solveAmounts()){
            return false;
        }
        if(!this.solveSupportedOperators()){
            return false;
        }
        if(this.getBracketDepth(i) > this.serverInfo.getDepthNesting()){
            return false;
        }
        for(var i = 0; i < this.elements.length-1; i++){
            element1 = this.elements[i];
            element2 = this.elements[i+1];
            if(element1 == null || element2 == null){
                continue;
            }
            if(this.isBoolean(element2.getType())){
                if(this.serverInfo.getDepthLevels().isAllowed(element2.getType(), this.getBracketDepth(i)) == "false"){
                    return false;
                }
            }
            if(!this.solveTwoElements(element1, element2, i)){
                return false;
            }
        }
        return true;
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
            if(this.elements[i].getType() == "rbrac"){
                brackNumber--;
            }
        }
        return brackNumber;
    },

    /**
     * Function: copyElements
     * This function create new array as a copy of existing one. 
     *
     * Returns:
     * {Array} Array of existing elements.
     */
    copyElements: function(){
        var newElement = new Array();
        for(var i = 0; i < this.elements.length; i++){
            newElement.push(this.elements[i]);
        }
        return newElement;
    },

    /**
     * Function: insertItem
     * Try to insert item on position position
     *
     * Parameters:
     * position     {int} position of the item
     * elementAR    {String} Type of new element
     *
     * Returns:
     * {Boolean} true if it was possible to change item
     */
    insertItem: function(elementAR){
        var elementsSup = this.copyElements();
        this.elements.push(elementAR);
        if(!this.isRuleCorrect()){
            this.elements = elementsSup;
            return false;
        }
        return true;
    },

    /**
     * Function: changeItem
     * It tries to change element on position position
     *
     * Parameters:
     * position     {int} position of the item
     * elementAR    {String} Type of new element
     *
     * Returns:
     * {Boolean} true if it was possible to change item
     */
    changeItem: function(elementAR, position){
        var elementsSup = this.copyElements();
        this.elements.splice(position-1, 1, elementAR);
        if(!this.isRuleCorrect()){
            this.elements = elementsSup;
            return false;
        }
        return true;
    },

    /**
     * Function: removeItem
     * Removes Item on position position
     *
     * Parameters:
     * position     {int} position of the item
     */
    removeItem: function(position){
        this.elements[position-1] = null;
    }
});

