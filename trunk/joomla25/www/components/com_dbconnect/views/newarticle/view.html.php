<?php

jimport( 'joomla.application.component.view' );
                                  
/**
 * @package Joomla
 * @subpackage Config
 */
class articlesViewNewArticle extends JView
{
	/**
	 * Display the view
	 */
	function display()
	{                                  
		//DEVNOTE: set document title
		$document = & JFactory::getDocument();
		$document->setTitle( JText::_('NEW_ARTICLE'));

    JHtml::stylesheet('main.css','media/com_sewebar_vyuka/css/');
                                        
    if((@$this->confirm=="created")||(@$this->confirm=="storno")){    
      $this->setLayout("info");
    }
      
    parent::display();		
  }
}
?>
