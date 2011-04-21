It shows performance of application when there are 30 rules. Each with
approximately 8 elements.

Running:
{Root of application}/AsocRuleGUI/Domready.js
Change line:
    asocRule = new AsociationRules("en","testGetInfo.php","testSetInfo.php"); //third param is the page on server to be called at saving rules
    To:
    asocRule = new AsociationRules("en","profiling/30Rules/testGetInfo.php","testSetInfo.php"); //third param is the page on server to be called at saving rules


