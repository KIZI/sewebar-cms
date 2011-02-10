<?php 
/**
 * @version		$Id: view.html.php 1587 2010-10-24 22:40:30Z andrej $
 * @package		com_kbi
 * @author		Andrej Hazucha
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.view' );

/**
 * Renders ARDesigner's main window with included all necessarry JavaScripts.
 *
 * @package com_kbi
 */
class KbiViewARDesigner extends JView {
	
	function display($tpl = NULL) {
		$component = JRequest::getVar('option', 'com_ardesigner');
		$root = 'components'. DS . $component . DS;
		$app = JFactory::getApplication();
		$document =& JFactory::getDocument();
		
		$jslib = $root . 'js/lib/';
		JHTML::script('Mootools.js', $jslib);
		JHTML::script('MootoolsMore.js', $jslib);
		JHTML::script('Prepravka.js', $jslib);
		JHTML::script('Utils.js', $jslib);
		
		// asocRuleGUI
		$ui = $root . 'js/asocRuleGUI/';
		JHTML::script('AsociationRules.js', $ui);
		JHTML::script('AsociationRule.js', $ui);
		JHTML::script('AttributeFields.js', $ui);
		JHTML::script('Controls.js', $ui);
		JHTML::script('DepthNesting.js', $ui);
		JHTML::script('DomReady.js', $ui);
		JHTML::script('Dragability.js', $ui);
		JHTML::script('Elements.js', $ui);
		JHTML::script('HTMLCreation.js', $ui);
		JHTML::script('ServerInfo.js', $ui);
		JHTML::script('LanguageSupport.js', $ui);
		
		//lib/hlavni.css
		$stylesheets = $root . 'assets/';
		JHTML::stylesheet('hlavni.css', $stylesheets);
		
		//remove mootools.js
		$headerstuff = $document->getHeadData();
		unset($headerstuff['scripts']['/media/system/js/mootools.js']);
		$document->setHeadData($headerstuff);
		
		parent::display($tpl);
	}
}
?>