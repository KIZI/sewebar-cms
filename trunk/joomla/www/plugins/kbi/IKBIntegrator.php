<?php
/**
 * @version		$Id$
 * @package		KBI
 * @author		Andrej Hazucha
 * @copyright	Copyright (C) 2010 All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

/**
 * Interface for communication between CMSs and KBs.
 *
 * @package KBI
 */
interface IKBIntegrator
{
	public function getUrl();
	public function setUrl($value);
	
	public function query($query, $xsl = '');
}