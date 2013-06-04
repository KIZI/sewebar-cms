<?php

jimport( 'joomla.application.component.view' );
                                  
/**
 * @package Joomla
 * @subpackage Config
 */
class iziViewIziNewReportArticle extends JView{

	/**
	 * Display the view
	 */
	function display(){
    JHtml::stylesheet('izi.css','media/com_dbconnect/css/');                                  
		
		$document = & JFactory::getDocument();
		$document->setTitle( JText::_('NEW_ARTICLE'));
      
    parent::display();		
  }
}
?>
