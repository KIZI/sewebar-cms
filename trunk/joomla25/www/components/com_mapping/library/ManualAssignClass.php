<?php
/**
 *  Třída pro manuální přiřazování nejlepší kombinace propojení klíčů
 *  @author Stanislav Vojíř
 *  @version 1.0 
 */         
require_once(JPATH_COMPONENT.DS.'library'.DS.'AbstractAssignClass.php');

class ManualAssignClass extends AbstractAssignClass{ 
	/**
	 *   Funkce vracející asociační pole dle automatického napárování
	 *   @return $finalArr;	 
	 */   	
	public function getAssignArr(){
	  return $this->finalArr;
  }
	
}       
?>
