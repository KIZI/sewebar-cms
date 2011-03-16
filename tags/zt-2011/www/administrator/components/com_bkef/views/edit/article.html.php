<?php
/**
 * HTML View class for the BKEF Component
 *  
 * @package    BKEF
 * @license    GNU/GPL
 * @author Stanislav Vojíř - xvojs03
 * @copyright Stanislav Vojíř, 2009
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
    if (JPATH_BASE==JPATH_ADMINISTRATOR){
      require_once(JApplicationHelper::getPath('toolbar_html'));
      TOOLBAR_bkef::_DEFAULT();
    }else{
      echo '<div class="componentheading">'.JText::_('BKEF').'</div>';
      $doc = &JFactory::getDocument();
      $doc->addStyleSheet('components/com_bkef/css/general.css');
      $doc->addStyleSheet('components/com_bkef/css/component.css');
    }
    /**/
    
    $xml=$this->xml;
    
    echo '<h1>BKEF:&nbsp;'.$xml->Header[0]->Title[0].' ('.$this->articleTitle.')</h1>';
    echo '<br /><div class="level1Div">'.JText::_('APPLICATION').': <strong>'.$xml->Header[0]->Application[0]['name'].'</strong>';
    if (@$xml->Header[0]->Application[0]['name']){
      echo ' ('.JText::_('VERSION').': '.$xml->Header[0]->Application[0]['version'].')';
    }
    echo '</div>';
    echo '<div style="margin-top:30px;margin-bottom:30px;" class="infotext">'.JText::_('SEL_META_INFO').'</div>';
    /*
    echo '<h2>Nadřazené metaatributy</h2>';
    echo '<div class="infotext">Nadřazené metaatributy shrnují vlastnosti několika základních metaatributů</div>';
    */
    echo '<h2>'.JText::_('GROUP_METAATTRIBUTES').'</h2>';
    echo '<div class="infotext">'.JText::_('GROUP_METAATTRIBUTES_INFO').'</div>';
    
    if (count($xml->MetaAttributes[0]->MetaAttribute)>0) {
      $maId=0;   
      $row=0;
      echo '<table class="adminlist">';
      echo '<thead><tr><th>'.JText::_('name').'</th><th></th></tr></thead>';
      foreach ($xml->MetaAttributes[0]->MetaAttribute as $key=>$MetaAttribute) {
        if ($MetaAttribute['level']==1){
          echo '<tr class="row'.($row%2).'"><td><a href="index.php?option=com_bkef&amp;task=groupMetaAttribute&amp;article='.$this->article.'&amp;maId='.$maId.'"><strong>'.$MetaAttribute['name'].'</strong></a>&nbsp;&nbsp;&nbsp;</td><td width="150">';
          echo '<a href="index.php?option=com_bkef&amp;task=groupMetaAttribute&amp;article='.$this->article.'&amp;maId='.$maId.'">'.JText::_('EDIT').'</a>';
          echo '&nbsp;&nbsp;&nbsp;';
          echo '<a href="index.php?option=com_bkef&amp;task=delMetaAttribute&amp;article='.$this->article.'&amp;tmpl=component&amp;maId='.$maId.'" rel="{handler: \'iframe\', size: {x: 400, y: 200}}" class="modal">'.JText::_('DELETE').'</a></td>';
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
      echo '<thead><tr><th>'.JText::_('name').'</th><th></th></tr></thead>';
      $maId=0;   
      $row=0;
      
      foreach ($xml->MetaAttributes[0]->MetaAttribute as $key=>$MetaAttribute) {
        if ($MetaAttribute['level']==0){
          echo '<tr class="row'.($row%2).'"><td><a href="index.php?option=com_bkef&amp;task=metaAttribute&amp;article='.$this->article.'&amp;maId='.$maId.'"><strong>'.$MetaAttribute['name'].'</strong></a>&nbsp;&nbsp;&nbsp;</td><td width="150">';
          echo '<a href="index.php?option=com_bkef&amp;task=metaAttribute&amp;article='.$this->article.'&amp;maId='.$maId.'">'.JText::_('EDIT').'</a>';
          echo '&nbsp;&nbsp;&nbsp;';
          echo '<a href="index.php?option=com_bkef&amp;task=delMetaAttribute&amp;article='.$this->article.'&amp;tmpl=component&amp;maId='.$maId.'" rel="{handler: \'iframe\', size: {x: 400, y: 200}}" class="modal">'.JText::_('DELETE').'</a></td>';
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