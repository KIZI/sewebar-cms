It shows how fast is application when there there is rule with 30 elements.
                        
Running:
{Root of application}/AsocRuleGUI/Domready.js
Change line:
    asocRule = new AsociationRules("en","testGetInfo.php","testSetInfo.php"); //third param is the page on server to be called at saving rules
    To:
    asocRule = new AsociationRules("en","profiling/30ElementsInRule/testGetInfo.php","testSetInfo.php"); //third param is the page on server to be called at saving rules

