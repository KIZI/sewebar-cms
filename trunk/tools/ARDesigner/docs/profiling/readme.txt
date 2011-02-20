This directory contains information about profiling which was done and a way to start it again. 

Structure of these tests is:
xml files necessary to start the profiling.
testGetData.php modified so it works with these data.
profileData contains data from Firebug profiler.

Running:
{Root of application}/AsocRuleGUI/Domready.js
Change line:
    asocRule = new AsociationRules("en","testGetInfo.php","testSetInfo.php"); //third param is the page on server to be called at saving rules
Instead testGetInfo.php change it to location where testGetInfo to this profiling is. If there is profiling file in directory {Root of application}/profiling/30rules then it should look like:
	asocRule = new AsociationRules("en","profiling/30rules/testGetInfo.php","testSetInfo.php"); //third param is the page on server to be called at saving rules

