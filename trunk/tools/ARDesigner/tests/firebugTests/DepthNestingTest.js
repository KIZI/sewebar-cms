/**
 * Class: DepthNestingTest
 * It tests whole functionality of cladd DepthNesting
 */
var DepthNestingTest = new Class({
    /**
     * Function: initialize
     * It creates instance of this class and instance of class DepthNesting.
     * This DepthNesting allows four levels of nesting.
     * First: disj(true), conj(true), neg(false)
     * Second: disj(false), conj(true), neg(false)
     * Third: disj(true), conj(false), neg(true)
     * Fourth: disj(false), conj(true), neg(true)
     */
    initialize: function(){
        this.depthNesting = new DepthNesting()
        this.depthNesting.add("true", "true", "false");
        this.depthNesting.add("false", "true", "false");
        this.depthNesting.add("true", "false", "true");
        this.depthNesting.add("false", "true", "true");

        this.CONJ = "and";
        this.DISJ = "or";
        this.NEG = "neg";
    },

    /**
     * Function: testConjLev1
     * It tests whether conjunction on level 1 is allowed
     */
    testConjLev1: function(){
        var type = this.CONJ;
        var level = 0;
        var result = this.depthNesting.isAllowed(type, level)
        var expectedResult = "true"
        if(result == expectedResult){
            console.log("testConjLev1 is OK")
        }
        else{
            console.log("testConjLev1 is WRONG Reuslt: "+result+"ExpectedResult"+expectedResult)
        }
    },

    /**
     * Function: testConjLev2
     * It tests whether conjunction on level 2 is allowed
     */
    testConjLev2: function(){
        var type = this.CONJ;
        var level = 1;
        var result = this.depthNesting.isAllowed(type, level)
        var expectedResult = "false"
        if(result == expectedResult){
            console.log("testConjLev2 is OK")
        }
        else{
            console.log("testConjLev2 is WRONG Reuslt: "+result+"ExpectedResult"+expectedResult)
        }
    },

    /**
     * Function: testConjLev3
     * It tests whether conjunction on level 3 is allowed
     */
    testConjLev3: function(){
        var type = this.CONJ;
        var level = 2;
        var result = this.depthNesting.isAllowed(type, level)
        var expectedResult = "true"
        if(result == expectedResult){
            console.log("testConjLev3 is OK")
        }
        else{
            console.log("testConjLev3 is WRONG Reuslt: "+result+"ExpectedResult"+expectedResult)
        }
    },

    /**
     * Function: testConjLev4
     * It tests whether conjunction on level 4 is allowed
     */
    testConjLev4: function(){
        var type = this.CONJ;
        var level = 3;
        var result = this.depthNesting.isAllowed(type, level)
        var expectedResult = "true"
        if(result == expectedResult){
            console.log("testConjLev4 is OK")
        }
        else{
            console.log("testConjLev4 is WRONG Reuslt: "+result+"ExpectedResult"+expectedResult)
        }
    },

    /**
     * Function: testDisjLev1
     * It tests whether disjunction on level 1 is allowed
     */
    testDisjLev1: function(){
        var type = this.DISJ;
        var level = 0;
        var result = this.depthNesting.isAllowed(type, level)
        var expectedResult = "false"
        if(result == expectedResult){
            console.log("testDisjLev1 is OK")
        }
        else{
            console.log("testDisjLev1 is WRONG Reuslt: "+result+"ExpectedResult"+expectedResult)
        }
    },

    /**
     * Function: testDisjLev2
     * It tests whether disjunction on level 2 is allowed
     */
    testDisjLev2: function(){
        var type = this.DISJ;
        var level = 1;
        var result = this.depthNesting.isAllowed(type, level)
        var expectedResult = "true"
        if(result == expectedResult){
            console.log("testDisjLev2 is OK")
        }
        else{
            console.log("testDisjLev2 is WRONG Reuslt: "+result+"ExpectedResult"+expectedResult)
        }
    },

    /**
     * Function: testDisjLev3
     * It tests whether disjunction on level 3 is allowed
     */
    testDisjLev3: function(){
        var type = this.DISJ;
        var level = 2;
        var result = this.depthNesting.isAllowed(type, level)
        var expectedResult = "false"
        if(result == expectedResult){
            console.log("testDisjLev3 is OK")
        }
        else{
            console.log("testDisjLev3 is WRONG Reuslt: "+result+"ExpectedResult"+expectedResult)
        }
    },

    /**
     * Function: testDisjLev4
     * It tests whether disjunction on level 4 is allowed
     */
    testDisjLev4: function(){
        var type = this.DISJ;
        var level = 3;
        var result = this.depthNesting.isAllowed(type, level)
        var expectedResult = "true"
        if(result == expectedResult){
            console.log("testDisjLev4 is OK")
        }
        else{
            console.log("testDisjLev4 is WRONG Reuslt: "+result+"ExpectedResult"+expectedResult)
        }
    },

    /**
     * Function: testNegLev1
     * It tests whether negation on level 1 is allowed
     */
    testNegLev1: function(){
        var type = this.NEG;
        var level = 0;
        var result = this.depthNesting.isAllowed(type, level)
        var expectedResult = "true"
        if(result == expectedResult){
            console.log("testNegLev1 is OK")
        }
        else{
            console.log("testNegLev1 is WRONG Reuslt: "+result+"ExpectedResult"+expectedResult)
        }
    },

    /**
     * Function: testNegLev2
     * It tests whether negation on level 2 is allowed
     */
    testNegLev2: function(){
        var type = this.NEG;
        var level = 1;
        var result = this.depthNesting.isAllowed(type, level)
        var expectedResult = "true"
        if(result == expectedResult){
            console.log("testNegLev2 is OK")
        }
        else{
            console.log("testNegLev2 is WRONG Reuslt: "+result+"ExpectedResult"+expectedResult)
        }
    },

    /**
     * Function: testNegLev3
     * It tests whether negation on level 3 is allowed
     */
    testNegLev3: function(){
        var type = this.NEG;
        var level = 2;
        var result = this.depthNesting.isAllowed(type, level)
        var expectedResult = "false"
        if(result == expectedResult){
            console.log("testNegLev3 is OK")
        }
        else{
            console.log("testNegLev3 is WRONG Reuslt: "+result+"ExpectedResult"+expectedResult)
        }
    },

    /**
     * Function: testNegLev4
     * It tests whether negation on level 4 is allowed
     */
    testNegLev4: function(){
        var type = this.NEG;
        var level = 3;
        var result = this.depthNesting.isAllowed(type, level)
        var expectedResult = "false"
        if(result == expectedResult){
            console.log("testNegLev4 is OK")
        }
        else{
            console.log("testNegLev4 is WRONG Reuslt: "+result+"ExpectedResult"+expectedResult)
        }
    },

    /**
     * Function: test
     * It only calls all test methods of this class.
     */
    test: function(){
        this.testConjLev1();
        this.testConjLev2();
        this.testConjLev3();
        this.testConjLev4();
        this.testDisjLev1();
        this.testDisjLev2();
        this.testDisjLev3();
        this.testDisjLev4();
        this.testNegLev1();
        this.testNegLev2();
        this.testNegLev3();
        this.testNegLev4();
    }
})

var dn = new DepthNestingTest();
dn.test();
