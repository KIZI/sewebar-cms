<?php
// no direct access
 
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.view');

 
class MappingViewUserEditValues extends JView
{

  function display($tpl = null)
  {        
    $doc = & JFactory::getDocument();

    $doc->addStyleSheet('components/com_mapping/css/usereditValues.css');
    $doc->addScript("components/com_mapping/js/jquery1.4.min.js");
    $doc->addScript("components/com_mapping/js/scroll.js");
    $doc->addScript("components/com_mapping/js/mapping_".JText::_('langEN').".js");
    $doc->addScript("components/com_mapping/js/usereditValues.js");
    
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
       
  }
  
}
?>
<h1><?php echo JText::_('VALUES_USER_MAPPING');?></h1>
<div id="contentDiv">
   
</div>
<script type="text/javascript">
/* <![CDATA[ */
  loadValuesLegendData();
/* ]]> */
</script>

<div>
    <a href="index.php?option=com_mapping&task=finalizeMapping" class="buttonA"><?php echo JText::_('FINALIZE');?>... &gt;</a>
</div>