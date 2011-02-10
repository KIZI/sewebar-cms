<?php 
/**
 * @version		$Id: view.raw.php 1587 2010-10-24 22:40:30Z andrej $
 * @package		com_kbi
 * @author		Andrej Hazucha
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.view' );

/**
 * Renders XML representation of built query (based on JSON POST input).
 *
 * @package com_kbi
 */
class KbiViewARDesigner extends JView
{	
	function display($tpl = NULL)
	{
		echo $this->value;
	}
}
?>