<?php
/**
 * @version		$Id: Semsol.php 1586 2010-10-24 22:32:27Z andrej $
 * @package		KBI
 * @author		Andrej Hazucha
 * @copyright	Copyright (C) 2010 All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

/**
 * IKBIntegrator implementation for Semsol/SPARQL endpoints.
 *
 * @package KBI
 */
class Semsol extends KBIntegrator
{
	public function __construct(Array $config)
	{
		parent::__contruct($config);
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