<?php
/**
 * HTML View class for the BKEF Component
 *  
 * @package    BKEF
 * @license    GNU/GPL
 * @author Stanislav Vojíř - xvojs03
 * @copyright Stanislav Vojíř, 2011
 *   
 */
 
// no direct access
 
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.view');

 
class BkefViewArticle extends JView
{
  
  function display($tpl = null)
  {        
    $doc = & JFactory::getDocument();
    $declaration	="
     function gSelectArticle(oldId,newId,title) {
       document.getElementById('art'+oldId).value=newId;
       document.getElementById('input'+oldId).value=title;
	     document.getElementById('sbox-window').close();
     }";
    $doc->addScriptDeclaration($declaration);
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
    
    echo '<h1>BKEF:&nbsp;'.$xml->Header[0]->Title[0].' ('.$this->articleTitle.')</h1>';
    echo '<br /><div class="level1Div">';
    echo '<table>
            <tr>
              <td>'.JText::_('APPLICATION').':</td>
              <td><strong>'.$xml->Header[0]->Application[0]['name'].'</strong> '.((@$xml->Header[0]->Application[0]['name'])?' ('.JText::_('VERSION').': '.$xml->Header[0]->Application[0]['version'].')':'').'</td>
            </tr>
            <tr>
              <td>'.JText::_('CREATED').':</td>
              <td><strong>'.date(JText::_('DATETIMEFORMAT'),strtotime($xml->Header[0]->Created[0]->Timestamp)).' ('.@$xml->Header[0]->Created[0]->Author.')'.'</strong></td>
            </tr>
            <tr>
              <td>'.JText::_('LAST_MODIFIED').':</td>
              <td><strong>'.date(JText::_('DATETIMEFORMAT'),strtotime($xml->Header[0]->LastModified[0]->Timestamp)).' ('.@$xml->Header[0]->LastModified[0]->Author.')'.'</strong></td>
            </tr>
          </table>';
    
    echo '</div>';
    echo '<div style="margin-top:30px;margin-bottom:30px;" class="infotext">'.JText::_('SEL_META_INFO').'</div>';
    
    echo '<h2>'.JText::_('GROUP_METAATTRIBUTES').'</h2>';
    echo '<div class="infotext">'.JText::_('GROUP_METAATTRIBUTES_INFO').'</div>';
    
    if (count($xml->MetaAttributes[0]->MetaAttribute)>0) {
      $maId=0;   
      $row=0;
      echo '<table class="adminlist">';
      echo '<thead><tr><th>'.JText::_('NAME').'</th><th>'.JText::_('CREATED').'</th><th>'.JText::_('LAST_MODIFIED').'</th><th>'.JText::_('ACTIONS').'</th></tr></thead>';
      foreach ($xml->MetaAttributes[0]->MetaAttribute as $key=>$MetaAttribute) {
        if ($MetaAttribute['level']==1){
          echo '<tr class="row'.($row%2).'">
                  <td>
                    <a href="index.php?option=com_bkef&amp;task=groupMetaAttribute&amp;article='.$this->article.'&amp;maId='.$maId.'"><strong>'.$MetaAttribute->Name[0].'</strong></a>
                  </td>
                  <td>
                    '.date(JText::_('DATETIMEFORMAT'),strtotime($MetaAttribute->Created[0]->Timestamp[0])).' ('.$MetaAttribute->Created[0]->Author[0].')
                  </td>
                  <td>
                    '.date(JText::_('DATETIMEFORMAT'),strtotime($MetaAttribute->LastModified[0]->Timestamp[0])).' ('.$MetaAttribute->LastModified[0]->Author[0].')
                  </td>
                  <td width="150">';
          echo '    <a href="index.php?option=com_bkef&amp;task=groupMetaAttribute&amp;article='.$this->article.'&amp;maId='.$maId.'">'.JText::_('EDIT').'</a>';
          echo '    &nbsp;&nbsp;&nbsp;';
          echo '    <a href="index.php?option=com_bkef&amp;task=delMetaAttribute&amp;article='.$this->article.'&amp;tmpl=component&amp;maId='.$maId.'" rel="{handler: \'iframe\', size: {x: 400, y: 200}}" class="modal">'.JText::_('DELETE').'</a>
                  </td>
                </tr>';
          $row++;
        }
        $maId++;
      }
      echo '</table>';
    }
    echo '<br />';
    echo '<div class="linksDiv"><a href="index.php?option=com_bkef&amp;task=newGroupMetaAttribute&amp;article='.$this->article.'&amp;tmpl=component&amp;level=0" rel="{handler: \'iframe\', size: {x: 500, y: 330}}" class="modal">'.JText::_('NEW_GROUP_META').'</a></div>';
    
    echo '<br />';
      
    echo '<h2>'.JText::_('BASIC_METAATTRIBUTES').'</h2>';
    echo '<div class="infotext">'.JText::_('BASIC_METAATTRIBUTES_INFO').'</div>';  
    $maCount=count($xml->MetaAttributes[0]->MetaAttribute);
    if ($maCount>0){
      echo '<table class="adminlist">';
      echo '<thead><tr><th>'.JText::_('NAME').'</th><th>'.JText::_('CREATED').'</th><th>'.JText::_('LAST_MODIFIED').'</th><th>'.JText::_('ACTIONS').'</th></tr></thead>';
      $maId=0;   
      $row=0;
      
      foreach ($xml->MetaAttributes[0]->MetaAttribute as $key=>$MetaAttribute) {
        if ($MetaAttribute['level']==0){
          echo '<tr class="row'.($row%2).'">
                  <td><a href="index.php?option=com_bkef&amp;task=metaAttribute&amp;article='.$this->article.'&amp;maId='.$maId.'"><strong>'.$MetaAttribute->Name[0].'</strong></a></td>
                  <td>
                    '.date(JText::_('DATETIMEFORMAT'),strtotime($MetaAttribute->Created[0]->Timestamp[0])).' ('.$MetaAttribute->Created[0]->Author[0].')
                  </td>
                  <td>
                    '.date(JText::_('DATETIMEFORMAT'),strtotime($MetaAttribute->LastModified[0]->Timestamp[0])).' ('.$MetaAttribute->LastModified[0]->Author[0].')
                  </td>
                  <td width="150">';
          echo '    <a href="index.php?option=com_bkef&amp;task=metaAttribute&amp;article='.$this->article.'&amp;maId='.$maId.'">'.JText::_('EDIT').'</a>';
          echo '    &nbsp;&nbsp;&nbsp;';
          echo '    <a href="index.php?option=com_bkef&amp;task=delMetaAttribute&amp;article='.$this->article.'&amp;tmpl=component&amp;maId='.$maId.'" rel="{handler: \'iframe\', size: {x: 400, y: 200}}" class="modal">'.JText::_('DELETE').'</a>
                  </td>
                </tr>';
          $row++;
        }
    	  $maId++;
      }          
      echo '</table>';
    }else {
      echo '<div class="missing infotext">'.JText::_('NO_META_INFO').'</div>';
    }
    
    echo '<br />';
    echo '<div class="linksDiv"><a href="index.php?option=com_bkef&amp;task=newMetaAttribute&amp;article='.$this->article.'&amp;tmpl=component&amp;level=0" rel="{handler: \'iframe\', size: {x: 500, y: 330}}" class="modal">'.JText::_('NEW_BASIC_META').'</a></div>';        
  }
  
}
?>