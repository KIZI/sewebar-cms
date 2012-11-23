<?php
// no direct access
 
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.view');

 
class MappingViewSelArticles extends JView
{
  function getSelectButton($oldId){
    $link = 'index.php?option=com_mapping&amp;task=articlesiframe&amp;tmpl=component&oldId='.$oldId;
		return '<a href="'.$link.'" rel="{handler: \'iframe\', size: {x: 700, y: 400}}" class="modal">'.JText::_('SELECT_OTHER_ARTICLE').'</a>';
  }

  function display($tpl = null)
  {        
    $doc = & JFactory::getDocument();
    $declaration	="
     function gSelectArticle(id,newId,title) {
       document.getElementById('art'+id).value=newId;
       document.getElementById('input'+id).value=title;
	     document.getElementById('sbox-window').close();
     }
     function clearArticle(id){
       document.getElementById('art'+id).value=0;
       document.getElementById('input'+id).value='--".JText::_("SELECT")."--';
     }
     ";
    $doc->addScriptDeclaration($declaration);
    JHTML::_('behavior.modal');

    /*Ověření, jestli jde o přístup z administrace nebo front-endu*/
    if (JPATH_BASE==JPATH_ADMINISTRATOR){
      require_once(JApplicationHelper::getPath('toolbar_html'));
      TOOLBAR_mapping::_DEFAULT();
    }else{
      echo '<div class="componentheading">'.JText::_('COM_MAPPING').'</div>';
      $doc = &JFactory::getDocument();
      $doc->addStyleSheet('components/com_mapping/css/general.css');
      $doc->addStyleSheet('components/com_mapping/css/component.css');
    } 
    /**/
    
    $model=$this->getModel();
    $article=$model->getArticleDB($this->articleId);
    $art1=$model->loadArticle(JRequest::getVar('art1',-1),true);
    $art2=$model->loadArticle(JRequest::getVar('art2',-1),true);
    $artFml=$model->loadArticle(JRequest::getVar('artFml',-1),true);
    $article=$article[0];
    echo '<h3>'.JText::_('PMML_BKEF_MAPPING').'</h3>';
    echo '<div>'.JText::_('SELECT_FILES_FOR_MAPPING').'</div>';
    echo '<form method="post" action="index.php?option=com_mapping&amp;task=similarity">';
    echo '<table>';
    echo '<tr><td>'.JText::_('FILE_1').'</td><td><input type="hidden" value="'.$art1->id.'" name="art1" id="art1" /><input id="input1" type="text" name="input1" value="'.($art1->title!=''?$art1->title:'--'.JText::_('SELECT').'--').'" style="width:250px;" readonly="readonly"/>&nbsp;&nbsp;&nbsp;'.$this->getSelectButton(1).'</td></tr>';
    echo '<tr><td>'.JText::_('FILE_2').'</td><td><input type="hidden" value="'.$art2->id.'" name="art2" id="art2" /><input id="input2" type="text" name="input2" value="'.($art2->title!=''?$art2->title:'--'.JText::_('SELECT').'--').'" style="width:250px;" readonly="readonly"/>&nbsp;&nbsp;&nbsp;'.$this->getSelectButton(2).'</td></tr>';
    echo '<tr><td>'.JText::_('FML_FILE').'</td><td><input type="hidden" value="'.$artFml->id.'" name="artFml" id="art3" /><input id="input3" type="text" name="inputFml" value="'.($artFml->title!=''?$artFml->title:'--'.JText::_('SELECT').'--').'" style="width:250px;" readonly="readonly"/>&nbsp;&nbsp;&nbsp;'.$this->getSelectButton(3).'&nbsp;<a href="javascript:clearArticle(3);">'.JText::_("STORNO").'</a></td></tr>';
    /*vyreseni, jestli se maji zobrazovat dalsi podrobnosti*/
    
    echo '<tr class="optionsLinkTr"><td colspan="2"><a href="$jq(\'.optionsLinkTr\').hide();$jq(\'.optionsTr\').show();"></td></tr>';
    echo '<tr class="optionsTr">
            <td>'.JText::_('COLUMNS_MAPPING_TYPE').'</td>
            <td>  
              <select name="assignClass">';
    $assignClassArr=$this->configModel->loadConfigs("assignClass");
    if (count($assignClassArr)>0){
      foreach ($assignClassArr as $assignClass) {
        if ($assignClass->value>0){
          echo '<option value="'.$assignClass->name.'"'.(($assignClass->value==2)?' selected="selected"':'').'>'.JText::_($assignClass->description).'</option>';
        }
      }
    }        
    echo '    </select>
            </td>
          </tr>';
    echo '<tr class="optionsTr">
            <td>'.JText::_('VALUES_MAPPING_TYPE').'</td>
            <td>  
              <select name="valuesAssignClass">';
    $assignClassArr=$this->configModel->loadConfigs("valuesAssignClass");
    if (count($assignClassArr)>0){
      foreach ($assignClassArr as $assignClass) {
        if ($assignClass->value>0){
          echo '<option value="'.$assignClass->name.'"'.(($assignClass->value==2)?' selected="selected"':'').'>'.JText::_($assignClass->description).'</option>';
        }
      }
    }        
    echo '    </select>
            </td>
          </tr>';          
    echo '</table>';
    echo '<div"><input type="submit" value="'.JText::_('START_MAPPING').'" /></div>';
    echo '</form>';
    
       
  }
  
}
?>