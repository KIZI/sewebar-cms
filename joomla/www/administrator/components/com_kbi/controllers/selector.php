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

/**
 * Controller for selector. Selector is a user window for selecting source, query and coresponding XSLT. 
 *
 * @package com_kbi
 */
class KbiControllerSelector extends JController
{
	function display()
	{
		$document =& JFactory::getDocument();
		$viewName = JRequest::getVar('view', 'selector');
		$viewType = $document->getType();
		
		$view =& $this->getView($viewName, $viewType);
		
		if($viewName == 'selector') {
			$model_sources = &$this->getModel('sources');		
			$view->assignRef('sources', $model_sources->getAssocList());
			
			$model_queries = &$this->getModel('queries');		
			$view->assignRef('queries', $model_queries->getAssocList());
			
			$model_xslts = &$this->getModel('xslts');		
			$view->assignRef('xslts', $model_xslts->getAssocList());
			
			$declaration = "function getRules(data){
				var url = '/administrator/index.php?option=com_kbi&controller=selector&view=params&format=raw';
				var params = data;
				var query = $$('#query');
				var params_raw = $$('#parameter_raw div.text');
				params_raw.empty();		
				params_raw.appendText(data);
				
				$$('#parameters').empty().addClass('ajax-loading');
				
				new Ajax(url + '&id_query=' + query.getValue(), {
					method: 'post',
					//update: $('someelement'),
					data: {data: params},
					onComplete: function() {
						var params = $$('#parameters');
						params.removeClass('ajax-loading');		
						params.appendText(this.response.text);
					}
				}).request();
			}";
			
			$document->addScriptDeclaration($declaration);
			
			$view->setLayout('default');
		} elseif($viewName == 'params') {
			$data = JRequest::getVar('data', '', 'post', 'string', JREQUEST_ALLOWRAW);
			$query_id = JRequest::getInt('id_query', NULL);
			
			$view->assign('value', '');
			
			if($query_id != NULL && !empty($data)) {
				$model_queries = &$this->getModel('queries');
				$query = $model_queries->getQuery($query_id);
				if($query != NULL) {
					$xml = new DOMDocument(); 
					$xml->loadXML($data);
					
					// START XSLT 
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
		}
		
		$view->display();		
	}
}
