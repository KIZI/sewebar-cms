<?php
jimport( 'joomla.application.component.view' );
                                  
class dbconnectViewshowTable extends JView
{
	/**
	 * Display the view
	 */
	function display()
	{
		//DEVNOTE: set document title
		$document = & JFactory::getDocument();
		$document->setTitle( JText::_('TABLE_PREVIEW') );
    JHtml::stylesheet('main.css','media/com_dbconnect/css/');
		                     // exit(var_dump($_POST));
    $unidbModel=$this->getModel('Unidb', 'dbconnectModel');
    $this->assign('limit',JRequest::getVar('limit',20));
    $this->assign('limitstart',JRequest::getVar('limitstart',0));
    $this->assign('total',$unidbModel->getTableRowsCount($this->dbtable));
    $this->assign('columns',$unidbModel->getColumns($this->dbtable));
    $this->assign('rows',$unidbModel->getRows($this->dbtable,$this->limitstart,$this->limit));
		//DEVNOTE:call parent display
    parent::display();		
  }
}
?>
