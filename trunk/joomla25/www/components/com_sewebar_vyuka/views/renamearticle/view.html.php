<?php

jimport( 'joomla.application.component.view' );
             
/**
 * @package Joomla
 * @subpackage Config
 */
class articlesViewRenameArticle extends JView
{
	/**
	 * Display the view
	 */
	function display()
	{
		//DEVNOTE: set document title
		$document = & JFactory::getDocument();
		$document->setTitle( JText::_('RENAME_ARTICLE'));

    JHtml::stylesheet('main.css','media/com_sewebar_vyuka/css/');
    
    if((@$this->confirm=="rename")||(@$this->confirm=="storno")){
      $this->setLayout('info');
    }
      
    parent::display();
    		
  }
}
?>
