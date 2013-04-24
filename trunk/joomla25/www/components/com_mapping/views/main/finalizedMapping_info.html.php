<?php
// no direct access
 
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.view');

 
class MappingViewFinalizedMapping_info extends JView
{ 
  
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
    
    
    /**/
    
    echo '<h1>'.JText::_('MAPPING_FINALIZATION').'</h1>';
    
    echo '<p>'.JText::_('MAPPING_SAVED_INFO').'</p>';
    
    if($this->redirectUrl){
      echo '<p>'.JText::_('REDIRECT_INFO').'</p>';
      echo '<script type="text/javascript">
              function redirectToUrl(){
                location.href="'.$this->redirectUrl.'";
              }
              var t=setTimeout("redirectToUrl();",5000);
              
            </script>';
    }
       
  }
  
}
?>
