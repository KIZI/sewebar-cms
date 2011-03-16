<?php
/**
 * HTML View class for the gInclude Component
 *  
 * @package    gInclude
 * @license    GNU/GPL
 * @author Stanislav Vojíř - xvojs03
 * @copyright Stanislav Vojíř, 2009
 *   
 */
 
// no direct access
 
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.view');
           
class BkefViewFormat extends JView
{
  function display($tpl = null)
  {               
      $doc = & JFactory::getDocument();
      if (JPATH_BASE!=JPATH_ADMINISTRATOR){
        $doc->addStyleSheet('components/com_bkef/css/general.css');
        $doc->addStyleSheet('components/com_bkef/css/component.css');
      }         
      JHTML::_('behavior.modal');
      $doc->addStyleSheet('components/com_bkef/css/main.css');
      
      $xml=$this->xml;
      $maId=intval($this->maId);
      $article=intval($this->article);
      $fId=intval($this->fId);
      
      $format=$xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId];
      
      /*PATH*/
      echo '<div class="navigationDiv">';
      echo '<a href="index.php?option=com_bkef&amp;task=selArticle&amp;article='.$article.'">'.$xml->Header[0]->Title[0].' ('.$this->articleTitle.')</a>';
      echo '&nbsp;-&gt;&nbsp;'.JText::_('METAATTRIBUTE').': <a href="index.php?option=com_bkef&amp;task=metaAttribute&amp;article='.$article.'&amp;maId='.$maId.'">'.$xml->MetaAttributes[0]->MetaAttribute[$maId]['name'].'</a>';
      echo '&nbsp;-&gt;&nbsp;'.JText::_('FORMAT').': <strong>'.$format['name'].'</strong>';
      echo '</div>';
      /**/
      
      echo '<h1>'.JText::_('FORMAT_EDITATION').': '.$format['name'].'(MetaAttribute: '.$xml->MetaAttributes[0]->MetaAttribute[$maId]['name'].')</h1>';
      echo '<div class="level1Div">';
      echo '<a name="basicInfo"></a>';
      echo '<h2>'.JText::_('BASIC_INFO').'</h2>';
      echo '<div>'.JText::_('AUTHOR').': <strong>'.$format->Author.'</strong></div>';
      echo '<div class="linksDiv"><a href="index.php?option=com_bkef&amp;task=editFormat&amp;maId='.$maId.'&amp;fId='.$fId.'&amp;article='.$this->article.'&amp;tmpl=component" rel="{handler: \'iframe\', size: {x: 500, y: 200}}" class="modal">'.JText::_('EDIT_BASIC_INFO').'</a></div>';
      echo '</div>';
      echo '<div class="level1Div">';
      echo '<a name="annotations"></a>';
      echo '<h2>'.JText::_('ANNOTATION').'</h2>';
      echo '<div class="linksDiv"><a href="index.php?option=com_bkef&amp;task=addAnnotation&amp;maId='.$maId.'&amp;fId='.$fId.'&amp;article='.$this->article.'&amp;tmpl=component" rel="{handler: \'iframe\', size: {x: 500, y: 280}}" class="modal">'.JText::_('ADD_ANNOTATION').'...</a></div>';
      if (count(@$format->Annotations[0]->Annotation)>0){
        $anId=0;
        foreach (@$format->Annotations[0]->Annotation as $annotation) {
        	echo '<div class="level2Div">';
        	echo '<strong>'.$annotation->Author[0].'</strong>:&nbsp;'.$annotation->Text[0];
        	echo '<div class="linksDiv">';
        	echo '<a href="index.php?option=com_bkef&amp;task=editAnnotation&amp;maId='.$maId.'&amp;fId='.$fId.'&amp;anId='.$anId.'&amp;article='.$this->article.'&amp;tmpl=component" rel="{handler: \'iframe\', size: {x: 500, y: 280}}" class="modal">'.JText::_('EDIT_ANNOTATION').'</a>';
        	echo '&nbsp;|&nbsp;<a href="index.php?option=com_bkef&amp;task=delAnnotation&amp;maId='.$maId.'&amp;fId='.$fId.'&amp;anId='.$anId.'&amp;article='.$this->article.'&amp;tmpl=component" rel="{handler: \'iframe\', size: {x: 500, y: 200}}" class="modal">'.JText::_('DELETE_ANNOTATION').'</a>';
          echo '</div>';
        	echo '</div>';
          $anId++;
        }
      }
      echo '</div>';
      
