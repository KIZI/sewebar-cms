<?php 
  defined('_JEXEC') or die('Restricted access');
  echo '<div id="iziDiv">';
  
  if (JRequest::getVar('close',@$this->close)!='no'){
    echo '<a href="javascript:parent.reloadBRBase();parent.close();" class="backButton">'.JText::_('CLOSE').'</a>';
  }
  
  echo '<h1>BR Base - saved rules</h1>';

  if (count($this->rulesXml->AssociationRule)>0){
    echo '<table class="rulesTable">';
    foreach($this->rulesXml->AssociationRule as $associationRule){
      echo '<tr>
              <td>'.htmlspecialchars((string)$associationRule->Text).'</td>
              <td>
                <a href="'.JRoute::_('index.php?option=com_dbconnect&controller=data&task=brBaseRemoveRule&tmpl=component&kbi='.$this->kbiId.'&rule='.((string)$associationRule['id'])).'" onclick="return confirm(\''.JText::_('REALLY').'\');" class="remove" title="'.JText::_('REMOVE').'"></a>
              </td>
            </tr>';
    }
    echo '</table>';
  }else{
    echo '<div style="font-style: italic;margin:20px;">'.JText::_('NO_RULES_FOUND').'</a></div>';
  }

  echo '<br />';
  echo '<div style="margin:20px;">
          <a href="'.JRoute::_('index.php?option=com_dbconnect&controller=data&task=brBaseRemoveAllRules&tmpl=component&kbi='.$this->kbiId).'" class="remove"></a>
          <a href="'.JRoute::_('index.php?option=com_dbconnect&controller=data&task=brBaseRemoveAllRules&tmpl=component&kbi='.$this->kbiId).'" class="blockA">'.JText::_('REMOVE_ALL_RULES').'</a>

          <a class="checkModel" href="'.JRoute::_('index.php?option=com_dbconnect&controller=data&task=modelTester&tmpl=component&kbi='.$this->kbiId.'&lmtask=BRBASE').'">'.JText::_('CHECK_MODEL').'</a>
        </div>';
  echo '</div>';
  echo '</div>';  
?>