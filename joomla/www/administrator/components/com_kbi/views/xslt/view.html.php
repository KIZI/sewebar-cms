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
 * Admin detail view for XSLTs.
 *
 * @package com_kbi
 */
class KbiViewXslt extends JView
{	
	function setToolbar()
	{
		$task = JRequest::getVar( 'task', '', 'method', 'string');

		JToolBarHelper::title( JText::_( 'KBI XSLT' ) . ($task == 'add' ? ': <small><small>[ '. JText::_( 'New' ) .' ]</small></small>' : ': <small><small>[ '. JText::_( 'Edit' ) .' ]</small></small>'), 'generic.png' );
		JToolBarHelper::save( 'save' );
		JToolBarHelper::apply('apply');
		JToolBarHelper::cancel( 'cancel' );
	}
	
	function display($tpl = NULL)
	{
		global $option, $mainframe;
		self::setToolbar();
		
		JRequest::setVar( 'hidemainmenu', 1 );
		$id = JRequest::getVar('id', array(0), 'method', 'array');
			
		$model =& $this->getModel();
		$user =& JFactory::getUser();
		$source = $model->getStyle($id[0]);
		//$pathway =& $mainframe->getPathWay();
		
		
		//$pathway->addItem($source->name, '');
		
		$this->assignRef('row', $source);		
		$this->assignRef('option', $option);
		//$this->assignRef('backlink', $backlink);
		$this->assignRef('name', $user->name);
		
		parent::display($tpl);
	}
}
?>