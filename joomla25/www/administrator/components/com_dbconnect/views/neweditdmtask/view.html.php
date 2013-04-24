<?php
                  
jimport( 'joomla.application.component.view' );
          
class dbconnectViewnewEditDMTask extends JView
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

    if (isset($this->dmTask)){
      $document->setTitle(JText::_('EDIT_DM_TASK'));
      $columnsXml=simplexml_load_string($this->dmTask->columns);
      //upravujeme stávající úlohu - máme zadané xml s daty o sloupcích
      $this->assign('editTask',true);
      $columnsArr=array();
      foreach ($columnsXml as $column){  
      	$columnName=(string)$column['name'];
        $columnsArr[$columnName]=array(
                        'use'=>(string)$column['use'],
                        'type'=>(string)$column['type']
                      );
      }
      $this->assignRef('columnsData',$columnsArr);
    }
                             
		//DEVNOTE:call parent display
    parent::display();		
  }
}
?>
