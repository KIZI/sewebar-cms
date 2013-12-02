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
class dbconnectViewadminConnections extends JView
{
	/**
	 * Display the view
	 */
	function display()
	{
		//DEVNOTE: set document title
		$document = & JFactory::getDocument();
		$document->setTitle( JText::_('DB_CONNECTIONS') );

    //TOOLBAR, CSS
    JHtml::stylesheet('main.css','media/com_dbmapping/css/');
    if (JPATH_BASE==JPATH_ADMINISTRATOR){
      JHtml::stylesheet('admin.css','media/com_dbmapping/css/');
      JToolBarHelper::title(JText::_( 'DB_CONNECTIONS' ),'dbconnect');
    }
    //

    JHTML::_('behavior.modal');

		$connectionsModel=$this->getModel('connections','unidbModel');
    $connections=$connectionsModel->getConnectionsAdminList(JRequest::getVar('order','id'));
  	$this->assignRef('connections',	$connections); 		

		//DEVNOTE:call parent display
    parent::display();		
  }
}
?>
