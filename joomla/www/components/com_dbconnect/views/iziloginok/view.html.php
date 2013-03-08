<?php
jimport( 'joomla.application.component.view' );
                                  
class userViewIziLoginOK extends JView
{
	/**
	 * Display the view
	 */
	function display()
	{                 
    JHtml::stylesheet('izi.css','media/com_dbconnect/css/');
    
		$document = & JFactory::getDocument();
		$document->setTitle($this->title);
    
    parent::display();		
  }
}
?>
