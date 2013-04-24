
/**
 *  Proměnné pro uložení přehledu
 */ 
var attributesNames=null;
var tasksNames=null;

/**
 *  Funkce pro kontrolu, jestli je zadané jméno unikátní - pracuje s formulářem
 */
function checkAttributeNameShow(){ 
  valueOriginal=$('attributeName').value;
  value=valueOriginal.trim();
  if (value!=valueOriginal){
    $('attributeName').value=value;
  }           
  checkResult=checkAttributeName(value); 
  if (value==''){
    $('attributeNameExists').style.display='none';
    $('attributeNameNotSet').style.display='inline-block';
    $('attributeNameNotChecked').style.display='none';
    $('attributeName').addClass('error');
  }else if (checkResult==true){
    $('attributeNameExists').style.display='none';
    $('attributeNameNotSet').style.display='none';
    $('attributeNameNotChecked').style.display='none';
    $('attributeName').removeClass('error');
  }else if(checkResult==null){
    $('attributeNameExists').style.display='none';
    $('attributeNameNotSet').style.display='none';
    $('attributeNameNotChecked').style.display='inline-block';
    $('attributeName').addClass('error');
  }else if(checkResult==false){
    $('attributeNameExists').style.display='inline-block';
    $('attributeNameNotSet').style.display='none';
    $('attributeNameNotChecked').style.display='none';
    $('attributeName').addClass('error');
  }             
}  

/**
 *  Funkce pro kontrolu, jestli je zadané jméno unikátní
 */ 
function checkAttributeName(name){
  if (attributesNames==null){
    return null;
  }else{
    return (!inArray(name,attributesNames));
  }
}

/**
 *  Funkce pro získání přehledu existujících atributů
 */ 
function getAttributesNames(kbiId,checkShow=true){ 
  var a = new Ajax('/index.php?option=com_dbconnect&controller=data&task=getExistingAttributes&format=raw&kbi='+kbiId,{
    method:'get',
    onComplete:function(response){  
       var resp=JSON && JSON.parse(response) || eval("(function(){return " + response + ";})()");
       if (resp.result=='ok'){
         attributesNames=resp.attributes;
       }
       if (checkShow){
         checkAttributeNameShow();
       }
    }
  }).request();  
}


/**
 *  Alternativa PHP funkce in_array
 */ 
function inArray(needle, haystack) {
  var length = haystack.length;
  for(var i = 0; i < length; i++) {
    if(haystack[i] == needle) return true;
  }
  return false;
}
                      
/**
 *  Funkce pro kontrolu, jestli je zadané jméno unikátní
 */ 
function checkTaskName(name){
  if (tasksNames==null){
    return null;
  }else{
    return (!inArray(name,tasksNames));
  }
}

/**
 *  Funkce pro kontrolu toho, zda se mají přenačítat 
 */ 
function checkTaskNameShow(){
  valueOriginal=$('name').value;
  value=valueOriginal.trim();           
  checkResult=checkTaskName(value); 
  if (value==''){                     
    $('taskNameExists').style.display='none';
    $('taskNameNotSet').style.display='inline-block';
    $('taskNameNotChecked').style.display='none';
    $('name').addClass('error');
  }else if(checkResult==true){    
    $('taskNameExists').style.display='none';
    $('taskNameNotSet').style.display='none';
    $('taskNameNotChecked').style.display='none';
    $('name').removeClass('error');
  }else if(checkResult==null){
    $('taskNameExists').style.display='none';
    $('taskNameNotSet').style.display='none';
    $('taskNameNotChecked').style.display='inline-block';
    $('name').addClass('error');
  }else if(checkResult==false){
    $('taskNameExists').style.display='inline-block';
    $('taskNameNotSet').style.display='none';
    $('taskNameNotChecked').style.display='none';
    $('name').addClass('error');
  }
}

/**
 *  Funkce pro získání přehledu existujících atributů
 */ 
function getTasksNames(checkShow=true){ 
  var requestUrl='/index.php?option=com_dbconnect&controller=data&task=getExistingTasks&format=raw&ignoreAnonymous=ok';
  var a = new Ajax(requestUrl,{
    method:'get',
    onComplete:function(response){  
       var resp=JSON && JSON.parse(response) || eval("(function(){return " + response + ";})()");
       if (resp.result=='ok'){
         tasksNames=resp.tasks;
       }          
       if (checkShow){
         checkTaskNameShow();
       }
    }
  }).request();  
}