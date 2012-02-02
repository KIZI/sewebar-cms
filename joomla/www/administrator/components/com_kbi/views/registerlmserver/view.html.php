<?php
/**
 * @version		$Id: view.html.php 15 2011-02-11 00:57:01Z hazucha.andrej@gmail.com $
 * @package		com_kbi
 * @author		Andrej Hazucha
 * @copyright	Copyright (C) 2010 All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.view' );

/**
 * Admin list view for sources.
 *
 * @package com_kbi
 */
class KbiViewRegisterlmserver extends JView
{
	function setToolbar()
	{
		JToolBarHelper::title( JText::_( 'Register LM Connect server as KBI source' ), 'generic.png' );
		JToolBarHelper::apply('apply');
		JToolBarHelper::cancel('cancel');
	}

	function getTypes()
	{
		return array(
			array('id' => 'AccessConnection', 'name' => 'Access'),
			array('id' => 'MySQLConnection', 'name' => 'MySQL'),
			array('id' => 'ODBCConnection', 'name' => 'ODBC'),
		);
	}
	
	function display($tpl = NULL)
	{
		global $option, $mainframe;
		self::setToolbar();

		JHTML::_('behavior.tooltip');

		JRequest::setVar('hidemainmenu', 1);
		$id = JRequest::getVar('id', array(0), 'method', 'array');

		$model =& $this->getModel();
		$user =& JFactory::getUser();
		$server = $model->getLmserver($id[0]);
		
		$lists = array();
		$lists['types'] = JHTML::_('select.genericlist',  $this->getTypes(), 'type', '', 'id', 'name', 'MySQLConnection');

		$this->assignRef('row', $server);
		$this->assignRef('option', $option);
		$this->assignRef('name', $user->name);
		$this->assignRef('lists', $lists);

		parent::display($tpl);
	}
}
?>