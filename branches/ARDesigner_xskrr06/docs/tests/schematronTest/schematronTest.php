<head>
    <title>Schematron sample</title>
</head>
<?php
include("../../lib/Schematron.php");
                 
print("<h1>Schematron Test</h1>");

$s = new Schematron();

$fp = fopen("validation_sample1.xml", "r");
$uncompiled = fread($fp, filesize("validation_sample1.xml"));
fclose($fp);

$fp = fopen("sample1.xml", "r");
$xml = fread($fp, filesize("sample1.xml"));
fclose($fp);


/* Uncompiled tests */
print("<h3>Testing Schematron for uncompiled scripts</h3>");

/* FILE USING FILE */
$ret = $s->schematron_validate_file_using_file("sample1.xml", "validation_sample1.xml");
print("<textarea rows='10' cols='20'>$ret</textarea>");

/* FILE USING MEM */
$ret = $s->schematron_validate_file_using_mem("sample1.xml", $uncompiled);
print("<textarea rows='10' cols='20'>$ret</textarea>");

/* MEM USING FILE */
$ret = $s->schematron_validate_mem_using_file($xml, "validation_sample1.xml");
print("<textarea rows='10' cols='20'>$ret</textarea>");

/* MEM USING MEM */
$ret = $s->schematron_validate_mem_using_mem($xml, $uncompiled);
print("<textarea rows='10' cols='20'>$ret</textarea>");

/* * ** COMPILED SCRIPTS * */

print("<h3>Now testing for compiled scripts</h3>");
/* FILE USING FILE */
$ret = $s->schematron_validate_file_using_compiled_file("sample1.xml", "validation1.xsl");
print("<textarea rows='10' cols='20'>$ret</textarea>");

/* FILE USING MEM */
$ret = $s->schematron_validate_file_using_compiled_mem("sample1.xml", $compiled);
print("<textarea rows='10' cols='20'>$ret</textarea>");

/* MEM USING FILE */
$ret = $s->schematron_validate_mem_using_compiled_file($xml, "validation1.xsl");
print("<textarea rows='10' cols='20'>$ret</textarea>");

/* MEM USING MEM */
$ret = $s->schematron_validate_mem_using_compiled_mem($xml, $compiled);
print("<textarea rows='10' cols='20'>$ret</textarea>");
?>
?>
