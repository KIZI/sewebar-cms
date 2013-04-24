<?php
                  
jimport( 'joomla.application.component.view' );
                    
class IziViewIziQuickDMTask extends JView
{
	/**
	 * Display the view
	 */
	function display()
	{                     
    JHtml::stylesheet('izi.css','media/com_dbconnect/css/');
    
		$document = & JFactory::getDocument();
		$document->setTitle( JText::_('NEW_DM_TASK') );
    JHtml::script('dmtaskForm.js','media/com_dbconnect/js/');
    //TOOLBAR, CSS

    JHTML::_('behavior.modal');
    
    $this->unidbModel=$this->getModel('Unidb','unidbModel'); 
    $this->assignRef('columns',$this->unidbModel->getColumns($this->connection->table)); 		
                                     
    JHtml::script('uniquenames.js','media/com_dbconnect/js/');                                                                                          
                                     
		//DEVNOTE:call parent display
    parent::display();		
  }
}
?>
