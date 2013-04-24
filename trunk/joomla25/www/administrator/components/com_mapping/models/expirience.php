<?php
  // no direct access
  defined('_JEXEC') or die('Restricted access');
  jimport( 'joomla.application.component.model');
  
  /**
   * @copyright	Copyright (C) 2010 Stanislav Vojíř. All rights reserved.
   * @license		GNU/GPL
   * 
   *  Model obsluhující DB zkušeností z předchozích napárování
   */
  class ExpirienceModel /*extends JModel*/{
    /**
     *  Výchozí inicializace - načtení potřebných tříd...
     */     
    function __construct(){                                          
      $this->db= & JFactory::getDBO();
    }
    
    /**
     *  Funkce pro načtení zkušeností z DB - používá se u porovnávání
     */         
    public function loadExpirience($name1,$name2){
      $name1=strtolower(substr($name1,0,100));
      $name2=strtolower(substr($name2,0,100));  
      $this->db->setQuery( "SELECT ratio FROM #__mapping_expirience WHERE (name1='".str_replace("'","\'",$name1)."' AND name2='".str_replace("'","\'",$name2)."')OR(name1='".str_replace("'","\'",$name2)."' AND name2='".str_replace("'","\'",$name1)."');");
      $objList=$this->db->loadObjectList();
      if (count($objList)>0){
        $ratio=0;
        /*máme načteny nějake podobnosti, tak je musíme sečíst a normalizovat*/
        foreach ($objList as $obj) {
        	$ratio+=$obj->ratio;
        }
        /*celkove podobnosti*/
        $this->db->setQuery("SELECT ratio FROM #__mapping_expirience WHERE name1='".str_replace("'","\'",$name1)."' OR name2='".str_replace("'","\'",$name1)."';");
        $objList2a=$this->db->loadObjectList();
        $ratioAll=0;
        foreach ($objList2a as $obj2) {
        	$ratioAll+=$obj2->ratio;
        }
        $this->db->setQuery("SELECT ratio FROM #__mapping_expirience WHERE name1='".str_replace("'","\'",$name2)."' OR name2='".str_replace("'","\'",$name2)."';");
        $objList2b=$this->db->loadObjectList();
        foreach ($objList2b as $obj2) {
        	$ratioAll+=$obj2->ratio;
        }
        return 2*$ratio/$ratioAll;
      }else{
        return 0;
      }
    }
    
    /**
     *  Funkce pro uložení zkušeností do DB
     */         
    public function updateExpirience($name1,$name2,$ratioPlus=0){
      $name1=strtolower(substr($name1,0,100));
      $name2=strtolower(substr($name2,0,100));
      $this->db->setQuery( "SELECT ratio FROM #__mapping_expirience WHERE name1='".str_replace("'","\'",$name1)."' AND name2='".str_replace("'","\'",$name2)."' LIMIT 1;");
      $obj=$this->db->loadObject();
      if ($obj){
        $newRatio=$obj->ratio+$ratioPlus;
        $query= "UPDATE #__mapping_expirience SET ratio='".$newRatio."' WHERE name1='".str_replace("'","\'",$name1)."' AND name2='".str_replace("'","\'",$name2)."' LIMIT 1;";
      }else{
        $newRatio=$ratioPlus;
        $query="INSERT INTO #__mapping_expirience (name1,name2,ratio)VALUES('".str_replace("'","\'",$name1)."','".str_replace("'","\'",$name2)."','".$newRatio."');";
      }
      $this->db->setQuery($query);
      $obj=$this->db->query();
    }
    
    
  }
?>