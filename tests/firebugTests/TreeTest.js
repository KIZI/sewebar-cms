/**  
 * Class: TreeTest
 * It provides tests for class Test
 */
var TreeTest = new Class({
    /**
     * Function: initialize
     * creates instance of class TreeTest.
     *
     * Returns:
     * {Void} Nothing
     */
    initialize: function(){
        this.tree = new Tree();
    },

    /**
     * Function: allTests
     * It starts all tests which this class contains. Info about whether
     * test passed or not is written in console. Console is in google chrome
     * and in firebug extension for Firefox.
     */
    allTests: function(){
        this.testGetNegation();
        this.testGetRBrac();
        this.testGetLBrac();
        this.testSolvePlainDBA1();
        this.testSolvePlainDBA2();
        this.testSolvePlainDBA3();
        this.testSolvePlainDBA4();
        this.testSolvePlainDBA5();
        this.testSolveRule();
        this.testSolveRule1();
        this.testSolveRule2();
        this.testSolveRule3();
        this.testGetLevels1();
        this.testGetLevels2();
        this.testGetLevels3();
        this.testGetLevels4();
    },

    /**
     * Function: testGetNegation
     * It tests getNegation function of Tree.
     */
    testGetNegation: function(){
        var elements = new Array();
        var and = new BooleanCl("AND","and");
        var or = new BooleanCl("OR","or");
        var neg = new BooleanCl("NEG","neg");
        var attr = new Attribute("attr1","",new Array());
        elements.push(attr);
        elements.push(and);
        elements.push(attr);
        elements.push(or);
        elements.push(neg);
        elements.push(attr);
        var position = this.tree.getNegation(elements);
        if(position != 4){
            console.log("testGetNegation is WRONG.");
        }
        else{
            console.log("testGetNegation is CORRECT.");
        }
    },

    /**
     * Function: testGetRBrac
     * It tests getRBracket function of Tree.
     */
    testGetRBrac: function(){
        var elements = new Array();
        var rbrac = new BooleanCl(")","rbrac");
        var and = new BooleanCl("AND","and");
        var or = new BooleanCl("OR","or");
        var neg = new BooleanCl("NEG","neg");
        var attr = new Attribute("attr1","",new Array());
        elements.push(attr);
        elements.push(and);
        elements.push(attr);
        elements.push(or);
        elements.push(neg);
        elements.push(attr);
        elements.push(rbrac);
        elements.push(attr);
        elements.push(neg);
        elements.push(attr);
        var position = this.tree.getRBracket(elements);
        if(position != 6){
            console.log("testGetRBracket is WRONG.");
        }
        else{
            console.log("testGetRBracket is CORRECT.");
        }
    },

    /**
     * Function: testGetLBrac
     * It tests getLBracket function of Tree.
     */
    testGetLBrac: function(){
        var elements = new Array();
        var lbrac = new BooleanCl("(","lbrac");
        var rbrac = new BooleanCl(")","rbrac");
        var or = new BooleanCl("OR","or");
        var neg = new BooleanCl("NEG","neg");
        var attr = new Attribute("attr1","",new Array());
        elements.push(attr);
        elements.push(lbrac);
        elements.push(attr);
        elements.push(or);
        elements.push(neg);
        elements.push(attr);
        elements.push(rbrac);
        elements.push(attr);
        elements.push(neg);
        elements.push(attr);
        var position = this.tree.getLBracket(elements, 6);
        if(position != 1){
            console.log("testGetLBrac is WRONG. "+position);
        }
        else{
            console.log("testGetLBrac is CORRECT.");
        }
    },

    /**
     * Function: testSolvePlainDBA1
     * It tests solvePlainDBA function of Tree. Function gets array
     * of elements consisting of two attributes and boolean and. Correct
     * answer is one DBA containing these two.
     */
    testSolvePlainDBA1: function(){
        var elements = new Array();
        var and = new BooleanCl("AND","and");
        var attr = new Attribute("attr1","",new Array());
        elements.push(attr);
        elements.push(and);
        elements.push(attr);
        var actualDBA = this.tree.solvePlainDBA(elements, -1, elements.length);
        if(actualDBA == null){
            console.log("testSolvePlainDBA1 is WRONG. Actual DBA is null");
            return
        }
        var resultChildren = actualDBA.getChildren();
        if(resultChildren == null || resultChildren.length < 2){
            console.log("testSolvePlainDBA1 is WRONG. ResultChildren is null or too short.");
            return
        }
        console.log("testSolvePlainDBA1 is CORRECT");
    },

    /**
     * Function: testSolvePlainDBA2
     * It tests solvePlainDBA function of Tree. Function gets array
     * of elements consisting of three attributes and  two booleans (and, or).
     */
    testSolvePlainDBA2: function(){
        var elements = new Array();
        var or = new BooleanCl("OR","or");
        var and = new BooleanCl("AND","and");
        var attr = new Attribute("attr1","",new Array());
        elements.push(attr);
        elements.push(and);
        elements.push(attr);
        elements.push(or);
        elements.push(attr);
        var actualDBA = this.tree.solvePlainDBA(elements, -1, elements.length);
        if(actualDBA == null){
            console.log("testSolvePlainDBA2 is WRONG. Actual DBA is null");
            return;
        }
        var resultChildren = actualDBA.getChildren();
        if(resultChildren == null || resultChildren.length < 2){
            console.log("testSolvePlainDBA2 is WRONG. ResultChildren is null or too short.");
            return;
        }
        var hasChild = 0;
        for(var i =0; i < resultChildren.length; i++){
            if(resultChildren[i].getChildren()!= null){
                var deti = resultChildren[i].getChildren();
                hasChild++;
                for(var j=0; j < deti.length; j++){
                    if(deti[j].getChildren() != null){
                        console.log("testSolvePlainDBA2 is WRONG. Problem in second depth level.");
                        return;
                    }
                }
            }
        }
        if(hasChild != 1){
            console.log("testSolvePlainDBA2 is WRONG");
            return;
        }
        console.log("testSolvePlainDBA2 is CORRECT");
    },

    /**
     * Function: testSolvePlainDBA3
     * It tests solvePlainDBA function of Tree. Function gets array
     * of elements consisting of three attributes and two booleans (and).
     */
    testSolvePlainDBA3: function(){
        var elements = new Array();
        var and = new BooleanCl("AND","and");
        var attr = new Attribute("attr1","",new Array());
        elements.push(attr);
        elements.push(and);
        elements.push(attr);
        elements.push(and);
        elements.push(attr);
        var actualDBA = this.tree.solvePlainDBA(elements, -1, elements.length);
        if(actualDBA == null){
            console.log("testSolvePlainDBA3 is WRONG. Actual DBA is null");
            return;
        }
        var resultChildren = actualDBA.getChildren();
        if(resultChildren == null || resultChildren.length < 3){
            console.log("testSolvePlainDBA3 is WRONG. ResultChildren is null or too short.")
            return;
        }
        console.log("testSolvePlainDBA3 is CORRECT");
    },

    /**
     * Function: testSolvePlainDBA4
     * It tests solvePlainDBA function of Tree. Function gets array
     * of elements consisting of three attributes and two booleans (and).
     * Array contains also some other elements which are unimportant.
     * I am testing different start and end than beginning and end of
     * the array.
     */
    testSolvePlainDBA4: function(){
        var elements = new Array();
        var and = new BooleanCl("AND","and");
        var attr = new Attribute("attr1","",new Array());
        var lbrac = new BooleanCl("(","lbrac");
        var rbrac = new BooleanCl(")","rbrac");
        elements.push(attr);
        elements.push(lbrac);
        elements.push(attr);
        elements.push(and);
        elements.push(attr);
        elements.push(and);
        elements.push(attr);
        elements.push(rbrac);
        elements.push(attr);
        elements.push(attr);
        var rBrackPos = this.tree.getRBracket(elements);
        var lBrackPos = this.tree.getLBracket(elements, rBrackPos);
        var actualDBA = this.tree.solvePlainDBA(elements, lBrackPos, rBrackPos);
        if(actualDBA == null){
            console.log("testSolvePlainDBA4 is WRONG. Actual DBA is null");
            return;
        }
        var resultChildren = actualDBA.getChildren();
        if(resultChildren == null || resultChildren.length < 3){
            console.log("testSolvePlainDBA4 is WRONG. ResultChildren is null or too short.")
            return;
        }
        console.log("testSolvePlainDBA4 is CORRECT");
    },

    /**
     * Function: testSolvePlainDBA5
     * It tests solvePlainDBA function of Tree. Function gets array
     * of elements consisting of only one attributes in brackets.
     */
    testSolvePlainDBA5: function(){
        var elements = new Array();
        var and = new BooleanCl("AND","and");
        var attr = new Attribute("attr1","",new Array());
        var lbrac = new BooleanCl("(","lbrac");
        var rbrac = new BooleanCl(")","rbrac");
        elements.push(lbrac);
        elements.push(attr);
        elements.push(rbrac);
        var rBrackPos = this.tree.getRBracket(elements);
        var lBrackPos = this.tree.getLBracket(elements, rBrackPos);
        var actualDBA = this.tree.solvePlainDBA(elements, lBrackPos, rBrackPos);
        if(actualDBA == null){
            console.log("testSolvePlainDBA5 is WRONG. Actual DBA is null");
            return;
        }
        var resultChildren = actualDBA.getChildren();
        if(resultChildren != null){
            console.log("testSolvePlainDBA5 is WRONG. ResultChildren is not null.")
            return;
        }
        console.log("testSolvePlainDBA5 is CORRECT");
    },

    /**
     * Function: testSolveRule
     * It tests solveRule function of Tree. Function gets array
     * of elements consisting of only attributes and booleans(and, or)
     * It tests if it can correctly pass data to function solvePlainDBA
     * and give correct answer.
     */
    testSolveRule: function(){
        var elements = new Array();
        var and = new BooleanCl("AND","and");
        var attr = new Attribute("attr1","",new Array());
        var lbrac = new BooleanCl("(","lbrac");
        var rbrac = new BooleanCl(")","rbrac");
        var or = new BooleanCl("OR","or");
        elements.push(attr);
        elements.push(or);
        elements.push(attr);
        elements.push(and);
        elements.push(attr);
        elements.push(and);
        elements.push(attr);
        elements.push(or);
        elements.push(attr);
        var tree = new Tree();
        tree.solveRule(elements);
        if(tree.root.getId() != 6){
            console.log("testSolveRule is WRONG. Root");
            return;
        }
        var levels = new Array();
        var level1 = [3,7];
        var level2 = [0,4,5];
        var level3 = [1,2];
        levels.push(level1);
        levels.push(level2);
        levels.push(level3);
        if(this.compare(tree.root, levels)){
            console.log("testSolveRule is CORRECT.")
        }
    },

    compare: function(root, levels){
        var actualLevel = root.getChildren();
        var newLevel = new Array();
        newLevel.push("begin");
        var actualId;
        var depth = 0;

        while(actualLevel != null && newLevel.length > 0){
            newLevel = new Array();
            for(var levelElement = 0; levelElement < actualLevel.length; levelElement++){
                actualId = actualLevel[levelElement].getId();
                if(levels[depth].indexOf(actualId) == -1){
                    console.log("WRONG level:"+depth+" ID:"+actualId);
                    return false;
                }
                if(actualLevel[levelElement].getChildren() != null){
                    newLevel = newLevel.concat(actualLevel[levelElement].getChildren());
                }
            }
            actualLevel = newLevel;
            depth++;
        }
        return true;
    },

    /**
     * Function: testSolveRule1
     * It tests solveRule function of Tree. Function gets array
     * of elements consisting of attributes, booleans(and, or) and
     * It tests if it can correctly pass data to function solvePlainDBA
     * and give correct answer.
     */
    testSolveRule1: function(){
        var elements = new Array();
        var and = new BooleanCl("AND","and");
        var attr = new Attribute("attr1","",new Array());
        var lbrac = new BooleanCl("(","lbrac");
        var rbrac = new BooleanCl(")","rbrac");
        var or = new BooleanCl("OR","or");
        elements.push(lbrac);
        elements.push(attr);
        elements.push(or);
        elements.push(attr);
        elements.push(rbrac);
        elements.push(and);
        elements.push(lbrac);
        elements.push(attr);
        elements.push(and);
        elements.push(attr);
        elements.push(or);
        elements.push(attr);
        elements.push(rbrac);
        var tree = new Tree();
        tree.solveRule(elements);
        if(tree.root.getId() != 8){
            console.log("testSolveRule1 is WRONG. Root");
            return;
        }
        var levels = new Array();
        var level1 = [0,6];
        var level2 = [1,2,7,3];
        var level3 = [4,5];
        levels.push(level1);
        levels.push(level2);
        levels.push(level3);
        if(this.compare(tree.root, levels)){
            console.log("testSolveRule1 is CORRECT.")
        }
    },

    /**
     * Function: testSolveRule2
     * It tests solveRule function of Tree. Function gets array
     * of elements consisting of attributes, booleans(and, or) and
     * It tests if negation is correctly solved
     */
    testSolveRule2: function(){
        var elements = new Array();
        var and = new BooleanCl("AND","and");
        var attr = new Attribute("attr1","",new Array());
        var lbrac = new BooleanCl("(","lbrac");
        var rbrac = new BooleanCl(")","rbrac");
        var or = new BooleanCl("OR","or");
        var neg = new BooleanCl("NEG","neg");
        elements.push(neg);
        elements.push(attr);
        elements.push(or);
        elements.push(attr);
        elements.push(and);
        elements.push(neg);
        elements.push(attr);
        elements.push(and);
        elements.push(attr);
        var tree = new Tree();
        tree.solveRule(elements);
        if(tree.root.getId() != 6){
            console.log("testSolveRule2 is WRONG. Root");
            return;
        }
        var levels = new Array();
        var level1 = [4,7,2];
        var level2 = [3,0,5];
        var level3 = [1];
        levels.push(level1);
        levels.push(level2);
        levels.push(level3);
        if(this.compare(tree.root, levels)){
            console.log("testSolveRule2 is CORRECT.")
        }
    },

    /**
     * Function: testSolveRule3
     * It tests solveRule function of Tree. Function gets array
     * of elements consisting of attributes, booleans(and, or) and
     * It tests if negation conibed with brackets is correctly solved
     */
    testSolveRule3: function(){
        var elements = new Array();
        var and = new BooleanCl("AND","and");
        var attr = new Attribute("attr1","",new Array());
        var lbrac = new BooleanCl("(","lbrac");
        var rbrac = new BooleanCl(")","rbrac");
        var or = new BooleanCl("OR","or");
        var neg = new BooleanCl("NEG","neg");
        elements.push(lbrac);
        elements.push(attr);
        elements.push(or);
        elements.push(neg);
        elements.push(attr);
        elements.push(rbrac);
        elements.push(and);
        elements.push(lbrac);
        elements.push(neg);
        elements.push(attr);
        elements.push(and);
        elements.push(attr);
        elements.push(or);
        elements.push(attr);
        elements.push(rbrac);
        var tree = new Tree();
        tree.solveRule(elements);
        if(tree.root.getId() != 10){
            console.log("testSolveRule3 is WRONG. Root");
            return;
        }
        var levels = new Array();
        var level1 = [4,8];
        var level2 = [0,5,9,6];
        var level3 = [1,2,7];
        var level4 = [3];
        levels.push(level1);
        levels.push(level2);
        levels.push(level3);
        levels.push(level4);
        if(this.compare(tree.root, levels)){
            console.log("testSolveRule3 is CORRECT.")
        }
    },

    /**
     * Function: testGetLevels1
     * It tests solveLevls function of Tree. Function gets array
     * of elements consisting of attributes, booleans(and, or) and
     * It tests if levels are correctly count.
     */
    testGetLevels1: function(){
        var elements = new Array();
        var and = new BooleanCl("AND","and");
        var attr = new Attribute("attr1","",new Array());
        var lbrac = new BooleanCl("(","lbrac");
        var rbrac = new BooleanCl(")","rbrac");
        var or = new BooleanCl("OR","or");
        elements.push(attr);
        elements.push(or);
        elements.push(attr);
        elements.push(and);
        elements.push(attr);
        elements.push(and);
        elements.push(attr);
        elements.push(or);
        elements.push(attr);
        var tree = new Tree();
        tree.solveRule(elements);
        var result = tree.getLevels();
        if(result != 3){
            console.log("testGetLevels1 is WRONG "+result);
        }
        else{
            console.log("testGetLevels1 is CORRECT");
        }
    },

    /**
     * Function: testGetLevels2
     * It tests solveLevls function of Tree. Function gets array
     * of elements consisting of attributes, booleans(and, or) and
     * It tests if levels are correctly count.
     */
    testGetLevels2: function(){
        var elements = new Array();
        var and = new BooleanCl("AND","and");
        var attr = new Attribute("attr1","",new Array());
        var lbrac = new BooleanCl("(","lbrac");
        var rbrac = new BooleanCl(")","rbrac");
        var or = new BooleanCl("OR","or");
        elements.push(lbrac);
        elements.push(attr);
        elements.push(or);
        elements.push(attr);
        elements.push(rbrac);
        elements.push(and);
        elements.push(lbrac);
        elements.push(attr);
        elements.push(and);
        elements.push(attr);
        elements.push(or);
        elements.push(attr);
        elements.push(rbrac);
        var tree = new Tree();
        tree.solveRule(elements);
        var result = tree.getLevels();
        if(result != 3){
            console.log("testGetLevels2 is WRONG "+result);
        }
        else{
            console.log("testGetLevels2 is CORRECT");
        }
    },

    /**
     * Function: testGetLevels3
     * It tests solveLevls function of Tree. Function gets array
     * of elements consisting of attributes, booleans(and, or) and
     * It tests if levels are correctly count.
     */
    testGetLevels3: function(){
        var elements = new Array();
        var and = new BooleanCl("AND","and");
        var attr = new Attribute("attr1","",new Array());
        var lbrac = new BooleanCl("(","lbrac");
        var rbrac = new BooleanCl(")","rbrac");
        var or = new BooleanCl("OR","or");
        var neg = new BooleanCl("NEG","neg");
        elements.push(neg);
        elements.push(attr);
        elements.push(or);
        elements.push(attr);
        elements.push(and);
        elements.push(neg);
        elements.push(attr);
        elements.push(and);
        elements.push(attr);
        var tree = new Tree();
        tree.solveRule(elements);
        var result = tree.getLevels();
        if(result != 3){
            console.log("testGetLevels3 is WRONG "+result);
        }
        else{
            console.log("testGetLevels3 is CORRECT");
        }
    },

    /**
     * Function: testGetLevels4
     * It tests solveLevls function of Tree. Function gets array
     * of elements consisting of attributes, booleans(and, or) and
     * It tests if levels are correctly count.
     */
    testGetLevels4: function(){
        var elements = new Array();
        var and = new BooleanCl("AND","and");
        var attr = new Attribute("attr1","",new Array());
        var lbrac = new BooleanCl("(","lbrac");
        var rbrac = new BooleanCl(")","rbrac");
        var or = new BooleanCl("OR","or");
        var neg = new BooleanCl("NEG","neg");
        elements.push(lbrac);
        elements.push(attr);
        elements.push(or);
        elements.push(neg);
        elements.push(attr);
        elements.push(rbrac);
        elements.push(and);
        elements.push(lbrac);
        elements.push(neg);
        elements.push(attr);
        elements.push(and);
        elements.push(attr);
        elements.push(or);
        elements.push(attr);
        elements.push(rbrac);
        var tree = new Tree();
        tree.solveRule(elements);
        var result = tree.getLevels();
        if(result != 4){
            console.log("testGetLevels4 is WRONG "+result);
        }
        else{
            console.log("testGetLevels4 is CORRECT");
        }
    }
});

var test = new TreeTest();
test.allTests();