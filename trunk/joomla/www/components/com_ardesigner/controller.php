<?php
/**
 * @version		$Id$
 * @package		com_ardesigner
 * @author		Andrej Hazucha
 * @copyright	Copyright (C) 2010 All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

require_once(dirname(__FILE__).'/ardesigner/models/serializeRules/AncestorSerializeRules.php');
require_once(dirname(__FILE__).'/ardesigner/models/serializeRules/SerializeRulesBackgroundAssociationRules.php');

require_once(dirname(__FILE__).'/ardesigner/models/JSON.php');
require_once(dirname(__FILE__).'/ardesigner/models/parseData/AncestorGetData.php');
require_once(dirname(__FILE__).'/ardesigner/models/parseData/GetDataARBuilderQuery.php');
require_once(dirname(__FILE__).'/ardesigner/models/parseData/AsociationRulesParser.php');
require_once(dirname(__FILE__).'/ardesigner/models/parseData/ARQueryParser.php');
require_once(dirname(__FILE__).'/ardesigner/models/parseData/TaskSettingParser.php');
require_once(dirname(__FILE__).'/ardesigner/models/Utils.php');

/**
 * Controller for ARDesigner.
 *
 * @package com_ardesigner
 */
class ARDesignerController extends JController
{
	function display()
	{
		$document =& JFactory::getDocument();
		$viewName = JRequest::getVar('view', 'ardesigner');
		$viewType = $document->getType();

		$view =& $this->getView($viewName, $viewType);
		$view->setLayout('default');
		$view->display();
	}

	/**
	 * Generates JSON from FeaturesList and DataDescription that initializes ARDesigner.
	 *
	 */
	function features()
	{
		$document =& JFactory::getDocument();
		$document->setMimeEncoding('application/json');

		$viewName = JRequest::getVar('view', 'features');
		$viewType = 'raw';
		$view =& $this->getView($viewName, $viewType);

		$query_id = JRequest::getInt('id_query', NULL);

		$view->assign('value', '');

		if($query_id != NULL) {
			// TODO: do it more clever (load component)
			// $arbuilder = JComponentHelper::getComponent('com_ardesigner', true);
			require_once(dirname(__FILE__).'/../../administrator/components/com_kbi/models/queries.php');
			$model_queries = new KbiModelQueries;
			$query = $model_queries->getQuery($query_id);
			$featurelist = !empty($query->featurelist) ? $query->featurelist : dirname(__FILE__).'/assets/featurelist.xml';
		} else {
			$featurelist = dirname(__FILE__).'/assets/featurelist.xml';
		}

		$datadescriptionfile = dirname(__FILE__).'/assets/datadescription.xml';

		$sr = new GetDataARBuilderQuery($datadescriptionfile, $featurelist, null, 'en');
		$result = $sr->getData();
		//var_dump($result);
		$view->assignRef('value', $result);

		$view->display();
	}

	/**
	 * Renders serialized rules
	 */
	function serialize()
	{
		$document =& JFactory::getDocument();
		$viewName = JRequest::getVar('view', 'serialize');
		$viewType = $document->getType();
		$view =& $this->getView($viewName, $viewType);

		// JRequest::getVar('data', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$data = JRequest::getVar('data', NULL);

		if($viewType == 'raw' && $data != NULL) {
			// TODO: why serialize (JREQUEST_ALLOWRAW) ?
			$toSolve = str_replace('\\"', '"', $_POST['data']);
			//var_dump($toSolve);
			//session_start();

			$sr = new SerializeRulesBackgroundAssociationRules();
			//$sr = new SerializeRulesTaskSetting();
			//$sr = new SerializeRulesARQuery();
			$view->assignRef('value', $sr->serializeRules($toSolve));
		}

		$view->display();
	}
}