<?php
/**
 * @version		$Id$
 * @package		com_kbi
 * @author		Andrej Hazucha
 * @copyright	Copyright (C) 2010 All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.model' );
JLoader::import('KBIntegrator', JPATH_PLUGINS . DS . 'kbi');

require_once 'sources.php';
require_once 'queries.php';
require_once 'xslts.php';

/**
 * JModel for transformator. Transformator calls KBI library's query with combination of source, query and xslt.
 *
 * @package com_kbi
 */
class KbiModelTransformator extends JModel {

	private $source = NULL;
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
		return $this->query;
	}

	/**
	 *
	 *
	 * @param mixed $value id | query | ardesigner query
	 */
	function setQuery($value)
	{
		if(is_numeric($value)) {
			$queries = new KbiModelQueries;
			$this->query = get_object_vars($queries->getQuery($value));
		} elseif(is_string($value)) {
			$this->query = array();
			$this->query['query'] = $value;
		}

		/*if(empty($this->query) || empty($this->query->query))
			return $this->getParams();*/
	}

	function getParams()
	{
		return $this->parameters;
	}

	function setParams($value)
	{
		$this->parameters = $value;
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
			return $source->query($this->getQuery(), $this->getXslt());
		} else {
			return JText::_('Chyba');
		}
	}
}

?>