      echo '<div class="level1Div">';
      echo '<a name="basicSettings"></a>';
      echo '<h2>'.JText::_('FORMAT_BASIC_SETTINGS').'</h2>';
      echo '<div class="level2Div">';
      echo '<table>';
      $dataTypeArr=array('Integer'=>'Integer Number','Float'=>'Real Number','String'=>'String');
      $dataType=(string)$format->DataType;
      echo '<tr><td>'.JText::_('DATA_TYPE').'&nbsp;&nbsp;</td><td>'.$dataTypeArr[$dataType].'</td></tr>';
      echo '<tr><td>'.JText::_('ValueType').'&nbsp;&nbsp;</td><td>'.$format->ValueType.'</td></tr>';
      echo '</table>';
      echo '<div class="linksDiv"><a href="index.php?option=com_bkef&amp;task=editFormat&amp;maId='.$maId.'&amp;fId='.$fId.'&amp;article='.$this->article.'&amp;tmpl=component" rel="{handler: \'iframe\', size: {x: 500, y: 400}}" class="modal">'.JText::_('EDIT').'...</a></div>';
      echo '</div>';
      
      echo '<div class="level2Div">';
      echo '<h3>'.JText::_('ALLOWED_RANGE').'</h3>';
      $allowedRangeExists=false;
      if (count($format->AllowedRange->Interval)>0){
        //rozsah je zadan intervalem
        foreach ($format->AllowedRange->Interval as $interval) {
        	echo JText::_('INTERVAL').': <strong>';
        	if ((string)$interval->LeftBound['type']=='closed'){echo '<';}else{echo '(';}
        	echo $interval->LeftBound['value'].' ; '.$interval->RightBound['value'];
        	if ((string)$interval->RightBound['type']=='closed'){echo '>';}else{echo ')';}
          echo '</strong><br />';
        }
        $allowedRangeExists=true;
      }
      $allowedRangeEnumerationExists=false;
      if (count($format->AllowedRange->Enumeration)>0){
        foreach ($format->AllowedRange->Enumeration as $enumeration) {
        	echo JText::_('VALUES_ENUMERATION').': ';
        	if (count($enumeration->Value)>0)
        	  foreach ($enumeration->Value as $key=>$value) {
           	  echo '<strong>'.$value.'</strong>; ';
            }
          echo '<br />';
        }
        $allowedRangeExists=true;
        $allowedRangeEnumerationExists=true;
      }      
      if (!$allowedRangeExists){
        echo '<div class="missing infotext">'.JText::_('ALLOWED_RANGE_NOT_SET_INFO').'</div>';
      }else {
        echo '<div class="infotext">';
        echo JText::_('ALLOWED_RANGE_EDIT_INFO');
        if ($allowedRangeEnumerationExists){
          echo '<br />'.JText::_('ALLOWED_RANGE_ENUMERATION_INFO');
        }
        echo '</div>';
      }    
      echo '<div class="linksDiv"><a href="index.php?option=com_bkef&amp;task=editRange&amp;type=enumeration&amp;maId='.$maId.'&amp;fId='.$fId.'&amp;article='.$this->article.'&amp;tmpl=component" rel="{handler: \'iframe\', size: {x: 500, y: 400}}" class="modal">'.JText::_('SET_ENUMERATION').'</a>';
      echo '&nbsp;|&nbsp;<a href="index.php?option=com_bkef&amp;task=editRange&amp;type=interval&amp;maId='.$maId.'&amp;fId='.$fId.'&amp;article='.$this->article.'&amp;tmpl=component" rel="{handler: \'iframe\', size: {x: 500, y: 400}}" class="modal">'.JText::_('SET_INTERVAL').'</a>';
      echo '</div></div>';
      
