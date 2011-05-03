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
JLoader::import('KBIntegrator', JPATH_PLUGINS . DS . 'kbi');

/**
 * Admin detail view for sources.
 *
 * @package com_kbi
 */
class KbiViewSource extends JView
{
	function setToolbar()
	{
		$task = JRequest::getVar( 'task', '', 'method', 'string');

		JToolBarHelper::title( JText::_( 'KBI Source' ) . ($task == 'add' ? ': <small><small>[ '. JText::_( 'New' ) .' ]</small></small>' : ': <small><small>[ '. JText::_( 'Edit' ) .' ]</small></small>'), 'generic.png' );
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

		$document = &JFactory::getDocument();
		$model =& $this->getModel();
		$user =& JFactory::getUser();
		$source = $model->getSource($id[0]);

		$lists = array();
		$lists['types'] = JHTML::_('select.genericlist',  $this->getTypes(), 'type', '', 'id', 'name', $source != NULL ? $source->type : NULL);
		$lists['methods'] = JHTML::_('select.genericlist',  $this->getMethods(), 'method', '', 'id', 'name', $source != NULL ? $source->method : NULL);

		$this->assignRef('row', $source);
		if(!empty($source))
			$this->assignRef('source', KBIntegrator::create(get_object_vars($source)));
		$this->assignRef('option', $option);
		$this->assignRef('name', $user->name);
		$this->assignRef('lists', $lists);

		$style = "#dictionaryLink.ajax-loading {background: url('/sewebar/components/com_kbi/assets/loader.gif') no-repeat center right; padding-right: 20px;}";
		$style.= ' ';
		$style.= "#dictionaryLink.ajax-error {background: url('/sewebar/components/com_kbi/assets/warning-icon.png') no-repeat center right; padding-right: 20px;}";
		$document->addStyleDeclaration($style);

		parent::display($tpl);
	}

	function getTypes()
	{
		return array(
			array('id' => 'GENERIC', 'name' => 'Generic XML+XSLT'),
			array('id' => 'ONTOPIA', 'name' => 'Ontopia'),
			array('id' => 'SPARQL', 'name' => 'Semsol SPARQL endpoint'),
			array('id' => 'XQUERY', 'name' => 'XQuery'),
			array('id' => 'JUCENE', 'name' => 'Jucene'),
		);
	}

	function getMethods()
	{
		return array(
			array('id' => 'GET', 'name' => 'GET'),
			array('id' => 'POST', 'name' => 'POST'),
			array('id' => 'SOAP', 'name' => 'SOAP'),
		);
	}
}
?>