<?php
jimport( 'joomla.application.component.view' );
                                  
/**
 * @package Joomla
 * @subpackage Config
 */
class iziViewIziQuickDMTaskGenerated extends JView
{
	/**
	 * Display the view
	 */
	function display()
	{
    JHtml::stylesheet('izi.css','media/com_dbconnect/css/');
		
		$document = & JFactory::getDocument();
		$document->setTitle( JText::_('QUICK_TASK') );

		//DEVNOTE:call parent display
    parent::display();		
  }
}
?>
