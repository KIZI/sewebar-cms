<?php
/**
 * @version		$Id: view.raw.php 15 2011-02-11 00:57:01Z hazucha.andrej@gmail.com $
 * @package		com_kbi
 * @author		Andrej Hazucha
 * @copyright	Copyright (C) 2010 All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');

/**
 * Renders serialized query into source/query identificator
 *
 * @package com_kbi
 */
class KbiViewSerialize extends JView
{
	function display($tpl = NULL)
	{
		parent::display($tpl);
	}
}
?>