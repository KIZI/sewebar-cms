/**
 * Class: ControlsTest
 * It tests whole functionality of Control class.
 */
var ControlsTest = new Class({
    /**
     * Function: initialize
     * Create instance of this class and tested class, which is Control
     */
    initialize: function(){
        this.controlsClass = new Control()
    },

    /**
     * Function: testCorrectInteger
     * It tests function control. Input datas are correct and type is integer.
     */
    testCorrectInteger: function(){
        var dataType = "integer"
        var minValue = 10
        var maxValue = 20
        var value = 15
        var valueReceived = this.controlsClass.control(dataType, minValue, maxValue, value)
        var expectedResult = true
        if(valueReceived == expectedResult){
            console.log("TestCorrectInteger is OK")
        }
        else{
            console.log("TestCorrectInteger is WRONG. Result"+valueReceived+"ExpectedResult"+expectedResult);
        }
    },

    /**
     * Function: testCorrectNumber
     * It tests function control. Input datas are correct and type is number.
     */
    testCorrectNumber: function(){
        var dataType = "number"
        var minValue = 10
        var maxValue = 20
        var value = 15
        var valueReceived = this.controlsClass.control(dataType, minValue, maxValue, value)
        var expectedResult = true
        if(valueReceived == expectedResult){
            console.log("testCorrectNumber is OK")
        }
        else{
            console.log("testCorrectNumber is WRONG. Result"+valueReceived+"ExpectedResult"+expectedResult);
        }
    },

    /**
     * Function: testCorrectDouble
     * It tests function control. Input datas are correct and type is double.
     */
    testCorrectDouble: function(){
        var dataType = "double"
        var minValue = 10
        var maxValue = 20
        var value = 15.4
        var valueReceived = this.controlsClass.control(dataType, minValue, maxValue, value)
        var expectedResult = true
        if(valueReceived == expectedResult){
            console.log("testCorrectDouble is OK")
        }
        else{
            console.log("testCorrectDouble is WRONG. Result"+valueReceived+"ExpectedResult"+expectedResult);
        }
    },

    /**
     * Function: testinCorrectIntegerLow
     * It tests function control. Input datas are wrong(low) and type is integer.
     */
    testinCorrectIntegerLow: function(){
        var dataType = "integer"
        var minValue = 10
        var maxValue = 20
        var value = 8
        var valueReceived = this.controlsClass.control(dataType, minValue, maxValue, value)
        var expectedResult = false
        if(valueReceived == expectedResult){
            console.log("testinCorrectIntegerLow is OK")
        }
        else{
            console.log("testinCorrectIntegerLow is WRONG. Result"+valueReceived+"ExpectedResult"+expectedResult);
        }
    },

    /**
     * Function: testinCorrectNumberLow
     * It tests function control. Input datas are wrong(low) and type is number.
     */
    testinCorrectNumberLow: function(){
        var dataType = "number"
        var minValue = 10
        var maxValue = 20
        var value = 7
        var valueReceived = this.controlsClass.control(dataType, minValue, maxValue, value)
        var expectedResult = false
        if(valueReceived == expectedResult){
            console.log("testinCorrectNumberLow is OK")
        }
        else{
            console.log("testinCorrectNumberLow is WRONG. Result"+valueReceived+"ExpectedResult"+expectedResult);
        }
    },

    /**
     * Function: testinCorrectDoubleLow
     * It tests function control. Input datas are wrong(low) and type is double.
     */
    testinCorrectDoubleLow: function(){
        var dataType = "double"
        var minValue = 10
        var maxValue = 20
        var value = 9
        var valueReceived = this.controlsClass.control(dataType, minValue, maxValue, value)
        var expectedResult = false
        if(valueReceived == expectedResult){
            console.log("testinCorrectDoubleLow is OK")
        }
        else{
            console.log("testinCorrectDoubleLow is WRONG. Result"+valueReceived+"ExpectedResult"+expectedResult);
        }
    },

    /**
     * Function: testinCorrectIntegerHigh
     * It tests function control. Input datas are wrong(high) and type is integer.
     */
    testinCorrectIntegerHigh: function(){
        var dataType = "integer"
        var minValue = 10
        var maxValue = 20
        var value = 30
        var valueReceived = this.controlsClass.control(dataType, minValue, maxValue, value)
        var expectedResult = false
        if(valueReceived == expectedResult){
            console.log("testinCorrectIntegerHigh is OK")
        }
        else{
            console.log("testinCorrectIntegerHigh is WRONG. Result"+valueReceived+"ExpectedResult"+expectedResult);
        }
    },

    /**
     * Function: testinCorrectNumberHigh
     * It tests function control. Input datas are wrong(high) and type is number.
     */
    testinCorrectNumberHigh: function(){
        var dataType = "number"
        var minValue = 10
        var maxValue = 20
        var value = 22
        var valueReceived = this.controlsClass.control(dataType, minValue, maxValue, value)
        var expectedResult = false
        if(valueReceived == expectedResult){
            console.log("testinCorrectNumberHigh is OK")
        }
        else{
            console.log("testinCorrectNumberHigh is WRONG. Result"+valueReceived+"ExpectedResult"+expectedResult);
        }
    },

    /**
     * Function: testinCorrectDoubleHigh
     * It tests function control. Input datas are incorrect(higher) and type is double.
     */
    testinCorrectDoubleHigh: function(){
        var dataType = "double"
        var minValue = 10
        var maxValue = 20
        var value = 22.1
        var valueReceived = this.controlsClass.control(dataType, minValue, maxValue, value)
        var expectedResult = false
        if(valueReceived == expectedResult){
            console.log("testinCorrectDoubleHigh is OK")
        }
        else{
            console.log("testinCorrectDoubleHigh is WRONG. Result"+valueReceived+"ExpectedResult"+expectedResult);
        }
    },

    /**
     * Function: testCorrectDoubleBorder
     * It tests function control. Input datas are correct border and type is double.
     */
    testCorrectDoubleBorder: function(){
        var dataType = "double"
        var minValue = 10.0
        var maxValue = 20
        var value = 10.0
        var valueReceived = this.controlsClass.control(dataType, minValue, maxValue, value)
        var expectedResult = true
        if(valueReceived == expectedResult){
            console.log("testCorrectDoubleBorder is OK")
        }
        else{
            console.log("testCorrectDoubleBorder is WRONG. Result"+valueReceived+"ExpectedResult"+expectedResult);
        }
    },

    /**
     * Function: test
     * It calls are test functions within this class.
     */
    test: function(){
        this.testCorrectNumber()
        this.testCorrectInteger()
        this.testCorrectDouble()
        this.testCorrectDoubleBorder();
        this.testinCorrectDoubleHigh();
        this.testinCorrectNumberHigh();
        this.testinCorrectIntegerHigh();
        this.testinCorrectDoubleLow();
        this.testinCorrectNumberLow();
        this.testinCorrectIntegerLow();
    }
})

window.addEvent('domready', function(){
    var testSk = new ControlsTest();
    testSk.test();
})


