<?php
  // no direct access
  defined('_JEXEC') or die('Restricted access');
  /*jimport( 'joomla.application.component.model');
    */
  /**
   * @copyright	Copyright (C) 2011 Stanislav Vojíř. All rights reserved.
   * 
   *  Model obsluhující DB s nastaveními
   */
  class ConfigModel/* extends JModel*/{
    
    /**
     *  Funkce pro načtení nastavení
     *  (pokud není zadáno jméno nastavení, načtou se všechna v podobě pole pomocí funkce loadConfigs)     
     */         
    public function loadConfigValue($group="constant",$name=""){
      if ($name!=""){
        $db = & JFactory::getDBO();
        $db->setQuery( "SELECT `value` FROM #__mapping_config WHERE name='".str_replace("'","\'",$name)."' AND `group`='".str_replace("'","\'",$group)."' LIMIT 1;");
        $obj=$db->loadObject();
        return $obj->value;
      }else{
        return $this->loadConfigs();
      }
    }
    
    /**
     *  Funkce pro načtení nastavení
     *  (pokud není zadáno jméno nastavení, načtou se všechna v podobě pole pomocí funkce loadConfigs)     
     */         
    public function loadConfig($group="constant",$name=""){
      if ($name!=""){
        $db = & JFactory::getDBO();
        $db->setQuery("SELECT * FROM #__mapping_config WHERE name='".str_replace("'","\'",$name)."' AND `group`='".str_replace("'","\'",$group)."' LIMIT 1;");
        $obj=$db->loadObject();
        return $obj;
      }else{
        return $this->loadConfigs();
      }
    }
    
    /**
     *  Funkce pro načtení všech nastavení v podobě pole
     */         
    public function loadConfigs($group=""){
      if ($name==""){                   
        $db = & JFactory::getDBO();
        $db->setQuery("SELECT * FROM #__mapping_config".(($group!="")?" WHERE `group`='".str_replace("'","\'",$group)."'":"").";");
        return $db->loadObjectList();
      }else{
        return $this->loadConfigs();
      }
    }
    
    /**
     *  Funkce pro uložení hodnoty nastavení
     */         
    public function saveConfigValue($group="constant",$name,$value){
      if ($name!=""){
        $db = & JFactory::getDBO();
        $db->setQuery("UPDATE #__mapping_config SET `value`='".str_replace("'","\'",$value)."' WHERE `name`='".str_replace("'","\'",$name)."' AND `group`='".str_replace("'","\'",$group)."' LIMIT 1;");
        $db->query();
      }
    }
  }
?>