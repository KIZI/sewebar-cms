<?php
/**
* Model pro práci s tabulkami uloženými v databázi v podobě connection stringů
*/                            

jimport('joomla.application.component.model');
                   
/**
 * @package Joomla
 * @subpackage Config
 */
class dbconnectModelKbi extends JModel
{
  
	/**
	 *   Funkce pro uložení nového záznamu o úloze
	 */   	
  public function getSource($id){                                                                  
    $this->_db->setQuery('SELECT * FROM #__kbi_sources WHERE id='.$this->_db->quote($id).' LIMIT 1;');
    return $this->_db->loadObject();
  }
  
  /**
   *  Funkce vracející ID mineru z předaných parametrů KBI zdroje
   */     
  public static function getMinerId($params){
    $paramsArr=json_decode($params,true);
    return @$paramsArr['miner_id'];
  }
  
  /**
   *  Funkce pro vytvoření nového KBI zdroje
   */     
  public function newLMSource($name,$minerUrl,$minerId,$matrixName=''){
    $this->_db->setQuery("INSERT INTO #__kbi_sources (name,url,type,method,params)VALUES(".$this->_db->quote($name).",
    ".$this->_db->quote($minerUrl).",
    'LISPMINER',
    'POST',
    ".$this->_db->quote(json_encode(array('miner_id'=>$minerId,'matrix'=>$matrixName)))."
    );");
    
    if ($this->_db->query()){
      return $this->_db->insertid();
    }
  }
  
  /**
   *  Funkce pro zaktualizování ID mineru v rámci stávajícího KBI zdroje
   */     
  public function updateLMSource_minerId($id,$minerId){
    $this->_db->setQuery("UPDATE #__kbi_sources SET params=".$this->_db->quote(json_encode(array('miner_id'=>$minerId)))." WHERE id=".$this->_db->quote($id)." LIMIT 1;");
    $this->_db->query();
  }
  
  
}
?>
