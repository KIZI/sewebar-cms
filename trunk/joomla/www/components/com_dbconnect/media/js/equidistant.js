// JavaScript Document
  
  function cleanNumber(number){
    //number=number.replace(/^\s+|\s+$/g,'');
    number=number.replace(',','.');
    return number.replace(' ','');
  }              
  function is_numeric(value){
    if (value==''){return false}
    return !isNaN(value);
  }
  function equidistantInputCheck(){
    if (checkAttributeName($('attributeName').value)==false){
      checkAttributeNameShow();
      alert(lang['INPUT_VALID_ATTRIBUTE_NAME']);
      return false;
    }   
    start=cleanNumber($('start').value);
    $('start').value=start;
    if (!is_numeric(start)){
      alert(lang['START_VALUE_IS_NOT_NUMBER']);
      return false;
    }
    end=cleanNumber($('end').value);
    $('end').value=end;
    if (!is_numeric(end)){
      alert(lang['END_VALUE_IS_NOT_NUMBER']);
      return false;
    }
    if (parseFloat(start)>=parseFloat(end)){
      alert(lang['END_VALUE_IS_NOT_NUMBER']);
      return false;
    }         
    step=cleanNumber($('step').value);
    $('step').value=step;
    if ((!is_numeric(step))||(step<=0)){
      alert(lang['END_VALUE_IS_NOT_NUMBER']);
      return false;
    }
    if (step>(end-start)){
      alert(lang['STEP_VALUE_IS_TOO_BIG']);
      return false;
    }
    return true;
  }