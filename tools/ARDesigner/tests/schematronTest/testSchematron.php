<head>
  <title>Schematron sample</title>
</head>
<?php

  require_once("../../lib/Schematron.php");

  $s = new Schematron();
  print("<h1>Schematron Test</h1>");

  //$fp=fopen("validation_sample1.xml","r");
  $fp=fopen("ARQuery_check.sch","r");
  $uncompiled=fread($fp,filesize("ARQuery_check.sch"));
  fclose($fp);
  
  //$fp=fopen("sample1.xml","r");
  $fp=fopen("ARQuery-Sample.xml","r");
  $xml=fread($fp,filesize("ARQuery-Sample.xml"));
  fclose($fp);

  /* Uncompiled tests */
  print("<h3>Testing Schematron for uncompiled scripts</h3>");

   /* MEM USING MEM */
  $ret=$s->schematronValidate($xml,$uncompiled);
  print("<textarea rows='10' cols='20'>$ret</textarea>");
?>