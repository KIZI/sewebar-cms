<?php
  jimport( 'joomla.application.component.model' );
              
                     
  class sewebarModelConfig extends JModel{
  	
    /**
     *  Funkce vracející hodnotu konkrétní položky
     */         
    public function getConfig($name){     
      $db=$this->getDBO();              
      //$user =& JFactory::getUser();
      $db->setQuery('SELECT value FROM #__sewebar_vyuka_config WHERE name='.$db->quote($name).' LIMIT 1;');
      $obj=$db->loadObject();
      if (isset($obj->value)){          
        return json_decode($obj->value);
      }else{
        return null;
      }
    }
    
    /**
     *  Funkce nastavující hodnotu pro konkrétní položku
     */         
    public function setConfig($name,$value){  
      $db=$this->getDBO();
      $db->setQuery("INSERT INTO #__sewebar_vyuka_config (value,name)VALUES(".$db->quote(json_encode($value)).",".$db->quote($name).");");
      $res0=$db->query();
      if (!$res0){                                                                                                                           
        $db->setQuery('UPDATE #__sewebar_vyuka_config SET value='.$db->quote(json_encode($value)).' WHERE name='.$db->quote($name).' LIMIT 1;');
        return $db->query();
      }else{
        return $res0;
      }
    }
  }
?>
