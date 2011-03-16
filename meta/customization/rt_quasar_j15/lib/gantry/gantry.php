<?php
/**
 * @package		gantry
 * @version		2.0.12 February 12, 2010
 * @author		RocketTheme http://www.rockettheme.com
 * @copyright 	Copyright (C) 2007 - 2010 RocketTheme, LLC
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
 *
 */

defined('JPATH_BASE') or die();

global $mainframe;
if (!defined('GANTRY_VERSION')) {
    /**
     * @global Gantry $gantry
     */
    global $gantry;
    
    /**
     * @name GANTRY_VERSION
     */
    define('GANTRY_VERSION', "\2.0.12");

    if (!defined('DS')) {
        define('DS', DIRECTORY_SEPARATOR);
    }

    /**
     * @param  string $path the gantry path to the class to import
     * @return 
     */
    function gantry_import($path) {
        require_once (realpath(dirname(__FILE__)) . '/core/gantryloader.class.php');
        return GantryLoader::import($path);
    }

    gantry_import('core.gantrysingleton');
    gantry_import('core.gantry');

    if (!$mainframe->isAdmin()) {
        $doc = & JFactory :: getDocument();
        $conf = & JFactory :: getConfig();
    }

    if (!$mainframe->isAdmin() && !is_array($doc->params) && ($doc->params->get("cache-enabled", 0) == 1)) {
        $user = & JFactory :: getUser();
        $cache = & JFactory :: getCache('Gantry');
        $cache->setCaching(true);
        $cache->setLifeTime($doc->params->get("cache-time", $conf->getValue('config.cachetime') * 60));
        $gantry = $cache->get(array('GantrySingleton','getInstance'), array('Gantry'), 'Gantry'.$user->get('aid', 0));
    } else {
        $gantry = GantrySingleton :: getInstance('Gantry');
    }
}