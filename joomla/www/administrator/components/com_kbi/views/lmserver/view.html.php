<?php
/**
 * @version		$Id: view.html.php 63 2011-02-28 21:24:04Z hazucha.andrej@gmail.com $
 * @package		com_kbi
 * @author		Andrej Hazucha
 * @copyright	Copyright (C) 2010 All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');

/**
 * Admin detail view for queries.
 *
 * @package com_kbi
 */
class KbiViewLmserver extends JView
{
	function setToolbar()
	{
		$task = JRequest::getVar('task', '', 'method', 'string');

		JToolBarHelper::title(JText::_('LispMiner Connect server') . ($task == 'add' ? ': <small><small>[ '. JText::_( 'New' ) .' ]</small></small>' : ': <small><small>[ '. JText::_('Edit') .' ]</small></small>'), 'generic.png' );
		JToolBarHelper::custom('register', 'default', '', 'Register', false, true);
		JToolBarHelper::save('save');
		JToolBarHelper::apply('apply');
		JToolBarHelper::cancel('cancel');
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
		$query = $model->getLmserver($id[0]);

		$this->assignRef('row', $query);
		$this->assignRef('option', $option);
		$this->assignRef('name', $user->name);

		parent::display($tpl);
	}
}
?>