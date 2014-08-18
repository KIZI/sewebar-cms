<?php

class dbconnectModelBRBase extends JModel {

  const EXTERNAL_API_URL='http://brserver.golemsoftware.cz/www';

  /**
   * @param int $taskId
   * @return BRRule[]
   */
  public function getRules($taskId){
    //TODO new version!!!
    $db=$this->getDbo();
    $db->setQuery('SELECT * FROM #__dbconnect_brbase WHERE task='.$db->quote($taskId));
    return $db->loadObjectList('','BRRule');
  }

  /**
   * @param int $taskId
   * @return int
   */
  public function getRulesCount($taskId){
    //TODO new version!!!
    $db=$this->getDbo();
    $db->setQuery('SELECT count(*)AS pocet FROM #__dbconnect_brbase WHERE task='.$db->quote($taskId));
    return $db->loadResult();
  }

  public function removeAllRules($taskId){
    //TODO new version!!!
    $db=$this->getDbo();
    $db->setQuery('DELETE FROM #__dbconnect_brbase WHERE task='.$db->quote($taskId));
    return $db->query();
  }

  public function removeRule($ruleId,$taskId){
    //TODO new version!!!
    $db=$this->getDbo();
    $db->setQuery('DELETE FROM #__dbconnect_brbase WHERE id='.$db->quote($ruleId).' AND task='.$db->quote($taskId).' LIMIT 1;');
    return $db->query();
  }

  /**
   * @param string $ruleXmlString
   * @param int $taskId
   * @param string $lmtask = ''
   */
  public function addRule($ruleXmlString,$taskId,$lmtask=''){
    //TODO new version!!!
    $db=$this->getDbo();
    $db->setQuery('SELECT * FROM #__dbconnect_brbase WHERE task='.$db->quote($taskId).' AND data='.$db->quote($ruleXmlString));
    if (!($db->loadObject())){
      $db->setQuery('INSERT INTO #__dbconnect_brbase (task,data,lmtask) VALUES('.$db->quote($taskId).','.$db->quote($ruleXmlString).','.$db->quote($lmtask).');');
      $db->query();
    }
  }

  /**
   * @param string $rulesXmlString
   * @param int $taskId
   * @param string $lmtask
   */
  public function addRules($rulesXmlString,$taskId,$lmtask=''){
    //TODO new version!!!
    $rulesArr=explode('</AssociationRule',$rulesXmlString);
    if (count($rulesArr)){
      foreach($rulesArr as $rule){
        if (!($startPos=strpos($rule,'<AssociationRule '))){
          continue;
        }
        $rule=substr($rule,$startPos).'</AssociationRule>';
        $this->addRule($rule,$taskId,$lmtask);
      }
    }
  }

  /**
   * @param int $taskId
   * @return string
   */
  public function getRulesXml($taskId){
    $rulesXml='<AssociationRules xmlns="http://keg.vse.cz/lm/AssociationRules/v1.0">';
      $rules=$this->getRules($taskId);
    if ($rules && count($rules)){
      foreach($rules as $rule){
        $ruleData=$rule->data;
        $ruleData='<AssociationRule id="'.$rule->id.'">'.substr($ruleData,strpos($ruleData,'>')+1);
        $rulesXml.=$ruleData;
      }
    }
    $rulesXml.='</AssociationRules>';
    return $rulesXml;
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