/**                                                
 * Class: DepthNesting
 * This class contains informations about different operations in different depths.
 */
var DepthNesting = new Class({
    /**
     * Function: initialize
     * This function creates instance of class DepthNesting.
     */
    initialize: function(){
        this.allowed = new Array();
        this.DISJ = 0;
        this.CONJ = 1;
        this.NEG = 2;
        this.DISJ_TYPE = "or";
        this.CONJ_TYPE = "and";
        this.NEG_TYPE = "neg";
    },

    /**
     * Function: add
     * It adds new level with allowance of disjunction, conjunction, negation
     *
     * Parameters:
     * disj     {String} true or false
     * conj    {String} true or false
     * neg           {String} true or false
     *
     * Returns:
     * {String} OK nebo ERR: cislo chyby
     */
    add: function(disj, conj, neg){
        var specArray = new Array(3);
        specArray[this.DISJ] = disj;
        specArray[this.CONJ] = conj;
        specArray[this.NEG] = neg;
        this.allowed.push(specArray);
    },

    /**
     * Function: isAllowed
     * This function solves whether some type is allowed on some position.
     *
     * Parameters:
     * type      {String} disjunction, conjunction or negation.
     * position  {String} depth of nesting
     *
     * Returns:
     * {String} True of false
     */
    isAllowed: function(type, position){
        if(this.allowed.length == 0){
            return "false";
        }
        if(type == this.DISJ_TYPE){
            if(this.allowed[this.allowed.length - position - 1] == undefined){
                return "false";
            }
            return this.allowed[this.allowed.length - position - 1][this.DISJ];
        }
        else if(type == this.CONJ_TYPE){
            if(this.allowed[this.allowed.length - position - 1] == undefined){
                return "false";
            }
            return this.allowed[this.allowed.length - position - 1][this.CONJ];
        }
        else if(type == this.NEG_TYPE){
            if(this.allowed[this.allowed.length - position - 1] == undefined){
                return "false";
            }
            return this.allowed[this.allowed.length - position - 1][this.NEG];
        }
        else{
            return "false";
        }
    }
})

