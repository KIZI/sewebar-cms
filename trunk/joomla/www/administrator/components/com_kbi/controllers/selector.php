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

jimport('joomla.application.component.controller');

/**
 * Controller for selector.
 *
 * @package com_kbi
 */
class KbiControllerSelector extends JController
{
	/**
	 * Selector is a user window for selecting source, query and coresponding XSLT.
	 *
	 */
	function display()
	{
		$document =& JFactory::getDocument();
		$viewName = JRequest::getVar('view', 'selector');
		$viewType = $document->getType();
		$view =& $this->getView($viewName, $viewType);

		$model_sources = &$this->getModel('sources');
		$view->assignRef('sources', $model_sources->getAssocList());

		$model_queries = &$this->getModel('queries');
		$view->assignRef('queries', $model_queries->getAssocList());

		$model_xslts = &$this->getModel('xslts');
		$view->assignRef('xslts', $model_xslts->getAssocList());

		$view->setLayout('default');
		$view->display();
	}

	/**
	 * Executes Params XSLT for given query
	 */
	function params()
	{
		$document =& JFactory::getDocument();
		$viewName = JRequest::getVar('view', 'params');
		$viewType = $document->getType();

		$view =& $this->getView($viewName, $viewType);

		$data = JRequest::getVar('data', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$query_id = JRequest::getInt('id_query', NULL);

		$view->assign('value', '');

		if($query_id != NULL && !empty($data)) {
			$model_queries = &$this->getModel('queries');
			$query = $model_queries->getQuery($query_id);
			if($query != NULL) {
				$xml = new DOMDocument();
				$xml->loadXML($data);

				// start xslt
				$xslt = new XSLTProcessor();
				$xsl = new DOMDocument();
				$xsl->loadXML($query->paramsxsl);
				$xslt->importStylesheet($xsl);

				$paramset = $xslt->transformToDoc($xml);
				$result = $xslt->transformToXML($xml);

				/*
				$result = '';
				$paramset = $paramset->getElementsByTagName('Params');
				foreach($paramset as $params) {
					foreach($params->childNodes as $param) {
						$result .= $param->getAttribute('name') . ":'{$param->nodeValue}', ";
					}
				}
				$result = substr($result, 0, -2);
				*/

				$view->assign('value', $result);
			}
		}

		$view->display();
	}

	/**
	 * Serializes query into source/query identificator
	 *
	 */
	function serialize()
	{
		$document =& JFactory::getDocument();
		//$document->setMimeEncoding('application/json');

		$viewName = JRequest::getVar('view', 'serialize');
		$viewType = $document->getType();
		$view =& $this->getView($viewName, $viewType);

		//'{kbi source:' + source + ' query:' + query + ' xslt:' + xslt + ' parameters:\'' + parameters + '\'}'
		$data = array(
			'source' => JRequest::getInt('source', NULL),
			'query' => JRequest::getInt('query', NULL),
		);

		$json = json_encode($data);
		$view->assignRef('json', $json);

		$view->display();
	}
}
