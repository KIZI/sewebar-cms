<?php
/**
* @package helloworld02
* @version 1.1
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* Joomla! is free software and parts of it may contain or be derived from the
* GNU General Public License or other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

jimport( 'joomla.application.component.view' );
                                 
/**
 * @package Joomla
 * @subpackage Config
 */
class adminViewInfo extends JView
{
	/**
	 * Display the view
	 */
	function display()
	{
		//DEVNOTE: set document title
		$document = & JFactory::getDocument();
		$document->setTitle( JText::_('SEWEBAR_ADMINISTRATION') );

    //TOOLBAR, CSS
    JHtml::stylesheet('main.css','media/com_sewebar_vyuka/css/');
    if (JPATH_BASE==JPATH_ADMINISTRATOR){
      JToolBarHelper::title(JText::_( 'USER_GROUPS' ),'sewebar_vyuka');
    }

    parent::display();		
  }
}
?>
