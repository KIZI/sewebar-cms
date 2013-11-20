<?php

jimport( 'joomla.application.component.controller' );

class configController extends JController{
  var $document;

  /**
   *  Konstruktor
   */
  public function __construct( $default = array()){
    parent::__construct( $default );
    $this->document =& JFactory::getDocument();
  }

  public function configList(){
    /** @var $configModel dbconnectModelConfig */
    $configModel=&$this->getModel('Config','dbconnectModel');
    $view = &$this->getView('ConfigList',$this->document->getType());
    $view->assign('configItems',$configModel->getConfigsList());
    $view->display();
  }

  public function saveConfigList(){
    /** @var $configModel dbconnectModelConfig */
    $configModel=&$this->getModel('Config','dbconnectModel');

    foreach ($_POST as $key=>$value){
      if (substr($key,0,7)=='config_'){
        $configModel->saveConfig(substr($key,7),$value);
      }
    }
    $this->setRedirect(JRoute::_('index.php?option=com_dbconnect&controller=config&task=configList'));

  }
}