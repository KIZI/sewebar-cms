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
           
class BkefViewMetaAttribute extends JView
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
      
      
      $metaAttribute=$xml->MetaAttributes[0]->MetaAttribute[$maId];
      
      /*PATH*/
      echo '<div class="navigationDiv">';
      echo '<a href="index.php?option=com_bkef&amp;task=selArticle&amp;article='.$article.'">'.$xml->Header[0]->Title[0].' ('.$this->articleTitle.')</a>';
      echo '&nbsp;-&gt;&nbsp;Group MetaAttribute: <strong>'.$xml->MetaAttributes[0]->MetaAttribute[$maId]['name'].'</strong>';
      echo '</div>';
      /**/
      
      echo '<h1>'.JText::_('GROUP_METAATTRIBUTE').': '.$metaAttribute['name'].'</h1>';
      echo '<div class="level1Div">';
      echo JText::_('VARIABILITY').': <strong>'.$metaAttribute->Variability.'</strong><br />';
      if (@$metaAttribute->Annotation){
        echo '<div class="annotation">'.JText::_('ANNOTATION').' '.@$metaAttribute->Annotation->Author.':<br />'.@$metaAttribute->Annotation->Text.'</div>';
      }
      echo '</div>';
      echo '<div class="linksDiv"><a class="modal" href="index.php?option=com_bkef&amp;task=editGroupMetaAttribute&amp;article='.$this->article.'&amp;tmpl=component&amp;maId='.$maId.'" rel="{handler: \'iframe\', size: {x: 500, y: 330}}">'.JText::_('EDIT_META_ANNOTATION').'...</a></div><br />';
      echo '<div class="infotext">'.JText::_('CHILDREN_BASIC_METAATTRIBUTES_INFO').'</div>';
      echo '<h2>'.JText::_('CHILDREN_BASIC_METAATTRIBUTES').'</h2>';
      if (count($metaAttribute->ChildMetaAttribute)>0){
        echo '<table class="adminlist">';
        echo '<thead><tr><th>'.JText::_('NAME').'</th><th></th></tr></thead>' ;
        
        $childArr=array();
        foreach ($metaAttribute->ChildMetaAttribute as $childMeta) {
          $childArr[]=intval($childMeta['id']);
        }
        
        $childId=0;
        $row=0;
        foreach ($xml->MetaAttributes[0]->MetaAttribute as $meta){
          if (in_array(intval($meta[id]),$childArr)){
            echo '<tr class="row'.($fId%2).'"><td><a href="index.php?option=com_bkef&task=metaAttribute&article='.$this->article.'&maId='.$childId.'"><strong>'.$meta['name'].'</strong></td><td width="200">';
            echo '<a class="modal" href="index.php?option=com_bkef&amp;task=delChildMeta&amp;article='.$this->article.'&amp;tmpl=component&amp;maId='.$maId.'&amp;childId='.array_search(intval($meta[id]),$childArr).'" rel="{handler: \'iframe\', size: {x: 400, y: 200}}">'.JText::_('DELETE_CHILD_META').'</a>';
            echo '</td></tr>';
          }
          $childId++;
        }
        
        foreach ($metaAttribute->ChildMetaattribute as $childMeta) {
          
          $fId++;
        }
        echo '</table>';  
      }else {
        echo '<div class="missing infotext">'.JText::_('NO_CHILDREN_INFO').'</div>';
      }
      
      echo '<div class="linksDiv"><a href="index.php?option=com_bkef&amp;task=addChildMeta&amp;maId='.$maId.'&amp;article='.$this->article.'&amp;tmpl=component" rel="{handler: \'iframe\', size: {x: 500, y: 195}}" class="modal">'.JText::_('ADD_CHILD_META').'</a></div>';
      ?>
      
      
      <?php
  }
}

?>