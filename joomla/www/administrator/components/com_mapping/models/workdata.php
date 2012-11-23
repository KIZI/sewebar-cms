<?php
  // no direct access
  defined('_JEXEC') or die('Restricted access');
  jimport( 'joomla.application.component.model');
  
  /**
   * @package		Joomla
   * @copyright	Copyright (C) 2010 Stanislav Vojíř. All rights reserved.
   * @license		GNU/GPL
   * 
   *  Model obsluhující aktuální pracovní data - nyní řešeno přes session 
   */
  class WorkDataModel /*extends JModel*/{
    /**
     *  Funkce pro uložení pracovních dat
     */         
    public function save($id,$data){
      $_SESSION[$id]=$data;             
      if (!in_array($id,@$_SESSION["workdataKeys"])){
        if (!is_array($_SESSION["workdataKeys"])){
          $_SESSION["workdataKeys"]=array();
        }
        $_SESSION["workdataKeys"][]=$id;
      }
    }
    /**
     *  Funkce pro načtení uložených dat
     */         
    public function load($id){
      return @$_SESSION[$id];
    }
    /**
     *  Funkce pro unset dat
     */         
    public function unsetVar($id){
      unset($_SESSION[$id]);
    } 
    
    /**
     *  Funkce pro vyresetovani pracovnich dat
     */         
    public function reset(){
      if (count($_SESSION["workdataKeys"])>0){
        foreach ($_SESSION["workdataKeys"] as $keyName) {
        	$this->unsetVar($keyName);
        }
      }
      $_SESSION["workdataKeys"]=array();
    }
             
  }
?>