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
  }elseif($formatType=='interval'){
    if (count(@$this->format->Range->Interval)>0){
      $datafieldRangeTr= '<tr><td>'.JText::_('DATAFIELD_RANGE').'</td><td>';
      $intervalsArr=array();
      foreach ($this->format->Range->Interval as $interval){
      	$closure=(string)$interval['closure'];
        if (substr($closure,0,4)=='open'){
          $leftBound=JText::_('INTERVAL_LEFT_OPEN');
          $closure=substr($closure,4);
        }else{
          $leftBound=JText::_('INTERVAL_LEFT_CLOSED');
          $closure=substr($closure,6);
        }
        if ($closure=='Closed'){
          $rightBound=JText::_('INTERVAL_RIGHT_CLOSED');
        }else{
          $rightBound=JText::_('INTERVAL_RIGHT_OPEN');
        }
        $intervalsArr[]='<strong>'.$leftBound.((string)$interval['leftMargin']).' ; '.((string)$interval['rightMargin']).$rightBound.'</strong>';
      }
      $datafieldRangeTr.= implode(', ',$intervalsArr);
      $datafieldRangeTr.= '</td></tr>';
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
        </script>';
  
  echo '<form method="post" onsubmit="return submitNominalEnumeration();" action="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=editPreprocessingHint_nominalEnumeration').'">
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
  echo @$datafieldRangeTr;
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