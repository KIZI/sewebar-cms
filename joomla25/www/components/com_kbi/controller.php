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

define('COM_KBI_ADMIN', JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_kbi');

jimport( 'joomla.application.component.controller' );
JPluginHelper::importPlugin('kbi', 'base');
JLoader::import('KBIntegrator', JPATH_LIBRARIES . DS . 'kbi');
JLoader::import('transformator', JPATH_COMPONENT . DS . 'models');
JLoader::import('sources', COM_KBI_ADMIN . DS . 'models');
/**
 *
 *
 * @package com_kbi
 */
class KbiControllerTransformator extends JController
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

		try {
			$model = new KbiModelTransformator($config);
			$view->assignRef('value', $model->transform());
		} catch (Exception $e) {
			$view->assign('value', "<p class=\"kbierror\">Chyba dotazu: {$e->getMessage()}</p>");
		}

		$view->display();
	}

    function cancelQuery()
    {
        $viewName = JRequest::getVar('view', 'results');
        $viewType = 'raw';

        $view =& $this->getView($viewName, $viewType);

        $config = array(
            'source' => JRequest::getVar('source', NULL, 'default', 'none', JREQUEST_ALLOWRAW)
        );

        $taskName = JRequest::getVar('query', NULL, 'default', 'none');

        try {
            $model = new KbiModelTransformator($config);
            $view->assignRef('value', $model->cancelQuery($taskName));
        } catch (Exception $e) {
            $view->assign('value', "<p class=\"kbierror\">Chyba dotazu: {$e->getMessage()}</p>");
        }

        $view->display();
    }

	function dataDescription()
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

		try {
			$model = new KbiModelTransformator($config);
			$view->assignRef('value', $model->getDataDescription());
		} catch (Exception $e) {
			$view->assign('value', "<p class=\"kbierror\">Chyba dotazu: {$e->getMessage()}</p>");
		}

		$view->display();
	}

	function storeDocument()
	{
		//$view =& $this->getView('synchronize', $document->getType());

		$id = JRequest::getVar('source', null, 'method', 'int');
		$pmml = JRequest::getVar('content', NULL, 'default', 'none', JREQUEST_ALLOWRAW);
		$pmml_id = JRequest::getVar('id', time(), 'default', 'none');
		$pmml_name = JRequest::getVar('title', "Document " . date("Y-m-d H:i:s"), 'default', 'none');

		$model = new KbiModelSources();
		$sourceConfig = $model->getSource($id);
		$source = KBIntegrator::create(get_object_vars($sourceConfig));

		$document = (object) array(
			'id' => $pmml_id,
			'title' => $pmml_name,
			'modified' => date("Y-m-d H:i:s"),
			'text'=> $pmml,
			'reportUri' => '',
		);

		try	{
			if($document && $source instanceof ISynchronable) {
				$source->addDocument($document->id, $document, FALSE);

				echo json_encode($document);
				//$view->assignRef('document', $document);
			}
		} catch(Exception $ex) {
			//TODO: add document title to error message
			echo json_encode(
				array(
					'error' => $ex->getMessage()
				)
			);
		}


	}
}
