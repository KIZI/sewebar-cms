<?php
/**
 * @license    GNU/GPL
 * @author Stanislav Vojíř - xvojs03
 * @copyright Stanislav Vojíř, 2009
 *   
 */
 
// no direct access
 
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.view');
           
class BkefViewEditRange extends JView
{
  function display($tpl = null)
  {               
      $doc = & JFactory::getDocument();
      if (JPATH_BASE!=JPATH_ADMINISTRATOR){
        $doc->addStyleSheet('components/com_bkef/css/general.css');
        $doc->addStyleSheet('components/com_bkef/css/component.css');
      }         
      
      echo '<h1>'.$this->h1.'</h1>';
      
      $xml=$this->xml;
      $maId=intval($this->maId);
      $fId=intval($this->fId);
      
      if ($fId>-1){
        $format=$xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId];
      }
      ?>
      <form action="index.php?option=com_bkef&amp;task=editRange" method="post" target="_parent" >
      <?php
      if (($_GET['type']=='enumeration')&&($_GET['type']!='interval')&&($_GET['type']!='regex')){
        /*ENUMERATION*/
        echo '<table>';
        echo '  <tr>
                  <td colspan="4">
                    <h3>'.JText::_('ENUMERATION').'</h3>
                    <div>'.JText::_('EDIT_RANGE_ENUMERATION_INFO').'</div>            
                  </td>
                </tr>';
        echo '  <tr>
                  <td colspan="4">
                    <textarea name="enumeration" style="width:300px;height:200px;" title="'.JText::_('TITLE_EDIT_RANGE_TEXTAREA').'">';
                      $text='';
                      if (count($format->Range[0]->Value)>0){
                        foreach ($format->Range[0]->Value as $value) {
            	            $text.= $value."\n";
                        }
                      }
                      echo trim($text);
        echo '      </textarea>                  
                  </td>
                </tr>
                <tr>
                  <td colspan="4">
                    <h3>'.JText::_('COLLATION').'</h3>
                  </td>
                </tr>
                <tr>
                  <td>'.JText::_('COLLATION_TYPE').'</td>
                  <td>
                    <select name="collation_type">
                      <option value="Alphabetical" '.($format->Collation['type']=='Alphabetical'?'selected="selected"':'').'>'.JText::_('COLLATION_TYPE_ALPHABETICAL').'</option>
                      <option value="Enumeration" '.($format->Collation['type']=='Enumeration'?'selected="selected"':'').'>'.JText::_('COLLATION_TYPE_ENUMERATION').'</option>
                      <option value="Numerical" '.($format->Collation['type']=='Numerical'?'selected="selected"':'').'>'.JText::_('COLLATION_TYPE_NUMERICAL').'</option>
                    </select>
                  </td>
                  <td>'.JText::_('COLLATION_SENSE').'</td>
                  <td>
                    <select name="collation_sense">
                      <option value="Ascending" '.($format->Collation['sense']=='Ascending'?'selected="selected"':'').'>'.JText::_('ASCENDING').'</option>
                      <option value="Descending" '.($format->Collation['sense']=='Descending'?'selected="selected"':'').'>'.JText::_('DESCENDING').'</option>
                    </select>
                  </td>
                </tr>';        
                
        echo '</table>';
        /*--ENUMERATION*/
        echo '<input type="hidden" name="potvrzeni" value="enumeration" id="potvrzeni" />';
      }elseif (($_GET['type']=='regex')&&($_GET['type']!='interval')&&($_GET['type']!='enumeration')){
        /*REGEX*/
        echo '<table>';
        echo '  <tr>
                  <td colspan="4">
                    <h3>'.JText::_('REGEX').'</h3>
                    <div>'.JText::_('EDIT_RANGE_REGEX_INFO').'</div>            
                  </td>
                </tr>';
        echo '  <tr>
                  <td>'.JText::_('REGEX_PATTERN').'</td>
                  <td colspan="3">
                    <input type="text" name="regex" style="width:200px;" title="'.JText::_('TITLE_EDIT_RANGE_REGEX').'" value="'.$format->Range[0]->Regex[0].'" />             
                  </td>
                </tr>
                <tr>
                  <td colspan="4">
                    <h3>'.JText::_('COLLATION').'</h3>
                  </td>
                </tr>
                <tr>
                  <td>'.JText::_('COLLATION_TYPE').'</td>
                  <td>
                    <select name="collation_type">
                      <option value="Alphabetical" '.($format->Collation['type']=='Alphabetical'?'selected="selected"':'').'>'.JText::_('COLLATION_TYPE_ALPHABETICAL').'</option>
                      <option value="Numerical" '.($format->Collation['type']=='Numerical'?'selected="selected"':'').'>'.JText::_('COLLATION_TYPE_NUMERICAL').'</option>
                    </select>
                  </td>
                  <td>'.JText::_('COLLATION_SENSE').'</td>
                  <td>
                    <select name="collation_sense">
                      <option value="Ascending" '.($format->Collation['sense']=='Ascending'?'selected="selected"':'').'>'.JText::_('ASCENDING').'</option>
                      <option value="Descending" '.($format->Collation['sense']=='Descending'?'selected="selected"':'').'>'.JText::_('DESCENDING').'</option>
                    </select>
                  </td>
                </tr>';        
        echo '</table>';
        /*--REGEX*/
        echo '<input type="hidden" name="potvrzeni" value="regex" id="potvrzeni" />';
      }else {
        echo '<table>';
        echo '  <tr>
                  <td colspan="4">
                    <h3>'.JText::_('INTERVAL').'</h3>
                    <div>'.JText::_('EDIT_RANGE_INTERVAL_INFO').'</div>            
                  </td>
                </tr>';
        //vyreseni hranic intervalu
        $closure=(string)$format->Range->Interval[0]['closure'];
        //--vyreseni hranic intervalu        
        echo   '<tr>
                  <td>'.JText::_('INTERVAL_LEFT_BOUND').':</td>
                  <td colspan="3">
                    <select name="leftBoundType" title="'.JText::_('TITLE_EDIT_RANGE_INTERVAL_LEFTBOUND').'">';                
                echo '<option value="closed" title="closed" '.((($closure=='closedClosed')||($closure=='closedOpen'))?'selected="selected"':'').'>&lt;</option>';
                echo '<option value="open" title="open" '.((($closure=='openClosed')||($closure=='openOpen'))?'selected="selected"':'').'>(</option>';
        echo   '    </select>
                    <input name="leftBoundValue" value="'.$format->Range->Interval['leftMargin'].'" title="'.JText::_('TITLE_EDIT_RANGE_INTERVAL_LEFTVALUE').'" />
                  </td>
                </tr>';
        echo '  <tr>
                  <td>'.JText::_('INTERVAL_RIGHT_BOUND').':</td>
                  <td colspan="3">
                    <input name="rightBoundValue" value="'.$format->Range->Interval['rightMargin'].'" title="'.JText::_('TITLE_EDIT_RANGE_INTERVAL_RIGHTVALUE').'" />
                    <select name="rightBoundType" title="'.JText::_('TITLE_EDIT_RANGE_INTERVAL_RIGHTBOUND').'">';
                echo '<option value="closed" title="closed" '.((($closure=='closedClosed')||($closure=='openClosed'))?'selected="selected"':'').'>&gt;</option>';
                echo '<option value="open" title="open" '.((($closure=='closedOpen')||($closure=='openOpen'))?'selected="selected"':'').'>)</option>';
        echo '      </select>
                  </td>
                </tr>';
        echo '  <tr>
                  <td colspan="4">
                    <h3>'.JText::_('COLLATION').'</h3>
                  </td>
                </tr>
                <tr>
                  <td>'.JText::_('COLLATION_TYPE').'</td>
                  <td>
                    <select name="collation_type">
                      <option value="Numerical" '.($format->Collation['type']=='Numerical'?'selected="selected"':'').'>'.JText::_('COLLATION_TYPE_NUMERICAL').'</option>
                    </select>
                  </td>
                  <td>'.JText::_('COLLATION_SENSE').'</td>
                  <td>
                    <select name="collation_sense">
                      <option value="Ascending" '.($format->Collation['sense']=='Ascending'?'selected="selected"':'').'>'.JText::_('ASCENDING').'</option>
                      <option value="Descending" '.($format->Collation['sense']=='Descending'?'selected="selected"':'').'>'.JText::_('DESCENDING').'</option>
                    </select>
                  </td>
                </tr>';                        
        echo '</table>';
        echo '<input type="hidden" name="potvrzeni" value="interval" id="potvrzeni" />';
      }
      
        
      ?>
        <input type="hidden" name="article" value="<?php echo $this->article; ?>" />
        <input type="hidden" name="maId" value="<?php echo $this->maId; ?>" />
        <input type="hidden" name="fId" value="<?php echo $this->fId; ?>" />
        <br />
        <input type="submit" value="<?php echo JText::_('SAVE');?>..." />
      </form>
      <?php
      //parent::display($tpl);
  }
}

?>