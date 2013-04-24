<?php
jimport( 'joomla.application.component.view' );
                       
/**
 * @package Joomla
 * @subpackage Config
 */
class iziViewIziCloneDMTask extends JView
{
	/**
	 * Display the view
	 */
	function display()
	{
    JHtml::stylesheet('izi.css','media/com_dbconnect/css/');
    
		$document = & JFactory::getDocument();
		$document->setTitle( JText::_('CLONE_DM_TASK') );
		
    parent::display();		
  }
}
?>
