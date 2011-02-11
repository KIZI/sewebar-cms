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
 * Selector windows itself. Selector is a user window for selecting source, query and coresponding XSLT. 
 *
 * @package com_kbi
 */
class KbiViewSelector extends JView {
	
	function display($tpl = NULL) {
		$app = JFactory::getApplication();
		$append = '';
		if($app->getClientId() == 1) $append = 'administrator/';

		JHTML::_('script', 'popup-kbimanager.js', $append .'components/com_kbi/assets/');
		
		$lists = array();
		
		// dynamic/static
		$lists['dynamic'] = JHTML::_('select.booleanlist',  'dynamic', '', false );
		
		// sources
		$lists['sources'] = JHTML::_('select.genericlist',  $this->sources, 'sources', '', 'id', 'name' );

		// queries
		$lists['queries'] = JHTML::_('select.genericlist',  $this->queries, 'query', '', 'id', 'name' );
		
		// xslt
		$lists['xslt'] = JHTML::_('select.genericlist',  $this->xslts, 'xslt', '', 'id', 'name' );
		
		// ARBuilder
		$arbuilder = JComponentHelper::getComponent('com_ardesigner', true);
		if($arbuilder->enabled) {
			$url = '/index.php?option=com_ardesigner&controller=ardesigner&tmpl=component';
			$attrs = array(
				'target' => '_blank',
				'onclick' => "window.open(this.href + '&id_query=' + $$('#query').getValue(),'ardesigner','width=1050,height=600,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,copyhistory=no,resizable=yes');return false;",
			);
			
			$this->assign('ardesigner', JHTML::_('link', $url, 'ARDesigner', $attrs));
		} else {
			$this->assign('ardesigner', NULL);
		}
		
		$this->assignRef('lists', $lists);
		
		parent::display($tpl);
	}
}
?>