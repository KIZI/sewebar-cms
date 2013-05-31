// JavaScript Document
  
  var groupsCount=0;
  var intervalsCount=0;
  var editedGroup="";
  /**
   *  Funkce pro kontrolu unikátnosti jednotlivých BINů
   */                     
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
  
  /**
   *  Funkce pro kontrolu, jestli se překrývají zadané intervaly
   */                     
  function intervalOverlap(leftValue1,rightValue1,leftBound1,rightBound1,leftValue2,rightValue2,leftBound2,rightBound2){
    //pripadne prohozeni intervalu
    if (leftValue1<leftValue2){
      leftValueX=leftValue2;rightValueX=rightValue2;leftBoundX=leftBound2;rightBoundX=rightBound2;
      leftValue2=leftValue1;rightValue2=rightValue1;leftBound2=leftBound1;rightBound2=rightBound1;
      leftValue1=leftValueX;rightValue1=rightValueX;leftBound1=leftBoundX;rightBound1=rightBoundX;
    }
    //podminky
    if ((leftValue1>leftValue2)&&(leftValue1<rightValue2)){return true;}
    if ((rightValue1<rightValue2)&&(rightValue1>leftValue2)){return true;}
    if ((leftValue1==leftValue2)&&(leftBound1=="closed")&&(leftBound2=="closed")){return true;}
    if ((rightValue1==rightValue2)&&(rightBound1=="Closed")&&(rightBound2=="Closed")){return true;}
    if ((rightValue1==leftValue2)&&(rightBound1=="Closed")&&(leftBound2=="closed")){return true;}
    if ((leftValue1==rightValue2)&&(leftBound1=="closed")&&(rightBound2=="Closed")){return true;}
    //proslo to...
    return false;
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
  
  function checkIntervalSubmit(group,event){ 
    var x;
    if(window.event){
    	x=event.keyCode;
    }else if(event.which){
    	x=event.which;
    }
    if (x==13){
      setTimeout("addIntervalSubmit('"+group+"')",100);
      return false;
    }else{
      return true;
    }
  }
  
  function addItem(group){
    if (!checkIntervalSubmitted()){return;}
    editedGroup=group;
    $(group+"_addDiv").set('html',lang["INTERVAL_TO_ADD"]+" <select name=\""+group+"_leftBound\" id=\""+group+"_leftBound\" onkeydown=\"alert('checkX'); return checkIntervalSubmit('"+group+"',event);\"><option value=\"closed\">"+lang["INTERVAL_LEFT_CLOSED"]+"</option><option value=\"open\">"+lang["INTERVAL_LEFT_OPEN"]+"</option></select> <input id=\""+group+"_startValueInput\" value=\"\" type=\"text\" onkeydown=\"return checkIntervalSubmit('"+group+"',event);\" /> <strong>;</strong> <input id=\""+group+"_endValueInput\" value=\"\" type=\"text\" onkeydown=\"return checkIntervalSubmit('"+group+"',event);\" /> <select name=\""+group+"_rightBound\" id=\""+group+"_rightBound\" onkeydown=\"return checkIntervalSubmit('"+group+"',event);\"><option value=\"Closed\">"+lang["INTERVAL_RIGHT_CLOSED"]+"</option><option value=\"Open\" selected=\"selected\">"+lang["INTERVAL_RIGHT_OPEN"]+"</option></select><a href=\"javascript:addIntervalSubmit('"+group+"');\" class=\"smallButton\">"+lang["ADD_TO_GROUP"]+"</a><a href=\"javascript:addIntervalCancel('"+group+"');\" class=\"smallButton\">"+lang["CANCEL"]+"</a>");
  }
  
  function deleteGroup(group){
    if (confirm(lang['REALLY_DELETE_SELECTED_GROUP'])){
      if (editedGroup==group){
        editedGroup="";
      }
      $(group).destroy();
    }
  }
  
  function deleteInterval(id){
    parentDiv=$(id).getParent();
    parentDiv.destroy();
  }
  
  function addIntervalCancel(group){ 
    editedGroup="";
    $(group+"_addDiv").set('html','<a href="javascript:addItem('+"'"+group+"'"+')" class="smallButton">'+lang["ADD_INTERVAL"]+'</a>');
  }
  
  
  function addIntervalSubmit(group){
    //TODO kontrola, jestli neni zadana hodnota v jine kategorii!
    startValue=$(group+'_startValueInput').get('value');
    startValue=startValue.trim();
    if (startValue!='0'){
      startValue=parseFloat(startValue.replace(',','.')) || 'X';
    }    
    if (startValue=='X'){
      alert(lang["START_VALUE_IS_NOT_NUMBER"]);
      return;
    }
    
    endValue=$(group+'_endValueInput').get('value');
    endValue=endValue.trim();
    if (endValue!='0'){
      endValue=parseFloat(endValue.replace(',','.')) || 'X';
    }
    if (endValue=='X'){
      alert(lang["END_VALUE_IS_NOT_NUMBER"]);
      return;
    }
    leftBound=$(group+'_leftBound').get('value');
    rightBound=$(group+'_rightBound').get('value');
    
    if ((startValue>endValue)||((startValue==endValue)&&(!((leftBound=='closed')&&(rightBound=='Closed'))))){
      alert(lang["START_VALUE_BIGGER_THAN_END_OR_SAME"]);
      return;
    }
    
    //projiti všech položek, které byly přidány v minulosti            
    var overlapingIntervalsArr=new Array();
    
    $$('input.intervalInput').each(function(el){
                                       intervalArr=el.value.split("#");
                                       if (intervalArr[0].substr(0,2)=='cl'){
                                         leftBound2="closed";
                                         rightBound2=intervalArr[0].substr(6);
                                       }else{
                                         leftBound2="open";
                                         rightBound2=intervalArr[0].substr(4);
                                       }
                                       leftValue2=intervalArr[1];
                                       rightValue2=intervalArr[2];
                                       
                                       if (intervalOverlap(startValue,endValue,leftBound,rightBound,leftValue2,rightValue2,leftBound2,rightBound2)){
                                         if (leftBound2=='closed'){
                                           str=lang["INTERVAL_LEFT_CLOSED"];
                                         }else{
                                           str=lang["INTERVAL_LEFT_OPEN"];
                                         }
                                         str+=leftValue2+" ; "+rightValue2;
                                         if (rightBound2=='closed'){
                                           str+=lang["INTERVAL_RIGHT_CLOSED"];
                                         }else{
                                           str+=lang["INTERVAL_RIGHT_OPEN"];
                                         }
                                         overlapingIntervalsArr.push(str);
                                       }
                                     });
    if (overlapingIntervalsArr.length>0){
      if (overlapingIntervalsArr.length>1){
        alert(lang["OVERLAP_WITH_INTERVALS"]+" "+overlapingIntervalsArr.join(", "));
      }else{
        alert(lang["OVERLAP_WITH_INTERVAL"]+" "+overlapingIntervalsArr.join(", "));
      }
      return;
    }        
    
    //pridani polozky...
    intervalHTML='';
    if (leftBound=='closed'){intervalHTML+=lang["INTERVAL_LEFT_CLOSED"];}else{intervalHTML+=lang['INTERVAL_LEFT_OPEN'];}
    intervalHTML+=startValue+' ; '+endValue;
    if (rightBound=='Closed'){intervalHTML+=lang["INTERVAL_RIGHT_CLOSED"];}else{intervalHTML+=lang["INTERVAL_RIGHT_OPEN"];}
    intervalData=leftBound+rightBound+'#'+startValue+'#'+endValue;
    editedGroup="";              
    intervalsCount++;
    valueDiv=new Element("div",{"id":"interval_"+intervalsCount});
    value=htmlspecialchars(intervalData);
    valueDiv.set('html',intervalHTML+"<input type=\"hidden\" name=\""+group+"_interval_"+intervalsCount+"\" value=\""+intervalData+"\" class=\"intervalInput\" /><a href=\"#\" onclick=\"deleteInterval(this);\" title=\""+lang["DELETE"]+"\">x</a>");
    valueDiv.inject(group+"_itemsDiv");
    
    $(group+"_addDiv").set('html','<a href="javascript:addItem('+"'"+group+"'"+')" class="smallButton">'+lang["ADD_ITEM"]+'</a>');
  }
  
  function addGroup(){
    if (!checkIntervalSubmitted()){return;}
    groupsCount++;
    groupDivId="group_"+groupsCount;
    groupDiv=new Element("div",{"id":groupDivId,"class":"groupDiv"});
    groupDiv.set('html','<a class="deleteGroupA" href="javascript:deleteGroup('+"'"+groupDivId+"'"+')">'+lang["DELETE_GROUP"]+'</a><div><label for="'+groupDivId+'_name">'+lang["GROUP_NAME"]+'</label> <input onblur="binNameCheck(this);" class="binNameInput" type="text" name="'+groupDivId+'_name" id="'+groupDivId+'_name" value="'+groupDivId+'" /></div><div id="'+groupDivId+'_itemsDiv" class="itemsDiv"></div><div id="'+groupDivId+'_addDiv"><a href="javascript:addItem('+"'"+groupDivId+"'"+')" class="smallButton">'+lang["ADD_ITEM"]+'</a></div>');
    groupDiv.inject("testDiv");
    
    groupName=$(groupDivId+"_name");
    groupName.focus();
    groupName.select();
    //var testDiv=$("testDiv");
    //testDiv.appendText('pokus');
    //testDiv.inject(groupDiv);
  }
  
  
  /**
   *  Funkce pro kontrolu, jestli je ukončené přidávání poslední hodnoty
   */                     
  function checkIntervalSubmitted(){  
    if (editedGroup==""){
      return true;
    }else{        
      if (confirm(lang["NOT_SUBMITTED_INTERVAL_WARNING"])){
        addIntervalCancel(editedGroup);
        return true;
      }
    }
    return false;
  }
  
  /**
   *  Funkce pro kontroly před odesláním formuláře
   */                     
  function submitIntervalEnumeration(){
    if (checkAttributeName($('attributeName').value)==false){
      checkAttributeNameShow();
      alert(lang['INPUT_VALID_ATTRIBUTE_NAME']);
      return false;
    }
    if (!checkIntervalSubmitted()){
      return false;
    }            
    if (!checkNoGroupedIntervals()){
      alert(lang["NO_GROUPED_INTERVALS_FOUND"]);
      return false;
    }
    if (checkBlankGroups()){
      return (confirm(lang["BLANK_INTERVAL_GROUPS_WARNING"]));
    }
    return true;
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
   *  Funkce pro kontrolu, jestli jsou v zadání alespoň nějaké hodnoty
   */
  function checkNoGroupedIntervals(){
    return ($$('input.intervalInput').length>0);
  }  
  
  
  /**
   *  Funkce pro předgenerování existujících skupin intervalů
   */                     
  function prepareDefaultBins(){
    binsArr.each(function(bin){
                    //pridame skupinu
                    groupsCount++;
                    var groupDivId="group_"+groupsCount;
                    var groupDiv=new Element("div",{"id":groupDivId,"class":"groupDiv"});
                    groupDiv.set('html','<a class="deleteGroupA" href="javascript:deleteGroup('+"'"+groupDivId+"'"+')">'+lang["DELETE_GROUP"]+'</a><div><label for="'+groupDivId+'_name">'+lang["GROUP_NAME"]+'</label> <input  onblur="binNameCheck(this);" type="text" name="'+groupDivId+'_name" id="'+groupDivId+'_name" value="'+htmlspecialchars(bin.name)+'" class="binNameInput" /></div><div id="'+groupDivId+'_itemsDiv" class="itemsDiv"></div><div id="'+groupDivId+'_addDiv"><a href="javascript:addItem('+"'"+groupDivId+"'"+')" class="smallButton">'+lang["ADD_ITEM"]+'</a></div>');
                    groupDiv.inject("testDiv");
                    
                    bin.intervals.each(function(interval){
                      intervalsCount++;
                      valueDiv=new Element("div",{"id":"interval_"+intervalsCount});
                      if (interval.leftBound=="open"){
                        intervalValue="open";
                        intervalText=lang["INTERVAL_LEFT_OPEN"];
                      }else{
                        intervalValue="closed";
                        intervalText=lang["INTERVAL_LEFT_CLOSED"];
                      }
                      intervalText+=interval.leftMargin+" ; "+interval.rightMargin;
                      if (interval.rightBound=="closed"){
                        intervalText+=lang["INTERVAL_RIGHT_CLOSED"];
                        intervalValue+="Closed";
                      }else{
                        intervalText+=lang["INTERVAL_RIGHT_OPEN"];
                        intervalValue+="Open";
                      }
                      intervalValue+="#"+interval.leftMargin+"#"+interval.rightMargin;
                      valueDiv.set('html',intervalText+"<input type=\"hidden\" name=\""+groupDivId+"_interval_"+intervalsCount+"\" value=\""+intervalValue+"\" class=\"intervalInput\" /><a href=\"#\" onclick=\"deleteInterval(this);\" title=\""+lang["DELETE"]+"\">x</a>");
                      valueDiv.inject(groupDivId+"_itemsDiv");
                    });
                 });
  }
