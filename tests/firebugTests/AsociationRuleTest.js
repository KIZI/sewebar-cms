var AsociationRuleTest = new Class({   
    initialize: function(){
        this.rule1 = new AsociationRule(new TestServerInfo());
    },

    testInsertItem: function(){
        var rule = this.rule1;
        var lbrac = new BooleanCl("(","lbrac");
        var result = rule.insertItemNew(lbrac);
        if(!result){
            console.log("testInsertItem is WRONG at insert lbrac1");
            return;
        }
        var attr1 = new Attribute("attr1","",new Array());
        result = rule.insertItemNew(attr1);
        if(!result){
            console.log("testInsertItem is WRONG at insert attr1");
            return;
        }
        var oper1 = new InterestMeasure("oper1","","","","","","","");
        result = rule.insertItemNew(oper1);
        if(result){
            console.log("testInsertItem is WRONG at insert oper1");
            return;
        }
        var rbrac = new BooleanCl(")","rbrac");
        result = rule.insertItemNew(rbrac);
        if(!result){
            console.log("testInsertItem is WRONG at insert rbrac1");
            return;
        }
        result = rule.insertItemNew(oper1);
        if(!result){
            console.log("testInsertItem is WRONG at insert oper2");
            return;
        }
        result = rule.insertItemNew(attr1);
        if(!result){
            console.log("testInsertItem is WRONG at insert attr2");
            return;
        }
        console.log("testInsertItem is OK");
    },

    testSolveAmounts: function(){
        var result = this.rule1.solveAmounts();
        var expectedResult = true;
        if(result != expectedResult){
            console.log("testSolveAmounts is WRONG Result "+result+" expectedResult "+expectedResult);
        }
        else{
            console.log("testSolveAmounts is OK");
        }
    },

    testSolveSupportedInterestMeasures: function(){
        var result = this.rule1.solveSupportedInterestMeasures();
        var expectedResult = true;
        if(result != expectedResult){
            console.log("testSolveSupportedInterestMeasures is WRONG Result "+result+" expectedResult "+expectedResult);
        }
        else{
            console.log("testSolveSupportedInterestMeasures is OK");
        }
    },

    testGetExistingInterestMeasures: function(){
        var result = this.rule1.getExistingInterestMeasures();
        var expectedResult = new Array();
        var oper1 = new InterestMeasure("oper1","","","","","","","");
        expectedResult.push(oper1);
        if(this.areArraysEqual(result , expectedResult)){
            console.log("testGetExistingInterestMeasures is WRONG Result "+result+" expectedResult "+expectedResult);
        }
        else{
            console.log("testGetExistingInterestMeasures is OK");
        }
    },

    testCountAttrsCons: function(){
        var result = this.rule1.countAttrsCon();
        var expectedResult = 1;
        if(result != expectedResult){
            console.log("testCountAttrsCons is WRONG Result "+result+" expectedResult "+expectedResult);
        }
        else{
            console.log("testCountAttrsCons is OK");
        }
    },

    testCountAttrsAnt: function(){
        var result = this.rule1.countAttrsAnt();
        var expectedResult = 1;
        if(result != expectedResult){
            console.log("testCountAttrsAnt is WRONG Result "+result+" expectedResult "+expectedResult);
        }
        else{
            console.log("testCountAttrsAnt is OK");
        }
    },

    testCountOpers: function(){
        var result = this.rule1.countOpers();
        var expectedResult = 1;
        if(result != expectedResult){
            console.log("testCountOpers is WRONG Result "+result+" expectedResult "+expectedResult);
        }
        else{
            console.log("testCountOpers is OK");
        }
    },

    testCountNegation: function(){
        var result = this.rule1.countNegation();
        var expectedResult = 0;
        if(result != expectedResult){
            console.log("testCountNegation is WRONG Result "+result+" expectedResult "+expectedResult);
        }
        else{
            console.log("testCountNegation is OK");
        }
    },

    testGetBracketDepth: function(){
        var result = this.rule1.getBracketDepth();
        var expectedResult = 0;
        if(result != expectedResult){
            console.log("testGetBracketDepth is WRONG Result "+result+" expectedResult "+expectedResult);
        }
        else{
            console.log("testGetBracketDepth is OK");
        }
    },

    /**
     * Function: testInsertItemS1
     * It tests insertItem function of class AsociationRule
     * Attr1 or ( Attr2 and Attr3 ) Oper1 Attr4 or ( Attr5 and Attr6 )
     * neg Attr1 and Attr2 Oper1 Oper2 Attr3 or neg Attr4
     */
    testInsertItemS1: function(){
        var attr1 = new Attribute("attr1","",new Array());
        var attr2 = new Attribute("attr1","",new Array());
        var attr3 = new Attribute("attr1","",new Array());
        var attr4 = new Attribute("attr1","",new Array());
        var attr5 = new Attribute("attr1","",new Array());
        var attr6 = new Attribute("attr1","",new Array());
        var oper1 = new InterestMeasure("oper1","","","","","","","");
        var rbrac = new BooleanCl(")","rbrac");
        var lbrac = new BooleanCl("(","lbrac");
        var and = new BooleanCl("AND","and");
        var or = new BooleanCl("OR","or");
        var neg = new BooleanCl("NEG","neg");

        var main = new TestServerInfo();
        var rule = new AsociationRule(main);
        var result = rule.insertItemNew(attr1);
        if(!result){
            console.log("testInsertItem is WRONG at insert attr1");
            return;
        }
        result = rule.insertItemNew(or);
        if(!result){
            console.log("testInsertItem is WRONG at insert or1");
            return;
        }
        result = rule.insertItemNew(lbrac);
        if(!result){
            console.log("testInsertItem is WRONG at insert lbrac1");
            return;
        }
        result = rule.insertItemNew(attr2);
        if(!result){
            console.log("testInsertItem is WRONG at insert attr2");
            return;
        }
        result = rule.insertItemNew(and);
        if(!result){
            console.log("testInsertItem is WRONG at insert and1");
            return;
        }
        result = rule.insertItemNew(attr3);
        if(!result){
            console.log("testInsertItem is WRONG at insert attr3");
            return;
        }
        result = rule.insertItemNew(rbrac);
        if(!result){
            console.log("testInsertItem is WRONG at insert rbrac1");
            return;
        }
        result = rule.insertItemNew(oper1);
        if(!result){
            console.log("testInsertItem is WRONG at insert attr2");
            return;
        }
        result = rule.insertItemNew(attr4);
        if(!result){
            console.log("testInsertItem is WRONG at insert attr4");
            return;
        }
        result = rule.insertItemNew(or);
        if(!result){
            console.log("testInsertItem is WRONG at insert or2");
            return;
        }
        result = rule.insertItemNew(lbrac);
        if(!result){
            console.log("testInsertItem is WRONG at insert lbrac2");
            return;
        }
        result = rule.insertItemNew(attr5);
        if(!result){
            console.log("testInsertItem is WRONG at insert attr5");
            return;
        }
        result = rule.insertItemNew(and);
        if(!result){
            console.log("testInsertItem is WRONG at insert and2");
            return;
        }
        result = rule.insertItemNew(attr6);
        if(!result){
            console.log("testInsertItem is WRONG at insert attr6");
            return;
        }
        result = rule.insertItemNew(rbrac);
        if(!result){
            console.log("testInsertItem is WRONG at insert rbrac2");
            return;
        }
        console.log("testInsertItem is OK");
    },

    /**
     * Function: testInsertItemS2
     * It tests insertItem function of class AsociationRule
     * neg Attr1 and Attr2 Oper1 Oper2 Attr3 or neg Attr4
     */
    testInsertItemS2: function(){
        var attr1 = new Attribute("attr1","",new Array());
        var attr2 = new Attribute("attr2","",new Array());
        var attr3 = new Attribute("attr3","",new Array());
        var attr4 = new Attribute("attr4","",new Array());
        var attr5 = new Attribute("attr5","",new Array());
        var attr6 = new Attribute("attr6","",new Array());
        var oper1 = new InterestMeasure("oper1","","","","","","","");
        var oper2 = new InterestMeasure("oper2","","","","","","","");
        var rbrac = new BooleanCl(")","rbrac");
        var lbrac = new BooleanCl("(","lbrac");
        var and = new BooleanCl("AND","and");
        var or = new BooleanCl("OR","or");
        var neg = new BooleanCl("NEG","neg");

        var main = new TestServerInfo();;
        var rule = new AsociationRule(main);
        var result = rule.insertItemNew(neg);
        if(!result){
            console.log("testInsertItem is WRONG at insert neg");
            return;
        }
        result = rule.insertItemNew(attr1);
        if(!result){
            console.log("testInsertItem is WRONG at insert attr1");
            return;
        }
        result = rule.insertItemNew(and);
        if(!result){
            console.log("testInsertItem is WRONG at insert and1");
            return;
        }
        result = rule.insertItemNew(attr2);
        if(!result){
            console.log("testInsertItem is WRONG at insert attr2");
            return;
        }
        result = rule.insertItemNew(oper1);
        if(!result){
            console.log("testInsertItem is WRONG at insert oper1");
            return;
        }
        result = rule.insertItemNew(oper2);
        if(!result){
            console.log("testInsertItem is WRONG at insert oper2");
            return;
        }
        result = rule.insertItemNew(attr3);
        if(!result){
            console.log("testInsertItem is WRONG at insert attr3");
            return;
        }
        result = rule.insertItemNew(or);
        if(!result){
            console.log("testInsertItem is WRONG at insert or2");
            return;
        }
        result = rule.insertItemNew(neg);
        if(!result){
            console.log("testInsertItem is WRONG at insert neg2");
            return;
        }
        result = rule.insertItemNew(attr4);
        if(!result){
            console.log("testInsertItem is WRONG at insert attr4");
            return;
        }
        console.log("testInsertItem is OK");
    },

    /**
     * Function: testInsertItem1
     * It tests insertItem function of class AsociationRule
     * attr1 oper1 (oper1) oper2 oper3 oper4 oper5 oper6 oper7 oper8 (oper9) attr2 and (attr3)
     */
    testInsertItem1: function(){
        var attr1 = new Attribute("attr1","",new Array());
        var attr2 = new Attribute("attr2","",new Array());
        var attr3 = new Attribute("attr3","",new Array());
        var oper1 = new InterestMeasure("oper1","","","","","","","");
        var oper2 = new InterestMeasure("oper2","","","","","","","");
        var oper3 = new InterestMeasure("oper3","","","","","","","");
        var oper4 = new InterestMeasure("oper4","","","","","","","");
        var oper5 = new InterestMeasure("oper5","","","","","","","");
        var oper6 = new InterestMeasure("oper6","","","","","","","");
        var oper7 = new InterestMeasure("oper7","","","","","","","");
        var oper8 = new InterestMeasure("oper8","","","","","","","");
        var oper9 = new InterestMeasure("oper9","","","","","","","");

        var and = new BooleanCl("AND","and");

        var main = new TestServerInfo();
        var rule = new AsociationRule(main);
        var result = null;

        result = rule.insertItemNew(attr1);
        if(!result){
            console.log("testInsertItem1 is WRONG at insert 0");
            return;
        }
        result = rule.insertItemNew(oper1);
        if(!result){
            console.log("testInsertItem1 is WRONG at insert 1");
            return;
        }
        result = rule.insertItemNew(oper1);
        if(result){
            console.log("testInsertItem1 is WRONG at insert 2");
            return;
        }
        result = rule.insertItemNew(oper2);
        if(!result){
            console.log("testInsertItem1 is WRONG at insert 3");
            return;
        }
        result = rule.insertItemNew(oper3);
        if(!result){
            console.log("testInsertItem1 is WRONG at insert 4");
            return;
        }
        result = rule.insertItemNew(oper4);
        if(!result){
            console.log("testInsertItem1 is WRONG at insert 5");
            return;
        }
        result = rule.insertItemNew(oper5);
        if(!result){
            console.log("testInsertItem1 is WRONG at insert 6");
            return;
        }
        result = rule.insertItemNew(oper6);
        if(!result){
            console.log("testInsertItem1 is WRONG at insert 7");
            return;
        }
        result = rule.insertItemNew(oper7);
        if(!result){
            console.log("testInsertItem1 is WRONG at insert 8");
            return;
        }
        result = rule.insertItemNew(oper8);
        if(!result){
            console.log("testInsertItem1 is WRONG at insert 9");
            return;
        }
        result = rule.insertItemNew(oper9);
        if(result){
            console.log("testInsertItem1 is WRONG at insert 10");
            return;
        }
        result = rule.insertItemNew(attr2);
        if(!result){
            console.log("testInsertItem1 is WRONG at insert 11");
            return;
        }
        result = rule.insertItemNew(and);
        if(!result){
            console.log("testInsertItem1 is WRONG at insert 12");
            return;
        }
        result = rule.insertItemNew(attr3);
        if(result){
            console.log("testInsertItem1 is WRONG at insert 13");
            return;
        }


        console.log("testInsertItem1 is OK");
    },

    /**
     * Function: testInsertItem2
     * It tests insertItem function of class AsociationRule
     * attr1 or attr2 and attr3 and lbrac attr4 (oper1) rbrac (rbrac) and attr5 (rbrac) (oper2) oper1 (oper2) attr6
     */
    testInsertItem2: function(){
        var attr1 = new Attribute("attr1","",new Array());
        var attr2 = new Attribute("attr2","",new Array());
        var attr3 = new Attribute("attr3","",new Array());
        var attr4 = new Attribute("attr4","",new Array());
        var attr5 = new Attribute("attr5","",new Array());
        var attr6 = new Attribute("attr6","",new Array());
        var oper1 = new InterestMeasure("oper1","","","","","","","");
        var oper2 = new InterestMeasure("oper2","","","","","","","");

        var rbrac = new BooleanCl(")","rbrac");
        var lbrac = new BooleanCl("(","lbrac");
        var and = new BooleanCl("AND","and");
        var or = new BooleanCl("OR","or");

        var main = new TestServerInfo1();
        var rule = new AsociationRule(main);
        var result = null;

        result = rule.insertItemNew(attr1);
        if(!result){
            console.log("testInsertItem2 is WRONG at insert 0");
            return;
        }
        result = rule.insertItemNew(or);
        if(!result){
            console.log("testInsertItem2 is WRONG at insert 1");
            return;
        }
        result = rule.insertItemNew(attr2);
        if(!result){
            console.log("testInsertItem2 is WRONG at insert 2");
            return;
        }
        result = rule.insertItemNew(and);
        if(!result){
            console.log("testInsertItem2 is WRONG at insert 3");
            return;
        }
        result = rule.insertItemNew(attr3);
        if(!result){
            console.log("testInsertItem2 is WRONG at insert 4");
            return;
        }
        result = rule.insertItemNew(and);
        if(!result){
            console.log("testInsertItem2 is WRONG at insert 5");
            return;
        }
        result = rule.insertItemNew(lbrac);
        if(!result){
            console.log("testInsertItem2 is WRONG at insert 6");
            return;
        }
        result = rule.insertItemNew(attr4);
        if(!result){
            console.log("testInsertItem2 is WRONG at insert 7");
            return;
        }
        result = rule.insertItemNew(oper1);
        if(result){
            console.log("testInsertItem2 is WRONG at insert 8");
            return;
        }
        result = rule.insertItemNew(rbrac);
        if(!result){
            console.log("testInsertItem2 is WRONG at insert 9");
            return;
        }
        result = rule.insertItemNew(rbrac);
        if(result){
            console.log("testInsertItem2 is WRONG at insert 10");
            return;
        }
        result = rule.insertItemNew(and);
        if(!result){
            console.log("testInsertItem2 is WRONG at insert 11");
            return;
        }
        result = rule.insertItemNew(attr5);
        if(!result){
            console.log("testInsertItem2 is WRONG at insert 12");
            return;
        }
        result = rule.insertItemNew(rbrac);
        if(result){
            console.log("testInsertItem2 is WRONG at insert 13");
            return;
        }
        result = rule.insertItemNew(oper2);
        if(result){
            console.log("testInsertItem2 is WRONG at insert 14");
            return;
        }
        result = rule.insertItemNew(oper1);
        if(!result){
            console.log("testInsertItem2 is WRONG at insert 15");
            return;
        }
        result = rule.insertItemNew(oper2);
        if(result){
            console.log("testInsertItem2 is WRONG at insert 16");
            return;
        }
        result = rule.insertItemNew(attr6);
        if(!result){
            console.log("testInsertItem2 is WRONG at insert 17");
            return;
        }


        console.log("testInsertItem2 is OK");
    },

    /**
     * Function: testInsertItem3
     * It tests insertItem function of class AsociationRule
     * neg attr1 (rbrac) (attr2) (lbrac) and neg (rbrac) (lbrac) (oper1) (and) (or) (neg) attr2 and (rbrac) (and) (or) (oper1) attr3 oper1
     */
    testInsertItem3: function(){
        var attr1 = new Attribute("attr1","",new Array());
        var attr2 = new Attribute("attr2","",new Array());
        var attr3 = new Attribute("attr3","",new Array());
        var oper1 = new InterestMeasure("oper1","","","","","","","");

        var rbrac = new BooleanCl(")","rbrac");
        var lbrac = new BooleanCl("(","lbrac");
        var and = new BooleanCl("AND","and");
        var or = new BooleanCl("OR","or");
        var neg = new BooleanCl("NEG","neg");

        var main = new TestServerInfo();
        var rule = new AsociationRule(main);
        var result = null;

        result = rule.insertItemNew(neg);
        if(!result){
            console.log("testInsertItem3 is WRONG at insert 0");
            return;
        }
        result = rule.insertItemNew(attr1);
        if(!result){
            console.log("testInsertItem3 is WRONG at insert 1");
            return;
        }
        result = rule.insertItemNew(rbrac);
        if(result){
            console.log("testInsertItem3 is WRONG at insert 2");
            return;
        }
        result = rule.insertItemNew(attr2);
        if(result){
            console.log("testInsertItem3 is WRONG at insert 3");
            return;
        }
        result = rule.insertItemNew(lbrac);
        if(result){
            console.log("testInsertItem3 is WRONG at insert 4");
            return;
        }
        result = rule.insertItemNew(and);
        if(!result){
            console.log("testInsertItem3 is WRONG at insert 5");
            return;
        }
        result = rule.insertItemNew(neg);
        if(!result){
            console.log("testInsertItem3 is WRONG at insert 6");
            return;
        }
        result = rule.insertItemNew(rbrac);
        if(result){
            console.log("testInsertItem3 is WRONG at insert 7");
            return;
        }
        result = rule.insertItemNew(lbrac);
        if(result){
            console.log("testInsertItem3 is WRONG at insert 8");
            return;
        }
        result = rule.insertItemNew(oper1);
        if(result){
            console.log("testInsertItem3 is WRONG at insert 9");
            return;
        }
        result = rule.insertItemNew(and);
        if(result){
            console.log("testInsertItem3 is WRONG at insert 10");
            return;
        }
        result = rule.insertItemNew(or);
        if(result){
            console.log("testInsertItem3 is WRONG at insert 11");
            return;
        }
        result = rule.insertItemNew(neg);
        if(result){
            console.log("testInsertItem3 is WRONG at insert 12");
            return;
        }
        result = rule.insertItemNew(attr2);
        if(!result){
            console.log("testInsertItem3 is WRONG at insert 13");
            return;
        }
        result = rule.insertItemNew(and);
        if(!result){
            console.log("testInsertItem3 is WRONG at insert 14");
            return;
        }
        result = rule.insertItemNew(rbrac);
        if(result){
            console.log("testInsertItem3 is WRONG at insert 15");
            return;
        }
        result = rule.insertItemNew(and);
        if(result){
            console.log("testInsertItem3 is WRONG at insert 16");
            return;
        }
        result = rule.insertItemNew(or);
        if(result){
            console.log("testInsertItem3 is WRONG at insert 17");
            return;
        }
        result = rule.insertItemNew(oper1);
        if(result){
            console.log("testInsertItem3 is WRONG at insert 18");
            return;
        }
        result = rule.insertItemNew(attr3);
        if(!result){
            console.log("testInsertItem3 is WRONG at insert 19");
            return;
        }
        result = rule.insertItemNew(oper1);
        if(!result){
            console.log("testInsertItem3 is WRONG at insert 20");
            return;
        }
        

        console.log("testInsertItem3 is OK");
    },

    /**
     * Function: testInsertItem4
     * It tests insertItem function of class AsociationRule
     * attr1 or neg attr2 or lbrac lbrac neg attr3 or (oper1) (rbrac) (and) (or) lbrac (oper1) (rbrac) (and) (or)
     */
    testInsertItem4: function(){
        var attr1 = new Attribute("attr1","",new Array());
        var attr2 = new Attribute("attr2","",new Array());
        var attr3 = new Attribute("attr3","",new Array());
        var oper1 = new InterestMeasure("oper1","","","","","","","");

        var rbrac = new BooleanCl(")","rbrac");
        var lbrac = new BooleanCl("(","lbrac");
        var and = new BooleanCl("AND","and");
        var or = new BooleanCl("OR","or");
        var neg = new BooleanCl("NEG","neg");

        var main = new TestServerInfo();
        var rule = new AsociationRule(main);
        var result = null;

        result = rule.insertItemNew(attr1);
        if(!result){
            console.log("testInsertItem4 is WRONG at insert 0");
            return;
        }
        result = rule.insertItemNew(or);
        if(!result){
            console.log("testInsertItem4 is WRONG at insert 1");
            return;
        }
        result = rule.insertItemNew(neg);
        if(!result){
            console.log("testInsertItem4 is WRONG at insert 2");
            return;
        }
        result = rule.insertItemNew(attr2);
        if(!result){
            console.log("testInsertItem4 is WRONG at insert 3");
            return;
        }
        result = rule.insertItemNew(or);
        if(!result){
            console.log("testInsertItem4 is WRONG at insert 4");
            return;
        }
        result = rule.insertItemNew(lbrac);
        if(!result){
            console.log("testInsertItem4 is WRONG at insert 5");
            return;
        }
        result = rule.insertItemNew(lbrac);
        if(!result){
            console.log("testInsertItem4 is WRONG at insert 6");
            return;
        }
        result = rule.insertItemNew(neg);
        if(!result){
            console.log("testInsertItem4 is WRONG at insert 7");
            return;
        }
        result = rule.insertItemNew(attr3);
        if(!result){
            console.log("testInsertItem4 is WRONG at insert 8");
            return;
        }
        result = rule.insertItemNew(or);
        if(!result){
            console.log("testInsertItem4 is WRONG at insert 9");
            return;
        }
        result = rule.insertItemNew(oper1);
        if(result){
            console.log("testInsertItem4 is WRONG at insert 10");
            return;
        }
        result = rule.insertItemNew(rbrac);
        if(result){
            console.log("testInsertItem4 is WRONG at insert 11");
            return;
        }
        result = rule.insertItemNew(and);
        if(result){
            console.log("testInsertItem4 is WRONG at insert 12");
            return;
        }
        result = rule.insertItemNew(or);
        if(result){
            console.log("testInsertItem4 is WRONG at insert 13");
            return;
        }
        result = rule.insertItemNew(lbrac);
        if(!result){
            console.log("testInsertItem4 is WRONG at insert 14");
            return;
        }
        result = rule.insertItemNew(oper1);
        if(result){
            console.log("testInsertItem4 is WRONG at insert 15");
            return;
        }
        result = rule.insertItemNew(rbrac);
        if(result){
            console.log("testInsertItem4 is WRONG at insert 16");
            return;
        }
        result = rule.insertItemNew(and);
        if(result){
            console.log("testInsertItem4 is WRONG at insert 17");
            return;
        }
        result = rule.insertItemNew(or);
        if(result){
            console.log("testInsertItem4 is WRONG at insert 18");
            return;
        }


        console.log("testInsertItem4 is OK");
    },


    /**
     * Function: testInsertItem5
     * It tests insertItem function of class AsociationRule
     * attr1 oper1 neg attr2
     */
    testInsertItem5: function(){
        var attr1 = new Attribute("attr1","",new Array());
        var attr2 = new Attribute("attr2","",new Array());
        var oper1 = new InterestMeasure("oper1","","","","","","","");

        var neg = new BooleanCl("NEG","neg");

        var main = new TestServerInfo();
        var rule = new AsociationRule(main);
        var result = null;

        result = rule.insertItemNew(attr1);
        if(!result){
            console.log("testInsertItem5 is WRONG at insert 0");
            return;
        }
        result = rule.insertItemNew(oper1);
        if(!result){
            console.log("testInsertItem5 is WRONG at insert 1");
            return;
        }
        result = rule.insertItemNew(neg);
        if(!result){
            console.log("testInsertItem5 is WRONG at insert 2");
            return;
        }
        result = rule.insertItemNew(attr2);
        if(!result){
            console.log("testInsertItem5 is WRONG at insert 3");
            return;
        }


        console.log("testInsertItem5 is OK");
    },

    /**
     * Function: testInsertItem6
     * It tests insertItem function of class AsociationRule
     * attr1 oper1 (rbrac) (and) (or) lbrac
     */
    testInsertItem6: function(){
        var attr1 = new Attribute("attr1","",new Array());
        var oper1 = new InterestMeasure("oper1","","","","","","","");

        var rbrac = new BooleanCl(")","rbrac");
        var lbrac = new BooleanCl("(","lbrac");
        var and = new BooleanCl("AND","and");
        var or = new BooleanCl("OR","or");

        var main = new TestServerInfo();
        var rule = new AsociationRule(main);
        var result = null;

        result = rule.insertItemNew(attr1);
        if(!result){
            console.log("testInsertItem6 is WRONG at insert 0");
            return;
        }
        result = rule.insertItemNew(oper1);
        if(!result){
            console.log("testInsertItem6 is WRONG at insert 1");
            return;
        }
        result = rule.insertItemNew(rbrac);
        if(result){
            console.log("testInsertItem6 is WRONG at insert 2");
            return;
        }
        result = rule.insertItemNew(and);
        if(result){
            console.log("testInsertItem6 is WRONG at insert 3");
            return;
        }
        result = rule.insertItemNew(or);
        if(result){
            console.log("testInsertItem6 is WRONG at insert 4");
            return;
        }
        result = rule.insertItemNew(lbrac);
        if(!result){
            console.log("testInsertItem6 is WRONG at insert 5");
            return;
        }


        console.log("testInsertItem6 is OK");
    },
    
    /**
     * Function: testInsertItem7
     * It tests insertItem function of class AsociationRule
     * attr1 and attr2 and attr3 and attr4 and attr5 and attr6 and attr7 and attr8 and (attr9)
     */
    testInsertItem7: function(){
        var attr1 = new Attribute("attr1","",new Array());
        var attr2 = new Attribute("attr2","",new Array());
        var attr3 = new Attribute("attr3","",new Array());
        var attr4 = new Attribute("attr4","",new Array());
        var attr5 = new Attribute("attr5","",new Array());
        var attr6 = new Attribute("attr6","",new Array());
        var attr7 = new Attribute("attr7","",new Array());
        var attr8 = new Attribute("attr8","",new Array());
        var attr9 = new Attribute("attr9","",new Array());

        var and = new BooleanCl("AND","and");

        var main = new TestServerInfo();
        var rule = new AsociationRule(main);
        var result = null;

        result = rule.insertItemNew(attr1);
        if(!result){
            console.log("testInsertItem7 is WRONG at insert 0");
            return;
        }
        result = rule.insertItemNew(and);
        if(!result){
            console.log("testInsertItem7 is WRONG at insert 1");
            return;
        }
        result = rule.insertItemNew(attr2);
        if(!result){
            console.log("testInsertItem7 is WRONG at insert 2");
            return;
        }
        result = rule.insertItemNew(and);
        if(!result){
            console.log("testInsertItem7 is WRONG at insert 3");
            return;
        }
        result = rule.insertItemNew(attr3);
        if(!result){
            console.log("testInsertItem7 is WRONG at insert 4");
            return;
        }
        result = rule.insertItemNew(and);
        if(!result){
            console.log("testInsertItem7 is WRONG at insert 5");
            return;
        }
        result = rule.insertItemNew(attr4);
        if(!result){
            console.log("testInsertItem7 is WRONG at insert 6");
            return;
        }
        result = rule.insertItemNew(and);
        if(!result){
            console.log("testInsertItem7 is WRONG at insert 7");
            return;
        }
        result = rule.insertItemNew(attr5);
        if(!result){
            console.log("testInsertItem7 is WRONG at insert 8");
            return;
        }
        result = rule.insertItemNew(and);
        if(!result){
            console.log("testInsertItem7 is WRONG at insert 9");
            return;
        }
        result = rule.insertItemNew(attr6);
        if(!result){
            console.log("testInsertItem7 is WRONG at insert 10");
            return;
        }
        result = rule.insertItemNew(and);
        if(!result){
            console.log("testInsertItem7 is WRONG at insert 11");
            return;
        }
        result = rule.insertItemNew(attr7);
        if(!result){
            console.log("testInsertItem7 is WRONG at insert 12");
            return;
        }
        result = rule.insertItemNew(and);
        if(!result){
            console.log("testInsertItem7 is WRONG at insert 13");
            return;
        }
        result = rule.insertItemNew(attr8);
        if(!result){
            console.log("testInsertItem7 is WRONG at insert 14");
            return;
        }
        result = rule.insertItemNew(and);
        if(!result){
            console.log("testInsertItem7 is WRONG at insert 15");
            return;
        }
        result = rule.insertItemNew(attr9);
        if(result){
            console.log("testInsertItem7 is WRONG at insert 16");
            return;
        }


        console.log("testInsertItem7 is OK");
    },

    /**
     * Function: testInsertItem8
     * It tests insertItem function of class AsociationRule
     * attr1 oper1 attr2 and attr3 and attr4 and attr5 and attr6 and attr7 and attr8 and attr9 and (attr10)
     */
    testInsertItem8: function(){
        var attr1 = new Attribute("attr1","",new Array());
        var attr2 = new Attribute("attr2","",new Array());
        var attr3 = new Attribute("attr3","",new Array());
        var attr4 = new Attribute("attr4","",new Array());
        var attr5 = new Attribute("attr5","",new Array());
        var attr6 = new Attribute("attr6","",new Array());
        var attr7 = new Attribute("attr7","",new Array());
        var attr8 = new Attribute("attr8","",new Array());
        var attr9 = new Attribute("attr9","",new Array());
        var attr10 = new Attribute("attr10","",new Array());
        var oper1 = new InterestMeasure("oper1","","","","","","","");

        var and = new BooleanCl("AND","and");

        var main = new TestServerInfo();
        var rule = new AsociationRule(main);
        var result = null;

        result = rule.insertItemNew(attr1);
        if(!result){
            console.log("testInsertItem8 is WRONG at insert 0");
            return;
        }
        result = rule.insertItemNew(oper1);
        if(!result){
            console.log("testInsertItem8 is WRONG at insert 1");
            return;
        }
        result = rule.insertItemNew(attr2);
        if(!result){
            console.log("testInsertItem8 is WRONG at insert 2");
            return;
        }
        result = rule.insertItemNew(and);
        if(!result){
            console.log("testInsertItem8 is WRONG at insert 3");
            return;
        }
        result = rule.insertItemNew(attr3);
        if(!result){
            console.log("testInsertItem8 is WRONG at insert 4");
            return;
        }
        result = rule.insertItemNew(and);
        if(!result){
            console.log("testInsertItem8 is WRONG at insert 5");
            return;
        }
        result = rule.insertItemNew(attr4);
        if(!result){
            console.log("testInsertItem8 is WRONG at insert 6");
            return;
        }
        result = rule.insertItemNew(and);
        if(!result){
            console.log("testInsertItem8 is WRONG at insert 7");
            return;
        }
        result = rule.insertItemNew(attr5);
        if(!result){
            console.log("testInsertItem8 is WRONG at insert 8");
            return;
        }
        result = rule.insertItemNew(and);
        if(!result){
            console.log("testInsertItem8 is WRONG at insert 9");
            return;
        }
        result = rule.insertItemNew(attr6);
        if(!result){
            console.log("testInsertItem8 is WRONG at insert 10");
            return;
        }
        result = rule.insertItemNew(and);
        if(!result){
            console.log("testInsertItem8 is WRONG at insert 11");
            return;
        }
        result = rule.insertItemNew(attr7);
        if(!result){
            console.log("testInsertItem8 is WRONG at insert 12");
            return;
        }
        result = rule.insertItemNew(and);
        if(!result){
            console.log("testInsertItem8 is WRONG at insert 13");
            return;
        }
        result = rule.insertItemNew(attr8);
        if(!result){
            console.log("testInsertItem8 is WRONG at insert 14");
            return;
        }
        result = rule.insertItemNew(and);
        if(!result){
            console.log("testInsertItem8 is WRONG at insert 15");
            return;
        }
        result = rule.insertItemNew(attr9);
        if(!result){
            console.log("testInsertItem8 is WRONG at insert 16");
            return;
        }
        result = rule.insertItemNew(and);
        if(!result){
            console.log("testInsertItem8 is WRONG at insert 17");
            return;
        }
        result = rule.insertItemNew(attr10);
        if(result){
            console.log("testInsertItem8 is WRONG at insert 18");
            return;
        }

        console.log("testInsertItem8 is OK");
    },

    /**
     * Function: testInsertItem9
     * It tests insertItem function of class AsociationRule
     * lbrac attr1 rbrac or lbrac lbrac attr2 rbrac (lbrac) (attr) (oper1) rbrac and neg attr3 (rbrac)
     */
    testInsertItem9: function(){
        var attr1 = new Attribute("attr1","",new Array());
        var attr2 = new Attribute("attr2","",new Array());
        var attr3 = new Attribute("attr3","",new Array());
        var oper1 = new InterestMeasure("oper1","","","","","","","");
        
        var rbrac = new BooleanCl(")","rbrac");
        var lbrac = new BooleanCl("(","lbrac");
        var and = new BooleanCl("AND","and");
        var or = new BooleanCl("OR","or");
        var neg = new BooleanCl("NEG","neg");

        var main = new TestServerInfo();
        var rule = new AsociationRule(main);
        var result = null;

        result = rule.insertItemNew(lbrac);
        if(!result){
            console.log("testInsertItem5 is WRONG at insert 0");
            return;
        }
        result = rule.insertItemNew(attr1);
        if(!result){
            console.log("testInsertItem5 is WRONG at insert 1");
            return;
        }
        result = rule.insertItemNew(rbrac);
        if(!result){
            console.log("testInsertItem5 is WRONG at insert 2");
            return;
        }
        result = rule.insertItemNew(or);
        if(!result){
            console.log("testInsertItem5 is WRONG at insert 3");
            return;
        }
        result = rule.insertItemNew(lbrac);
        if(!result){
            console.log("testInsertItem5 is WRONG at insert 4");
            return;
        }
        result = rule.insertItemNew(lbrac);
        if(!result){
            console.log("testInsertItem5 is WRONG at insert 5");
            return;
        }
        result = rule.insertItemNew(attr2);
        if(!result){
            console.log("testInsertItem5 is WRONG at insert 6");
            return;
        }
        result = rule.insertItemNew(rbrac);
        if(!result){
            console.log("testInsertItem5 is WRONG at insert 7");
            return;
        }
        result = rule.insertItemNew(lbrac);
        if(result){
            console.log("testInsertItem5 is WRONG at insert 8");
            return;
        }
        result = rule.insertItemNew(attr1);
        if(result){
            console.log("testInsertItem5 is WRONG at insert 9");
            return;
        }
        result = rule.insertItemNew(oper1);
        if(result){
            console.log("testInsertItem5 is WRONG at insert 10");
            return;
        }
        result = rule.insertItemNew(rbrac);
        if(!result){
            console.log("testInsertItem5 is WRONG at insert 11");
            return;
        }
        result = rule.insertItemNew(and);
        if(!result){
            console.log("testInsertItem5 is WRONG at insert 12");
            return;
        }
        result = rule.insertItemNew(neg);
        if(!result){
            console.log("testInsertItem5 is WRONG at insert 13");
            return;
        }
        result = rule.insertItemNew(attr3);
        if(!result){
            console.log("testInsertItem5 is WRONG at insert 14");
            return;
        }
        result = rule.insertItemNew(rbrac);
        if(result){
            console.log("testInsertItem5 is WRONG at insert 15");
            return;
        }


        console.log("testInsertItem9 is OK");
    },

    /**
     * Function: testInsertItem10
     * It tests insertItem function of class AsociationRule
     * (oper1) (and) (or) (rbrac) lbrac attr rbrac oper1 (oper4) oper2 (oper5) oper3
     */
    testInsertItem10: function(){
        var attr11 = new Attribute("attr5","",new Array());
        var oper1 = new InterestMeasure("oper1","","","","","","","");
        var oper2 = new InterestMeasure("oper2","","","","","","","");
        var oper3 = new InterestMeasure("oper3","","","","","","","");
        var oper4 = new InterestMeasure("oper4","","","","","","","");
        var oper5 = new InterestMeasure("oper5","","","","","","","");
        
        var rbrac = new BooleanCl(")","rbrac");
        var lbrac = new BooleanCl("(","lbrac");
        var and = new BooleanCl("AND","and");
        var or = new BooleanCl("OR","or");
        
        var main = new TestServerInfo2();
        var rule = new AsociationRule(main);
        var result = null;

        result = rule.insertItemNew(oper1);
        if(result){
            console.log("testInsertItem10 is WRONG at insert 0");
            return;
        }
        result = rule.insertItemNew(and);
        if(result){
            console.log("testInsertItem10 is WRONG at insert 1");
            return;
        }
        result = rule.insertItemNew(or);
        if(result){
            console.log("testInsertItem10 is WRONG at insert 2");
            return;
        }
        result = rule.insertItemNew(rbrac);
        if(result){
            console.log("testInsertItem10 is WRONG at insert 3");
            return;
        }
        result = rule.insertItemNew(lbrac);
        if(!result){
            console.log("testInsertItem10 is WRONG at insert 4");
            return;
        }
        result = rule.insertItemNew(attr11);
        if(!result){
            console.log("testInsertItem10 is WRONG at insert 5");
            return;
        }
        result = rule.insertItemNew(rbrac);
        if(!result){
            console.log("testInsertItem10 is WRONG at insert 6");
            return;
        }
        result = rule.insertItemNew(oper1);
        if(!result){
            console.log("testInsertItem10 is WRONG at insert 7");
            return;
        }
        result = rule.insertItemNew(oper4);
        if(result){
            console.log("testInsertItem10 is WRONG at insert 8");
            return;
        }
        result = rule.insertItemNew(oper2);
        if(!result){
            console.log("testInsertItem10 is WRONG at insert 9");
            return;
        }
        result = rule.insertItemNew(oper5);
        if(result){
            console.log("testInsertItem10 is WRONG at insert 10");
            return;
        }
        result = rule.insertItemNew(oper3);
        if(!result){
            console.log("testInsertItem10 is WRONG at insert 11");
            return;
        }

        console.log("testInsertItem10 is OK");
    },

    testChangeItem: function(){

    },

    testRemoveItem: function(){

    },

    test: function(){
        this.testInsertItem();
        this.testSolveAmounts();
        this.testSolveSupportedInterestMeasures();
        this.testCountAttrsCons();
        this.testCountAttrsAnt();
        this.testCountOpers();
        this.testCountNegation();
        this.testGetBracketDepth();
        this.testGetExistingInterestMeasures();
        this.testInsertItemS1();
        this.testInsertItemS2();
        this.testInsertItem1();
        this.testInsertItem2();
        this.testInsertItem3();
        this.testInsertItem4();
        this.testInsertItem5();
        this.testInsertItem6();
        this.testInsertItem7();
        this.testInsertItem8();
        this.testInsertItem9();
        this.testInsertItem10();
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
    }
})

