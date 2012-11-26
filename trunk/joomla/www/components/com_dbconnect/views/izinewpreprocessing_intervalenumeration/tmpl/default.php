<?php 
  defined('_JEXEC') or die('Restricted access');
  echo '<div id="iziDiv">';
  
  echo '<a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=newAttribute&col='.urlencode($this->pmmlName).'&taskId='.$this->taskId).'&tmpl=component" class="backButton">'.JText::_('BACK').'</a>';
  
  echo '<h1>'.JText::_('PREPROCESSING_INTERVAL_ENUMERATION').'</h1>';
  
  $binsArr=array();
  if (isset($this->preprocessingHint)){
    if (count($this->preprocessingHint->IntervalBin)>0){
      foreach ($this->preprocessingHint->IntervalBin as $intervalBin){
      	//TODO zpracování existujících preprocessing hintů!!
      }
    }
  }
  
  echo '<script type="text/javascript">
          var groupsCount=0;
          var intervalsCount=0;
          
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
              addIntervalSubmit(group);
              return false;
            }else{
              return true;
            }
          }
          
          function addItem(group){
            $(group+"_addDiv").setHTML("'.JText::_('INTERVAL_TO_ADD').' <select name=\""+group+"_leftBound\" id=\""+group+"_leftBound\" onkeydown=\"return checkIntervalSubmit(\'"+group+"\',event);\"><option value=\"closed\">'.JText::_('INTERVAL_LEFT_CLOSED').'</option><option value=\"open\">'.JText::_('INTERVAL_LEFT_OPEN').'</option></select> <input id=\""+group+"_startValueInput\" value=\"\" type=\"text\" onkeydown=\"return checkIntervalSubmit(\'"+group+"\',event);\" /> <strong>;</strong> <input id=\""+group+"_endValueInput\" value=\"\" type=\"text\" onkeydown=\"return checkIntervalSubmit(\'"+group+"\',event);\" /> <select name=\""+group+"_rightBound\" id=\""+group+"_rightBound\" onkeydown=\"return checkIntervalSubmit(\'"+group+"\',event);\"><option value=\"Closed\">'.JText::_('INTERVAL_RIGHT_CLOSED').'</option><option value=\"Open\" selected=\"selected\">'.JText::_('INTERVAL_RIGHT_OPEN').'</option></select><a href=\"javascript:addIntervalSubmit(\'"+group+"\');\" class=\"smallButton\">'.JText::_('ADD_TO_GROUP').'</a><a href=\"javascript:addIntervalCancel(\'"+group+"\');\" class=\"smallButton\">'.JText::_('CANCEL').'</a>");
          }
          
          function deleteGroup(group){
            if (confirm("'.JText::_('REALLY_DELETE_SELECTED_GROUP').'")){
              $(group).remove();
            }
          }
          
          function deleteInterval(id){
            parentDiv=$(id).getParent();
            parentDiv.remove();
          }
          
          function addIntervalCancel(group){ 
            $(group+"_addDiv").setHTML(\'<a href="javascript:addItem(\'+"\'"+group+"\'"+\')" class="smallButton">'.JText::_("ADD_INTERVAL").'</a>\');
          }
          
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
            
            if ((startValue>endValue)||(!((startValue==endValue)&&(leftBound==\'closed\')&&(rightBound==\'closed\')))){
              alert(\''.JText::_('START_VALUE_BIGGER_THAN_END_OR_SAME').'\');
              return;
            }
            
            //pridani polozky...
            intervalHTML=\'\';
            if (leftBound==\'closed\'){intervalHTML+=\''.JText::_('INTERVAL_LEFT_CLOSED').'\';}else{intervalHTML+=\''.JText::_('INTERVAL_LEFT_OPEN').'\';}
            intervalHTML+=startValue+\' ; \'+endValue;
            if (rightBound==\'Closed\'){intervalHTML+=\''.JText::_('INTERVAL_RIGHT_CLOSED').'\';}else{intervalHTML+=\''.JText::_('INTERVAL_RIGHT_OPEN').'\';}
            intervalData=leftBound+rightBound+\'#\'+startValue+\'#\'+endValue;
                          
            intervalsCount++;
            valueDiv=new Element("div",{"id":"interval_"+intervalsCount});
            value=htmlspecialchars(intervalData);
            valueDiv.setHTML(intervalHTML+"<input type=\"hidden\" name=\""+group+"_interval_"+intervalsCount+"\" value=\""+intervalData+"\" /><a href=\"#\" onclick=\"deleteInterval(this);\" title=\"'.JText::_('DELETE').'\">x</a>");
            valueDiv.inject(group+"_itemsDiv");
            
            $(group+"_addDiv").setHTML(\'<a href="javascript:addItem(\'+"\'"+group+"\'"+\')" class="smallButton">'.JText::_("ADD_ITEM").'</a>\');
          }
          
          function addGroup(){
            groupsCount++;
            groupDivId="group_"+groupsCount;
            groupDiv=new Element("div",{"id":groupDivId,"class":"groupDiv"});
            groupDiv.setHTML(\'<a class="deleteGroupA" href="javascript:deleteGroup(\'+"\'"+groupDivId+"\'"+\')">'.JText::_('DELETE_GROUP').'</a><div><label for="\'+groupDivId+\'_name">'.JText::_('GROUP_NAME').'</label> <input type="text" name="\'+groupDivId+\'_name" id="\'+groupDivId+\'_name" value="\'+groupDivId+\'" /></div><div id="\'+groupDivId+\'_itemsDiv" class="itemsDiv"></div><div id="\'+groupDivId+\'_addDiv"><a href="javascript:addItem(\'+"\'"+groupDivId+"\'"+\')" class="smallButton">'.JText::_("ADD_ITEM").'</a></div>\');
            groupDiv.inject("testDiv");
            
            groupName=$(groupDivId+"_name");
            groupName.focus();
            groupName.select();
            //var testDiv=$("testDiv");
            //testDiv.appendText(\'pokus\');
            //testDiv.inject(groupDiv);
          }
          
        </script>';
  
  echo '<form method="post" action="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=editPreprocessingHint_intervalEnumeration').'">
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