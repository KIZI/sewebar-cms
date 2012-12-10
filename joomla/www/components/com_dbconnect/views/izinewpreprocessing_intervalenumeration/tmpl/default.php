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