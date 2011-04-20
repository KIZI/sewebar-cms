<?php
/**
 * @version		$Id: view.html.php 206 2011-04-20 05:18:53Z hazucha.andrej@gmail.com $
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
class KbiViewQuerydefinition extends JView
{
	function setToolbar()
	{
		$task = JRequest::getVar('task', '', 'method', 'string');

		JToolBarHelper::title(JText::_('KBI Query Definition') . ($task == 'add' ? ': <small><small>[ '. JText::_( 'New' ) .' ]</small></small>' : ': <small><small>[ '. JText::_('Edit') .' ]</small></small>'), 'generic.png' );
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
		$query = $model->getQuery($id[0]);

		$lists = array();

		// sources
		$lists['sources'] = JHTML::_('select.genericlist',  $this->sources, 'sources', '', 'id', 'name', !empty($query) ? $query->source : '');

		// queries
		$lists['queries'] = JHTML::_('select.genericlist',  $this->queries, 'query', '', 'id', 'name', !empty($query) ? $query->query : '');

		// xslt
		$lists['xslt'] = JHTML::_('select.genericlist',  $this->xslts, 'xslt', '', 'id', 'name', !empty($query) ? $query->xslt : '');

		// ARDesigner
		/*$ardesigner = JComponentHelper::getComponent('com_ardesigner', true);
		if($ardesigner->enabled) {
			if(!empty($query) && !empty($query->featurelist)) {
				$url = "/index.php?option=com_ardesigner&tmpl=component&id_query={$id[0]}";
				$attrs = array(
					'target' => '_blank',
					'onclick' => "window.open(this.href,'ardesigner','width=1024,height=600,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,copyhistory=no,resizable=yes');return false;",
				);

				$this->assign('ardesigner', JHTML::_('link', $url, 'ARDesigner', $attrs));
			} else {
				$this->assign('ardesigner', '<span style="color: gray;">ARDesigner</span>');
			}
		} else {
			$this->assign('ardesigner', NULL);
		}

		JHTML::_('script', 'ardesigner.js', 'administrator/components/com_kbi/assets/');
		*/

		$this->assignRef('row', $query);
		$this->assignRef('option', $option);
		$this->assignRef('name', $user->name);
		$this->assignRef('lists', $lists);

		parent::display($tpl);
	}
}
?>