<?php
/**
 * @version		$Id$
 * @package		editors-xtd/kbinclude
 * @author		Andrej Hazucha
 * @copyright	Copyright (C) 2010 All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

/**
 * Adds KB include button to the Joomla!'s editor. This button opens selector window from administration compoment (com_kbi).
 *
 * @package editors-xtd/kbinclude
 */
class plgButtonKBInclude extends JPlugin
{
	function plgButtonKBInclude(& $subject, $config)
	{
		parent::__construct($subject, $config);
	}

	/**
	 * Display the button
	 *
	 * @return array A two element array of (imageName, textToInsert)
	 */
	function onDisplay($name)
	{
		global $mainframe;

		// TODO: Make sure the user is authorized to view this page
		/*$user = & JFactory::getUser();
		if (!$user->authorize( 'com_oks', 'popup' )) {
			return;
		}*/

		$doc 		=& JFactory::getDocument();
		$template 	= $mainframe->getTemplate();

		$link = 'index.php?option=com_kbi&amp;controller=selector&amp;tmpl=component&amp;e_name='.$name;

		JHTML::_('behavior.modal');

		$button = new JObject();
		$button->set('modal', true);
		$button->set('link', $link);
		$button->set('text', JText::_('KB include'));
		$button->set('name', 'image');
		$button->set('options', "{handler: 'iframe', size: {x: 570, y: 400}}");

		return $button;
	}
}