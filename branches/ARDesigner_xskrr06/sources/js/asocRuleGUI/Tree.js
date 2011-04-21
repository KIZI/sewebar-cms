/**
 * Class: Tree
 * It is tree structure used to count depth of rule. It works similarly to the
 * algorithm used in serialization.
 */
var Tree = new Class({
    BOOL : "0",
    ATTR : "1",

    /**
     * Function: initialize
     * creates instance of class Tree.
     *
     * Returns:
     * {Void} Nothing
     */
    initialize: function(){
        this.root = null;
        this.id = 0;
    },

    /**
     * Function: solveRule
     * This function takes one part of rule(either antecedent or consequent) and
     * makes from it tree structure, stored in instance of this class. During
     * processing array of these elements, some of them are replaced with DBAs
     *
     * Parameters:
     * elementsIn  {Array} Array of ARElements.
     *
     * Returns:
     * {Void} Nothing
     */
    solveRule: function(elementsIn){
        var elements = elementsIn.slice(0);
        for(var i = 0; i < elements.length; i++){
            if(elements[i] == null){
                return;
            }
        }
        var rBracketPos = -1;
        var lBracketPos = -1;
        var plainDBA; // Type Node
        var length;
        var negPos = this.getNegation(elements);
        while(negPos != -1){
            var actualDba = new DBA(this.id++);
            actualDba.setConnective("neg");
            actualDba.addChild(new BBA(this.id++));
            elements.splice(negPos, 2, actualDba);
            negPos = this.getNegation(elements);
        }
        rBracketPos = this.getRBracket(elements);
        while(rBracketPos != -1){
            lBracketPos = this.getLBracket(elements,rBracketPos);
            plainDBA = this.solvePlainDBA(elements, lBracketPos, rBracketPos);
            length = rBracketPos - lBracketPos + 1;
            elements.splice(lBracketPos, length, plainDBA);
            rBracketPos = this.getRBracket(elements);
        }
        this.root = this.solvePlainDBA(elements, -1, elements.length);
    },

    /**
     * Function: solvePlainDBA
     * It receives array of elements and the borders surrounding part, which does
     * not contain brackets nor negation. From this array it creates on DBA
     * correctly containing all elements from base array.
     *
     * Parameters:
     * elements  {Array}  Array of ARElements and DBAs
     * lBracket  {Number} Position of starting bracket
     * rBracket  {Number} Position of ending bracket
     *
     * Returns:
     * {DBA} DBA representing this part of rule.
     */
    solvePlainDBA: function(elements, lBracket, rBracket){
        if(rBracket - lBracket < 3){
            return new BBA(this.id++);
        }
        var lastConnective = null;
        var actualDba = new DBA(this.id++);
        var supDba;
        for(var actualElement = lBracket + 1; actualElement < rBracket; actualElement++){
            if(!elements[actualElement].isElementBoolean()){
                if(elements[actualElement] instanceof DBA){
                    actualDba.addChild(elements[actualElement]);
                }
                else{
                    actualDba.addChild(new BBA(this.id++));
                }
            }
            else{
                if(lastConnective == null){
                    actualDba.setConnective(elements[actualElement].getType());
                    lastConnective = elements[actualElement].getType();
                }
                if(elements[actualElement].getType() != lastConnective){
                    supDba = new DBA(this.id++);
                    supDba.addChild(actualDba);
                    actualDba = supDba;
                    lastConnective = elements[actualElement].getType();
                }
            }
        }
        return actualDba;
    },

    /**
     * Function: getNegation
     * It gets position of first negation. If there is no negation in given
     * elements, it returns -1
     *
     * Parameters:
     * elements  {Array} Array containing ARElement's and DBA's
     *
     * Returns:
     * {Number} Position of negation or -1
     */
    getNegation: function(elements){
        for(var actualElement = 0; actualElement < elements.length; actualElement++){
            if(elements[actualElement].getType() == "neg"){
                return actualElement;
            }
        }
        return -1;
    },

    /**
     * Function: getRBracket
     * It gets position of first right bracket - ). If there is no right bracket
     * in given elements, it returns -1
     *
     * Parameters:
     * elements  {Array} Array containing ARElement's and DBA's
     *
     * Returns:
     * {Number} Position of right bracket or -1
     */
    getRBracket: function(elements){
        for(var actualElement = 0; actualElement < elements.length; actualElement++){
            if(elements[actualElement].getType() == "rbrac"){
                return actualElement;
            }
        }
        return -1;
    },

    /**
     * Function: getLBracket
     * It gets position of first left bracket - (. If there is no left bracket
     * in given elements, it returns -1
     *
     * Parameters:
     * elements  {Array} Array containing ARElement's and DBA's
     *
     * Returns:
     * {Number} Position of left bracket or -1
     */
    getLBracket: function(elements, rBracPos){
        for(var actualElement = rBracPos; actualElement > -1; actualElement--){
            if(elements[actualElement].getType() == "lbrac"){
                return actualElement;
            }
        }
        return -1;
    },

    /**
     * Function: getLevels
     * It counts levels of this tree. I am using simple width-first algorithm.
     * It starts with root if there is any and then continues to its children.
     * If any child is BBA, then it returns null as its children. I am on the
     * lowest level, when all elements on actualLevel are BBA's
     *
     * Returns:
     * {Number} Number representing amount of levels in this tree.
     */
    getLevels: function(){
        var actualLevel = null;
        if(this.root != null){
            actualLevel = this.root.getChildren();
        }
        else{
            return -1;
        }
        var newLevel = new Array();
        newLevel.push("begin");
        var depth = 0;
        while(actualLevel != null && newLevel.length > 0){
            depth++;
            newLevel = new Array();
            for(var levelElement = 0; levelElement < actualLevel.length; levelElement++){
                if(actualLevel[levelElement].getChildren() != null){
                    newLevel = newLevel.concat(actualLevel[levelElement].getChildren());
                }
            }
            actualLevel = newLevel;
        }
        return depth;
    }
});


