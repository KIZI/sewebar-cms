<?php
/**
 * @version		$Id: kbinclude.php 1586 2010-10-24 22:32:27Z andrej $
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
class plgButtonKBInclude extends JPlugin {
	
	function plgButtonKBInclude(& $subject, $config) {
		parent::__construct($subject, $config);
	}
	
	/**
	 * Display the button
	 *
	 * @return array A two element array of ( imageName, textToInsert )
	 */
	function onDisplay($name) {
		global $mainframe;
		
		//Make sure the user is authorized to view this page
		/*$user = & JFactory::getUser();
		if (!$user->authorize( 'com_oks', 'popup' )) {
			return;
		}*/
		
		$doc 		=& JFactory::getDocument();
		$template 	= $mainframe->getTemplate();
		
		$jAjaxRoot = JURI::root();
		if (JPATH_BASE==JPATH_ADMINISTRATOR)
		{
			$jAjaxRoot .= 'administrator/';
		}

		$declaration = "function kbiStaticInclude(id_source, id_query, id_xslt, parameters) {
				url = '{$jAjaxRoot}index.php?option=com_kbi&controller=transformator&format=raw';
				url += '&source=' + id_source;
				url += '&query=' + id_query;
				url += '&xslt=' + id_xslt;
				url += '&parameters=' + escape(parameters);
				
				new Ajax(url,{
                    method:'get',
                    onSuccess: function(response){
                       jInsertEditorText(response, 'text');
                    }
                }).request();
			}";
		
		$doc->addScriptDeclaration($declaration);

		$link = 'index.php?option=com_kbi&amp;controller=selector&amp;tmpl=component&amp;e_name='.$name;;

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