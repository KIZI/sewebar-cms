<?php 
/**
 * @version		$Id: view.html.php 1602 2010-11-06 12:21:19Z andrej $
 * @package		com_kbi
 * @author		Andrej Hazucha
 * @copyright	Copyright (C) 2010 All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.view' );

/**
 * Admin detail view for queries.
 *
 * @package com_kbi
 */
class KbiViewQuery extends JView
{	
	function setToolbar()
	{
		$task = JRequest::getVar( 'task', '', 'method', 'string');

		JToolBarHelper::title( JText::_( 'KBI Query' ) . ($task == 'add' ? ': <small><small>[ '. JText::_( 'New' ) .' ]</small></small>' : ': <small><small>[ '. JText::_( 'Edit' ) .' ]</small></small>'), 'generic.png' );
		JToolBarHelper::save('save');
		JToolBarHelper::apply('apply');
		JToolBarHelper::cancel('cancel');
	}
	
	function display($tpl = NULL)
	{
		global $option, $mainframe;
		self::setToolbar();
		
		JRequest::setVar( 'hidemainmenu', 1 );
		$id = JRequest::getVar('id', array(0), 'method', 'array');
			
		$model =& $this->getModel();
		$user =& JFactory::getUser();
		$source = $model->getQuery($id[0]);
		
		// ARBuilder
		$arbuilder = JComponentHelper::getComponent('com_ardesigner', true);
		if($arbuilder->enabled) {
			$url = "/index.php?option=com_ardesigner&controller=ardesigner&tmpl=component&id_query={$id[0]}";
			$attrs = array(
				'target' => '_blank',
				'onclick' => "window.open(this.href,'ardesigner','width=1024,height=600,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,copyhistory=no,resizable=yes');return false;",
			);
			
			$this->assign('ardesigner', JHTML::_('link', $url, 'ARDesigner (Feature List, Data Dictionary Query and XSLTs of query should be saved)', $attrs));
		} else {
			$this->assign('ardesigner', NULL);
		}
		$this->assignRef('row', $source);		
		$this->assignRef('option', $option);
		$this->assignRef('name', $user->name);
		
		parent::display($tpl);
	}
}
?>