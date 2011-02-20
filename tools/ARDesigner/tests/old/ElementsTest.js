/**
 * Class: BooleanClTest
 * It tests whole functionality of class BooleanCl
 */
var BooleanClTest = new Class({
    /**
     * Function: initialize
     * It just creates instance of this class and tested class BooleanCl
     */
    initialize: function(){
        this.booleanCl = new BooleanCl("AND","and");
    },

    /**
     * Function: testGetType
     * It tests function getType
     */
    testGetType: function(){
        var result = this.booleanCl.getType()
        var expectedResult = "and"
        if(result == expectedResult){
            console.log("testGetType is OK")
        }
        else{
            console.log("testGetType is WRONG Reuslt: "+result+"ExpectedResult"+expectedResult)
        }
    },

    /**
     * Function: testGetName
     * It just tests function getName
     */
    testGetName: function(){
        var result = this.booleanCl.getName()
        var expectedResult = "AND"
        if(result == expectedResult){
            console.log("testGetName is OK")
        }
        else{
            console.log("testGetName is WRONG Reuslt: "+result+"ExpectedResult"+expectedResult)
        }
    },

    /**
     * Function: test
     * It just calls all testing functions within this class.
     */
    test: function(){
        this.testGetType();
        this.testGetName();
    }
})

console.log("Boolean test");
bol = new BooleanClTest();
bol.test();

/**
 * Class: AttributeTest
 * It tests whole functionality of class Attribute
 */
var AttributeTest = new Class({
    /**
     * Function: initialize
     * It just creates instance of this class and tested class Attribute
     */
    initialize: function(){
        this.atribute = new Attribute("attribute","info","explanation");
    },

    /**
     * Function: testGetType
     * It just tests function getType
     */
    testGetType: function(){
        var result = this.atribute.getType()
        var expectedResult = "attr"
        if(result == expectedResult){
            console.log("testGetType is OK")
        }
        else{
            console.log("testGetType is WRONG Reuslt: "+result+"ExpectedResult"+expectedResult)
        }
    },

    /**
     * Function: testGetName
     * It just tests function getName
     */
    testGetName: function(){
        var result = this.atribute.getName()
        var expectedResult = "attribute"
        if(result == expectedResult){
            console.log("testGetName is OK")
        }
        else{
            console.log("testGetName is WRONG Reuslt: "+result+"ExpectedResult"+expectedResult)
        }
    },

    /**
     * Function: testGetOneCategoryInfo
     * It just tests function getOneCategoryInfo
     */
    testGetOneCategoryInfo: function(){
        var result = this.atribute.getOneCategoryInfo()
        var expectedResult = "info"
        if(result == expectedResult){
            console.log("testGetOneCategoryInfo is OK")
        }
        else{
            console.log("testGetOneCategoryInfo is WRONG Reuslt: "+result+"ExpectedResult"+expectedResult)
        }
    },

    /**
     * Function: test
     * It just calls all other test functions within this class.
     */
    test: function(){
        this.testGetType();
        this.testGetName();
        this.testGetOneCategoryInfo();
    }
})

console.log("Attribute test");
bol = new AttributeTest();
bol.test();

/**
 * Class: OperatorTest
 * It tests whole functionality of class Operator
 */
