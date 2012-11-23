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
      
      /*Ověření, jestli jde o přístup z administrace nebo front-endu*/
      require_once(JApplicationHelper::getPath('toolbar_html'));
      if (JPATH_BASE==JPATH_ADMINISTRATOR){ 
        TOOLBAR_bkef::_DEFAULT();
      }else{
        TOOLBAR_bkef::frontend();
      }
      /**/
      
      $xml=$this->xml;
      $maId=intval($this->maId);
      $article=intval($this->article);
      $fId=intval($this->fId);
      
      $format=$xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId];
      
      /*PATH*/
      echo '<div class="navigationDiv">';
      echo '<a href="index.php?option=com_bkef&amp;task=selArticle&amp;article='.$article.'">'.$xml->Header[0]->Title[0].' ('.$this->articleTitle.')</a>';
      echo '&nbsp;-&gt;&nbsp;'.JText::_('METAATTRIBUTE').': <a href="index.php?option=com_bkef&amp;task=metaAttribute&amp;article='.$article.'&amp;maId='.$maId.'">'.$xml->MetaAttributes[0]->MetaAttribute[$maId]->Name[0].'</a>';
      echo '&nbsp;-&gt;&nbsp;'.JText::_('FORMAT').': <strong>'.$format->Name[0].'</strong>';
      echo '</div>';
      /**/
      
      echo '<h1>'.JText::_('FORMAT_EDITATION').': '.$format->Name[0].' (MetaAttribute: '.$xml->MetaAttributes[0]->MetaAttribute[$maId]->Name[0].')</h1>';
      echo '<div class="level1Div">';
      echo '<a name="basicInfo"></a>';
      echo '<h2>'.JText::_('BASIC_INFO').'</h2>';
      echo '<table>
              <tr>
                <td>'.JText::_('CREATED').':</td>
                <td><strong>'.date(JText::_('DATETIMEFORMAT'),strtotime($format->Created[0]->Timestamp)).' ('.$format->Created[0]->Author.')</strong></td>
              </tr>
              <tr>  
                <td>'.JText::_('LAST_MODIFIED').':</td>
                <td><strong>'.date(JText::_('DATETIMEFORMAT'),strtotime($format->LastModified[0]->Timestamp)).' ('.$format->LastModified[0]->Author.')</strong></td>
              </tr>
            </table>';
      
      if (count(@$format->Annotations[0]->Annotation)>0){
        echo '<h3>'.JText::_('ANNOTATIONS').'</h3>';
        $anId=0;
        foreach ($format->Annotations[0]->Annotation as $annotation) {
        	echo '<div class="annotation level2Div">';
        	echo '<strong>'.($annotation->Text[0]!=''?$annotation->Text[0]:'&lt;&lt;???&gt;&gt;').'</strong>';
          echo '<br />'.JText::_('CREATED').': '.$annotation->Created[0]->Author.' ('.date(JText::_('DATETIMEFORMAT'),strtotime($annotation->Created[0]->Timestamp)).')';
          if ((string)$annotation->Created[0]->Timestamp!=(string)$annotation->LastModified[0]->Timestamp){
            echo '; '.JText::_('LAST_MODIFIED').': '.$annotation->LastModified[0]->Author.' ('.date(JText::_('DATETIMEFORMAT'),strtotime($annotation->LastModified[0]->Timestamp)).')';
          }
          echo ' |&nbsp;';
          echo '<a class="modal" href="index.php?option=com_bkef&amp;task=editFormatAnnotation&amp;article='.$this->article.'&amp;tmpl=component&amp;maId='.$maId.'&amp;fId='.$fId.'&amp;anId='.$anId.'" rel="{handler: \'iframe\', size: {x: 500, y: 330}}" >'.JText::_('EDIT_ANNOTATION').'</a> ';
          echo ' |&nbsp;';
          echo '<a class="modal" href="index.php?option=com_bkef&amp;task=delFormatAnnotation&amp;article='.$this->article.'&amp;tmpl=component&amp;maId='.$maId.'&amp;fId='.$fId.'&amp;anId='.$anId.'" rel="{handler: \'iframe\', size: {x: 500, y: 330}}" >'.JText::_('DELETE_ANNOTATION').'</a> ';
          echo '</div>';
          $anId++;
        }
      }      

        echo '<div class="linksDiv">
                <a href="index.php?option=com_bkef&amp;task=editFormat&amp;maId='.$maId.'&amp;fId='.$fId.'&amp;article='.$this->article.'&amp;tmpl=component" rel="{handler: \'iframe\', size: {x: 500, y: 200}}" class="modal">'.JText::_('EDIT_BASIC_INFO').'</a>
                 |&nbsp;
                <a href="index.php?option=com_bkef&amp;task=addFormatAnnotation&amp;maId='.$maId.'&amp;fId='.$fId.'&amp;article='.$this->article.'&amp;tmpl=component" rel="{handler: \'iframe\', size: {x: 500, y: 280}}" class="modal">'.JText::_('ADD_ANNOTATION').'...</a>
              </div>';
              
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
      $allowedRangeEnumerationExists=true;
      if (isset($format->Range[0]['type'])){
        //máme nějak zadaný rozsah
        if (count($format->Range[0]->Interval)>0){
          //jde o intervaly
          foreach ($format->Range[0]->Interval as $interval) {
          	echo JText::_('INTERVAL').': <strong>';
          	if (($interval['closure']=='openClosed')||($interval['closure']=='openOpen')){echo '(';}else{echo '&lt;';}
          	echo $interval['leftMargin'].' ; '.$interval['rightMargin'];
          	if (($interval['closure']=='closedOpen')||($interval['closure']=='openOpen')){echo ')';}else{echo '&gt;';}
          	echo '</strong><br />';
          }
        }elseif(isset($format->Range[0]->Regex[0])){
          echo JText::_('REGEX').':<strong>'.$format->Range[0]->Regex[0].'</strong>';
        }elseif(count($format->Range[0]->Value)>0){
          $valuesArr=array();
          foreach ($format->Range[0]->Value as $value){
            $valuesArr[]=$value;
          }
          echo JText::_('VALUES').': <strong>{'.implode('; ',$valuesArr).'}</strong>';
        }else{
          $allowedRangeEnumerationExists=false;
        }
      }else{
        $allowedRangeEnumerationExists=false;
      }
      if (!$allowedRangeEnumerationExists){
        echo '<div class="missing infotext">'.JText::_('ALLOWED_RANGE_NOT_SET_INFO').'</div>';
      }else{
        echo '<h3>'.JText::_('COLLATION').'</h3>';
        echo '<table>
                <tr>
                  <td>'.JText::_('COLLATION_TYPE').'</td>
                  <td><strong>'.$format->Collation[0]['type'].'</strong></td>
                </tr>
                <tr>
                  <td>'.JText::_('COLLATION_SENSE').'</td>
                  <td><strong>'.$format->Collation[0]['sense'].'</strong></td>
                </tr>
              </table>';
        echo '<br /><div class="infotext">'.JText::_('ALLOWED_RANGE_EDIT_INFO').'</div>';      
      }
         
      echo '<div class="linksDiv"><a href="index.php?option=com_bkef&amp;task=editRange&amp;type=enumeration&amp;maId='.$maId.'&amp;fId='.$fId.'&amp;article='.$this->article.'&amp;tmpl=component" rel="{handler: \'iframe\', size: {x: 500, y: 400}}" class="modal">'.JText::_('SET_ENUMERATION').'</a>';
      echo '&nbsp;|&nbsp;<a href="index.php?option=com_bkef&amp;task=editRange&amp;type=interval&amp;maId='.$maId.'&amp;fId='.$fId.'&amp;article='.$this->article.'&amp;tmpl=component" rel="{handler: \'iframe\', size: {x: 500, y: 400}}" class="modal">'.JText::_('SET_INTERVAL').'</a>';
      echo '&nbsp;|&nbsp;<a href="index.php?option=com_bkef&amp;task=editRange&amp;type=regex&amp;maId='.$maId.'&amp;fId='.$fId.'&amp;article='.$this->article.'&amp;tmpl=component" rel="{handler: \'iframe\', size: {x: 500, y: 400}}" class="modal">'.JText::_('SET_REGEX').'</a>';
      echo '</div></div>';
      
      
      echo '</div>';

 echo '<div class="level1Div">';
 echo '<a name="preprocessingHints"></a>';
 echo '<h2>'.JText::_('PREPROCESSING_HINTS').'</h2>';     

      echo '<div class="linksDiv"><a href="index.php?option=com_bkef&amp;task=addPreprocessingHint&amp;maId='.$maId.'&amp;fId='.$fId.'&amp;article='.$this->article.'&amp;tmpl=component" rel="{handler: \'iframe\', size: {x: 300, y: 200}}" class="modal">'.JText::_('ADD_PREPROCESSING_HINT').'</a></div>';
      $phId=0;
      if (count($format->PreprocessingHints[0]->DiscretizationHint)>0){
        foreach ($format->PreprocessingHints[0]->DiscretizationHint as $discretizationHint) {
         //zobrazeni jednoho PreprocessingHint
          echo '<div class="level2Div">'; 
        	echo '<h3>'.(string)$discretizationHint->Name.'</h3>';
          echo '<div class="infoDiv">
                  <table>
                    <tr>
                      <td>'.JText::_('CREATED').':</td>
                      <td><strong>'.date(JText::_('DATETIMEFORMAT'),strtotime($discretizationHint->Created[0]->Timestamp)).' ('.$format->Created[0]->Author.')</strong></td>
                    </tr>
                    <tr>  
                      <td>'.JText::_('LAST_MODIFIED').':</td>
                      <td><strong>'.date(JText::_('DATETIMEFORMAT'),strtotime($discretizationHint->LastModified[0]->Timestamp)).' ('.$format->LastModified[0]->Author.')</strong></td>
                    </tr>
                  </table>
                </div>';
          //pokud jsou anotace, tak je zobrazime
          if (count(@$discretizationHint->Annotations[0]->Annotation)>0){
            echo '<h4>'.JText::_('ANNOTATIONS').'</h4>';
            $dhAnId=0;
            foreach ($discretizationHint->Annotations[0]->Annotation as $annotation) {
            	echo '<div class="annotation level3Div">';
            	echo '<strong>'.($annotation->Text[0]!=''?$annotation->Text[0]:'&lt;&lt;???&gt;&gt;').'</strong>';
              echo '<br />'.JText::_('CREATED').': '.$annotation->Created[0]->Author.' ('.date(JText::_('DATETIMEFORMAT'),strtotime($annotation->Created[0]->Timestamp)).')';
              if ((string)$annotation->Created[0]->Timestamp!=(string)$annotation->LastModified[0]->Timestamp){
                echo '; '.JText::_('LAST_MODIFIED').': '.$annotation->LastModified[0]->Author.' ('.date(JText::_('DATETIMEFORMAT'),strtotime($annotation->LastModified[0]->Timestamp)).')';
              }
              echo ' |&nbsp;';
              echo '<a class="modal" href="index.php?option=com_bkef&amp;task=editPreprocessingHintAnnotation&amp;article='.$this->article.'&amp;tmpl=component&amp;maId='.$maId.'&amp;fId='.$fId.'&amp;phId='.$phId.'&amp;anId='.$dhAnId.'" rel="{handler: \'iframe\', size: {x: 500, y: 330}}" >'.JText::_('EDIT_ANNOTATION').'</a> ';
              echo ' |&nbsp;';
              echo '<a class="modal" href="index.php?option=com_bkef&amp;task=delPreprocessingHintAnnotation&amp;article='.$this->article.'&amp;tmpl=component&amp;maId='.$maId.'&amp;fId='.$fId.'&amp;phId='.$phId.'&amp;anId='.$dhAnId.'" rel="{handler: \'iframe\', size: {x: 500, y: 330}}" >'.JText::_('DELETE_ANNOTATION').'</a> ';
              echo '</div>';
              $dhAnId++;
            }
          }   
          //odkazy pro nastavení PH   
          echo '<div class="linksDiv">
                  <a href="index.php?option=com_bkef&amp;task=addPreprocessingHintAnnotation&amp;maId='.$maId.'&amp;fId='.$fId.'&amp;phId='.$phId.'&amp;article='.$this->article.'&amp;tmpl=component" rel="{handler: \'iframe\', size: {x: 500, y: 330}}" class="modal">'.JText::_('ADD_ANNOTATION').'</a>
                  &nbsp;&nbsp;|&nbsp;&nbsp;<a href="index.php?option=com_bkef&amp;task=renamePreprocessingHint&amp;maId='.$maId.'&amp;fId='.$fId.'&amp;phId='.$phId.'&amp;article='.$this->article.'&amp;tmpl=component" rel="{handler: \'iframe\', size: {x: 300, y: 200}}" class="modal">'.JText::_('RENAME_PREPROCESSING_HINT').'</a>
                  &nbsp;&nbsp;|&nbsp;&nbsp;<a href="index.php?option=com_bkef&amp;task=delPreprocessingHint&amp;maId='.$maId.'&amp;fId='.$fId.'&amp;phId='.$phId.'&amp;article='.$this->article.'&amp;tmpl=component" rel="{handler: \'iframe\', size: {x: 300, y: 200}}" class="modal">'.JText::_('DELETE_PREPROCESSING_HINT').'</a>                  
                </div>';
          //vypsání konkrétních dat PH
          if(isset($discretizationHint->EachValueOneBin)){
            echo '<div class="level3Div">
                    <h4>'.JText::_('TYPE').': '.JText::_('EACH_VALUE_ONE_BIN').'</h4>
                  </div>';
          }elseif(isset($discretizationHint->NominalEnumeration)){
            echo '<div class="level3Div">
                    <h4>'.JText::_('TYPE').': '.JText::_('NOMINAL_ENUMERATION').'</h4>';
                    $binId=0;
                    if (count($discretizationHint->NominalEnumeration[0]->NominalBin)>0){
                      foreach ($discretizationHint->NominalEnumeration[0]->NominalBin as $nominalBin){
                      	echo '<div class="level4Div">'; 
                        echo '<h4>'.(string)@$nominalBin->Name.'</h4>';
                        if (count($nominalBin->Value)>0){
                          $valId=0;
                          foreach ($nominalBin->Value as $binValue) {
                          	echo '<div>'.(string)$binValue.' &nbsp;&nbsp;-&nbsp;<a href="index.php?option=com_bkef&amp;task=nominalEnumerationDeleteValue&amp;maId='.$maId.'&amp;fId='.$fId.'&amp;phId='.$phId.'&amp;binId='.$binId.'&amp;valId='.$valId.'&amp;article='.$this->article.'&amp;tmpl=component" rel="{handler: \'iframe\',size:{x:300,y:200}}">'.JText::_('DELETE').'</a></div>';
                            $valId++;
                          }
                        }else{
                          echo '<div class="missing infotext">'.JText::_('NOMINAL_ENUMERATION_BIN_NO_VALUES_INFO').'</div>';
                        }  
                        echo '<div class="linksDiv">
                                <a href="index.php?option=com_bkef&amp;task=nominalEnumerationAddValue&amp;maId='.$maId.'&amp;fId='.$fId.'&amp;phId='.$phId.'&amp;binId='.$binId.'&amp;article='.$this->article.'&amp;tmpl=component" rel="{handler: \'iframe\',size:{x:300,y:200}}" class="modal">'.JText::_('ADD_VALUE').'</a>
                                &nbsp;&nbsp;|&nbsp;&nbsp;
                                <a href="index.php?option=com_bkef&amp;task=nominalEnumerationEditBin&amp;maId='.$maId.'&amp;fId='.$fId.'&amp;phId='.$phId.'&amp;binId='.$binId.'&amp;article='.$this->article.'&amp;tmpl=component" rel="{handler: \'iframe\',size:{x:300,y:200}}" class="modal">'.JText::_('EDIT_BIN').'</a>
                                &nbsp;&nbsp;|&nbsp;&nbsp;
                                <a href="index.php?option=com_bkef&amp;task=nominalEnumerationDeleteBin&amp;maId='.$maId.'&amp;fId='.$fId.'&amp;phId='.$phId.'&amp;binId='.$binId.'&amp;article='.$this->article.'&amp;tmpl=component" rel="{handler: \'iframe\',size:{x:300,y:200}}" class="modal">'.JText::_('DELETE_BIN').'</a>
                              </div>';      
                        echo '</div>';
                        $binId++;
                      }
                    }else{
                      echo '<div class="missing infotext">'.JText::_('NOMINAL_ENUMERATION_NO_BINS_INFO').'</div>';
                    }
                    echo '<div class="linksDiv">
                            <a href="index.php?option=com_bkef&amp;task=nominalEnumerationAddBin&amp;maId='.$maId.'&amp;fId='.$fId.'&amp;phId='.$phId.'&amp;article='.$this->article.'&amp;tmpl=component" rel="{handler: \'iframe\',size:{x:300,y:200}}" class="modal">'.JText::_('ADD_BIN').'</a>
                          </div>';
            echo '</div>';
          }elseif(isset($discretizationHint->IntervalEnumeration)){
            echo '<div class="level3Div">
                    <h4>'.JText::_('TYPE').': '.JText::_('INTERVAL_ENUMERATION').'</h4>';
                    $binId=0;
                    if (count($discretizationHint->IntervalEnumeration[0]->IntervalBin)>0){
                      foreach ($discretizationHint->IntervalEnumeration[0]->IntervalBin as $intervalBin){
                      	echo '<div class="level4Div">'; 
                        echo '<h4>'.(string)@$intervalBin->Name.'</h4>';
                        if (count($intervalBin->Interval)>0){
                          $intId=0;
                          foreach ($intervalBin->Interval as $interval) {     
                            //vypsani jednoho konkretniho intervalu - nejdriv vyresime znazorneni hranic a pak ho vypiseme i s odkazem na odstraneni
                            $closure=(string)$interval['closure'];
                            if ($closure=='closedClosed'){
                              $intervalTextStart='&lt;';
                              $intervalTextEnd='&gt;';
                            }elseif($closure=='closedOpen'){
                              $intervalTextStart='&lt;';
                              $intervalTextEnd=')';
                            }elseif($closure=='openClosed'){
                              $intervalTextStart='(';
                              $intervalTextEnd='&gt;';
                            }else{
                              $intervalTextStart='(';
                              $intervalTextEnd=')';
                            }
                            $intervalText=((string)$interval['leftMargin']).' ; '.((string)$interval['rightMargin']); 
                          	echo '<div>'.$intervalTextStart.$intervalText.$intervalTextEnd.' &nbsp;&nbsp;-&nbsp;<a href="index.php?option=com_bkef&amp;task=intervalEnumerationDeleteInterval&amp;maId='.$maId.'&amp;fId='.$fId.'&amp;phId='.$phId.'&amp;binId='.$binId.'&amp;intId='.$intId.'&amp;article='.$this->article.'">'.JText::_('DELETE').'</a></div>';
                            $intId++;
                          }
                        }else{
                          echo '<div class="missing infotext">'.JText::_('INTERVAL_ENUMERATION_NO_VALUES_INFO').'</div>';
                        }  
                        echo '<div class="linksDiv">
                                <a href="index.php?option=com_bkef&amp;task=intervalEnumerationAddInterval&amp;maId='.$maId.'&amp;fId='.$fId.'&amp;phId='.$phId.'&amp;binId='.$binId.'&amp;article='.$this->article.'&amp;tmpl=component" rel="{handler: \'iframe\',size:{x:300,y:200}}" class="modal">'.JText::_('ADD_INTERVAL').'</a>
                                &nbsp;&nbsp;|&nbsp;&nbsp;
                                <a href="index.php?option=com_bkef&amp;task=intervalEnumerationEditBin&amp;maId='.$maId.'&amp;fId='.$fId.'&amp;phId='.$phId.'&amp;binId='.$binId.'&amp;article='.$this->article.'&amp;tmpl=component" rel="{handler: \'iframe\',size:{x:300,y:200}}" class="modal">'.JText::_('EDIT_BIN').'</a>
                                &nbsp;&nbsp;|&nbsp;&nbsp;
                                <a href="index.php?option=com_bkef&amp;task=intervalEnumerationDeleteBin&amp;maId='.$maId.'&amp;fId='.$fId.'&amp;phId='.$phId.'&amp;binId='.$binId.'&amp;article='.$this->article.'&amp;tmpl=component" rel="{handler: \'iframe\',size:{x:300,y:200}}" class="modal">'.JText::_('DELETE_BIN').'</a>
                              </div>';      
                        echo '</div>';
                        $binId++;
                      }
                    }else{
                      echo '<div class="missing infotext">'.JText::_('INTERVAL_ENUMERATION_NO_BINS_INFO').'</div>';
                    }
                    echo '<div class="linksDiv">
                            <a href="index.php?option=com_bkef&amp;task=intervalEnumerationAddBin&amp;maId='.$maId.'&amp;fId='.$fId.'&amp;phId='.$phId.'&amp;article='.$this->article.'&amp;tmpl=component" rel="{handler: \'iframe\',size:{x:300,y:200}}" class="modal">'.JText::_('ADD_BIN').'</a>
                          </div>';
            echo '</div>';
          }elseif(isset($discretizationHint->EquidistantInterval)){
            $equidistantInterval=$discretizationHint->EquidistantInterval[0];
            echo '<div class="level3Div">
                    '.JText::_('EQUIDISTANT_INTERVAL').'
                    <table>
                      <tr>
                        <td>'.JText::_('START').'</td>
                        <td><strong>'.((string)$equidistantInterval->Start).'</strong></td>
                      </tr>
                      <tr>
                        <td>'.JText::_('END').'</td>
                        <td><strong>'.((string)$equidistantInterval->End).'</strong></td>
                      </tr>
                      <tr>
                        <td>'.JText::_('STEP').'</td>
                        <td><strong>'.((string)$equidistantInterval->Step).'</strong></td>
                      </tr>
                    </table>';
                    if (((string)$equidistantInterval->Start=='')||((string)$equidistantInterval->End=='')||((string)$equidistantInterval->Step=='')){
                      echo '<div class="missing infotext">
                              '.JText::_('EQUIDISTANT_INTERVAL_MISSING_PARAMS_INFO').'
                            </div>';
                    }        
            echo   '<div class="linksDiv">
                      <a href="index.php?option=com_bkef&amp;task=equidistant&amp;maId='.$maId.'&amp;fId='.$fId.'&amp;phId='.$phId.'&amp;article='.$this->article.'&amp;tmpl=component" rel="{handler: \'iframe\',size:{x:300,y:200}}" class="modal">'.JText::_('EQUIDISTANT_INTERVAL_SET_PARAMS').'</a>
                    </div>
                  </div>'; 
          }else{
            echo '<div class="level3Div error">
                    '.JText::_('PLEASE_DELETE_THIS_HINT').'
                  </div>';
          }      
         //
         echo '</div>';
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
        	echo '<h3>';
          if (count($valueDescription->Features->Feature)>0){
            $featuresArr=array();
            foreach ($valueDescription->Features->Feature as $feature) {
            	$featuresArr[]=JText::_((string)$feature);
            }
          }
          echo implode(', ',$featuresArr);
          echo '</h3>';
          $scopeNotSet=true;
          //scope u value description
          if (@count($valueDescription->Scope->Interval)>0){
            $scopeNoteSet=false;
            $vdIntervalId=0;
            //jde o intervaly
            foreach ($format->Range[0]->Interval as $interval) {
            	echo JText::_('INTERVAL').': <strong>';
            	if (($interval['closure']=='openClosed')||($interval['closure']=='openOpen')){echo '(';}else{echo '&lt;';}
            	echo $interval['leftMargin'].' ; '.$interval['rightMargin'];
            	if (($interval['closure']=='closedOpen')||($interval['closure']=='openOpen')){echo ')';}else{echo '&gt;';}
            	echo '</strong>';
              echo '<a class="modal" href="index.php?option=com_bkef&amp;task=delValueDescriptionInterval&amp;article='.$this->article.'&amp;maId='.$maId.'&amp;fId='.$fId.'&amp;vdId='.$vdId.'&amp;vdIntervalId='.$vdIntervalId.'" >'.JText::_('DELETE').'</a> ';
              echo '<br />';
              $vdIntervalId++;
            }
          }
          if (@count($valueDescription->Scope->Value)>0){
            $scopeNoteSet=false;
            $vdValueId=0;
            //jde o intervaly
            foreach ($format->Range[0]->Value as $value) {
            	echo JText::_('VALUE').': <strong>'.((string)$value).'</strong>';
              echo '<a class="modal" href="index.php?option=com_bkef&amp;task=delValueDescriptionValue&amp;article='.$this->article.'&amp;maId='.$maId.'&amp;fId='.$fId.'&amp;vdId='.$vdId.'&amp;vdValueId='.$vdValueId.'" >'.JText::_('DELETE').'</a> ';
              echo '<br />';
              $vdValueId++;
            }
          }
          if ($scopeNotSet){
            echo '<div class="missing infotext">'.JText::_('VALUE_DESCRIPTION_NO_VALUES_INFO').'</div>';
          }
          echo '<div class="linksDiv">
                  <a href="index.php?option=com_bkef&amp;task=addValueDescriptionValue&amp;maId='.$maId.'&amp;fId='.$fId.'&amp;vdId='.$vdId.'&amp;article='.$this->article.'&amp;tmpl=component" rel="{handler: \'iframe\', size: {x: 500, y: 280}}" class="modal">'.JText::_('ADD_VALUE').'...</a>
                  |&nbsp;
                  <a href="index.php?option=com_bkef&amp;task=addValueDescriptionInterval&amp;maId='.$maId.'&amp;fId='.$fId.'&amp;vdId='.$vdId.'&amp;article='.$this->article.'&amp;tmpl=component" rel="{handler: \'iframe\', size: {x: 500, y: 280}}" class="modal">'.JText::_('ADD_INTERVAL').'...</a>
                </div>';
          //--scope u value description
          //anotace u value description
          if (@count($valueDescription->Annotations[0]->Annotation)>0){
            echo '<h4>'.JText::_('ANNOTATIONS').'</h4>';
            $anId=0;
            foreach ($valueDescription->Annotations[0]->Annotation as $annotation) {
            	echo '<div class="annotation level3Div">';
            	echo '<strong>'.($annotation->Text[0]!=''?$annotation->Text[0]:'&lt;&lt;???&gt;&gt;').'</strong>';
              echo '<br />'.JText::_('CREATED').': '.$annotation->Created[0]->Author.' ('.date(JText::_('DATETIMEFORMAT'),strtotime($annotation->Created[0]->Timestamp)).')';
              if ((string)$annotation->Created[0]->Timestamp!=(string)$annotation->LastModified[0]->Timestamp){
                echo '; '.JText::_('LAST_MODIFIED').': '.$annotation->LastModified[0]->Author.' ('.date(JText::_('DATETIMEFORMAT'),strtotime($annotation->LastModified[0]->Timestamp)).')';
              }
              echo ' |&nbsp;';
              echo '<a class="modal" href="index.php?option=com_bkef&amp;task=editValueDescriptionAnnotation&amp;article='.$this->article.'&amp;tmpl=component&amp;maId='.$maId.'&amp;fId='.$fId.'&amp;vdId='.$vdId.'&amp;anId='.$anId.'" rel="{handler: \'iframe\', size: {x: 500, y: 330}}" >'.JText::_('EDIT_ANNOTATION').'</a> ';
              echo ' |&nbsp;';
              echo '<a class="modal" href="index.php?option=com_bkef&amp;task=deleteValueDescriptionAnnotation&amp;article='.$this->article.'&amp;tmpl=component&amp;maId='.$maId.'&amp;fId='.$fId.'&amp;vdId='.$vdId.'&amp;anId='.$anId.'" rel="{handler: \'iframe\', size: {x: 500, y: 330}}" >'.JText::_('DELETE_ANNOTATION').'</a> ';
              echo '</div>';
              $anId++;
            }
          }
          echo '<div class="linksDiv">
                  <a href="index.php?option=com_bkef&amp;task=addValueDescriptionAnnotation&amp;maId='.$maId.'&amp;fId='.$fId.'&amp;vdId='.$vdId.'&amp;article='.$this->article.'&amp;tmpl=component" rel="{handler: \'iframe\', size: {x: 500, y: 280}}" class="modal">'.JText::_('ADD_ANNOTATION').'...</a>
                </div>';
          //--anotace u value description
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