var TestServerInfo = new Class({
    initialize: function(){
        
    },

    getMinValues: function(name){
        return 0;
    },
    
    getMaxValues: function(name){
        if(name == "general"){
            return 10;
        }
        else{
            return 8;
        }
    },

    getSupportedIMCombinations: function(){
        return new Array();
    },

    getDepthNesting: function(){
        return 6;
    },

    getDepthLevels: function(){
        var depth = new DepthNesting();
        var CONJ_TRUE = "true";
        var CONJ_FALSE = "false";
        var DISJ_TRUE = "true";
        var DISJ_FALSE = "false"
        var NEG_TRUE = "true";
        var NEG_FALSE = "false";

        depth.add(DISJ_TRUE, CONJ_TRUE, NEG_TRUE);
        depth.add(DISJ_TRUE, CONJ_TRUE, NEG_TRUE);
        depth.add(DISJ_TRUE, CONJ_TRUE, NEG_TRUE);
        depth.add(DISJ_TRUE, CONJ_TRUE, NEG_TRUE);
        depth.add(DISJ_TRUE, CONJ_TRUE, NEG_TRUE);
        depth.add(DISJ_TRUE, CONJ_TRUE, NEG_TRUE);
        return depth;
    }
});

var TestServerInfo1 = new Class({
    initialize: function(){

    },

    getMaxValues: function(name){
        if(name == "general"){
            return 10;
        }
        else{
            return 8;
        }
    },

    getMinValues: function(name){
        return 0;
    },

    getSupportedIMCombinations: function(){
        var supIMCom = new Array();
        var supCom1 = new Array();
        supCom1.push("oper1");
        supIMCom.push(supCom1);
        return supIMCom;
    },

    getDepthNesting: function(){
        return 6;
    },

    getDepthLevels: function(){
        var depth = new DepthNesting();
        var CONJ_TRUE = "true";
        var CONJ_FALSE = "false";
        var DISJ_TRUE = "true";
        var DISJ_FALSE = "false"
        var NEG_TRUE = "true";
        var NEG_FALSE = "false";

        depth.add(DISJ_TRUE, CONJ_TRUE, NEG_TRUE);
        depth.add(DISJ_TRUE, CONJ_TRUE, NEG_TRUE);
        depth.add(DISJ_TRUE, CONJ_TRUE, NEG_TRUE);
        depth.add(DISJ_TRUE, CONJ_TRUE, NEG_TRUE);
        depth.add(DISJ_TRUE, CONJ_TRUE, NEG_TRUE);
        depth.add(DISJ_TRUE, CONJ_TRUE, NEG_TRUE);
        return depth;
    }
});

