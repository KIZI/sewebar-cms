/**
 * Class: ServerInfoTest
 * It tests whole functionality of ServerInfo class.
 */
var ServerInfoTest = new Class({
    /**
     * Function: initialize
     * It creates instance of this class and tested class ServerInfo.
     */
    initialize: function(){
        this.serverInfo = new ServerInfo();
    },

    /**
     * Function: testSolveOperators
     * It tests function solveOperators
     */
    testSolveOperators: function(){
        this.serverInfo = new ServerInfo();
        var item = new TestClassItem();
        var names = new Array();
        names.push("InterestMeasure1");
        names.push("InterestMeasure2");
        var fieldsAmounts = item.setOperator(names);
        this.serverInfo.solveOperators(item);
        var result = this.serverInfo.getOperators();
        var operatorClass = new TestClassElements();
        var expectedResult = operatorClass.setOperator(names, fieldsAmounts);
        for(var actualOperator = 0; actualOperator < result.length; actualOperator++){
            if(!this.areOperatorsEqual(result[actualOperator], expectedResult[actualOperator])){
                console.log("testSolveOperators is Wrong result"+result[actualOperator]+"expectedResult "+expectedResult[actualOperator]);
                return;
            }
        }
        console.log("testSolveOperators is OK");
    },

    /**
     * Function: testSolveOperators1
     * It tests function solveOperators
     */
    testSolveOperators1: function(){
        this.serverInfo = new ServerInfo();
        var item = new TestClassItem();
        var names = new Array();
        names.push("Name mena");
        names.push("Measure22");
        names.push("sdMeasure22");
        names.push("sdfsdf Measure22");
        var fieldsAmounts = item.setOperator(names);
        this.serverInfo.solveOperators(item);
        var result = this.serverInfo.getOperators();
        var operatorClass = new TestClassElements();
        var expectedResult = operatorClass.setOperator(names, fieldsAmounts);
        for(var actualOperator = 0; actualOperator < result.length; actualOperator++){
            if(!this.areOperatorsEqual(result[actualOperator], expectedResult[actualOperator])){
                console.log("testSolveOperators1 is Wrong result"+result[actualOperator]+"expectedResult "+expectedResult[actualOperator]);
                return;
            }
        }
        console.log("testSolveOperators1 is OK");
    },

    /**
     * Function: testSolveOperators2
     * It tests function solveOperators
     */
    testSolveOperators2: function(){
        this.serverInfo = new ServerInfo();
        var item = new TestClassItem();
        var names = new Array();
        names.push("Name mena");
        var fieldsAmounts = item.setOperator(names);
        this.serverInfo.solveOperators(item);
        var result = this.serverInfo.getOperators();
        var operatorClass = new TestClassElements();
        var expectedResult = operatorClass.setOperator(names, fieldsAmounts);
        for(var actualOperator = 0; actualOperator < result.length; actualOperator++){
            if(!this.areOperatorsEqual(result[actualOperator], expectedResult[actualOperator])){
                console.log("testSolveOperators2 is Wrong result"+result[actualOperator]+"expectedResult "+expectedResult[actualOperator]);
                return;
            }
        }
        console.log("testSolveOperators2 is OK");
    },

    /**
     * Function: testSolveOperators
     * It tests function solveOperators
     */
    testSolveOperators3: function(){
        this.serverInfo = new ServerInfo();
        var MAX_EXPECTED_OPERATORS = 20;
        var item = new TestClassItem();
        var names = new Array();
        for(var i = 0; i < MAX_EXPECTED_OPERATORS; i++){
            names.push("OperatorNumber"+i);
        }
        var fieldsAmounts = item.setOperator(names);
        this.serverInfo.solveOperators(item);
        var result = this.serverInfo.getOperators();
        var operatorClass = new TestClassElements();
        var expectedResult = operatorClass.setOperator(names, fieldsAmounts);
        for(var actualOperator = 0; actualOperator < result.length; actualOperator++){
            if(!this.areOperatorsEqual(result[actualOperator], expectedResult[actualOperator])){
                console.log("testSolveOperators3 is Wrong result"+result[actualOperator]+"expectedResult "+expectedResult[actualOperator]);
                return;
            }
        }
        console.log("testSolveOperators3 is OK");
    },

    /**
     * Function: testSolveAttributes
     * It tests function solveAttributes
     */
    testSolveAttributes: function(){
        this.serverInfo = new ServerInfo();
        var item = new TestClassItem();
        var names = new Array();
        names.push("Attribute1");
        names.push("Attribute2");
        var fieldsAmounts = item.setAttributes(names);
        this.serverInfo.solveAttributes(item);
        var result = this.serverInfo.getAttributes();
        var attributeClass = new TestClassElements();
        var expectedResult = attributeClass.setAttribute(names, fieldsAmounts);
        if(result.length != expectedResult.length){
            console.log("testSolveAttributes is WRONG "+result.length+" "+expectedResult.length);
        }
        for(var actualAttribute = 0; actualAttribute < result.length; actualAttribute++){
            if(!this.areAttributesEqual(result[actualAttribute], expectedResult[actualAttribute])){
                console.log("testSolveAttributes is Wrong result"+result[actualAttribute]+"expectedResult "+expectedResult[actualAttribute]);
                return;
            }
        }
        console.log("testSolveAttributes is OK");
    },

    /**
     * Function: testSolveAttributes1
     * It tests function solveAttributes
     */
    testSolveAttributes1: function(){
        this.serverInfo = new ServerInfo();
        var item = new TestClassItem();
        var names = new Array();
        names.push("Attribute1");
        var fieldsAmounts = item.setAttributes(names);
        this.serverInfo.solveAttributes(item);
        var result = this.serverInfo.getAttributes();
        var attributeClass = new TestClassElements();
        var expectedResult = attributeClass.setAttribute(names, fieldsAmounts);
        for(var actualAttribute = 0; actualAttribute < result.length; actualAttribute++){
            if(!this.areAttributesEqual(result[actualAttribute], expectedResult[actualAttribute])){
                console.log("testSolveAttributes1 is Wrong result"+result[actualAttribute]+"expectedResult "+expectedResult[actualAttribute]);
                return;
            }
        }
        console.log("testSolveAttributes1 is OK");
    },

    /**
     * Function: testSolveAttributes2
     * It tests function solveAttributes
     */
    /*
    testSolveAttributes2: function(){
        this.serverInfo = new ServerInfo();
        var item = new TestClassItem();
        var names = new Array();
        names.push("Attribute1");
        names.push("Attribute2");
        names.push("At tribute6");
        names.push("Attribute34f");
        names.push("A tt riBbute5");
        names.push("attribute");
        names.push("ATTR");
        names.push("Attribute8");
        names.push("Attribute9");
        names.push("Attribute10");
        var fieldsAmounts = item.setAttributes(names);
        this.serverInfo.solveAttributes(item);
        var result = this.serverInfo.getAttributes();
        var attributeClass = new TestClassElements();
        var expectedResult = attributeClass.setAttribute(names, fieldsAmounts);
        if(result.length != expectedResult.length){
            console.log("testSolveAttributes2 is Wrong LENGTH result"+result.length+"expectedResult "+expectedResult.length);
        }
        for(var actualAttribute = 0; actualAttribute < result.length; actualAttribute++){
            if(!this.areAttributesEqual(result[actualAttribute], expectedResult[actualAttribute])){
                console.log("testSolveAttributes2 is Wrong result"+result[actualAttribute]+"expectedResult "+expectedResult[actualAttribute]);
                return;
            }
        }
        console.log("testSolveAttributes2 is OK");
    },
    */

    /**
     * Function: testSolveAttributes3
     * It tests function solveAttributes
     */
    testSolveAttributes3: function(){
        this.serverInfo = new ServerInfo();
        var item = new TestClassItem();
        var names = new Array();
        names.push("Attribute1");
        names.push("Attribute2");
        names.push("Attribute3");
        names.push("Attribute4");
        names.push("Attribute5");
        var fieldsAmounts = item.setAttributes(names);
        this.serverInfo.solveAttributes(item);
        var result = this.serverInfo.getAttributes();
        var attributeClass = new TestClassElements();
        var expectedResult = attributeClass.setAttribute(names, fieldsAmounts);
        for(var actualAttribute = 0; actualAttribute < result.length; actualAttribute++){
            if(!this.areAttributesEqual(result[actualAttribute], expectedResult[actualAttribute])){
                console.log("testSolveAttributes3 is Wrong result"+result[actualAttribute]+"expectedResult "+expectedResult[actualAttribute]);
                return;
            }
        }
        console.log("testSolveAttributes3 is OK");
    },
    
    /**
     * Function: testSolvePossibleCoefficients
     * It tests function solvePossibleCoeficients
     */
    testSolvePossibleCoefficients: function(){
        this.serverInfo = new ServerInfo();
        var item = new TestClassItem();
        var names = new Array();
        names.push("Coef1");
        names.push("Coef2");
        names.push("Coef3");
        names.push("Coef4");
        names.push("Coef5");
        var fieldsAmounts = item.setPossibleCoefficients(names);
        this.serverInfo.solvePossibleCoefficients(item);
        var result = this.serverInfo.getAttributesFields();
        var attributeClass = new TestClassElements();
        var expectedResult = attributeClass.setPossibleCoefficients(names, fieldsAmounts);
        for(var actualAttribute = 0; actualAttribute < result.length; actualAttribute++){
            if(!this.areCoefficientsEqual(result[actualAttribute], expectedResult[actualAttribute])){
                console.log("testSolvePossibleCoefficients is Wrong result"+result[actualAttribute]+"expectedResult "+expectedResult[actualAttribute]);
                return;
            }
        }
        console.log("testSolvePossibleCoefficients is OK");
    },

    /**
     * Function: testSolvePossibleCoefficients1
     * It tests function solvePossibleCoeficients
     */
    testSolvePossibleCoefficients1: function(){
        this.serverInfo = new ServerInfo();
        var item = new TestClassItem();
        var names = new Array();
        names.push("Coef1");
        var fieldsAmounts = item.setPossibleCoefficients(names);
        this.serverInfo.solvePossibleCoefficients(item);
        var result = this.serverInfo.getAttributesFields();
        var attributeClass = new TestClassElements();
        var expectedResult = attributeClass.setPossibleCoefficients(names, fieldsAmounts);
        for(var actualAttribute = 0; actualAttribute < result.length; actualAttribute++){
            if(!this.areCoefficientsEqual(result[actualAttribute], expectedResult[actualAttribute])){
                console.log("testSolvePossibleCoefficients1 is Wrong result"+result[actualAttribute]+"expectedResult "+expectedResult[actualAttribute]);
                return;
            }
        }
        console.log("testSolvePossibleCoefficients1 is OK");
    },

    /**
     * Function: testSolvePossibleCoefficients2
     * It tests function solvePossibleCoeficients
     */
    testSolvePossibleCoefficients2: function(){
        this.serverInfo = new ServerInfo();
        var item = new TestClassItem();
        var names = new Array();
        names.push("Coef1");
        names.push("Co ef2");
        names.push("coef3");
        names.push("coef");
        names.push("COEF");
        var fieldsAmounts = item.setPossibleCoefficients(names);
        this.serverInfo.solvePossibleCoefficients(item);
        var result = this.serverInfo.getAttributesFields();
        var attributeClass = new TestClassElements();
        var expectedResult = attributeClass.setPossibleCoefficients(names, fieldsAmounts);
        for(var actualAttribute = 0; actualAttribute < result.length; actualAttribute++){
            if(!this.areCoefficientsEqual(result[actualAttribute], expectedResult[actualAttribute])){
                console.log("testSolvePossibleCoefficients2 is Wrong result"+result[actualAttribute]+"expectedResult "+expectedResult[actualAttribute]);
                return;
            }
        }
        console.log("testSolvePossibleCoefficients2 is OK");
    },

    /**
     * Function: testSolvePossibleCoefficients3
     * It tests function solvePossibleCoeficients
     */
    testSolvePossibleCoefficients3: function(){
        this.serverInfo = new ServerInfo();
        var item = new TestClassItem();
        var names = new Array();
        names.push("Coef1");
        names.push("Coef2");
        names.push("Coef3");
        names.push("Coef4");
        names.push("Coef5");
        names.push("Coef6");
        names.push("Coef7");
        names.push("Coef8");
        names.push("Coef9");
        names.push("Coef10");
        names.push("Coef11");
        names.push("Coef12");
        names.push("Coef13");
        names.push("Coef14");
        names.push("Coef15");
        var fieldsAmounts = item.setPossibleCoefficients(names);
        this.serverInfo.solvePossibleCoefficients(item);
        var result = this.serverInfo.getAttributesFields();
        var attributeClass = new TestClassElements();
        var expectedResult = attributeClass.setPossibleCoefficients(names, fieldsAmounts);
        for(var actualAttribute = 0; actualAttribute < result.length; actualAttribute++){
            if(!this.areCoefficientsEqual(result[actualAttribute], expectedResult[actualAttribute])){
                console.log("testSolvePossibleCoefficients3 is Wrong result"+result[actualAttribute]+"expectedResult "+expectedResult[actualAttribute]);
                return;
            }
        }
        console.log("testSolvePossibleCoefficients3 is OK");
    },

    /**
     * Function: testSolveSupIM
     * It tests function solveSupIM
     */
    testSolveSupIM: function(){
        this.serverInfo = new ServerInfo();
        var item = new TestClassItem();
        item.supIMCombinations = 3;
        var supCom0 = new Array();
        supCom0.push("Support");
        supCom0.push("Confidence");
        var supCom1 = new Array();
        supCom1.push("Support");
        var supCom2 = new Array();
        supCom2.push("IM1");
        supCom2.push("im");
        supCom2.push("Interest Measure");
        supCom2.push("Confidence");
        supCom2.push("Support");
        supCom2.push("Above average implication");
        item["supIMCom1"] = supCom0;
        item["supIMCom2"] = supCom1;
        item["supIMCom3"] = supCom2;
        this.serverInfo.solveSupIM(item);
        var result = this.serverInfo.getSupportedIMCombinations();
        var expectedResult = new Array();
        expectedResult.push(supCom0);
        expectedResult.push(supCom1);
        expectedResult.push(supCom2);
        if(this.areSupIMEqual(result, expectedResult)){
            console.log("testSolveSupIM is OK");
        }
        else{
            console.log("testSolveSupIM is wrong. Result "+result+" ExpectedResult "+expectedResult);
        }
    },

    /**
     * Function: testSolveMinMax1
     * It tests function solveMinMax
     */
    testSolveMinMax1: function(){
        this.serverInfo = new ServerInfo();
        var item = new TestClassItem();
        item.setMinMax();
        this.serverInfo.solveMinMax(item);
        var result = this.serverInfo.getMinValues("cons");
        var expectedResult = 1
        if(result == expectedResult){
            console.log("testSolveMinMax1 is OK");
        }
        else{
            console.log("testSolveMinMax1 is wrong. Result "+result+" ExpectedResult "+expectedResult);
        }
    },
    
    /**
     * Function: testSolveMinMax2
     * It tests function solveMinMax
     */
    testSolveMinMax2: function(){
        this.serverInfo = new ServerInfo();
        var item = new TestClassItem();
        item.setMinMax();
        this.serverInfo.solveMinMax(item);
        var result = this.serverInfo.getMinValues("ant");
        var expectedResult = 2
        if(result == expectedResult){
            console.log("testSolveMinMax2 is OK");
        }
        else{
            console.log("testSolveMinMax2 is wrong. Result "+result+" ExpectedResult "+expectedResult);
        }
    },

    /**
     * Function: testSolveMinMax3
     * It tests function solveMinMax
     */
    testSolveMinMax3: function(){
        this.serverInfo = new ServerInfo();
        var item = new TestClassItem();
        item.setMinMax();
        this.serverInfo.solveMinMax(item);
        var result = this.serverInfo.getMinValues("IM");
        var expectedResult = -1;
        if(result == expectedResult){
            console.log("testSolveMinMax3 is OK");
        }
        else{
            console.log("testSolveMinMax3 is wrong. Result "+result+" ExpectedResult "+expectedResult);
        }
    },

    /**
     * Function: testSolveMinMax4
     * It tests function solveMinMax
     */
    testSolveMinMax4: function(){
        this.serverInfo = new ServerInfo();
        var item = new TestClassItem();
        item.setMinMax();
        this.serverInfo.solveMinMax(item);
        var result = this.serverInfo.getMinValues("general");
        var expectedResult = 0
        if(result == expectedResult){
            console.log("testSolveMinMax4 is OK");
        }
        else{
            console.log("testSolveMinMax4 is wrong. Result "+result+" ExpectedResult "+expectedResult);
        }
    },

    /**
     * Function: testSolveMinMax5
     * It tests function solveMinMax
     */
    testSolveMinMax5: function(){
        this.serverInfo = new ServerInfo();
        var item = new TestClassItem();
        item.setMinMax();
        this.serverInfo.solveMinMax(item);
        var result = this.serverInfo.getMaxValues("cons");
        var expectedResult = 2
        if(result == expectedResult){
            console.log("testSolveMinMax5 is OK");
        }
        else{
            console.log("testSolveMinMax5 is wrong. Result "+result+" ExpectedResult "+expectedResult);
        }
    },

    /**
     * Function: testSolveMinMax6
     * It tests function solveMinMax
     */
    testSolveMinMax6: function(){
        this.serverInfo = new ServerInfo();
        var item = new TestClassItem();
        item.setMinMax();
        this.serverInfo.solveMinMax(item);
        var result = this.serverInfo.getMaxValues("ant");
        var expectedResult = 4
        if(result == expectedResult){
            console.log("testSolveMinMax6 is OK");
        }
        else{
            console.log("testSolveMinMax6 is wrong. Result "+result+" ExpectedResult "+expectedResult);
        }
    },

    /**
     * Function: testSolveMinMax7
     * It tests function solveMinMax
     */
    testSolveMinMax7: function(){
        this.serverInfo = new ServerInfo();
        var item = new TestClassItem();
        item.setMinMax();
        this.serverInfo.solveMinMax(item);
        var result = this.serverInfo.getMaxValues("IM");
        var expectedResult = 3
        if(result == expectedResult){
            console.log("testSolveMinMax7 is OK");
        }
        else{
            console.log("testSolveMinMax7 is wrong. Result "+result+" ExpectedResult "+expectedResult);
        }
    },

    /**
     * Function: testSolveMinMax8
     * It tests function solveMinMax
     */
    testSolveMinMax8: function(){
        this.serverInfo = new ServerInfo();
        var item = new TestClassItem();
        item.setMinMax();
        this.serverInfo.solveMinMax(item);
        var result = this.serverInfo.getMaxValues("general");
        var expectedResult = 6
        if(result == expectedResult){
            console.log("testSolveMinMax8 is OK");
        }
        else{
            console.log("testSolveMinMax8 is wrong. Result "+result+" ExpectedResult "+expectedResult);
        }
    },

    /**
     * Function: testSolveDepth
     * It tests function solveDepth
     */
    testSolveDepth: function(){
        this.serverInfo = new ServerInfo();
         var item = new TestClassItem();
         item.depthNesting = 1;
         item["depth1"] = new Array();
         item["depth1"][0] = "true";
         item["depth1"][1] = "false";
         item["depth1"][2] = "true";
         this.serverInfo.solveDepth(item);
         var result = this.serverInfo.getDepthNesting();
         var expectedResult = 1;
         if(result == expectedResult){
            console.log("testSolveDepth is OK");
        }
        else{
            console.log("testSolveDepth is wrong. Result "+result+" ExpectedResult "+expectedResult);
        }
    },

    /**
     * Function: testSolveDepth1
     * It tests function solveDepth
     */
    testSolveDepth1: function(){
        this.serverInfo = new ServerInfo();
         var item = new TestClassItem();
         item.depthNesting = 1;
         item["depth1"] = new Array();
         item["depth1"][0] = "true";
         item["depth1"][1] = "false";
         item["depth1"][2] = "true";
         this.serverInfo.solveDepth(item);
         var result = this.serverInfo.getDepthLevels();
         var expectedResult = new DepthNesting();
         expectedResult.add(item["depth1"][0], item["depth1"][1], item["depth1"][2]);
         if(this.areDepthNestingsEqual(result, expectedResult, 1)){
            console.log("testSolveDepth is OK");
        }
        else{
            console.log("testSolveDepth is wrong. Result "+result+" ExpectedResult "+expectedResult);
        }
    },

    /**
     * Function: test
     * It calls all other testing functions within this class.
     */
    test: function(){
        this.testSolveOperators();
        this.testSolveOperators1();
        this.testSolveOperators2();
        this.testSolveOperators3();
        this.testSolveAttributes();
        this.testSolveAttributes1();
      //  this.testSolveAttributes2();
        this.testSolveAttributes3();
        this.testSolvePossibleCoefficients();
        this.testSolvePossibleCoefficients1();
        this.testSolvePossibleCoefficients2();
        this.testSolvePossibleCoefficients3();
        this.testSolveSupIM();
        this.testSolveMinMax1();
        this.testSolveMinMax2();
        this.testSolveMinMax3();
        this.testSolveMinMax4();
        this.testSolveMinMax5();
        this.testSolveMinMax6();
        this.testSolveMinMax7();
        this.testSolveMinMax8();
        this.testSolveDepth();
        this.testSolveDepth1();
    },

    /**
     * Function: areDepthNestingEqual
     * It decides whether two depth nestings are equal.
     *
     * Parameters:
     * depthNesting1     {DepthNesting}
     * depthNesting2     {DepthNesting}
     * depth {Number} How many depth levels do thez have
     *
     * Returns:
     * {boolean}
     */
    areDepthNestingsEqual: function(depthNesting1, depthNesting2, depth){
        var possibleChoices = new Array();
        possibleChoices.push("or","and","neg");
        for(var actualDepth = 0; actualDepth < depth; actualDepth++){
            for(var choice = 0; choice < possibleChoices.length; possibleChoices++){
                if(depthNesting1.isAllowed(possibleChoices[choice],actualDepth) != depthNesting2.isAllowed(possibleChoices[choice],actualDepth)){
                    return false;
                }
            }
        }
        return true;
    },

    /**
     * Function: areArraysEqual
     * It decides whether two Arrays are equal.
     *
     * Parameters:
     * array1     {Array}
     * array2     {Array}
     *
     * Returns:
     * {boolean}
     */
    areArraysEqual: function(array1, array2){
        if(array1.length != array2.length){
            return false;
        }
        for(var actualElement = 0; actualElement < array1.length; actualElement++){
            if(array1[actualElement] != array2[actualElement]){
                return false;
            }
        }
        return true;
    },

    areSupIMEqual: function(supIM1, supIM2){
        if(supIM1.length != supIM2.length){
            return false;
        }
        for(var i = 0; i < supIM1.length; i++){
            if(!this.areArraysEqual(supIM1[i], supIM2[i])){
                return false;
            }
        }
        return true;
    },

    /**
     * Function: areCoefficientsEqual
     * It decides whether two coefficients are equal.
     *
     * Parameters:
     * coef1     {AttributeFields}
     * coef2     {AttributeFields}
     *
     * Returns:
     * {boolean}
     */
    areCoefficientsEqual: function(coef1, coef2){
        if(coef1.getCategory() != coef2.getCategory()){
            return false;
        }
        if(!this.areArraysEqual(coef1.getfNameLang() , coef2.getfNameLang())){
            return false;
        }
        if(!this.areArraysEqual(coef1.getMinValue() , coef2.getMinValue())){
            return false;
        }
        if(!this.areArraysEqual(coef1.getMaxValue() , coef2.getMaxValue())){
            return false;
        }
        if(!this.areArraysEqual(coef1.getDatatype() , coef2.getDatatype())){
            return false;
        }
        if(!this.areArraysEqual(coef1.getfName() , coef2.getfName())){
            return false;
        }
        if(!this.areArraysEqual(coef1.getExplanation() , coef2.getExplanation())){
            return false;
        }
        return true;
    },

    /**
     * Function: areOperatorsEqual
     * It decides whether two operators are equal.
     *
     * Parameters:
     * operator1     {Operator}
     * operator2     {Operator}
     *
     * Returns:
     * {boolean}
     */
    areOperatorsEqual: function(operator1, operator2){
        if(operator1.getType() != operator2.getType()){
            return false;
        }
        if(operator1.getName() != operator2.getName()){
            return false;
        }
        if(operator1.getNameLang() != operator2.getNameLang()){
            return false;
        }
        if(!this.areArraysEqual(operator1.getFieldNames(),operator2.getFieldNames())){
            return false;
        }
        if(!this.areArraysEqual(operator1.getFieldLangs() , operator2.getFieldLangs())){
            return false;
        }
        if(!this.areArraysEqual(operator1.getFieldMinValues() , operator2.getFieldMinValues())){
            return false;
        }
        if(!this.areArraysEqual(operator1.getFieldMaxValues() , operator2.getFieldMaxValues())){
            return false;
        }
        if(!this.areArraysEqual(operator1.getFieldDatatype() , operator2.getFieldDatatype())){
            return false;
        }
        if(!this.areArraysEqual(operator1.getExplanation() , operator2.getExplanation())){
            return false;
        }
        return true;
    },

    /**
     * Function: areAttributesEqual
     * It decides whether two attributes are equal.
     *
     * Parameters:
     * attribute1     {Attribute}
     * attribute2     {Attribute}
     *
     * Returns:
     * {boolean}
     */
    areAttributesEqual: function(attribute1, attribute2){
        if(attribute1.getType() != attribute2.getType()){
            return false;
        }
        if(attribute1.getName() != attribute2.getName()){
            return false;
        }
        if(!this.areArraysEqual(attribute1.getOneCategoryInfo(), attribute2.getOneCategoryInfo())){
            return false;
        }
        return true;
    }
})

