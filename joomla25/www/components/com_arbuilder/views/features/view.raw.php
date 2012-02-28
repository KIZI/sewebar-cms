<?php
/**
 * @version		$Id$
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
 * @package		com_arbuilder
 */
class ARBuilderViewFeatures extends JView
{
	function display($tpl = NULL)
	{
		echo $this->value;
	}
}
?>