var OperatorTest = new Class({
    /**
     * Function: initialize
     * It just creates instance of this class and tested class Operator
     */
    initialize: function(){
        this.operator = new Operator("name","nameLang","fieldNames", "fieldLangs", "fieldMinValues", "fieldMaxValues","datatypes","explanation");
    },

    /**
     * Function: testGetType
     * It just tests function getType
     */
    testGetType: function(){
        var result = this.operator.getType()
        var expectedResult = "oper"
        if(result == expectedResult){
            console.log("testGetType is OK")
        }
        else{
            console.log("testGetType is WRONG Reuslt: "+result+"ExpectedResult"+expectedResult)
        }
    },

    /**
     * Function: testGetName
     * It just tests function getName
     */
    testGetName: function(){
        var result = this.operator.getName()
        var expectedResult = "name"
        if(result == expectedResult){
            console.log("testGetName is OK")
        }
        else{
            console.log("testGetName is WRONG Reuslt: "+result+"ExpectedResult"+expectedResult)
        }
    },

    /**
     * Function: testGetNameLang
     * It just tests function getNameLang
     */
    testGetNameLang: function(){
        var result = this.operator.getNameLang()
        var expectedResult = "nameLang"
        if(result == expectedResult){
            console.log("testGetNameLang is OK")
        }
        else{
            console.log("testGetNameLang is WRONG Reuslt: "+result+"ExpectedResult"+expectedResult)
        }
    },

    /**
     * Function: testGetFieldNames
     * It just tests function getFieldNames
     */
    testGetFieldNames: function(){
        var result = this.operator.getFieldNames()
        var expectedResult = "fieldNames"
        if(result == expectedResult){
            console.log("testGetFieldNames is OK")
        }
        else{
            console.log("testGetFieldNames is WRONG Reuslt: "+result+"ExpectedResult"+expectedResult)
        }
    },

    /**
     * Function: testGetFieldLangs
     * It just tests function getFieldLangs
     */
    testGetFieldLangs: function(){
        var result = this.operator.getFieldLangs()
        var expectedResult = "fieldLangs"
        if(result == expectedResult){
            console.log("testGetFieldLangs is OK")
        }
        else{
            console.log("testGetFieldLangs is WRONG Reuslt: "+result+"ExpectedResult"+expectedResult)
        }
    },

    /**
     * Function: testGetFieldMinValues
     * It just tests function getFieldMinValues
     */
    testGetFieldMinValues: function(){
        var result = this.operator.getFieldMinValues()
        var expectedResult = "fieldMinValues"
        if(result == expectedResult){
            console.log("testGetFieldMinValues is OK")
        }
        else{
            console.log("testGetFieldMinValues is WRONG Reuslt: "+result+"ExpectedResult"+expectedResult)
        }
    },

    /**
     * Function: testGetFieldMaxValues
     * It just tests function getFieldMaxValues
     */
    testGetFieldMaxValues: function(){
        var result = this.operator.getFieldMaxValues()
        var expectedResult = "fieldMaxValues"
        if(result == expectedResult){
            console.log("testGetFieldMaxValues is OK")
        }
        else{
            console.log("testGetFieldMaxValues is WRONG Reuslt: "+result+"ExpectedResult"+expectedResult)
        }
    },

    /**
     * Function: testGetxplanation
     * It just tests function getExplanation
     */
    testGetExplanation: function(){
        var result = this.operator.getExplanation()
        var expectedResult = "explanation"
        if(result == expectedResult){
            console.log("testGetExplanation is OK")
        }
        else{
            console.log("testGetExplanation is WRONG Reuslt: "+result+"ExpectedResult"+expectedResult)
        }
    },

    /**
     * Function: testGetFieldDatatype
     * It just tests function getFieldMaxValues
     */
    testGetFieldDatatype: function(){
        var result = this.operator.getFieldDatatype()
        var expectedResult = "datatypes"
        if(result == expectedResult){
            console.log("testGetFieldDatatype is OK")
        }
        else{
            console.log("testGetFieldDatatype is WRONG Reuslt: "+result+"ExpectedResult"+expectedResult)
        }
    },

    /**
     * Function: test
     * It just calls all testing functions within this class.
     */
    test: function(){
        this.testGetType();
        this.testGetName();
        this.testGetExplanation();
        this.testGetNameLang();
        this.testGetFieldNames();
        this.testGetFieldLangs();
        this.testGetFieldMinValues();
        this.testGetFieldMaxValues();
        this.testGetExplanation();
        this.testGetFieldDatatype();
    }
})

console.log("Operator test");
bol = new OperatorTest();
bol.test();