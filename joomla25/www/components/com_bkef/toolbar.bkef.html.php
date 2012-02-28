<?php 
defined('_JEXEC') or die ('Restricted access');

/**
 * @package BKEF
 * @author Stanislav Vojíř - xvojs03
 * @copyright Stanislav Vojíř, 2009
 * 
 * Třída definující upravený toolbar pro administrační část komponenty BKEF.   
 */ 
class TOOLBAR_bkef{
  function _DEFAULT(){
    JToolBarHelper::title(JText::_('BKEF'),'generic.png');
    
    $bar = & JToolBar::getInstance('toolbar');
    @$bar->appendButton( 'Link', 'cancel', 'Cancel', '.' );
  }
}

?>