/**
 * Class: TestClassElements
 * This class provides methods for creating arrays of attributes, operators and
 * attributeFields
 */
var TestClassElements = new Class({
    /**
     * Function: setOperator
     * It creates array of operators based on their names and amounts of their fields.
     *
     * Parameters:
     * names    {Array} Array of names
     * fieldAmounts {Array} Array of amounts of fields belonging to every name.
     *
     * Returns:
     * {Array} Array of operators
     */
    setOperator: function(names, fieldAmounts){
        var FIELD_NAME = "field";
        var operators = new Array();
        var operator = null;
        var fieldsAmount = 10;
        var name = "";
        var nameLang = "";
        var fieldNames = new Array();
        var fieldLangs = new Array();
        var fieldMinValues = new Array();
        var fieldMaxValues = new Array();
        var fieldDatatypes = new Array();
        var fieldExplanations = new Array();
        for(var actualName =0; actualName < names.length; actualName++){
            name = names[actualName];
            nameLang = names[actualName]+"CZ";
            fieldsAmount = fieldAmounts[actualName];
            fieldNames = new Array();
            fieldLangs = new Array();
            fieldMinValues = new Array();
            fieldMaxValues = new Array();
            fieldDatatypes = new Array();
            fieldExplanations = new Array();
            for(var actualField = 0; actualField < fieldsAmount; actualField++){
                fieldNames.push(FIELD_NAME+actualField);
                fieldLangs.push(FIELD_NAME+actualField+"CZ");
                fieldMinValues.push(actualField);
                fieldMaxValues.push(actualField+1);
                fieldDatatypes.push("integer");
                fieldExplanations.push(FIELD_NAME+actualField+"Expl");
            }
            operator = new Operator(name, nameLang, fieldNames, fieldLangs, fieldMinValues, fieldMaxValues, fieldDatatypes, fieldExplanations)
            operators.push(operator);
        }
        return operators;
    },

    /**
     * Function: setAttribute
     * It creates array of Attributes based on their names and amounts of their one category choices.
     *
     * Parameters:
     * names    {Array} Array of names
     * oneCategoryAmounts {Array} Array of amounts of one category choices belonging to every name.
     *
     * Returns:
     * {Array} Array of Attributes
     */
    setAttribute: function(names, oneCategoryAmounts){
        var attributes = new Array();
        var oneCategoryAmount = 0;
        var nameForOneCategory = "";
        for(var actualName = 0; actualName < names.length; actualName++){
            nameForOneCategory = names[actualName];
            var oneCategoryNames = new Array();
            oneCategoryAmount = oneCategoryAmounts[actualName];
            for(var actualField = 0; actualField < oneCategoryAmount; actualField++){
                oneCategoryNames.push("Field name"+actualField);
            }
            attributes.push(new Attribute(nameForOneCategory, oneCategoryNames));
        }
        return attributes;
    },

    /**
     * Function: setPossibleCoefficients
     * It creates array of AttribuetFields based on their names and amounts of their fields.
     *
     * Parameters:
     * typeNames    {Array} Array of names
     * fieldAmounts {Array} Array of amounts of fields belonging to every name.
     *
     * Returns:
     * {Array} Array of AttributeFields
     */
    setPossibleCoefficients: function(typeNames, fieldAmounts){
        var posCoefs = new Array();
        var posCoefAmount = 0;
        var category = "";
        var fieldNames = new Array();
        var fieldNamesLang = new Array();
        var minValues = new Array();
        var maxValues = new Array();
        var datatypes = new Array();
        var explanations = new Array();

        for(var actualType = 0; actualType < typeNames.length; actualType++){
            category = typeNames[actualType];
            fieldNames = new Array();
            fieldNamesLang = new Array();
            minValues = new Array();
            maxValues = new Array();
            datatypes = new Array();
            explanations = new Array();
            posCoefAmount = fieldAmounts[actualType];
            for(var actualField = 0; actualField < posCoefAmount; actualField++){
                fieldNames.push("FieldName "+actualField);
                fieldNamesLang.push("FieldNameCR "+actualField);
                minValues.push(actualField);
                maxValues.push(actualField * 2);
                datatypes.push("Datatype"+actualField);
                explanations.push("Explanation "+actualField);
            }
            posCoefs.push(new PossibleCoefficients(category, fieldNames, fieldNamesLang, minValues, maxValues, datatypes, explanations))
        }
        return posCoefs;
    }
})

