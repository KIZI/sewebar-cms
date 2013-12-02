<?php

jimport( 'joomla.application.component.view' );
                                  
/**
 * @package Joomla
 * @subpackage Config
 */
class articlesViewUploadPmmlFiles2 extends JView
{
	/**
	 * Display the view
	 */
	function display()
	{                                  
		//DEVNOTE: set document title
		$document = & JFactory::getDocument();
		$document->setTitle( JText::_('UPLOAD_PMML_FILES2'));

    JHtml::stylesheet('main.css','media/com_sewebar_vyuka/css/');
      
    parent::display();		
  }
}
?>
