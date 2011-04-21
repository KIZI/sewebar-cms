Name   : ARBuilder
Version: Release 1.0
Author : Jakub Balhar
Contact: balhar.jakub@gmail.com

What is it:
It is application which purpose is to allow anyone create and edit constructs
similar to Asociation Rules.

Architecture:
Basically it has two parts. First part was written in PHP and is tightly connected
to these XML schemas:
ARFeature - http://bazaar.launchpad.net/~sewebar-team/sewebar-cms/trunk/annotate/head:/Specifications/Common/current/validation/GUHAARfeature.xsd
DataDescription - http://bazaar.launchpad.net/~sewebar-team/sewebar-cms/trunk/annotate/head:/Specifications/Common/current/validation/DataDescription0_1.xsd
ARBuilder - http://bazaar.launchpad.net/~sewebar-team/sewebar-cms/trunk/annotate/head:/Specifications/Common/current/validation/ARBuilder0_1.xsd
At this moment it implements support for parsing XML that is valid by ARFeature and
DataDescription xsd. These XML files supports it with data it needs to create itself.
These data are send to second part. Then the user works with the second part and
first part gets from second part data that user inserted and serialize these data
into XML that is valid by ARBuilder xsd.
There are three possible implementations of format into which it is serialized:
SerializeRulesBackgroundAssociationRules.php, SerializeRulesTaskSetting.php and
SerializeRulesARQuery.php
Second part was written in HTML, CSS and Javascript. It is the part, which should
be used by users. It creates in the web browser ARBuilder with supplied data and lets
user create and edit the constructs and save them on the server as well.

Instalation:
You need PHP server running. For the basic usage you can only copy code from sourceCode.zip
into server directory and then go to {directory of application}/createARBuilder.html.
There is a button, which creates application.
For good use you will also have to change these two files:
testGetInfo.php and testSetInfo.php
testGetInfo.php
$DD = "/datadescription.xml"; Location of DataDescription
$FL = "/featurelist6.xml"; Location of ARFeature

testSetInfo.php
$sr = new SerializeRulesBackgroundAssociationRules(); Used implementation of class serializing data