<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Standa
 * Date: 19.11.13
 * Time: 11:25
 * To change this template use File | Settings | File Templates.
 */

class dbconnectModelConfig extends JModel{

  /**
   *   Funkce pro uložení konkrétního nastavení
   */
  public function saveConfig($name,$value){
    $db=$this->getDBO();
    $user =& JFactory::getUser();
    $db->setQuery('UPDATE #__dbconnect_config SET value="'.$db->getEscaped($value).'" WHERE `name`="'.$db->getEscaped($name).'" LIMIT 1;');
    return $db->query();
  }

  /**
   *   Funkce pro načtení konkrétního nastavení
   */
  public function loadConfig($name){
    $db=$this->getDBO();
    $db->setQuery('SELECT value FROM #__dbconnect_config WHERE `name`="'.$db->getEscaped($name).'" LIMIT 1;');
    $object=$db->loadObject();
    return $object->value;
  }

  /**
   * Funkce vracející přehled všech nastavení
   * @return array
   */
  public function getConfigsArr(){
    $db=$this->getDBO();
    $db->setQuery('SELECT * FROM #__dbconnect_config;');
    $list=$db->loadObjectList();
    $result=array();
    foreach ($list as $item){
      $result[$item->name]=$item->value;
    }
    return $result;
  }

  /**
   * Funkce vracející přehled všech nastavení
   * @return array
   */
  public function getConfigsList(){
    $db=$this->getDBO();
    $db->setQuery('SELECT * FROM #__dbconnect_config;');
    return $db->loadObjectList();
  }
}