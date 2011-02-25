<?php
/**
 * @version		$Id$
 * @package		com_kbi
 * @author		Andrej Hazucha
 * @copyright	Copyright (C) 2010 All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.view' );

/**
 * Admin detail view for sources.
 *
 * @package com_kbi
 */
class KbiViewDemo extends JView
{
	function display($tpl = NULL)
	{
		global $option, $mainframe;

		$document = &JFactory::getDocument();
		$document->addScript( '/media/system/js/mootools.js' );
		$document->addScript( '/components/com_kbi/assets/js.js' );

		$style = 'label{display: block;margin:5px 0;font-weight:bold;}';
		$style.= ' ';
		$style.= 'label span{color: gray; font-weight:normal; margin-left: .5em;}';
		$style.= ' ';
		$style.= '#messages{min-height: 1.5em;}';
		$document->addStyleDeclaration( $style );

		$this->url = /*$_SERVER['HTTP_HOST'].*/'/index.php?option=com_kbi&amp;&amp;task=query&amp;format=raw';

		parent::display($tpl);
	}
}