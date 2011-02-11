<?php 
/**
 * @version		$Id$
 * @package		com_kbi
 * @author		Andrej Hazucha
 * @copyright	Copyright (C) 2010 All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.view' );

/**
 * Renders the result of params XSLT which can be defined in query. This is used in combination of ARDesigner. Selector is a user window for selecting source, query and coresponding XSLT. 
 *
 * @package com_kbi
 */
class KbiViewParams extends JView
{	
	function display($tpl = NULL)
	{
		echo $this->value;
	}
}
?>