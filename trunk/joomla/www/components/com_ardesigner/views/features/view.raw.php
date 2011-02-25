<?php
/**
 * @version		$Id: view.raw.php 1586 2010-10-24 22:32:27Z andrej $
 * @package		com_ardesigner
 * @author		Andrej Hazucha
 * @copyright	Copyright (C) 2010 All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.view' );

/**
 * Renders JSON from FeaturesList and DataDescription that initializes ARDesigner.
 *
 * @package		com_ardesigner
 */
class ARDesignerViewFeatures extends JView
{
	function display($tpl = NULL)
	{
		echo $this->value;
	}
}
?>