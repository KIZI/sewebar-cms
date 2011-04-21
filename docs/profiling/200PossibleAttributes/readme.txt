It shows performance of application when there are approximately 200 attributes
and the same amount of Interest measures.

Running:
{Root of application}/AsocRuleGUI/Domready.js
Change line:
    asocRule = new AsociationRules("en","testGetInfo.php","testSetInfo.php"); //third param is the page on server to be called at saving rules
    To:
    asocRule = new AsociationRules("en","profiling/200PossibleAttributes/testGetInfo.php","testSetInfo.php"); //third param is the page on server to be called at saving rules

