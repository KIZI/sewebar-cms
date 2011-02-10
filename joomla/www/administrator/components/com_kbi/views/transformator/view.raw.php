<?php 
/**
 * @version		$Id: view.raw.php 1586 2010-10-24 22:32:27Z andrej $
 * @package		com_kbi
 * @author		Andrej Hazucha
 * @copyright	Copyright (C) 2010 All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.view' );

/**
 * Renders the result of XSLT transformed query. Transformator calls KBI library's query with combination of source, query and xslt.
 *
 * @package com_kbi
 */
class KbiViewTransformator extends JView
{	
	function display($tpl = NULL)
	{
		echo $this->value;
	}
}
?>