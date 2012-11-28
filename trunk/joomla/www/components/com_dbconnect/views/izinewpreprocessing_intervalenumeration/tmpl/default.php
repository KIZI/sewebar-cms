<?php 
  defined('_JEXEC') or die('Restricted access');
  echo '<div id="iziDiv">';
  
  echo '<a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=newAttribute&col='.urlencode($this->pmmlName).'&taskId='.$this->taskId).'&tmpl=component" class="backButton">'.JText::_('BACK').'</a>';
  
  echo '<h1>'.JText::_('PREPROCESSING_INTERVAL_ENUMERATION').'</h1>';
  
  $binsArr=array();       
  if (isset($this->preprocessingHint)&&isset($this->preprocessingHint->IntervalEnumeration)){ 
    if (count($this->preprocessingHint->IntervalEnumeration->IntervalBin)>0){         
      foreach ($this->preprocessingHint->IntervalEnumeration->IntervalBin as $intervalBin){
        $binArr=array();
        $binArr['name']=(string)$intervalBin->Name;
        $binArr['intervals']=array();
        if (count($intervalBin->Interval)>0){
          foreach ($intervalBin->Interval as $interval) {
            $closure=(string)$interval['closure'];
            if (substr($closure,0,4)=='open'){
              $leftBound='open';
              $closure=substr($closure,4);
            }else{
              $leftBound='closed';
              $closure=substr($closure,6);
            }
            if ($closure=='Closed'){
              $rightBound='Closed';
            }else{
              $rightBound='Open';
            }
        	  $binArr['intervals'][]=array('leftMargin'=>(string)$interval['leftMargin'],'rightMargin'=>(string)$interval['rightMargin'],'leftBound'=>$leftBound,'rightBound'=>$rightBound); 
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
              str = str.replace(/\'/g, "&#039;");
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
              setTimeout("addIntervalSubmit(\'"+group+"\')",100);
              return false;
            }else{
              return true;
            }
          }
          
          function addItem(group){
            if (!checkIntervalSubmitted()){return;}
            editedGroup=group;
            $(group+"_addDiv").setHTML("'.JText::_('INTERVAL_TO_ADD').' <select name=\""+group+"_leftBound\" id=\""+group+"_leftBound\" onkeydown=\"alert(\'checkX\'); return checkIntervalSubmit(\'"+group+"\',event);\"><option value=\"closed\">'.JText::_('INTERVAL_LEFT_CLOSED').'</option><option value=\"open\">'.JText::_('INTERVAL_LEFT_OPEN').'</option></select> <input id=\""+group+"_startValueInput\" value=\"\" type=\"text\" onkeydown=\"return checkIntervalSubmit(\'"+group+"\',event);\" /> <strong>;</strong> <input id=\""+group+"_endValueInput\" value=\"\" type=\"text\" onkeydown=\"return checkIntervalSubmit(\'"+group+"\',event);\" /> <select name=\""+group+"_rightBound\" id=\""+group+"_rightBound\" onkeydown=\"return checkIntervalSubmit(\'"+group+"\',event);\"><option value=\"Closed\">'.JText::_('INTERVAL_RIGHT_CLOSED').'</option><option value=\"Open\" selected=\"selected\">'.JText::_('INTERVAL_RIGHT_OPEN').'</option></select><a href=\"javascript:addIntervalSubmit(\'"+group+"\');\" class=\"smallButton\">'.JText::_('ADD_TO_GROUP').'</a><a href=\"javascript:addIntervalCancel(\'"+group+"\');\" class=\"smallButton\">'.JText::_('CANCEL').'</a>");
          }
          
          function deleteGroup(group){
            if (confirm("'.JText::_('REALLY_DELETE_SELECTED_GROUP').'")){
              if (editedGroup==group){
                editedGroup="";
              }
              $(group).remove();
            }
          }
          
          function deleteInterval(id){
            parentDiv=$(id).getParent();
            parentDiv.remove();
          }
          
          function addIntervalCancel(group){ 
            editedGroup="";
            $(group+"_addDiv").setHTML(\'<a href="javascript:addItem(\'+"\'"+group+"\'"+\')" class="smallButton">'.JText::_("ADD_INTERVAL").'</a>\');
          }
          
          var leftBoundOpenString="'.JText::_('INTERVAL_LEFT_OPEN').'";
          var rightBoundClosedString="'.JText::_('INTERVAL_RIGHT_CLOSED').'"; 
          var rightBoundOpenString="'.JText::_('INTERVAL_RIGHT_OPEN').'";
          var leftBoundClosedString="'.JText::_('INTERVAL_LEFT_CLOSED').'";
          
          function addIntervalSubmit(group){
            //TODO kontrola, jestli neni zadana hodnota v jine kategorii!
            startValue=$(group+\'_startValueInput\').getValue();
            startValue=startValue.trim();
            if (startValue!=\'0\'){
              startValue=parseFloat(startValue.replace(\',\',\'.\')) || \'X\';
            }    
            if (startValue==\'X\'){
              alert(\''.JText::_('START_VALUE_IS_NOT_NUMBER').'\');
              return;
            }
            
            endValue=$(group+\'_endValueInput\').getValue();
            endValue=endValue.trim();
            if (endValue!=\'0\'){
              endValue=parseFloat(endValue.replace(\',\',\'.\')) || \'X\';
            }
            if (endValue==\'X\'){
              alert(\''.JText::_('END_VALUE_IS_NOT_NUMBER').'\');
              return;
            }
            leftBound=$(group+\'_leftBound\').getValue();
            rightBound=$(group+\'_rightBound\').getValue();
            
            if ((startValue>endValue)||((startValue==endValue)&&(!((leftBound==\'closed\')&&(rightBound==\'Closed\'))))){
              alert(\''.JText::_('START_VALUE_BIGGER_THAN_END_OR_SAME').'\');
              return;
            }
            
            //projiti všech položek, které byly přidány v minulosti            
            var overlapingIntervalsArr=new Array();
            
            $$(\'input.intervalInput\').each(function(el){
                                               intervalArr=el.value.split("#");
                                               if (intervalArr[0].substr(0,2)==\'cl\'){
                                                 leftBound2="closed";
                                                 rightBound2=intervalArr[0].substr(6);
                                               }else{
                                                 leftBound2="open";
                                                 rightBound2=intervalArr[0].substr(4);
                                               }
                                               leftValue2=intervalArr[1];
                                               rightValue2=intervalArr[2];
                                               
                                               if (intervalOverlap(startValue,endValue,leftBound,rightBound,leftValue2,rightValue2,leftBound2,rightBound2)){
                                                 if (leftBound2==\'closed\'){
                                                   str=leftBoundClosedString;
                                                 }else{
                                                   str=leftBoundOpenString;
                                                 }
                                                 str+=leftValue2+" ; "+rightValue2;
                                                 if (rightBound2==\'closed\'){
                                                   str+=rightBoundClosedString;
                                                 }else{
                                                   str+=rightBoundOpenString;
                                                 }
                                                 overlapingIntervalsArr.push(str);
                                               }
                                             });';
  echo'     if (overlapingIntervalsArr.length>0){
              if (overlapingIntervalsArr.length>1){
                alert("'.JText::_('OVERLAP_WITH_INTERVALS').' "+overlapingIntervalsArr.join(", "));
              }else{
                alert("'.JText::_('OVERLAP_WITH_INTERVAL').' "+overlapingIntervalsArr.join(", "));
              }
              return;
            }        
            
            //pridani polozky...
            intervalHTML=\'\';
            if (leftBound==\'closed\'){intervalHTML+=\''.JText::_('INTERVAL_LEFT_CLOSED').'\';}else{intervalHTML+=\''.JText::_('INTERVAL_LEFT_OPEN').'\';}
            intervalHTML+=startValue+\' ; \'+endValue;
            if (rightBound==\'Closed\'){intervalHTML+=\''.JText::_('INTERVAL_RIGHT_CLOSED').'\';}else{intervalHTML+=\''.JText::_('INTERVAL_RIGHT_OPEN').'\';}
            intervalData=leftBound+rightBound+\'#\'+startValue+\'#\'+endValue;
            editedGroup="";              
            intervalsCount++;
            valueDiv=new Element("div",{"id":"interval_"+intervalsCount});
            value=htmlspecialchars(intervalData);
            valueDiv.setHTML(intervalHTML+"<input type=\"hidden\" name=\""+group+"_interval_"+intervalsCount+"\" value=\""+intervalData+"\" class=\"intervalInput\" /><a href=\"#\" onclick=\"deleteInterval(this);\" title=\"'.JText::_('DELETE').'\">x</a>");
            valueDiv.inject(group+"_itemsDiv");
            
            $(group+"_addDiv").setHTML(\'<a href="javascript:addItem(\'+"\'"+group+"\'"+\')" class="smallButton">'.JText::_("ADD_ITEM").'</a>\');
          }
          
          function addGroup(){
            if (!checkIntervalSubmitted()){return;}
            groupsCount++;
            groupDivId="group_"+groupsCount;
            groupDiv=new Element("div",{"id":groupDivId,"class":"groupDiv"});
            groupDiv.setHTML(\'<a class="deleteGroupA" href="javascript:deleteGroup(\'+"\'"+groupDivId+"\'"+\')">'.JText::_('DELETE_GROUP').'</a><div><label for="\'+groupDivId+\'_name">'.JText::_('GROUP_NAME').'</label> <input onblur="binNameCheck(this);" class="binNameInput" type="text" name="\'+groupDivId+\'_name" id="\'+groupDivId+\'_name" value="\'+groupDivId+\'" /></div><div id="\'+groupDivId+\'_itemsDiv" class="itemsDiv"></div><div id="\'+groupDivId+\'_addDiv"><a href="javascript:addItem(\'+"\'"+groupDivId+"\'"+\')" class="smallButton">'.JText::_("ADD_ITEM").'</a></div>\');
            groupDiv.inject("testDiv");
            
            groupName=$(groupDivId+"_name");
            groupName.focus();
            groupName.select();
            //var testDiv=$("testDiv");
            //testDiv.appendText(\'pokus\');
            //testDiv.inject(groupDiv);
          }
          
          
          /**
           *  Funkce pro kontrolu, jestli je ukončené přidávání poslední hodnoty
           */                     
          function checkIntervalSubmitted(){  
            if (editedGroup==""){
              return true;
            }else{        
              if (confirm("'.JText::_('NOT_SUBMITTED_INTERVAL_WARNING').'")){
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
            if (!checkIntervalSubmitted()){
              return false;
            }            
            if (!checkNoGroupedIntervals()){
              alert(\''.JText::_('NO_GROUPED_INTERVALS_FOUND').'\');
              return false;
            }
            if (checkBlankGroups()){
              return (confirm(\''.JText::_('BLANK_INTERVAL_GROUPS_WARNING').'\'))
            }               
            return true;
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
           *  Funkce pro kontrolu, jestli jsou v zadání alespoň nějaké hodnoty
           */
          function checkNoGroupedIntervals(){
            return ($$(\'input.intervalInput\').length>0);
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
                            groupDiv.setHTML(\'<a class="deleteGroupA" href="javascript:deleteGroup(\'+"\'"+groupDivId+"\'"+\')">'.JText::_('DELETE_GROUP').'</a><div><label for="\'+groupDivId+\'_name">'.JText::_('GROUP_NAME').'</label> <input  onblur="binNameCheck(this);" type="text" name="\'+groupDivId+\'_name" id="\'+groupDivId+\'_name" value="\'+htmlspecialchars(bin.name)+\'" class="binNameInput" /></div><div id="\'+groupDivId+\'_itemsDiv" class="itemsDiv"></div><div id="\'+groupDivId+\'_addDiv"><a href="javascript:addItem(\'+"\'"+groupDivId+"\'"+\')" class="smallButton">'.JText::_("ADD_ITEM").'</a></div>\');
                            groupDiv.inject("testDiv");
                            
                            bin.intervals.each(function(interval){
                              intervalsCount++;
                              valueDiv=new Element("div",{"id":"interval_"+intervalsCount});
                              if (interval.leftBound=="open"){
                                intervalValue="open";
                                intervalText=leftBoundOpenString;
                              }else{
                                intervalValue="closed";
                                intervalText=leftBoundClosedString;
                              }
                              intervalText+=interval.leftMargin+" ; "+interval.rightMargin;
                              if (interval.rightBound=="closed"){
                                intervalText+=rightBoundClosedString;
                                intervalValue+="Closed";
                              }else{
                                intervalText+=rightBoundOpenString;
                                intervalValue+="Open";
                              }
                              intervalValue+="#"+interval.leftMargin+"#"+interval.rightMargin;
                              valueDiv.setHTML(intervalText+"<input type=\"hidden\" name=\""+groupDivId+"_interval_"+intervalsCount+"\" value=\""+intervalValue+"\" class=\"intervalInput\" /><a href=\"#\" onclick=\"deleteInterval(this);\" title=\"'.JText::_('DELETE').'\">x</a>");
                              valueDiv.inject(groupDivId+"_itemsDiv");
                            });
                         });
          }                   
          
        </script>';
  
  echo '<form method="post" onsubmit="return submitIntervalEnumeration();" action="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=editPreprocessingHint_intervalEnumeration').'">
          <table>
            <tr>
              <td>'.JText::_('DATAFIELD').'</td>
              <td><strong>'.htmlspecialchars($this->pmmlName).'</strong></td>
            </tr>
            <tr>
              <td>'.JText::_('DATAFIELD_RANGE').'</td>
              <td>
                <strong>';
  if (count($this->format->Range->Interval)>0){
    $br=false;
    foreach ($this->format->Range->Interval as $interval) {
    	if ($br){echo '<br />';}
      $intervalText=(float)$interval['leftMargin'].' ; '.(float)$interval['rightMargin'];
      $closure=strtolower((string)$interval['closure']);
      if (substr($closure,0,4)=='open'){
        $intervalText=JText::_('INTERVAL_LEFT_OPEN').$intervalText;
        $closure=substr($closure,4);
      }else{
        $intervalText=JText::_('INTERVAL_LEFT_CLOSED').$intervalText;
        $closure=substr($closure,6);
      }
      if ($closure=='open'){
        $intervalText.=JText::_('INTERVAL_RIGHT_OPEN');
      }else{
        $intervalText.=JText::_('INTERVAL_RIGHT_CLOSED');
      }
      echo $intervalText;
      $br=true;
    }
  }              
  echo         '</strong>
              </td>
            </tr>
            <tr>
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