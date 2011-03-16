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
 * Renders JSON
 *
 * @package		com_kbi
 */
class KbiViewExport extends JView
{
	function display($tpl = NULL)
	{
		echo $this->rows;
	}
}
?>