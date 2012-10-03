<?php

$config = [
    'DDPath' => APP_PATH.'/data/datadescription_0.2.xml',
    'FLPath' => [
        APP_PATH.'/data/featureList_beginner.xml',
        APP_PATH.'/data/featureList_exploration.xml'
    ],
    'FGCPath' => APP_PATH.'/data/fieldGroupConfig.xml',
    'ERPath' => null,
    'ETreePath' => null,
    'FAPath' => APP_PATH.'/data/frequencyAnalysis.xml',
    'KB_CONF_ID' => 6, // KBI query ID - confirmation
    'KB_EXC_ID' => 7, // KBI query ID - exception
];

foreach ($config as $k => $c) {
    is_array($c) ? define($k, serialize($c)) : define($k, $c);
}

// num curl error requests to be considered as failure
define('MAX_INITIALIZATION_REQUESTS', 15);
define('MAX_MINING_REQUESTS', 10);
define('MAX_ETREE_REQUESTS', 10);

// delay between curl requests
define('REQUEST_DELAY', 1);

// PHP error reporting
error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors', 1);

// PHP date timezone
date_default_timezone_set('Europe/Paris');

// FB logging
define('FB_ENABLED', class_exists('FB'));