      echo '<div class="level2Div">';    
      echo '<h3>'.JText::_('COLLATION').'</h3>';
      if ((string)$format->Collation['type']=='Enumeration'){
        //řazení řešené výčtem
        echo '<div>'.JText::_('VALUES_COLLATION').': ';
        if (count($format->Collation->Value)>0)
        	  foreach ($format->Collation->Value as $key=>$value) {
           	  echo '<strong>'.$value.'</strong>; ';
            }
        echo '</div>';    
      }elseif ((string)$format->Collation['type']!='') {
        //řazení řešené automaticky
        echo '<div>'.JText::_('TYPE').': <strong>'.$format->Collation['type'].'</strong>, sense: <strong>'.$format->Collation['sense'].'</strong></div>';
      }else {
        echo '<div class="missing infotext">'.JText::_('SET_COLLATION_INFO').'</div>';
      }
      
      echo '<div class="linksDiv"><a href="index.php?option=com_bkef&amp;task=editCollation&amp;maId='.$maId.'&amp;fId='.$fId.'&amp;article='.$this->article.'&amp;tmpl=component" rel="{handler: \'iframe\', size: {x: 300, y: 200}}" class="modal">'.JText::_('SET_COLLATION').'</a>';
      if ($format->Collation['type']=='Enumeration'){
              echo '&nbsp;|&nbsp;<a href="index.php?option=com_bkef&amp;task=editCollationEnumeration&amp;maId='.$maId.'&amp;fId='.$fId.'&amp;article='.$this->article.'&amp;tmpl=component" rel="{handler: \'iframe\', size: {x: 300, y: 300}}" class="modal">'.JText::_('EDIT_VALUES_COLLATION').'</a>';
      }
      echo '</div></div></div>';

 echo '<div class="level1Div">';
 echo '<a name="preprocessingHints"></a>';
 echo '<h2>'.JText::_('PREPROCESSING_HINTS').'</h2>';     

