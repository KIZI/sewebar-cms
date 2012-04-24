<?php

require_once '../config/Config.php';
require_once '../lib/Bootstrap.php';

$DP = new DataParser(DDPath, FLPath, FGCPath, null, null, LANG);
$DP->loadData();
echo $DP->parseData();