/**
 * Class: Node
 * It is base class representing DBA and BBA. It should not be used. It's main
 * reason is to allow its subclassing.
 * It is Node from tree.
 */
var Node = new Class({
    /**
     * Function: initialize
     * It creates instance of class Node. Used only by its descendants.
     * 
     * Parameters:
     * elements  {Number} Id of this Node
     *
     * Returns:
     * {Void} Nothing
     */
    initialize: function(id){
        this.id = id;
    },

    /**
     * Function: getChildren
     * It returns all children of this Node.
     *
     * Returns:
     * {Array} Array of Nodes
     */
    getChildren: function(){
        return this.children;
    },

    /**
     * Function: getId
     * It returns Id of this node
     *
     * Returns:
     * {Number} Id
     */
    getId: function(){
        return this.id;
    },

    /**
     * Function: getType
     * It returns "attr". It is necessary so DBA's can act as a ARElement
     *
     * Returns:
     * {String} "attr"
     */
    getType: function(){
        return "attr";
    },

    /**
     * Function: isElementBoolean
     * It returns false. It is necessary so DBA's can act as a ARElement
     *
     * Returns:
     * {Boolean} false
     */
    isElementBoolean: function(){
        return false;
    }
})

/**
 * Class: DBA
 * Subclass of Node. It represents one derived boolean attribute and therefore
 * node of a tree, which is not list.
 */
var DBA = new Class({
    Extends: Node,

    /**
     * Function: initialize
     * It creates instance of class DBA and calls initialize function of its
     * parent.
     *
     * Parameters:
     * id  {Number} Id of this DBA
     *
     * Returns:
     * {Void} Nothing
     */
    initialize: function(id){
        this.connective = null;
        this.children = new Array();
        this.parent(id);
    },

    /**
     * Function: setConnective
     * It sets connective to this DBA.
     *
     * Parameters:
     * connective  {String}  Connective, usually "or", "and"
     *
     * Returns:
     * {Void} Nothing
     */
    setConnective: function(connective){
        this.connective = connective;
    },

    /**
     * Function: addChild
     * It adds another child to this DBA.
     *
     * Parameters:
     * child  {Node}  BBA or DBA
     *
     * Returns:
     * {Void} Nothing
     */
    addChild: function(child){
        this.children.push(child);
    }
})

/**
 * Class: DBA
 * Subclass of Node. It represents one basic boolean attribute and therefore
 * list of a tree.
 */
var BBA = new Class({
    Extends: Node,

    /**
     * Function: initialize
     * It creates instance of class BBA and calls initialize function of its
     * parent.
     *
     * Parameters:
     * id  {Number} Id of this BBA
     *
     * Returns:
     * {Void} Nothing
     */
    initialize: function(id){
        this.children = null;
        this.parent(id);
    }
})