      echo '<div class="linksDiv"><a href="index.php?option=com_bkef&amp;task=addPreprocessingHint&amp;maId='.$maId.'&amp;fId='.$fId.'&amp;article='.$this->article.'&amp;tmpl=component" rel="{handler: \'iframe\', size: {x: 300, y: 200}}" class="modal">'.JText::_('ADD_PREPROCESSING_HINT').'</a></div>';
      $phId=0;
      if (count($format->PreprocessingHints[0]->PreprocessingHint)>0){
        foreach ($format->PreprocessingHints[0]->PreprocessingHint as $preprocessingHint) {
         //zobrazeni jednoho PreprocessingHint
          echo '<div class="level2Div">'; 
        	echo '<h3>'.$preprocessingHint['name'].'</h3>';
          echo '<div class="linksDiv"><a href="index.php?option=com_bkef&amp;task=delPreprocessingHint&amp;maId='.$maId.'&amp;fId='.$fId.'&amp;phId='.$phId.'&amp;article='.$this->article.'&amp;tmpl=component" rel="{handler: \'iframe\', size: {x: 300, y: 200}}" class="modal">'.JText::_('DELETE_PREPROCESSING_HINT').'</a>';
          echo '&nbsp;&nbsp;|&nbsp;&nbsp;<a href="index.php?option=com_bkef&amp;task=renamePreprocessingHint&amp;maId='.$maId.'&amp;fId='.$fId.'&amp;phId='.$phId.'&amp;article='.$this->article.'&amp;tmpl=component" rel="{handler: \'iframe\', size: {x: 300, y: 200}}" class="modal">'.JText::_('RENAME_PREPROCESSING_HINT').'</a></div>';
        	foreach ($preprocessingHint->DiscretizationHint as $discretizationHint) {
         	 //zobrazeni jednoho DiscretizationHint
            if (isset($discretizationHint->ExhaustiveEnumeration[0])){
              echo '<h4>'.JText::_('TYPE').': '.JText::_('EXHAUSTIVE_ENUMERATION').'</h4>';
              echo '<div class="linksDiv"><a href="index.php?option=com_bkef&amp;task=exhaustiveEnumerationAddBin&amp;maId='.$maId.'&amp;fId='.$fId.'&amp;phId='.$phId.'&amp;article='.$this->article.'&amp;tmpl=component" rel="{handler: \'iframe\', size: {x: 300, y: 200}}" class="modal">'.JText::_('ADD_BIN').'</a></div>';
              $bId=0;
              foreach ($discretizationHint->ExhaustiveEnumeration[0]->Bin as $bin) {
                echo '<div class="level3Div">';
                echo '<h5>'.JText::_('BIN_H5').': '.$bin['name'].'</h5>';
                echo '<div style="font-style:italic;">'.$bin->Annotation[0]->Text[0];
                if (@$bin->Annotation[0]->Author[0]){
                  echo ' ('.$bin->Annotation[0]->Author[0].')';
                }
                echo '</div>';
                echo '<div class="linksDiv">';
                echo '<a href="index.php?option=com_bkef&amp;task=exhaustiveEnumerationEditBin&amp;maId='.$maId.'&amp;fId='.$fId.'&amp;phId='.$phId.'&amp;article='.$this->article.'&amp;tmpl=component&amp;bId='.$bId.'" rel="{handler: \'iframe\', size: {x: 300, y: 200}}" class="modal">'.JText::_('EDIT_BIN').'</a>';
                echo '&nbsp;&nbsp;|&nbsp;&nbsp;<a href="index.php?option=com_bkef&amp;task=delExhaustiveEnumerationBin&amp;maId='.$maId.'&amp;fId='.$fId.'&amp;phId='.$phId.'&amp;article='.$this->article.'&amp;tmpl=component&amp;bId='.$bId.'" rel="{handler: \'iframe\', size: {x: 300, y: 200}}" class="modal">'.JText::_('DELETE_BIN').'</a>'; 
                echo '&nbsp;&nbsp;|&nbsp;&nbsp;<a href="index.php?option=com_bkef&amp;task=addExhaustiveEnumerationBinValue&amp;maId='.$maId.'&amp;fId='.$fId.'&amp;phId='.$phId.'&amp;article='.$this->article.'&amp;tmpl=component&amp;bId='.$bId.'" rel="{handler: \'iframe\', size: {x: 300, y: 200}}" class="modal">'.JText::_('ADD_VALUE').'</a>';
                echo '</div>';
                $vId=0;
                $vCount=0;
                foreach ($bin->children() as $child) {
                	if ($child->getName()=='Value'){
                    echo JText::_('VALUE').': <strong>'.$child.'</strong> &nbsp;&nbsp;';
                    echo '<a href="index.php?option=com_bkef&amp;task=delExhaustiveEnumerationBinValue&amp;maId='.$maId.'&amp;fId='.$fId.'&amp;phId='.$phId.'&amp;article='.$this->article.'&amp;tmpl=component&amp;vId='.$vId.'&amp;bId='.$bId.'" >'.JText::_('DELETE').'</a>';
                    echo '<br />';
                    $vCount++;
                  }elseif ($child->getName()=='Interval'){
                    echo JText::_('INTERVAL').': <strong>';
                  	if ((string)$child->LeftBound['type']=='closed'){echo '<';}else{echo '(';}
                  	echo $child->LeftBound['value'].' ; '.$child->RightBound['value'];
                  	if ((string)$child->RightBound['type']=='closed'){echo '>';}else{echo ')';}
                    echo '</strong>';
                    echo ' &nbsp;&nbsp;'.JText::_('DELETE').'<br />';
                    $vCount++;
                  }
                	$vId++;
                }	
                if ($vCount==0){
                  echo '<div class="missing infotext">'.JText::_('EXHAUSTIVE_ENUMERATION_NO_VALUES_INFO').'</div>';
                }
                $bId++;
                echo '</div>';
              }
            }elseif (isset($discretizationHint->IntervalEnumeration[0])){
              echo '<h4>'.JText::_('TYPE').': '.JText::_('INTERVAL_ENUMERATION').'</h4>';
              echo '<div class="linksDiv"><a href="index.php?option=com_bkef&amp;task=intervalEnumerationAddBin&amp;maId='.$maId.'&amp;fId='.$fId.'&amp;phId='.$phId.'&amp;article='.$this->article.'&amp;tmpl=component" rel="{handler: \'iframe\', size: {x: 300, y: 200}}" class="modal">'.JText::_('ADD_INTERVAL_BIN').'</a></div>';
              /**/
              $bId=0;
              foreach ($discretizationHint->IntervalEnumeration[0]->IntervalBin as $bin) {
                echo '<div class="level3Div">';
                echo '<h5>'.JText::_('INTEVAL_BIN').': '.$bin['name'].'</h5>';
                echo '<div style="font-style:italic;">'.$bin->Annotation[0]->Text[0];
                if (@$bin->Annotation[0]->Author[0]){
                  echo ' ('.$bin->Annotation[0]->Author[0].')';
                }
                echo '</div>';
                echo '<div class="linksDiv"><a href="index.php?option=com_bkef&amp;task=intervalEnumerationEditBin&amp;maId='.$maId.'&amp;fId='.$fId.'&amp;phId='.$phId.'&amp;article='.$this->article.'&amp;tmpl=component&amp;bId='.$bId.'" rel="{handler: \'iframe\', size: {x: 300, y: 200}}" class="modal">'.JText::_('EDIT_INTERVAL_BIN').'</a>';
                echo '&nbsp;&nbsp;|&nbsp;&nbsp;<a href="index.php?option=com_bkef&amp;task=delIntervalEnumerationBin&amp;maId='.$maId.'&amp;fId='.$fId.'&amp;phId='.$phId.'&amp;article='.$this->article.'&amp;tmpl=component&amp;bId='.$bId.'" rel="{handler: \'iframe\', size: {x: 300, y: 200}}" class="modal">'.JText::_('DELETE_INTERVAL_BIN').'</a>'; 
                echo '&nbsp;&nbsp;|&nbsp;&nbsp;<a href="index.php?option=com_bkef&amp;task=addIntervalEnumerationBinValue&amp;maId='.$maId.'&amp;fId='.$fId.'&amp;phId='.$phId.'&amp;article='.$this->article.'&amp;tmpl=component&amp;bId='.$bId.'" rel="{handler: \'iframe\', size: {x: 300, y: 200}}" class="modal">'.JText::_('ADD_INTERVAL').'</a></div>';
                $vId=0;
                $vCount=0;
                foreach ($bin->children() as $child) {
                	if ($child->getName()=='Interval'){
                    echo JText::_('INTERVAL').': <strong>';
                  	if ((string)$child->LeftBound['type']=='closed'){echo '<';}else{echo '(';}
                  	echo $child->LeftBound['value'].' ; '.$child->RightBound['value'];
                  	if ((string)$child->RightBound['type']=='closed'){echo '>';}else{echo ')';}
                    echo '</strong>';
                    echo ' &nbsp;&nbsp;<a href="index.php?option=com_bkef&amp;task=delIntervalEnumerationBinValue&amp;maId='.$maId.'&amp;fId='.$fId.'&amp;phId='.$phId.'&amp;article='.$this->article.'&amp;tmpl=component&amp;vId='.$vId.'&amp;bId='.$bId.'" >'.JText::_('DELETE').'</a><br />';
                    $vCount++;
                  }
                	$vId++;
                }
                if ($vCount==0){
                  echo '<div class="missing infotext">'.JText::_('INTERVAL_ENUMERATION_NO_VALUES_INFO').'</div>';
                }	
                $bId++;
                echo '</div>';
              }
            }elseif (isset($discretizationHint->Equidistant[0])){
              /**/
              echo '<h4>'.JText::_('TYPE').': '.JText::_('EQUIDISTANT').'</h4>';
              echo '<div class="linksDiv"><a href="index.php?option=com_bkef&amp;task=equidistant&amp;maId='.$maId.'&amp;fId='.$fId.'&amp;phId='.$phId.'&amp;article='.$this->article.'&amp;tmpl=component" rel="{handler: \'iframe\', size: {x: 300, y: 200}}" class="modal">'.JText::_('EDIT_EQUIDISTANT').'</a></div>';
              echo '<table><tr><td>';
              $equidistant=$discretizationHint->Equidistant[0];
              if ((count($equidistant->Start)==0)||(count($equidistant->End)==0)||(count($equidistant->Step)==0)){
                echo '<div class="missing infotext">'.JText::_('EQUIDISTANT_SET_INTERVAL_STEP_INFO').'</div>';
              }else {
                echo JText::_('INTERVAL').':&nbsp;</td><td><strong>';
                if ((string)$equidistant->Start['type']=='closed'){echo '<';}else{echo '(';}
                echo $equidistant->Start.' ; '.$equidistant->End;
                if ((string)$equidistant->End['type']=='closed'){echo '>';}else{echo ')';}
                echo '</strong></td></tr><tr><td>'.JText::_('STEP').':</td><td><strong>';
                echo (string)$equidistant->Step[0];
                echo '</strong>';
              }
              echo '</td></tr></table>';
            }elseif (isset($discretizationHint->EquifrequentInterval[0])){
              echo '<h4>'.JText::_('TYPE').': '.JText::_('EQUIFREQUENT_INTERVAL').'</h4>';
              echo '<div class="linksDiv">';
              //echo '<a href="index.php?option=com_bkef&amp;task=delPreprocessingHint&amp;maId='.$maId.'&amp;fId='.$fId.'&amp;phId='.$phId.'&amp;article='.$this->article.'&amp;tmpl=component" rel="{handler: \'iframe\', size: {x: 300, y: 200}}" class="modal">'.JText::_('DELETE_PREPROCESSING_HINT').'</a>'; 
              //echo '&nbsp;&nbsp;|&nbsp;&nbsp;';
              echo '<a href="index.php?option=com_bkef&amp;task=editEquifrequentInterval&amp;maId='.$maId.'&amp;fId='.$fId.'&amp;phId='.$phId.'&amp;article='.$this->article.'&amp;tmpl=component" rel="{handler: \'iframe\', size: {x: 300, y: 200}}" class="modal">'.JText::_('EQUIFREQUENT_EDIT_VALUE').'</a>';
              echo '</div>';
              /*echo '<div style="font-style:italic;">'.$discretizationHint->EquifrequentInterval[0]->Annotation[0]->Text[0];
              if (@$discretizationHint->EquifrequentInterval[0]->Annotation[0]->Author[0]){
                echo ' ('.$discretizationHint->EquifrequentInterval[0]->Annotation[0]->Author[0].')';
              }
              echo '</div>';*/
              echo JText::_('EQUIFREQUENT_COUNT').': <strong>';
              if (@$discretizationHint->EquifrequentInterval[0]->Count[0]){
                echo $discretizationHint->EquifrequentInterval[0]->Count[0];
              }else {
                echo '<span class="missing infotext">'.JText::_('EQUIFREQUENT_SET_COUNT_INFO').'</span>';
              }
              echo '</strong>';
            }
           // 
           echo '</div>'; 
          }
         //
         $phId++;
        }
      }else {
        echo '<div class="missing infotext">'.JText::_('NO_PREPROCESSING_HINTS_INFO').'</div>';
      }
      
