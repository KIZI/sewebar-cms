/**
 * Class: Control
 * This class shuold contains all controls of whether value is supported. At the
 * moment(August 2010) it contains control of integer whether it is between min
 * and max Value
 */
var Control = new Class({
    /**
     * Function: control
     * It controls whether elementValue is between maxValue and minValue
     *
     * Parameters:
     * datatype     {String} Datatype
     * minValue     {Number} Minimal value
     * maxValue     {Number} Maximal value
     * elementValue {Number} element value
     *
     * Returns:
     * {Boolean} true if it is ok.
     */
    control: function(datatype, minValue, maxValue, elementValue){
        var lowerDatatype = datatype.toLowerCase();
        if(lowerDatatype == "integer"){
            if(isNaN(elementValue.toFloat())){
                return false;
            }
            if(Math.ceil(elementValue) != Math.floor(elementValue)){
                return false;
            }
            if(elementValue < minValue || elementValue > maxValue){
                return false;
            }
        }
        if(lowerDatatype == "number"){
            if(isNaN(elementValue.toFloat())){
                return false;
            }
            if(elementValue < minValue || elementValue > maxValue){
                return false;
            }
        }
        if(lowerDatatype == "double"){
            if(isNaN(elementValue.toFloat())){
                return false;
            }
            if(elementValue < minValue || elementValue > maxValue){
                return false;
            }
        }
        return true
    }
})


