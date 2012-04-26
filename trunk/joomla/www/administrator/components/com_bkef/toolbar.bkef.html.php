<?php 
defined('_JEXEC') or die ('Restricted access');

/**
 * @package BKEF
 * @author Stanislav Vojíř - xvojs03
 * @copyright Stanislav Vojíř, 2009-2011
 * 
 * Třída definující upravený toolbar pro administrační část komponenty BKEF.   
 */ 
class TOOLBAR_bkef{
  function _DEFAULT(){
    JToolBarHelper::title(JText::_('BKEF'),'generic.png');
    
    $bar = & JToolBar::getInstance('toolbar');
    @$bar->appendButton( 'Link', 'cancel', 'Cancel', '.' );
  }
  
  function frontend(){
    $doc = &JFactory::getDocument();
    $doc->addStyleSheet('components/com_bkef/css/general.css');
    $doc->addStyleSheet('components/com_bkef/css/component.css');
    
    echo '<div class="componentheading">';
    echo JText::_('BKEF');
    if(@$_SESSION['showmodal']){
      echo '<a href="index.php?option=com_bkef&amp;task=endModal" id="endModalLink">END MODAL</a> ';
      if (@$_SESSION['showstorno']){
        echo '<a href="index.php?option=com_bkef&amp;task=endModal&storno=1" id="endModalLink">STORNO MODAL</a>';
      }
    }
    echo '</div>';
  }
}

?>