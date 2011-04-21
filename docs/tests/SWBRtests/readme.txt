These are tests testing intensively the PHP part of the application and therefore
the parsing XML into JSON and then serializing JSON to XML.

Every directory contains sample file, schematron validation, ARFeature that is
used, data description and test.php

Running test:
Open file test.php in your browser. Example: If you have whole application in
directory {serverRoot}/ARDesigner then the test can be in directory similar to
{serverRoot}/ARDesigner/SWBRtests/ARQuery/test1/test.php

The page shall now contain three textareas a d a bit of text inbetween. First
textarea contains JSON which was produced from XML files. Second contains xml
which was produced from said JSON and the third one contains output from schematron
validation.
Between second and third textarea shall be either: "Validation was incorrect" or
"Validation was finished correctly", which speaks about validation against XSD and
"Schematron Test" just above the third textarea.

Sample File
It is a file containing expected result of operation. It is also the file from
which are the rules parsed.

Schematron Validation
Schematron validation file, which is used in schematron validation

ARFeature and dataDescription
Necessary XML inputs to application.