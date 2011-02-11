<?php
/**
 * @version		$Id$
 * @package		com_kbi
 * @author		Andrej Hazucha
 * @copyright	Copyright (C) 2010 All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );
JPluginHelper::importPlugin('kbi', 'base');
JLoader::import('KBIntegrator', JPATH_PLUGINS . DS . 'kbi');
JLoader::import('transformator', JPATH_COMPONENT_ADMINISTRATOR . DS . 'models');

/**
 *
 *
 * @package com_kbi
 */
class KbiControllerServer extends JController
{
	function display()
	{
		$viewName = JRequest::getVar('view', 'demo');
		$viewType = 'html';

		$view =& $this->getView($viewName, $viewType);

		$view->display();
	}

	function query()
	{
		$viewName = JRequest::getVar('view', 'results');
		$viewType = 'raw';

		$view =& $this->getView($viewName, $viewType);

		$config = array(
			'source' => JRequest::getVar('source', NULL, 'default', 'none', JREQUEST_ALLOWRAW),
			'query' => JRequest::getVar('query', NULL, 'default', 'none', JREQUEST_ALLOWRAW),
			'xslt' => JRequest::getVar('xslt', NULL, 'default', 'none', JREQUEST_ALLOWRAW),
			'parameters' => JRequest::getVar('parameters', NULL, 'default', 'none', JREQUEST_ALLOWRAW)
		);

		$model = new KbiModelTransformator($config);
		$view->assignRef('value', $model->transform());

		//$view->assign('value', JPATH_COMPONENT_ADMINISTRATOR);
		$view->display();
	}
}
