<?php
/**
 * @version		$Id$
 * @package		com_arbuilder
 * @author		Andrej Hazucha
 * @copyright	Copyright (C) 2010 All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
JLoader::import('KBIntegrator', JPATH_PLUGINS . DS . 'kbi');

require_once(dirname(__FILE__).'/arbuilder/models/serializeRules/AncestorSerializeRules.php');
require_once(dirname(__FILE__).'/arbuilder/models/serializeRules/SerializeRulesARQuery.php');
require_once(dirname(__FILE__).'/arbuilder/models/serializeRules/SerializeRulesQueryByAR.php');
require_once(dirname(__FILE__).'/arbuilder/models/serializeRules/SerializeRulesTaskSetting.php');

require_once(dirname(__FILE__).'/arbuilder/models/JSON.php');
require_once(dirname(__FILE__).'/arbuilder/models/parseData/AncestorGetData.php');
require_once(dirname(__FILE__).'/arbuilder/models/parseData/GetDataARBuilderQuery.php');
require_once(dirname(__FILE__).'/arbuilder/models/parseData/AsociationRulesParser.php');
require_once(dirname(__FILE__).'/arbuilder/models/parseData/ARQueryParser.php');
require_once(dirname(__FILE__).'/arbuilder/models/parseData/TaskSettingParser.php');
require_once(dirname(__FILE__).'/arbuilder/models/Utils.php');

/**
 * Controller for arbuilder.
 *
 * @package com_arbuilder
 */
class ARBuilderController extends JController
{
	protected static $com_kbi_admin;
	protected $featurelist;
	protected $datadescription;

	static function createSerializeRules($source)
	{
		$sourceType = get_class($source);

		KBIDebug::log($sourceType);
		switch($sourceType) {
			case 'XQuery':
				return new SerializeRulesQueryByAR();
				break;
			case 'LispMiner':
			default:
				return new SerializeRulesTaskSetting();
		}
	}

	function __construct($config = array())
	{
		parent::__construct($config);

		self::$com_kbi_admin = JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_kbi';
		$this->featurelist = dirname(__FILE__).'/assets/featurelistQueryByAr.xml';
		$this->datadescription = dirname(__FILE__).'/assets/datadescription.xml';
	}

	function display()
	{
		$document =& JFactory::getDocument();
		$viewName = JRequest::getVar('view', 'default');
		$viewType = $document->getType();

		$view =& $this->getView($viewName, $viewType);
		$view->setLayout('default');
		$view->display();
	}

	/**
	 * Generates JSON from FeaturesList and DataDescription that initializes arbuilder.
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

			if(!class_exists('KbiModelQueries')) {
				$kbi = JComponentHelper::getComponent('com_kbi', true);
				if($kbi->enabled) {
					JLoader::import('queries', self::$com_kbi_admin . DS . 'models');
					JLoader::import('sources', self::$com_kbi_admin . DS . 'models');
				} else {
					throw new Exception(JText::_('Component com_kbi not found / enabled!'));
				}
			}

			$model_queries = new KbiModelQueries;
			$query = $model_queries->getQuery($query_id);

			$model_sources = new KbiModelSources;
			$source = $model_sources->getSource(JRequest::getInt('id_source', NULL));

			KBIDebug::log($source);

			$featurelist = !empty($query->featurelist) ? $query->featurelist : $this->featurelist;

			if(!empty($source->dictionaryquery)) {
				$datadescription = $source->dictionaryquery;
			} else {
				$kbi_source = KBIntegrator::create(get_object_vars($source));
				if($kbi_source instanceof ISynchronable) {
					$datadescription = $kbi_source->getDataDescription();
				} else {
					$datadescription = $this->datadescription;
				}
			}
		} else {
			$featurelist = $this->featurelist;
			$datadescription = $this->datadescription;
		}

		if(class_exists('KBIDebug')) {
			KBIDebug::log(array('featurelist' => $featurelist, 'datadescription' => $datadescription), 'Loading ARB with FL and DL');
		}

		$sr = new GetDataARBuilderQuery($datadescription, $featurelist, null, 'en');
		$result = $sr->getData();
		$view->assignRef('value', $result);

		$view->display();
	}

	function hits()
	{
		$document =& JFactory::getDocument();
		$viewName = JRequest::getVar('view', 'hits');
		$viewType = $document->getType();
		$view =& $this->getView($viewName, $viewType);

		$data = JRequest::getVar('data', '', 'post', 'string', JREQUEST_ALLOWRAW);

		if($viewType == 'raw' && $data != NULL) {
			$config = array(
				'source' => JRequest::getVar('id_source', NULL, 'default', 'none', JREQUEST_ALLOWRAW),
				'query' => NULL,
				'xslt' => NULL,
				'parameters' => NULL
			);

			try {
				$com_kbi = JComponentHelper::getComponent('com_kbi', true);

				if(!$com_kbi->enabled)
					throw new Exception('KBI component not intalled or enabled');

				JLoader::import('transformator', JPATH_COMPONENT . DS . '..' . DS . $com_kbi->option . DS . 'models');

				$model = new KbiModelTransformator($config);

				$sr = self::createSerializeRules($model->getSource());
				$model->setQuery($sr->serializeRules($data));

				$dd = null;
				$fl = null;
				$er = $model->transform();

				$sr = new GetDataARBuilderQuery($dd, $fl, $er, 'en');
				$view->assignRef('value', $sr->getData());
			} catch (Exception $e) {
				$view->assign('value', "<p class=\"kbierror\">Chyba dotazu: {$e->getMessage()}</p>");
			}
		}

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

			$sr = new SerializeRulesQueryByAR();
			//$sr = new SerializeRulesTaskSetting();
			//$sr = new SerializeRulesARQuery();
			$view->assignRef('value', $sr->serializeRules($toSolve));
		}

		$view->display();
	}
}