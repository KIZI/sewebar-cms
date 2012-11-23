<?php
// no direct access
 
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.view');

 
class MappingViewFinalizedMapping extends JView
{
  function getSelectButton($oldId){
    $link = 'index.php?option=com_mapping&amp;task=articlesiframe&amp;tmpl=component&oldId='.$oldId;
		return '<a href="'.$link.'" rel="{handler: \'iframe\', size: {x: 700, y: 400}}" class="modal">'.JText::_('SELECT_OTHER_ARTICLE').'</a>';
  }
  
  
  function display($tpl = null)
  {        
    /*Ověření, jestli jde o přístup z administrace nebo front-endu*/
    $doc = &JFactory::getDocument();
    if (JPATH_BASE==JPATH_ADMINISTRATOR){
      require_once(JApplicationHelper::getPath('toolbar_html'));
      TOOLBAR_mapping::_DEFAULT();
    }else{
      echo '<div class="componentheading">'.JText::_('COM_MAPPING').'</div>';
      
      $doc->addStyleSheet('components/com_mapping/css/general.css');
      $doc->addStyleSheet('components/com_mapping/css/component.css');
      
    } 
    
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
    /**/
    
    echo '<h1>'.JText::_('MAPPING_FINALIZATION').'</h1>';
    
    if ($this->taskFmlArticle){
      echo '<div>
              <h3>'.JText::_('REWRITE_CURRENT_MAPPING').'</h3>
              <form method="post" action="">
              <p>'.JText::_('SAVE_INTO_TASKS_MAPPING').': <strong>'.$this->taskFmlArticle->title.'</strong></p>
              <input type="hidden" name="fmlArticleId" value="'.$this->taskFmlArticle->id.'" />
              <input type="submit" value="'.JText::_('SAVE_INTO_TASKS_MAPPING_BTN').'" /></form>
            </div>'; 
      $connectTask=' ('.JText::_('AND_CONNECT_TASK').')';      
    }else{
      $connectTask='';
    }
    echo '<hr />';
    echo '<div>
            <h3>'.JText::_('SAVE_AS_NEW_ARTICLE').$connectTask.'</h3>
            <form method="post">
              <table>
                <tr>
                  <td>'.JText::_('TITLE').':</td>
                  <td><input type="text" value="" name="title" /></td>
                </tr>
                <tr>
                  <td>'.JText::_('CATEGORY').'</td>
                  <td>
                    <select name="category">
                      <option value="0">-no category-</option>
                    </select>
                  </td>
                </tr>  
              </table>
              <input type="submit" value="'.JText::_('SAVE').'" />
            </form>
          </div>';
    echo '<hr />';
    echo '<div>
            <h3>'.JText::_('REWRITE_EXISTING_FILE').$connectTask.'</h3>
            <form method="post">
              <table>
                <tr>
                  <td>'.JText::_('FML_ARTICLE').'</td>
                  <td><input type="hidden" value="" name="fmlArticleId" id="art1" /><input id="input1" type="text" name="input1" value="--'.JText::_('SELECT').'--" style="width:250px;" readonly="readonly"/>&nbsp;&nbsp;&nbsp;'.$this->getSelectButton(1).'</td>
                </tr>
              </table>
              <input type="submit" value="'.JText::_('SAVE').'" />
            </form>  
          </div>';      
    
    if (!isset($this->task)){
      echo '<hr />';
      echo '<div>
              <h3>Stáhnout...</h3>
              <a href="index.php?option=com_mapping&task=downloadFML&format=raw">'.JText::_('DOWNLOAD_FML').'...</a>
            </div>';
    }
    
       
  }
  
}
?>
