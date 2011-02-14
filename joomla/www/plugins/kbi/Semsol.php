<?php
/**
 * @version		$Id$
 * @package		KBI
 * @author		Andrej Hazucha
 * @copyright	Copyright (C) 2010 All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

require_once dirname(__FILE__).'/KBIntegrator.php';

/**
 * IKBIntegrator implementation for Semsol/SPARQL endpoints.
 *
 * @package KBI
 */
class Semsol extends KBIntegrator
{
	public function __construct(Array $config)
	{
		parent::__construct($config);
	}
	
	public function queryGet($query) {
		$data = array(
			'query' => $query,
			'output' => '',
			'jsonp' => '',
			'key' => '',
		);
			
		return $this->requestCurl($this->getUrl(), $data); 
	}	
}