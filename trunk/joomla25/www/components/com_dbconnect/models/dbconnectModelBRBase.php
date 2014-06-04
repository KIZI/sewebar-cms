<?php
/**
 * Created by PhpStorm.
 * User: Stanislav
 * Date: 4.6.14
 * Time: 15:03
 */

class dbconnectModelBRBase extends JModel {
  /**
   * @param int $taskId
   * @return BRRule[]
   */
  public function getRules($taskId){
    $db=$this->getDbo();
    $db->setQuery('SELECT * FROM #__dbconnect_brbase WHERE task='.$db->quote($taskId));
    return $db->loadObjectList('','BRRule');
  }

  /**
   * @param int $taskId
   * @return int
   */
  public function getRulesCount($taskId){
    $db=$this->getDbo();
    $db->setQuery('SELECT count(*)AS pocet FROM #__dbconnect_brbase WHERE task='.$db->quote($taskId));
    return $db->loadColumn(0);
  }


}

  /**
   * Class BRRule
   * @property int $id - ID pravidla v BR base
   * @property int $task
   * @property string $data
   */
class BRRule{
  var $data='';

  /**
   * @return SimpleXMLElement
   */
  public function getDataXml(){
    return simplexml_load_string($this->data);
  }

  /**
   * @param SimpleXMLElement|string $xml
   */
  public function setDataXml($xml){
    if (!is_string($xml)){
      $this->data=$xml->asXML();
    }else{
      $this->data=$xml;
    }
  }
}