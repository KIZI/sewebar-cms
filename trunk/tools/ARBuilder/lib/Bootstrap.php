<?php

// PHP native JSON is preferred
if (!function_exists('json_encode')) {
  require_once 'lib/JSON.php';
}

// class autoloader
function __autoload($class_name) {
    $paths = array('lib', 'lib'.DIRECTORY_SEPARATOR.'exceptions', 'lib'.DIRECTORY_SEPARATOR.'parseData', 
    		   'lib'.DIRECTORY_SEPARATOR.'serializeRules', 'lib'.DIRECTORY_SEPARATOR.'algorithms');
    foreach ($paths as $p) {
        $path = APP_PATH.DIRECTORY_SEPARATOR.$p.DIRECTORY_SEPARATOR.$class_name.'.php';
        if (file_exists($path)) {
            require_once $path;
            break;
        }
    }
}

// start session
if (session_id() === '') { session_start(); }

// init session
$_SESSION['DDPath'] = DDPath;
$_SESSION['FLPath'] = FLPath;
$_SESSION['FGCPath'] = FGCPath;
$_SESSION['ERPath'] = ERPath;
$_SESSION['lang'] = LANG;