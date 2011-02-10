<?php
/**
 * @version		$Id: KBIntegrator.php 1632 2010-11-30 11:35:53Z andrej $
 * @package		KBI
 * @author		Andrej Hazucha
 * @copyright	Copyright (C) 2010 All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

require_once 'IKBIntegrator.php';
require_once 'ISynchronable.php';

/**
 * Generic implementation for IKBIntegrator.
 *
 * @package KBI
 */
abstract class KBIntegratorSynchronable extends KBIntegrator implements ISynchronable
{
	public function __contruct(Array $config = array())
	{
		$this->config = $config;
	}

	protected function encodeData($array)
	{
		$data = "";
		foreach ($array as $key=>$value) $data .= "{$key}=".urlencode($value).'&';
		return $data;
	}
}
