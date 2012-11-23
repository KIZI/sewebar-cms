<?php 



defined('_JEXEC') or die('Restricted access');
  
  echo '<h1>'.JText::_('PREPROCESSING').'</h1>';
  
  if (count($this->pmmlFieldNamesArr)>0){
    echo '<table class="myAdminTable looser">';
    echo '<tr><th>'.JText::_('FIELD').'</th><th>'.JText::_('METAATTRIBUTE_NAME').'</th><th>'.JText::_('METAATTRIBUTE_FORMAT').'</th><th>'.JText::_('PREPROCESSING_HINT').'</th><th>'.JText::_('ACTIONS').'</th></tr>';
    
    $rowClass=0;
    foreach ($this->pmmlFieldNamesArr as $fieldName=>$pmmlField) {
      $fieldId=$pmmlField['id'];
      $bkefId=$this->fieldMappingsArr[$fieldId]['bkefId'];
      $preprocessings=$this->fieldMappingsArr[$fieldId]['preprocessings'];
      $bkefField=$this->bkefFieldArr[$bkefId];
    	echo '<tr class="row'.$rowClass.'">
              <td>'.$fieldName.'</td>
              <td>'.$bkefField['MetaAttribute'].'</td>
              <td>'.$bkefField['Format'].'</td>
              <td>';
              if (count($preprocessings->Preprocessing)>0){
                foreach ($preprocessings->Preprocessing as $preprocessing) {
                	echo '<div><strong title="'.htmlspecialchars(@$preprocessing->AttributeName).'">'.htmlspecialchars(@$preprocessing->PreprocessingHint).'</strong></div>';
                }
              }else{
                echo JText::_('NOT_SET'));
              }
        echo '</td>
              <td class="actionsTd"><a href="'.JRoute::_('index.php?option=com_dbconnect&task=showPreprocessingHints&taskId='.$this->taskId.'&tmpl=component&maName='.urlencode($bkefField['MetaAttribute']).'&formatName='.urlencode($bkefField['Format']).'&pmmlName='.urlencode($fieldName)).'" class="modal" rel="{handler: \'iframe\', size: {x: 500, y: 300}}">'.JText::_('SELECT').'</a></td>
            </tr>';
      $rowClass++;      
    }   
    echo '</table>';
  }else{
    echo '<div class="error">'.JText::_('NO_PMML_FIELDS_FOUND').'</div>';
  }
  
  
  //TODO dodělat vypsání nepoužitých položek z TASK i BKEF
  
  
  echo '<div class="actionsDiv">
          <a href="'.JRoute::_('index.php?option=com_dbconnect&task=generatePMML&taskId='.$this->taskId).'">'.JText::_('GENERATE_KBI_SOURCE').'</a>
        </div>';
  
  
  
?>