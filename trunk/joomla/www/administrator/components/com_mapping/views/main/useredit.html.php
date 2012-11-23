<?php
// no direct access
 
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.view');

 
class MappingViewUserEdit extends JView
{

  function display($tpl = null)
  {        
    $doc = & JFactory::getDocument();

    $doc->addStyleSheet('components/com_mapping/css/useredit.css');
    $doc->addScript("components/com_mapping/js/jquery1.4.min.js");
    $doc->addScript("components/com_mapping/js/scroll.js");
    $doc->addScript("components/com_mapping/js/mapping_".JText::_('langEN').".js");
    $doc->addScript("components/com_mapping/js/useredit.js");
    

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
<h1><?php echo JText::_('KEYS_MAPPING_EDITATION'); ?></h1>
<div class="legendDiv">
  <?php echo JText::_('KEYS_MAPPING_LEGEND'); ?>
  <br />
</div>
<div class="legendDiv" style="margin-top:5px;margin-bottom:10px;">
  <?php echo JText::_('LEGEND'); ?>:
    <span class="autoMerge"><?php echo JText::_('AUTO_MAPPING_SUGGESTION'); ?></span> 
    <span class="userMerge"><?php echo JText::_('CONFIRMED_MAPPING'); ?></span>
    <span class="nonMerge"><?php echo JText::_('UNMAPPED_KEY'); ?></span>
    <span class="ignoreMerge"><?php echo JText::_('IGNORED_KEY'); ?></span>
</div>
<div id="contentDiv">
   
</div>
<script type="text/javascript">
/* <![CDATA[ */
  loadLegendData();
  //loadData();
/* ]]> */
</script>

<div style="margin-top:10px;">
  <a href="index.php?option=com_mapping&task=startMapValues" class="buttonA"><?php echo JText::_('GOTO_VALUES_MAPPING'); ?>... &raquo;</a>
</div>