 echo '</div>';
 echo '<div class="level1Div">';
 echo '<a name="valueDescriptions"></a>';   
 echo '<h2>'.JText::_('VALUE_DESCRIPTIONS').'</h2>'; 
 echo '<div class="infotext">'.JText::_('VALUE_DESCRIPTIONS_INFO').'</div>';    

 echo '<div class="linksDiv"><a href="index.php?option=com_bkef&amp;task=addValueDescription&amp;maId='.$maId.'&amp;fId='.$fId.'&amp;article='.$this->article.'&amp;tmpl=component" rel="{handler: \'iframe\', size: {x: 550, y: 300}}" class="modal">'.JText::_('ADD_VALUE_DESCRIPTION').'...</a></div>';

      $vdId=0;
      if (count($format->ValueDescriptions[0]->ValueDescription)>0){
        foreach ($format->ValueDescriptions[0]->ValueDescription as $valueDescription) {
          echo '<div class="level2Div">';
        	echo '<h3>'.$valueDescription['type'].'</h3>';
        	echo '<div class="annotation">'.$valueDescription->Annotation[0]->Text[0];
        	if (@$valueDescription->Annotation[0]->Author[0]){
            echo '('.$valueDescription->Annotation[0]->Author[0].')';
          }
          echo '</div>';
          echo '<br /><div class="infotext">';
          echo JText::_('VALUE_DESCRIPTIONS_GROUP_'.strtoupper(str_replace(' ','',$valueDescription['type'])));
          echo '</div>';
          echo '<div class="linksDiv"><a href="index.php?option=com_bkef&amp;task=editValueDescription&amp;maId='.$maId.'&amp;fId='.$fId.'&amp;vdId='.$vdId.'&amp;article='.$this->article.'&amp;tmpl=component" rel="{handler: \'iframe\', size: {x: 300, y: 300}}" class="modal">'.JText::_('EDIT_ANNOTATION').'...</a>';
          echo '&nbsp;&nbsp;|&nbsp;&nbsp;<a href="index.php?option=com_bkef&amp;task=delValueDescription&amp;maId='.$maId.'&amp;fId='.$fId.'&amp;vdId='.$vdId.'&amp;article='.$this->article.'&amp;tmpl=component" rel="{handler: \'iframe\', size: {x: 300, y: 300}}" class="modal">'.JText::_('DELETE_VALUE_DESCRIPTION').'...</a>';
          echo '&nbsp;&nbsp;|&nbsp;&nbsp;<a href="index.php?option=com_bkef&amp;task=addValueDescriptionValue&amp;maId='.$maId.'&amp;fId='.$fId.'&amp;vdId='.$vdId.'&amp;article='.$this->article.'&amp;type=value&amp;tmpl=component" rel="{handler: \'iframe\', size: {x: 300, y: 300}}" class="modal">'.JText::_('ADD_VALUE').'...</a>';
          echo '&nbsp;&nbsp;|&nbsp;&nbsp;<a href="index.php?option=com_bkef&amp;task=addValueDescriptionValue&amp;maId='.$maId.'&amp;fId='.$fId.'&amp;vdId='.$vdId.'&amp;article='.$this->article.'&amp;type=interval&amp;tmpl=component" rel="{handler: \'iframe\', size: {x: 300, y: 300}}" class="modal">'.JText::_('ADD_INTERVAL').'...</a></div><br />';
          
          $vdValueId=0;
          if (count($valueDescription->children())>0){
            $realChildCount=0;
            foreach ($valueDescription->children() as $child) {
            	if ($child->getName()=='Interval'){
            	  $realChildCount++;
                //vypiseme interval
                echo '<div>'.JText::_('INTERVAL').': <strong>';
              	if ((string)$child->LeftBound['type']=='closed'){echo '<';}else{echo '(';}
              	echo $child->LeftBound['value'].' ; '.$child->RightBound['value'];
              	if ((string)$child->RightBound['type']=='closed'){echo '>';}else{echo ')';}
                echo '</strong>';
                echo '&nbsp;&nbsp;&nbsp;<a href="index.php?option=com_bkef&amp;task=delVdValue&amp;maId='.$maId.'&amp;fId='.$fId.'&amp;article='.$this->article.'&amp;vdId='.$vdId.'&amp;vdValueId='.$vdValueId.'">'.JText::_('DELETE').'</a></div>';
              }elseif ($child->getName()=='Value') {
                $realChildCount++;
                //vypiseme hodnotu
                echo '<div>'.JText::_('VALUE').': <strong>'.$child.'</strong>';
                echo '&nbsp;&nbsp;&nbsp;<a href="index.php?option=com_bkef&amp;task=delVdValue&amp;maId='.$maId.'&amp;fId='.$fId.'&amp;article='.$this->article.'&amp;vdId='.$vdId.'&amp;vdValueId='.$vdValueId.'">'.JText::_('DELETE').'</a></div>';
              }
              $vdValueId++;
            }
            if (!($realChildCount>0)){
              echo '<div class="missing infotext">'.JText::_('VALUE_DESCRIPTION_NO_VALUES_INFO').'</div>';
            }
          }else {
            echo '<div class="missing infotext">'.JText::_('VALUE_DESCRIPTION_NO_VALUES_INFO').'</div>';
          }
          
          $vdId++;
          echo '</div>';
        }
      }else {
        echo '<div class="missing infotext">'.JText::_('NO_VALUE_DESCRIPTIONS_INFO').'</div>';
      }
    
    echo '</div>';

  }
}

?>