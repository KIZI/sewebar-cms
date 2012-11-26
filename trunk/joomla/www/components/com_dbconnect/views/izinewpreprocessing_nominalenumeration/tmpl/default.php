<?php 
  defined('_JEXEC') or die('Restricted access');
  echo '<div id="iziDiv">';
  
  echo '<a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=newAttribute&col='.urlencode($this->pmmlName).'&taskId='.$this->taskId).'&tmpl=component" class="backButton">'.JText::_('BACK').'</a>';
  
  echo '<h1>'.JText::_('PREPROCESSING_NOMINAL_ENUMERATION').'</h1>';
  
  $formatType=strtolower(@$this->format->Range['type']);
  $valuesArr=array();
  if (($formatType)&&($formatType!='interval')&&($formatType!='regex')){
    //má smysl řešit hodnoty
    if (count($this->format->Range->Value)>0){
      foreach ($this->format->Range->Value as $value) {
    	  $valuesArr[]=(string)$value;
      }
    }
  }
  
  $binsArr=array();       
  if (isset($this->preprocessingHint)&&isset($this->preprocessingHint->NominalEnumeration)){ 
    if (count($this->preprocessingHint->NominalEnumeration->NominalBin)>0){         
      foreach ($this->preprocessingHint->NominalEnumeration->NominalBin as $nominalBin){
        $binArr=array();
        $binArr['name']=(string)$nominalBin->Name;
        $binArr['values']=array();
        if (count($nominalBin->Value)>0){
          foreach ($nominalBin->Value as $value) {
        	  $binArr['values'][]=(string)$value; 
          }
        }
        $binsArr[]=$binArr;
      }
      echo '<script type="text/javascript">
              var binsJson=\''.json_encode($binsArr).'\'; 
              var binsArr= JSON && JSON.parse(binsJson) || eval("(function(){return " + binsJSON + ";})()");

              window.addEvent(\'domready\',function() {   
                  prepareDefaultBins();
                }
              );
            </script>';
    }
  }
  
  echo '<script type="text/javascript">
          var valuesArr='.json_encode($valuesArr).';
          var usedValuesArr=new Array();
          var groupsCount=0;
          var valuesCount=0;
          var editedGroup="";          
          
          function binNameCheck(binNameInput){
            value=binNameInput.value;
            value.trim();
            if (value==""){
              alert(\''.JText::_('BLANK_BIN_NAME').'\');
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
                alert(\''.JText::_('BIN_NAME_HAS_BEEN_USED_FOR_OTHER_BIN').'\');
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
                            groupDiv.setHTML(\'<a class="deleteGroupA" href="javascript:deleteGroup(\'+"\'"+groupDivId+"\'"+\')">'.JText::_('DELETE_GROUP').'</a><div><label for="\'+groupDivId+\'_name">'.JText::_('GROUP_NAME').'</label> <input  onblur="binNameCheck(this);" type="text" name="\'+groupDivId+\'_name" id="\'+groupDivId+\'_name" value="\'+htmlspecialchars(bin.name)+\'" class="binNameInput" /></div><div id="\'+groupDivId+\'_itemsDiv" class="itemsDiv"></div><div id="\'+groupDivId+\'_addDiv"><a href="javascript:addItem(\'+"\'"+groupDivId+"\'"+\')" class="smallButton">'.JText::_("ADD_ITEM").'</a></div>\');
                            groupDiv.inject("testDiv");
                            
                            bin.values.each(function(value){
                              valuesCount++;
                              valueDiv=new Element("div",{"id":"value_"+valuesCount});
                              value=htmlspecialchars(value);
                              valueDiv.setHTML(value+"<input type=\"hidden\" name=\""+groupDivId+"_value_"+valuesCount+"\" value=\""+value+"\" /><a href=\"#\" onclick=\"deleteValue(this);\" title=\"'.JText::_('DELETE').'\">x</a>");
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
              str = str.replace(/\'/g, "&#039;");
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
                alert("'.JText::_('NO_ITEMS_TO_ADD').'");
              }
              $(group+"_addDiv").setHTML("<label for=\""+group+"_valueInput\">'.JText::_('VALUE_TO_ADD').'</label> <select id=\""+group+"_valueInput\" onkeydown=\"return checkValueSubmit(\'"+group+"\',event);\" >"+itemsHTML+"</select> <a href=\"javascript:addValueSubmit(\'"+group+"\');\" class=\"smallButton\">'.JText::_('ADD_TO_GROUP').'</a><a href=\"javascript:addValueCancel(\'"+group+"\');\" class=\"smallButton\">'.JText::_('CANCEL').'</a>");
            }else{
              $(group+"_addDiv").setHTML("<label for=\""+group+"_valueInput\">'.JText::_('VALUE_TO_ADD').'</label> <input id=\""+group+"_valueInput\" value=\"\" type=\"text\" onkeydown=\"return checkValueSubmit(\'"+group+"\',event);\" /> <a href=\"javascript:addValueSubmit(\'"+group+"\');\" class=\"smallButton\">'.JText::_('ADD_TO_GROUP').'</a><a href=\"javascript:addValueCancel(\'"+group+"\');\" class=\"smallButton\">'.JText::_('CANCEL').'</a>");
            }
          }
          
          function deleteGroup(group){
            if (confirm("'.JText::_('REALLY_DELETE_SELECTED_GROUP').'")){
              if (editedGroup==group){
                editedGroup="";
              }
              $(group).remove();
            }
          }
          
          function deleteValue(id){
            parentDiv=$(id).getParent();
            parentDiv.remove();
          }
          
          function addValueCancel(group){
            editedGroup=""; 
            $(group+"_addDiv").setHTML(\'<a href="javascript:addItem(\'+"\'"+group+"\'"+\')" class="smallButton">'.JText::_("ADD_ITEM").'</a>\');
          }
          
          function addValueSubmit(group){
            //TODO kontrola, jestli neni zadana hodnota v jine kategorii!
            value=$(group+\'_valueInput\').getValue();
            value=value.trim();
            editedGroup="";
            if (value!=""){
              //pridani polozky...
              valuesCount++;
              valueDiv=new Element("div",{"id":"value_"+valuesCount});
              value=htmlspecialchars(value);
              valueDiv.setHTML(value+"<input type=\"hidden\" name=\""+group+"_value_"+valuesCount+"\" value=\""+value+"\" /><a href=\"#\" onclick=\"deleteValue(this);\" title=\"'.JText::_('DELETE').'\">x</a>");
              valueDiv.inject(group+"_itemsDiv");
            }
            $(group+"_addDiv").setHTML(\'<a href="javascript:addItem(\'+"\'"+group+"\'"+\')" class="smallButton">'.JText::_("ADD_ITEM").'</a>\');
          }
          
          function addGroup(){
            groupsCount++;
            groupDivId="group_"+groupsCount;
            groupDiv=new Element("div",{"id":groupDivId,"class":"groupDiv"});
            groupDiv.setHTML(\'<a class="deleteGroupA" href="javascript:deleteGroup(\'+"\'"+groupDivId+"\'"+\')">'.JText::_('DELETE_GROUP').'</a><div><label for="\'+groupDivId+\'_name">'.JText::_('GROUP_NAME').'</label> <input type="text" onblur="binNameCheck(this);" name="\'+groupDivId+\'_name" id="\'+groupDivId+\'_name" value="\'+groupDivId+\'" class="binNameInput" /></div><div id="\'+groupDivId+\'_itemsDiv" class="itemsDiv"></div><div id="\'+groupDivId+\'_addDiv"><a href="javascript:addItem(\'+"\'"+groupDivId+"\'"+\')" class="smallButton">'.JText::_("ADD_ITEM").'</a></div>\');
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
            $$(\'div.itemsDiv\').each(function(el){
              items=$$("div#"+el.id+\' input\');
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
              if (confirm("'.JText::_('NOT_SUBMITTED_VALUE_WARNING').'")){
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
            if (!checkValueSubmitted()){
              return false;
            }
            if (checkBlankGroups()){
              return (confirm(\''.JText::_('BLANK_NOMINAL_GROUPS_WARNING').'\'))
            }               
            return true;
          }
          
        </script>';
  
  echo '
        <a href="#" onclick="javascript:checkValueSubmitted();">check</a>
        <form method="post" onsubmit="return submitNominalEnumeration();" action="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=editPreprocessingHint_nominalEnumeration').'">
          <table>
            <tr>
              <td>'.JText::_('DATAFIELD').'</td>
              <td><strong>'.htmlspecialchars($this->pmmlName).'</strong></td>
            </tr>';
  if ($this->preprocessingHint){
    echo '  <tr>
              <td>'.JText::_('OLD_PREPROCESSING_NAME').'</td>
              <td><strong>'.htmlspecialchars($this->preprocessingHint->Name).'</strong></td>
            </tr>';
  }          
  echo '    <tr>
              <td>
                <label for="attributeName">'.JText::_('ATTRIBUTE_NAME').'</label>
              </td>
              <td>
                <input type="text" name="attributeName" id="attributeName" value="'.htmlspecialchars($this->pmmlName).'" />
              </td>
            </tr>
          </table>
          <div id="testDiv"></div>
          <div style="padding-left:10px;">
            <a href="javascript:addGroup();" class="smallButton">'.JText::_("ADD_GROUP").'</a>
          </div>
          <div class="formActionsDiv">
            <input type="submit" value="'.JText::_('SAVE_PREPROCESSING').'">
          </div>
          <input type="hidden" name="col" value="'.$this->pmmlName.'" />
          <input type="hidden" name="maName" value="'.$this->maName.'" />
          <input type="hidden" name="formatName" value="'.$this->formatName.'" />
          <input type="hidden" name="task_id" value="'.$this->taskId.'" />
        </form>';
?>