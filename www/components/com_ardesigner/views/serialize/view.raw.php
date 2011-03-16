<?php
/**
 * @version		$Id$
 * @package		com_ardesigner
 * @author		Andrej Hazucha
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.view' );

/**
 * Renders XML representation of built query (based on JSON POST input).
 *
 * @package com_ardesigner
 */
class ARDesignerViewSerialize extends JView
{
	function display($tpl = NULL)
	{
		echo $this->value;
	}
}
?>