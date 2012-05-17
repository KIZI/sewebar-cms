<?php defined('_JEXEC') or die('Restricted access');
/**
 * @version		$Id$
 * @package		com_kbi
 * @author		Andrej Hazucha
 * @copyright	Copyright (C) 2010 All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

define('COM_KBI_ADMIN', JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_kbi');

jimport( 'joomla.application.component.model' );
JLoader::import('KBIntegrator', JPATH_PLUGINS . DS . 'kbi');

/**
 * JModel for transformator. Transformator calls KBI library's query with combination of source, query and xslt.
 *
 * @package com_kbi
 */
class KbiModelTransformator extends JModel
{
	/** @var KBIntegrator */
	private $source = NULL;

	/** @var KBIQuery */
	private $query = NULL;

	private $xslt = NULL;
	private $parameters = NULL;

	public function __construct($config)
	{
		$this->setSource(isset($config['source']) ? $config['source'] : NULL);
		$this->setQuery(isset($config['query']) ? $config['query'] : NULL);
		$this->setParams(isset($config['parameters']) ? $config['parameters'] : NULL);
		$this->setXslt(isset($config['xslt']) ? $config['xslt'] : NULL);

		parent::__construct($config);
	}

	function getSource()
	{
		return $this->source;
	}

	/**
	 *
	 *
	 * @param mixed $value id | array | object | json
	 */
	function setSource($value)
	{
		$config = array();

		if(is_numeric($value)) {
			if(!class_exists('KbiModelSources')) {
				JLoader::import('sources', COM_KBI_ADMIN . DS . 'models');
			}

			$sources = new KbiModelSources;
		 	$config = get_object_vars($sources->getSource($value));
		} elseif (is_array($value)) {
			$config = $value;
		} elseif(is_string($value)) {
			$config = json_decode($value, true);
		} else {
			$config = $value;
		}

		if(!is_array($config)) throw new Exception("Not valid source configuration");

		$this->source = KBIntegrator::create($config);
	}

	function getQuery()
	{
		if($this->query === NULL) {
			$this->query = new KBIQuery();
		}

		return $this->query;
	}

	/**
	 * Sets query to perform.
	 *
	 * @param KBIQuery | int | string Query
	 */
	function setQuery($value)
	{
		if($value instanceof KBIQuery) {
			$this->query = $value;
			return $this;
		}

		if(is_numeric($value)) {
			if(!class_exists('KbiModelQueries')) {
				JLoader::import('queries', COM_KBI_ADMIN . DS . 'models');
			}

			$queries = new KbiModelQueries;
			$db = get_object_vars($queries->getQuery($value));
			$query = new KBIQuery();

			// TODO: implement all properties
			$query->setQuery($db['query']);
			$query->setDelimiter($db['delimiter']);
			$query->setXslt($db['paramsxsl']);

			$this->query = $query;
		} elseif(is_string($value)) {
			$query = new KBIQuery();

			$query->setQuery($value);

			$this->query = $query;
		}

		if($this->query != NULL) {
			$query->setOptions($_GET, 'GET');
			$query->setOptions($_POST, 'POST');

			return $this->query->setParameters($this->getParams());
		}

		return $this;
	}

	function getParams()
	{
		return $this->parameters;
	}

	function setParams($value)
	{
		$this->parameters = $value;

		if($this->query != NULL)
			return $this->query->setParameters($this->getParams());
	}

	function getXslt()
	{
		return isset($this->xslt->style) ? $this->xslt->style : NULL;
	}

	/**
	 *
	 *
	 * @param mixed $value id | xml
	 */
	public function setXslt($value)
	{
		if(is_numeric($value)) {
			if(!class_exists('KbiModelXslts')) {
				JLoader::import('xslts', COM_KBI_ADMIN . DS . 'models');
			}

			$xslts = new KbiModelXslts;
			$this->xslt = $xslts->getStyle($value);
		} elseif(is_string($value)) {
			$this->xslt = $value;
		}
	}

	public function transform()
	{
		$source = $this->getSource();

		if($source != NULL) {
			$q = $this->getQuery();
			$x = $this->getXslt();
			KBIDebug::log(array('source' => $source, 'query' => $q, 'XSLT (post)' => $x), 'Query');
			return $source->query($q, $x);
		} else {
			return JText::_('Chyba');
		}
	}

	public function getDataDescription()
	{
		$source = $this->getSource();

		if($source != NULL && $source instanceof IHasDataDictionary) {
			KBIDebug::log(array('source' => $source), 'DataDescription Source');
			return $source->getDataDescription();
		} else {
			return JText::_('Given source has no DataDictionary (does not implenent IHasDataDictionary)');
		}
	}
}

?>