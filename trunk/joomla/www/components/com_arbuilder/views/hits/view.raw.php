<?php
/**
 * @version		$Id: view.raw.php 44 2011-02-25 13:57:33Z hazucha.andrej@gmail.com $
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
class ARBuilderViewHits extends JView
{
	function display($tpl = NULL)
	{
		echo $this->value;
	}
}
?>