<?php
/**
 *  Třída pro manuální přiřazování
 *  @author Stanislav Vojíř
 *  @version 1.0 
 */         
require_once(JPATH_COMPONENT.DS.'library'.DS.'AbstractValuesAssignClass.php');

class ManualValuesAssignClass extends AbstractValuesAssignClass{ 
	
  
  public function initMapping(){
    //plně manuální režim - nic neřešíme...
  }
	
}       
?>
