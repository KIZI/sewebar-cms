<?php
/**
 * @version		$Id: transformator.php 1765 2011-01-29 22:40:14Z andrej $
 * @package		com_kbi
 * @author		Andrej Hazucha
 * @copyright	Copyright (C) 2010 All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );
JPluginHelper::importPlugin('kbi', 'base');

/**
 * Controller for transformator. Transformator calls KBI library's query with combination of source, query and xslt.
 *
 * @package com_kbi
 */
class KbiControllerTransformator extends JController
{
	function display()
	{
		$viewName = JRequest::getVar('view', 'transformator');
		$viewType = 'raw';

		$view =& $this->getView($viewName, $viewType);

		$model = &$this->getModel('transformator', '',
			array(
				'id_source' => JRequest::getVar('source', NULL),
				'id_query' => JRequest::getVar('query', NULL),
				'id_xslt' => JRequest::getVar('xslt', NULL),
				'parameters' => JRequest::getVar('parameters', NULL)
			)
		);

		$view->assignRef('value', $model->transform());
		$view->display();
	}
}