var TestServerInfo2 = new Class({
    initialize: function(){

    },

    getMaxValues: function(name){
        if(name == "general"){
            return 10;
        }
        else{
            return 8;
        }
    },

    getMinValues: function(name){
        return 1;
    },

    getSupportedIMCombinations: function(){
        var supIMCom = new Array();
        var supCom1 = new Array();
        supCom1.push("oper1");
        supCom1.push("oper2");
        var supCom2 = new Array();
        supCom2.push("oper1");
        supCom2.push("oper2");
        supCom2.push("oper3");
        supIMCom.push(supCom1);
        supIMCom.push(supCom2);
        return supIMCom;
    },

    getDepthNesting: function(){
        return 6;
    },

    getDepthLevels: function(){
        var depth = new DepthNesting();
        var CONJ_TRUE = "true";
        var CONJ_FALSE = "false";
        var DISJ_TRUE = "true";
        var DISJ_FALSE = "false"
        var NEG_TRUE = "true";
        var NEG_FALSE = "false";

        depth.add(DISJ_TRUE, CONJ_TRUE, NEG_TRUE);
        depth.add(DISJ_TRUE, CONJ_TRUE, NEG_TRUE);
        depth.add(DISJ_TRUE, CONJ_TRUE, NEG_TRUE);
        depth.add(DISJ_TRUE, CONJ_TRUE, NEG_TRUE);
        depth.add(DISJ_TRUE, CONJ_TRUE, NEG_TRUE);
        depth.add(DISJ_TRUE, CONJ_TRUE, NEG_TRUE);
        return depth;
    }
});

testClas = new AsociationRuleTest();
testClas.test();
