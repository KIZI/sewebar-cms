<?php
/**
 * @package		Gantry Template Framework - RocketTheme
 * @version		2.0.12 February 12, 2010
 * @author		RocketTheme http://www.rockettheme.com
 * @copyright 	Copyright (C) 2007 - 2010 RocketTheme, LLC
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
 *
 */

global $mainframe, $gantry;

// no direct access
defined( '_JEXEC' ) or die( 'Restricted index access' );
if (!$mainframe->isAdmin()) die();

// comment out the following 2 lines for debugging
$request = @$_SERVER['HTTP_X_REQUESTED_WITH'];
if(!isset($request) || strtolower($request) != 'xmlhttprequest') die("Direct access not allowed.");

// get current template
$db =& JFactory::getDBO();
$query = 'SELECT template'
			. ' FROM #__templates_menu'
			. ' WHERE client_id = 0 AND (menuid = 0 OR menuid = 0)'
			. ' ORDER BY menuid DESC';
$db->setQuery($query, 0, 1);
$template = $db->loadResult();
$requestedTemplate = JRequest::getString('template');

if (isset($requestedTemplate) && is_string($requestedTemplate)) $template = $requestedTemplate;

// load and inititialize gantry class
require_once(JPATH_THEMES."/../../templates/$template/lib/gantry/gantry.php");
//$gantry->init();

$modelsPath = $gantry->gantryPath . '/admin/ajax-models/';
$model = $modelsPath . JRequest::getString('model') . '.php';

if (!file_exists($model)) die();

include_once($model);
/* 
	- USAGE EXAMPLE -

	new Ajax('http://url/template/administrator/index.php?option=com_admin&tmpl=gantry-ajax-admin', {
		onSuccess: function(response) {console.log(response);}
	}).request({
	   	'model': 'example', // <- mandatory, see "ajax-models" folder
		'template': 'template_folder', // <- mandatory, the name of the gantry template folder (rt_dominion_j15)
	   	'example': 'example1', // <-- from here are all custom query posts you can use
	   	'name': 'w00fz',
	   	'message': 'Hello World!'
	});
*/

// Clear the cache gantry cache after each call
$cache =& JFactory::getCache('', 'callback', 'file');
$cache->clean( 'Gantry' );

?>