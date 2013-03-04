<?php
jimport( 'joomla.application.component.view' );
                                  
/**
 * @package Joomla
 * @subpackage Config
 */
class iziViewIziNewDatabase extends JView
{
	/**
	 * Display the view
	 */
	function display()
	{
		//DEVNOTE: set document title
		$document = & JFactory::getDocument();
		$document->setTitle( JText::_('NEW_DABATASE_CONNECTION') );
    JHtml::script('dbConfigForm.js','media/com_dbconnect/js/');

    JHtml::stylesheet('izi.css','media/com_dbconnect/css/');

    if (@$this->pdoError!=''){
      //máme chybu
      $this->setLayout('error');
      $this->assignRef('error',$this->pdoError);
    }else{
      if(($_POST['step']==2)&&(isset($_POST['db_table']))){
        //máme vybranou tabulku, musíme vybrat primární klíč
        $this->setLayout('selectprimarykey');
        $unidbModel=$this->getModel('unidb','unidbModel');
        $this->assignRef('columns',$unidbModel->getColumns($_POST['db_table']));
      }elseif(($_POST['step']==1)&&isset($_POST['db_username'])&&isset($_POST['db_password'])){
        //máme nastavené připojení k DB, musíme vybrat tabulku
        $this->setLayout('selecttable');
        $unidbModel=$this->getModel('unidb','unidbModel');
        $this->assignRef('tables',$unidbModel->getTables());
      }else{
        //musíme vybrat nastavení serveru atp.
        $this->setLayout('setdatabase');
        $this->assignRef('dbTypes',$this->get('DBTypes'));
      }
    }
    
    parent::display();		
  }
}
?>
