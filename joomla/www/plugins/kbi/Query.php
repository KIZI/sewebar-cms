<?php
/**
 * @version		$Id$
 * @package		KBI
 * @author		Andrej Hazucha
 * @copyright	Copyright (C) 2010 All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

/**
 * Query representation.
 *
 * @package KBI
 */
class KBIQuery
{
	protected $query;
	protected $params;
	protected $params_xslt;
	protected $delimiter;
	//dictionaryquery
	//dictionaryqueryxsl
	//featurelist

	public function getQuery()
	{
		return $this->query;
	}

	public function setQuery($value)
	{
		$this->query = $value;
		return $this;
	}

	public function getParameters()
	{
		return $this->params;
	}

	public function setParameters($value)
	{
		$this->params = $this->recleanup($value);
		return $this;
	}

	public function getXslt()
	{
		return $this->params_xslt;
	}

	public function setXslt($value)
	{
		$this->params_xslt = $value;
		return $this;
	}

	public function getDelimiter()
	{
		return isset($this->delimiter) ? $this->delimiter : ':';
	}

	public function setDelimiter($value)
	{
		$this->delimiter = $value;
		return $this;
	}

	public function __construct()
	{

	}

	/**
	 * Generates final query from skeleton using parameters and/or XSLT transformation.
	 *
	 * @return string
	 */
	public function proccessQuery()
	{
		$parameters = $this->getParameters();
		$xslt = $this->getXslt();

		if(!empty($parameters))	{
			if(is_array($parameters)) {
				$delimiter = $this->getDelimiter();
				$replace_pairs = array();

				foreach($parameters as $name=>$value)
				{
					$replace_pairs[$delimiter.$name.$delimiter] = $value;
				}

				$this->query = strtr($this->query, $replace_pairs);
			} elseif(empty($this->query) && is_string($parameters)) {
				// in case query is empty and parameters is string then we assume parameters to be query itself
				return $parameters;
			}
		}

		if(!empty($xslt)) {
			$xml = new DOMDocument();
			if($xml->loadXML($this->query)) {
				// Create XSLT document
				$xsl_document = new DOMDocument();
				$xsl_document->loadXML($xslt, LIBXML_NOCDATA);

				// Process XSLT
				$xslt = new XSLTProcessor();
				$xslt->importStylesheet($xsl_document);

				$this->query = $xslt->transformToXML($xml);
			}
		}

		return $this->getQuery();
	}

	protected function recleanup($p)
	{
		return str_replace('&gt;', '>',
			str_replace('&lt;', '<', $p));
	}
}