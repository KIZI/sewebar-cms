<?php
                  
jimport( 'joomla.application.component.view' );
                    
class dbconnectViewQuickDMTask extends JView
{
	/**
	 * Display the view
	 */
	function display()
	{                     
		$document = & JFactory::getDocument();
		$document->setTitle( JText::_('NEW_DM_TASK') );
    JHtml::script('dmtaskForm.js','media/com_dbconnect/js/');
    //TOOLBAR, CSS
    JHtml::stylesheet('main.css','media/com_dbconnect/css/');
    if (JPATH_BASE==JPATH_ADMINISTRATOR){
      JHtml::stylesheet('admin.css','media/com_dbconnect/css/');
      JToolBarHelper::title(JText::_( 'NEW_DM_TASK' ),'dbconnect');
    }
    //

    JHTML::_('behavior.modal');
    
    $this->unidbModel=$this->getModel('unidb','unidbModel');
    $this->assignRef('columns',$this->unidbModel->getColumns($this->connection->table)); 		
                             
		//DEVNOTE:call parent display
    parent::display();		
  }
}
?>
