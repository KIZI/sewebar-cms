<?php
/**
 * @package Quasar Template - RocketTheme
 * @version 1.0 September 11, 2011
 * @author RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2010 RocketTheme, LLC
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
 *
 */
defined('JPATH_BASE') or die();

gantry_import('core.gantryfeature');

class GantryFeatureStyleDeclaration extends GantryFeature {
    var $_feature_name = 'styledeclaration';

    function isEnabled() {
        global $gantry;
        $menu_enabled = $this->get('enabled');

        if (1 == (int)$menu_enabled) return true;
        return false;
    }

	function init() {
        global $gantry;

		//inline css for dynamic stuff
		$css = ' body a {color:'.$gantry->get('linkcolor').';}'."\n";
		if ($gantry->browser->platform != 'iphone') {
        	$css .= 'body a, #rt-main-surround .rt-article-title, #rt-main-surround .title, #rt-showcase .title, #rt-showcase .showcase-title span, #rt-top .title, #rt-header .title, #rt-feature .title {color:'.$gantry->get('linkcolor').';}';
		} else {
			$css .= 'body a, #rt-main .rt-article-title, #rt-main .title, #rt-showcase .title, #rt-showcase .showcase-title span, #rt-top .title, #rt-header .title, #rt-feature .title {color:'.$gantry->get('linkcolor').';}';
		}



        $gantry->addInlineStyle($css);
		$this->_disableRokBoxForiPhone();

		//style stuff
        $gantry->addStyle($gantry->get('cssstyle').".css");

	}
	
	function _disableRokBoxForiPhone() {
		global $gantry;
		
		if ($gantry->browser->platform == 'iphone') {
			$gantry->addInlineScript("window.addEvent('domready', function() {\$\$('a[rel^=rokbox]').removeEvents('click');});");
		}
	}
}