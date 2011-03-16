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
      echo '&nbsp;-&gt;&nbsp;MetaAttribute: <strong>'.$xml->MetaAttributes[0]->MetaAttribute[$maId]['name'].'</strong>';
      echo '</div>';
      /**/
      
      echo '<h1>'.JText::_('METAATTRIBUTE').': '.$metaAttribute['name'].'</h1>';
      echo '<div class="level1Div">';
      echo JText::_('VARIABILITY').': <strong>'.$metaAttribute->Variability.'</strong><br />';
      if (@$metaAttribute->Annotation){
        echo '<div class="annotation">'.JText::_('ANNOTATION').' '.@$metaAttribute->Annotation->Author.':<br />'.@$metaAttribute->Annotation->Text.'</div>';
      }
      echo '</div>';
      echo '<div class="linksDiv"><a class="modal" href="index.php?option=com_bkef&amp;task=editMetaAttribute&amp;article='.$this->article.'&amp;tmpl=component&amp;maId='.$maId.'" rel="{handler: \'iframe\', size: {x: 500, y: 330}}">'.JText::_('EDIT_META_ANNOTATION').'...</a></div><br />';
      echo '<div class="infotext">'.JText::_('SELECT_FORMAT_INFO').'</div>';
      echo '<h2>'.JText::_('FORMATS').'</h2>';
      echo '<div class="infotext"></div>';
      if (count($metaAttribute->Formats->Format)>0){
        echo '<table class="adminlist">';
        echo '<thead><tr><th>'.JText::_('FORMAT_NAME').'</th><th>'.JText::_('AUTHOR').'</th><th></th></tr></thead>';
        $fId=0;
        foreach ($metaAttribute->Formats->Format as $format) {
        	echo '<tr class="row'.($fId%2).'"><td><a href="index.php?option=com_bkef&amp;task=format&amp;article='.$this->article.'&amp;maId='.$maId.'&amp;fId='.$fId.'"><strong>'.$format[name].'</strong></a></td><td>'.$format->Author.'</td><td width="200">';
       
          echo '<a href="index.php?option=com_bkef&amp;task=format&amp;article='.$this->article.'&amp;maId='.$maId.'&amp;fId='.$fId.'">'.JText::_('EDIT_FORMAT').'</a>';
          echo '&nbsp;&nbsp;|&nbsp;&nbsp;';
          echo '<a class="modal" href="index.php?option=com_bkef&amp;task=delFormat&amp;article='.$this->article.'&amp;tmpl=component&amp;maId='.$maId.'&amp;fId='.$fId.'" rel="{handler: \'iframe\', size: {x: 400, y: 200}}">'.JText::_('DELETE_FORMAT').'</a>';
          
          echo '</td></tr>';
          $fId++;
        }
        echo '</table>';  
      }else {
        echo '<div class="missing infotext">'.JText::_('NO_FORMATS_INFO').'</div>';
      }
      
      echo '<div class="linksDiv"><a href="index.php?option=com_bkef&amp;task=newFormat&amp;maId='.$maId.'&amp;article='.$this->article.'&amp;tmpl=component" rel="{handler: \'iframe\', size: {x: 500, y: 195}}" class="modal">'.JText::_('NEW_FORMAT').'</a></div>';
      ?>
      
      
      <?php
  }
}

?>