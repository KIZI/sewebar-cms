  var usedValuesArr=new Array();
  var groupsCount=0;
  var valuesCount=0;
  var editedGroup="";          
  
  function binNameCheck(binNameInput){
    value=binNameInput.value;
    value.trim();
    if (value==""){
      alert(lang['BLANK_BIN_NAME']);
      setTimeout("$("+binNameInput.id+").focus();",50);
      return;
    }else{
      usedName=false;
      $$("input.binNameInput").each(function(el){
                                   if (el.id!=binNameInput.id){ 
                                     if (el.value==binNameInput.value){
                                       usedName=true;
                                     }
                                   }                                            
                                 });
      if (usedName){
        alert(lang['BIN_NAME_HAS_BEEN_USED_FOR_OTHER_BIN']);
        setTimeout("$("+binNameInput.id+").focus();",50);
        return;
      }                           
    }
  }
  
  function prepareDefaultBins(){
    binsArr.each(function(bin){
                    //pridame skupinu
                    groupsCount++;
                    var groupDivId="group_"+groupsCount;
                    var groupDiv=new Element("div",{"id":groupDivId,"class":"groupDiv"});
                    groupDiv.set('html','<a class="deleteGroupA" href="javascript:deleteGroup('+"'"+groupDivId+"'"+')">'+lang['DELETE_GROUP']+'</a><div><label for="'+groupDivId+'_name">'+lang['GROUP_NAME']+'</label> <input  onblur="binNameCheck(this);" type="text" name="'+groupDivId+'_name" id="'+groupDivId+'_name" value="'+htmlspecialchars(bin.name)+'" class="binNameInput" /></div><div id="'+groupDivId+'_itemsDiv" class="itemsDiv"></div><div id="'+groupDivId+'_addDiv"><a href="javascript:addItem('+"'"+groupDivId+"'"+')" class="smallButton">'+lang["ADD_ITEM"]+'</a></div>');
                    groupDiv.inject("testDiv");
                    
                    bin.values.each(function(value){
                      valuesCount++;
                      valueDiv=new Element("div",{"id":"value_"+valuesCount});
                      value=htmlspecialchars(value);
                      valueDiv.set('html',value+"<input type=\"hidden\" name=\""+groupDivId+"_value_"+valuesCount+"\" value=\""+value+"\" class=\"valueInput\" /><a href=\"#\" onclick=\"deleteValue(this);\" title=\""+lang['DELETE']+"\">x</a>");
                      valueDiv.inject(groupDivId+"_itemsDiv");
                    });
                 });
  }
  
  function checkUsedValues(){
    usedValuesArr=new Array();
    //projdeme všechny položky, které byly přidány do jednotlivých skupin
    $$("div.itemsDiv input[type=hidden]").each(function(el){
                                                 usedValuesArr.push(el.value);
                                               });
  }
  
  function getItemsArr(){
    checkUsedValues();
    itemsArr=new Array();
    valuesArr.each(function(item){
                     var itemUsed=false;
                     usedValuesArr.each(function(usedItem){
                       if (usedItem==item){
                         itemUsed=true; 
                       }
                     });
                     if (!itemUsed){
                       itemsArr.push(item);
                     }
                   });
     itemsArr.sort();              
     return itemsArr;              
  } 
  
  function htmlspecialchars(str) {
    if (typeof(str) == "string") {
      str = str.replace(/&/g, "&amp;"); /* must do &amp; first */
      str = str.replace(/"/g, "&quot;");
      str = str.replace(/'/g, "&#039;");
      str = str.replace(/</g, "&lt;");
      str = str.replace(/>/g, "&gt;");
    }
    return str;
  }
  
  function checkValueSubmit(group,event){
    var x;
    if(window.event){
    	x=event.keyCode;
    }else if(event.which){
    	x=event.which;
    }
    if (x==13){
      addValueSubmit(group);
      return false;
    }else{
      return true;
    }
  }
  
  function addItem(group){
    if (!checkValueSubmitted()){return;}
    editedGroup=group;
    if (valuesArr.length>0){
      var itemsArr=getItemsArr();
      var itemsHTML="";
      itemsArr.each(function(item){
        itemsHTML+="<option value=\""+htmlspecialchars(item)+"\">"+htmlspecialchars(item)+"</option>";
      });
      if (itemsHTML==""){
        alert(lang['NO_ITEMS_TO_ADD']);
      }
      $(group+"_addDiv").set('html',"<label for=\""+group+"_valueInput\">"+lang['VALUE_TO_ADD']+"</label> <select id=\""+group+"_valueInput\" onkeydown=\"return checkValueSubmit('"+group+"',event);\" >"+itemsHTML+"</select> <a href=\"javascript:addValueSubmit('"+group+"');\" class=\"smallButton\">"+lang['ADD_TO_GROUP']+"</a><a href=\"javascript:addValueCancel('"+group+"');\" class=\"smallButton\">"+lang['CANCEL']+"</a>");
    }else{
      $(group+"_addDiv").set('html',"<label for=\""+group+"_valueInput\">"+lang['VALUE_TO_ADD']+"</label> <input id=\""+group+"_valueInput\" value=\"\" type=\"text\" onkeydown=\"return checkValueSubmit('"+group+"',event);\" /> <a href=\"javascript:addValueSubmit('"+group+"');\" class=\"smallButton\">"+lang['ADD_TO_GROUP']+"</a><a href=\"javascript:addValueCancel('"+group+"');\" class=\"smallButton\">"+lang['CANCEL']+"</a>");
    }
  }
  
  function deleteGroup(group){
    if (confirm(lang['REALLY_DELETE_SELECTED_GROUP'])){
      if (editedGroup==group){
        editedGroup="";
      }
      $(group).remove();
    }
  }
  
  function deleteValue(id){
    parentDiv=$(id).getParent();
    parentDiv.destroy();
  }
  
  function addValueCancel(group){
    editedGroup=""; 
    $(group+"_addDiv").set('html','<a href="javascript:addItem('+"'"+group+"'"+')" class="smallButton">'+lang["ADD_ITEM"]+'</a>');
  }
  
  function addValueSubmit(group){
    //TODO kontrola, jestli neni zadana hodnota v jine kategorii!
    value=$(group+'_valueInput').get('value');
    value=value.trim();
    editedGroup="";
    if (value!=""){
      //pridani polozky...
      valuesCount++;
      valueDiv=new Element("div",{"id":"value_"+valuesCount});
      value=htmlspecialchars(value);
      valueDiv.set('html',value+"<input type=\"hidden\" name=\""+group+"_value_"+valuesCount+"\" value=\""+value+"\" class=\"valueInput\" /><a href=\"#\" onclick=\"deleteValue(this);\" title=\""+lang['DELETE']+"\">x</a>");
      valueDiv.inject(group+"_itemsDiv");
    }
    $(group+"_addDiv").set('html','<a href="javascript:addItem('+"'"+group+"'"+')" class="smallButton">'+lang["ADD_ITEM"]+'</a>');
  }
  
  function addGroup(){
    if (!checkValueSubmitted()){return;}
    groupsCount++;
    groupDivId="group_"+groupsCount;
    groupDiv=new Element("div",{"id":groupDivId,"class":"groupDiv"});
    groupDiv.set('html','<a class="deleteGroupA" href="javascript:deleteGroup('+"'"+groupDivId+"'"+')">'+lang['DELETE_GROUP']+'</a><div><label for="'+groupDivId+'_name">'+lang['GROUP_NAME']+'</label> <input type="text" onblur="binNameCheck(this);" name="'+groupDivId+'_name" id="'+groupDivId+'_name" value="'+groupDivId+'" class="binNameInput" /></div><div id="'+groupDivId+'_itemsDiv" class="itemsDiv"></div><div id="'+groupDivId+'_addDiv"><a href="javascript:addItem('+"'"+groupDivId+"'"+')" class="smallButton">'+lang["ADD_ITEM"]+'</a></div>');
    groupDiv.inject("testDiv");
    
    groupName=$(groupDivId+"_name");
    groupName.focus();
    groupName.select();
  }
  
  /**
   *  Funkce pro kontrolu, jestli jsou v zadání prázdné skupiny (bez hodnot)
   */                     
  function checkBlankGroups(){
    var blankResult=false;       
    $$('div.itemsDiv').each(function(el){
      items=$$("div#"+el.id+' input');
      if (items.length==0){   
        blankResult=true;
      }
    });
    return blankResult;
  }
  
  /**
   *  Funkce pro kontrolu, jestli je ukončené přidávání poslední hodnoty
   */                     
  function checkValueSubmitted(){  
    if (editedGroup==""){
      return true;
    }else{        
      if (confirm(lang['NOT_SUBMITTED_VALUE_WARNING'])){
        addValueCancel(editedGroup);
        return true;
      }
    }
    return false;
  }
  
  /**
   *  Funkce pro kontroly před odesláním formuláře
   */                     
  function submitNominalEnumeration(){   
    if (checkAttributeName($('attributeName').value)==false){
      checkAttributeNameShow();
      alert(lang['INPUT_VALID_ATTRIBUTE_NAME']);
      return false;
    }
    if (!checkValueSubmitted()){
      return false;
    }
    if (!checkNoGroupedValues()){
      alert(lang['NO_GROUPED_VALUES_FOUND']);
      return false;
    }
    if (checkBlankGroups()){
      return (confirm(lang['BLANK_NOMINAL_GROUPS_WARNING']));
    }
    return true;
  }
  /**
   *  Funkce pro kontrolu, jestli jsou v zadání alespoň nějaké hodnoty
   */
  function checkNoGroupedValues(){
    return ($$('input.valueInput').length>0);
  }  