<?php 
defined('_JEXEC') or die ('Restricted access');

/**
 * @author Stanislav Vojíř - xvojs03
 * 
 * Třída definující upravený toolbar pro administrační část komponenty.   
 */ 
class TOOLBAR_mapping{
  function _DEFAULT(){
    JToolBarHelper::title(JText::_('COM_MAPPING'),'generic.png');
    
    $bar = & JToolBar::getInstance('toolbar');
    @$bar->appendButton( 'Link', 'cancel', 'Cancel', '.' );
  }
}

?>