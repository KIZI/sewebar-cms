<?php
/**
 * @version		$Id$
 * @package		com_ardesigner
 * @author		Andrej Hazucha
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.view' );

/**
 * Renders ARDesigner's main window with included all necessarry JavaScripts.
 *
 * @package com_ardesigner
 */
class ARDesignerViewARDesigner extends JView
{
	function display($tpl = NULL)
	{
		$component = JRequest::getVar('option', 'com_ardesigner');
		$root = 'components'. DS . $component . DS;
		$ardesigner = $root . 'ardesigner' . DS;
		$app = JFactory::getApplication();
		$document =& JFactory::getDocument();

		$jslib = $ardesigner . 'js/';
		JHTML::script('Mootools.js', $jslib);
		JHTML::script('MootoolsMore.js', $jslib);
		JHTML::script('Prepravka.js', $jslib);
		JHTML::script('Utils.js', $jslib);

		// asocRuleGUI
		$ui = $ardesigner . 'js/asocRuleGUI/';
		JHTML::script('UtilsAR.js', $ui);
		JHTML::script('AsociationRules.js', $ui);
		JHTML::script('AsociationRule.js', $ui);
		JHTML::script('BasicStructureGUI.js', $ui);
		JHTML::script('Controls.js', $ui);
		JHTML::script('Controls.js', $ui);
		JHTML::script('DepthNesting.js', $ui);
		JHTML::script('Dragability.js', $ui);
		JHTML::script('Elements.js', $ui);
		JHTML::script('FieldAR.js', $ui);
		JHTML::script('ServerInfo.js', $ui);
		JHTML::script('LanguageSupport.js', $ui);
		JHTML::script('Tree.js', $ui);

		JHTML::script('DomReady.js', $root . 'js/');

		//lib/hlavni.css
		$stylesheets = $ardesigner . 'assets/';
		JHTML::stylesheet('hlavni.css', $stylesheets);

		//remove mootools.js
		$headerstuff = $document->getHeadData();
		unset($headerstuff['scripts']['/media/system/js/mootools.js']);
		$document->setHeadData($headerstuff);

		parent::display($tpl);
	}
}
?>