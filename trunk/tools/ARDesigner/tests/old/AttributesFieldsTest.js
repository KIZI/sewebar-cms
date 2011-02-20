/**
 * Class: AttributeFieldsTest
 * It tests whole functionalitz of AttributeFields class.
 */
var AttributeFieldsTest = new Class({
    /**
     * Function: initialize
     * It creates instance of this class. And creates tested object: AttributeFields
     */
    initialize:function(){
        var category = "One category"
        var fieldName = "Name"
        var fieldNameLang = "Jmeno"
        var minValue = 5
        var maxValue = 20
        var datatype = "number"
        var explanation = "vysvetleni"
        this.attributeFields = new PossibleCoefficients(category, fieldName, fieldNameLang, minValue, maxValue, datatype,explanation);
    },

    /**
     * Function: testGetCategory
     * It tests function getCategory
     */
    testGetCategory: function(){
        var result = this.attributeFields.getCategory()
        var expectedResult = "One category";
        if(result == expectedResult){
            console.log("testGetCategory is OK")
        }
        else{
            console.log("testGetCategory is WRONG Reuslt: "+result+"ExpectedResult"+expectedResult)
        }
    },

    /**
     * Function: testGetName
     * It tests function getfName
     */
    testGetName: function(){
        var result = this.attributeFields.getfName()
        var expectedResult = "Name";
        if(result == expectedResult){
            console.log("testGetName is OK")
        }
        else{
            console.log("testGetName is WRONG Reuslt: "+result+"ExpectedResult"+expectedResult)
        }
    },

    /**
     * Function: testGetLangName
     * It tests function getfNameLang
     */
    testGetLangName: function(){
        var result = this.attributeFields.getfNameLang()
        var expectedResult = "Jmeno";
        if(result == expectedResult){
            console.log("testGetLangName is OK")
        }
        else{
            console.log("testGetLangName is WRONG Reuslt: "+result+"ExpectedResult"+expectedResult)
        }
    },

    /**
     * Function: testGetMinValue
     * It tests function getMinValue
     */
    testGetMinValue: function(){
        var result = this.attributeFields.getMinValue()
        var expectedResult = 5;
        if(result == expectedResult){
            console.log("testGetMinValue is OK")
        }
        else{
            console.log("testGetMinValue is WRONG Reuslt: "+result+"ExpectedResult"+expectedResult)
        }
    },

    /**
     * Function: testGetMaxValue
     * It tests function getMaxValue
     */
    testGetMaxValue: function(){
        var result = this.attributeFields.getMaxValue()
        var expectedResult = 20;
        if(result == expectedResult){
            console.log("testGetMaxValue is OK")
        }
        else{
            console.log("testGetMaxValue is WRONG Reuslt: "+result+"ExpectedResult"+expectedResult)
        }
    },

    /**
     * Function: testGetDatatype
     * It tests function get Datatype
     */
    testGetDatatype: function(){
        var result = this.attributeFields.getDatatype()
        var expectedResult = "number";
        if(result == expectedResult){
            console.log("testGetDatatype is OK")
        }
        else{
            console.log("testGetDatatype is WRONG Reuslt: "+result+"ExpectedResult"+expectedResult)
        }
    },

    /**
     * Function: testGetExplanation
     * It tests funciton getExplanation
     */
    testGetExplanation: function(){
        var result = this.attributeFields.getExplanation()
        var expectedResult = "vysvetleni";
        if(result == expectedResult){
            console.log("testGetExplanation is OK")
        }
        else{
            console.log("testGetExplanation is WRONG Reuslt: "+result+"ExpectedResult"+expectedResult)
        }
    },

    /**
     * Function: tests
     * It justs calls all test methods in this class.
     */
    test: function(){
        this.testGetExplanation();
        this.testGetDatatype();
        this.testGetMaxValue();
        this.testGetMinValue();
        this.testGetLangName();
        this.testGetName();
        this.testGetCategory();
    }
})

var attrTest = new AttributeFieldsTest();
attrTest.test();
