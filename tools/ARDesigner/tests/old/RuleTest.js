/**
 * Class: RuleTest
 * It tests whole functionality of class Rule
 */
var RuleTest = new Class({
    /**
     * Function: initialize
     * It creates instance of this class.
     */
    initialize: function(){

    },

    /**
     * Function: testAddData1
     * It tests function getRuleElements on adding and getting one element.
     */
    testAddData1: function(){
       this.rule = new Rule()
       this.rule.addNewElement("element")
       var result = this.rule.getRuleElements()
       var expectedResult = new Array();
       expectedResult.push("element")
       if(this.areArraysEqual(result, expectedResult)){
            console.log("testAddData1 is OK")
        }
        else{
            console.log("testAddData1 is WRONG. Result"+result+"ExpectedResult"+expectedResult);
        }
    },

    /**
     * Function: testAddData10
     * It tests function getRuleElements on adding and getting ten elements.
     */
    testAddData10: function(){
       this.rule = new Rule();
       this.rule.addNewElement("element1");
       this.rule.addNewElement("element2");
       this.rule.addNewElement("element3");
       this.rule.addNewElement("element4");
       this.rule.addNewElement("element5");
       this.rule.addNewElement("element6");
       this.rule.addNewElement("element7");
       this.rule.addNewElement("element8");
       this.rule.addNewElement("element9");
       this.rule.addNewElement("element10");
       var result = this.rule.getRuleElements();
       var expectedResult = new Array();
       expectedResult.push("element1");
       expectedResult.push("element2");
       expectedResult.push("element3");
       expectedResult.push("element4");
       expectedResult.push("element5");
       expectedResult.push("element6");
       expectedResult.push("element7");
       expectedResult.push("element8");
       expectedResult.push("element9");
       expectedResult.push("element10");
       if(this.areArraysEqual(result, expectedResult)){
            console.log("testAddData10 is OK");
        }
        else{
            console.log("testAddData10 is WRONG. Result"+result+"ExpectedResult"+expectedResult);
        }
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

    /**
     * Function: test
     * It calls all other testing functions within this class.
     */
    test: function(){
        this.testAddData1();
        this.testAddData10();
    }
})

ruleData = new RuleTest();
ruleData.test();
