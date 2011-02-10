<?php
/**
 * @version		$Id: IKBIntegrator.php 1586 2010-10-24 22:32:27Z andrej $
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