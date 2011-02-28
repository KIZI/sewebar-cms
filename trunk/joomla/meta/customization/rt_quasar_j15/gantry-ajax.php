<?php
/**
 * @package		gantry
 * @version		@VERSION@ @BUILD_DATE@
 * @author		RocketTheme http://www.rockettheme.com
 * @copyright 	Copyright (C) 2007 - @COPYRIGHT_YEAR@ RocketTheme, LLC
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
 *
 */

global $mainframe, $gantry;

// no direct access
defined( '_JEXEC' ) or die( 'Restricted index access' );

// comment out the following 2 lines for debugging
$request = @$_SERVER['HTTP_X_REQUESTED_WITH'];
if(!isset($request) || strtolower($request) != 'xmlhttprequest') die("Direct access not allowed.");

// get current template
$template = &JFactory::getApplication()->getTemplate();

// load and inititialize gantry class
require_once(JPATH_THEMES."/$template/lib/gantry/gantry.php");
$gantry->init();

$modelsPath = $gantry->gantryPath . '/ajax-models/';
$model = $modelsPath . JRequest::getString('model') . '.php';

if (!file_exists($model)) die();

include_once($model);


/* 
	- USAGE EXAMPLE -

	new Ajax('http://url/template/?tmpl=gantry-ajax', {
		onSuccess: function(response) {console.log(response);}
	}).request({
	   	'model': 'example', // <- mandatory, see "ajax-models" folder
	   	'example': 'example1', // <-- from here are all custom query posts you can use
	   	'name': 'w00fz',
	   	'message': 'Hello World!'
	});
*/


?>