/**
 * Class: TestClassItem
 * It creates item and fills it with necessarz informations.
 */
var TestClassItem = new Class({
    initialize: function(){
        this.attributes = new Array();
        this.operators = new Array();
    },

    /**
     * Function: setMaxMin
     * It sets to this class its attributes necessarz for function solveMaxMin.
     */
    setMinMax: function(){
        this.consMinNumberBBA = 1;
        this.consMaxNumberBBA = 2;
        this.antMinNumberBBA = 2;
        this.antMaxNumberBBA = 4;
        this.IMMinNumberBBA = -1;
        this.IMMaxNumberBBA = 3;
        this.minNumberBBA = 0;
        this.maxNumberBBA = 6;
    },
    
    /**
     * Function: setOperator
     * It creates array of operators based on their names
     *
     * Parameters:
     * names    {Array} Array of names
     *
     * Returns:
     * {Array} Array of amounts of fields
     */
    setOperator: function(names){
        var fieldAmounts = new Array();
        var FIELD_NAME = "field";
        this.operators = new Array();
        this.operatorsLang = new Array();
        var nameForFields = "";
        var fieldsAmount = 10
        for(var actualName =0; actualName < names.length; actualName++){
            this.operators.push(names[actualName]);
            this.operatorsLang.push(names[actualName]+"CZ");
            nameForFields = names[actualName].replace(/ /g,"");
            fieldsAmount = this.getRandomInt(1,20);
            fieldAmounts.push(fieldsAmount);
            this[nameForFields+"F"] = new Array();
            this[nameForFields+"FLang"] = new Array();
            this[nameForFields+"MinValue"] = new Array();
            this[nameForFields+"MaxValue"] = new Array();
            this[nameForFields+"Datatype"] = new Array();
            this[nameForFields+"Expl"] = new Array();
            for(var actualField = 0; actualField < fieldsAmount; actualField++){
                this[nameForFields+"F"].push(FIELD_NAME+actualField);
                this[nameForFields+"FLang"].push(FIELD_NAME+actualField+"CZ");
                this[nameForFields+"MinValue"].push(actualField);
                this[nameForFields+"MaxValue"].push(actualField+1);
                this[nameForFields+"Datatype"].push("integer");
                this[nameForFields+"Expl"].push(FIELD_NAME+actualField+"Expl");
            }
        }
        return fieldAmounts;
    },

    /**
     * Function: getRandomInt
     * It gets min and max and returns random integer between min and max.
     *
     * Parameters:
     * min     {Integer} minimal number
     * max     {Integer} maximal number
     *
     * Returns:
     * {Integer} Random number beteen min and max
     */
    getRandomInt: function(min, max)
    {
        return Math.floor(Math.random() * (max - min + 1)) + min;
    },

    /**
     * Function: setAttributes
     * It creates array of Attributes based on their names
     *
     * Parameters:
     * names    {Array} Array of names
     *
     * Returns:
     * {Array} Array of amounts of one category choices
     */
    setAttributes: function(names){
        this.attributes = new Array();
        var oneCategoryAmounts = new Array();
        var oneCategoryAmount = 0;
        var nameForOneCategory = "";
        for(var actualName = 0; actualName < names.length; actualName++){
            this.attributes.push(names[actualName]);
            nameForOneCategory = names[actualName];
            this[nameForOneCategory] = new Array();
            oneCategoryAmount = this.getRandomInt(1,20);
            oneCategoryAmounts.push(oneCategoryAmount);
            for(var actualField = 0; actualField < oneCategoryAmount; actualField++){
                this[nameForOneCategory].push("Field name"+actualField);
            }
        }
        return oneCategoryAmounts;
    },

    /**
     * Function: setPossibleCoefficients
     * It creates array of AttribuetFields based on their names
     *
     * Parameters:
     * typeNames    {Array} Array of names
     *
     * Returns:
     * {Array} Array of amounts of fields
     */
    setPossibleCoefficients: function(typeNames){
        var posCoefFields = new Array();
        this.posCoef = new Array();
        var posCoefAmount = 0;
        var MAXIMUM_EXPECTED_POS_COEF = 30;
        var nameForFields = "";
        for(var actualType = 0; actualType < typeNames.length; actualType++){
            this.posCoef.push(typeNames[actualType]);
            nameForFields = typeNames[actualType].replace(/ /g,"");
            this[nameForFields+"F"] = new Array();
            this[nameForFields+"FLang"] = new Array();
            this[nameForFields+"MinValue"] = new Array();
            this[nameForFields+"MaxValue"] = new Array();
            this[nameForFields+"Datatype"] = new Array();
            this[nameForFields+"Expl"] = new Array();
            posCoefAmount = this.getRandomInt(1,MAXIMUM_EXPECTED_POS_COEF);
            posCoefFields.push(posCoefAmount);
            for(var actualField = 0; actualField < posCoefAmount; actualField++){
                this[nameForFields+"F"].push("FieldName "+actualField);
                this[nameForFields+"FLang"].push("FieldNameCR "+actualField);
                this[nameForFields+"MinValue"].push(actualField);
                this[nameForFields+"MaxValue"].push(actualField * 2);
                this[nameForFields+"Datatype"].push("Datatype"+actualField);
                this[nameForFields+"Expl"].push("Explanation "+actualField);
            }
        }
        return posCoefFields;
    }
})

sit = new ServerInfoTest();
sit.test();