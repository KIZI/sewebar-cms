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
    return $db->loadResult();
  }

  public function removeAllRules($taskId){
    $db=$this->getDbo();
    $db->setQuery('DELETE FROM #__dbconnect_brbase WHERE task='.$db->quote($taskId));
    return $db->query();
  }

  public function removeRule($ruleId,$taskId){
    $db=$this->getDbo();
    $db->setQuery('DELETE FROM #__dbconnect_brbase WHERE id='.$db->quote($ruleId).' AND task='.$db->quote($taskId).' LIMIT 1;');
    return $db->query();
  }

  /**
   * @param string $ruleXmlString
   * @param int $taskId
   */
  public function addRule($ruleXmlString,$taskId){
    $db=$this->getDbo();
    $db->setQuery('INSERT INTO #__dbconnect_brbase (task,data) VALUES('.$db->quote($taskId).','.$db->quote($ruleXmlString).');');
    $db->query();
  }

  /**
   * @param string $rulesXmlString
   * @param int $taskId
   */
  public function addRules($rulesXmlString,$taskId){
    $rulesArr=explode('</AssociationRule',$rulesXmlString);
    if (count($rulesArr)){
      foreach($rulesArr as $rule){
        if (!($startPos=strpos($rule,'<AssociationRule '))){
          continue;
        }
        $rule=substr($rule,$startPos).'</AssociationRule>';
        $this->addRule($rule,$taskId);
      }
    }
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