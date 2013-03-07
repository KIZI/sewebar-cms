// JavaScript Document
  /**
   *  Funkce pro kontrolu formuláře před odesláním
   */     
  function eachoneInputCheck(){ 
    if (checkAttributeName($('attributeName').value)==false){
      checkAttributeNameShow();
      alert(lang['INPUT_VALID_ATTRIBUTE_NAME']);
      return false;
    }
    return true;
  }