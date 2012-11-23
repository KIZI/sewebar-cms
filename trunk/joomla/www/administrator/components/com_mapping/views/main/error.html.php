<?php
// no direct access
 
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.view');

 
class MappingViewError extends JView
{
  var $text='';
  var $link='';
  
  
  function display($tpl = null)
  {        
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
    
    echo '<h1>'.JText::_('ERROR').'!</h1>';
    echo '<div style="border:2px solid red;">'.$this->text.'</div>';
    echo '<div><a href="'.$this->link.'">OK</a></div>';
    
       
  }
  
}
?>
