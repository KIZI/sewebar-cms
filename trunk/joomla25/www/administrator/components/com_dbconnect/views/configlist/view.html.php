<?php

jimport( 'joomla.application.component.view' );
                       
/**
 * @package Joomla
 * @subpackage Config
 */
class configViewConfigList extends JView
{
	/**
	 * Display the view
	 */
	function display()
	{
		//DEVNOTE: set document title
		$document = & JFactory::getDocument();
		$document->setTitle( JText::_('CONFIG') );

    //TOOLBAR, CSS
    JHtml::stylesheet('main.css','media/com_dbconnect/css/');
    if (JPATH_BASE==JPATH_ADMINISTRATOR){
      JHtml::stylesheet('admin.css','media/com_dbconnect/css/');
      JToolBarHelper::title(JText::_( 'DB_CONNECT_CONFIG' ),'dbconnect');
    }
    //

		//DEVNOTE:call parent display
    parent::display();		
  }
}
?>
