<?php
/**
 * @package     gantry
 * @subpackage  features
 * @version		@VERSION@ @BUILD_DATE@
 * @author		RocketTheme http://www.rockettheme.com
 * @copyright 	Copyright (C) 2007 - @COPYRIGHT_YEAR@ RocketTheme, LLC
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
 *
 */

defined('JPATH_BASE') or die();

gantry_import('core.gantryfeature');

/**
 * @package     gantry
 * @subpackage  features
 */
class GantryFeatureIEQuasar extends GantryFeature {
    var $_feature_name = 'iequasar';

    function isEnabled(){
        return true;
    }
    function isInPosition($position) {
        return false;
    }
	function isOrderable(){
		return false;
	}
	

	function init() {
		global $gantry;

		if ($gantry->browser->name == 'ie' && $gantry->browser->shortversion == '6') {
			$gantry->addScript('suckerfish-ie6.js');
		}
	}
}