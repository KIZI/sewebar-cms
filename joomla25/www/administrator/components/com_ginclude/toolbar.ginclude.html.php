<?php 
defined('_JEXEC') or die ('Restricted access');

/**
 * @package gInclude
 * @author Stanislav Vojíř - xvojs03
 * @copyright Stanislav Vojíř, 2009
 * 
 * Třída definující upravený toolbar pro administrační část komponenty gInclude.   
 */ 
class TOOLBAR_ginclude{
  function _DEFAULT(){
    JToolBarHelper::title(JText::_('GINCLUDE-UPDATE'),'generic.png');
    
    $bar = & JToolBar::getInstance('toolbar');
    @$bar->appendButton( 'Link', 'cancel', 'Cancel', '.' );
  }
}

?>