<?php
$path = '..';
echo 'Updating "<i>'. realpath($path) . '"</i>...<br>';

echo exec("svn update $path 2>&1");

?>