<?php
jimport( 'joomla.application.component.view' );
                                  
/**
 * @package Joomla
 * @subpackage Config
 */
class iziViewIziShowPreprocessingHints extends JView
{
  /**
   *  Funkce vracející textové vyjádření typu discretizace
   */     
  function discretizationHintType($discretizationHint){
    if (isset($discretizationHint->NominalEnumeration)){
      return JText::_('NOMINAL_ENUMERATION');
    }elseif (isset($discretizationHint->IntervalEnumeration)){
      return JText::_('INTERVAL_ENUMERATION');
    }elseif (isset($discretizationHint->EachValueOneBin)){
      return JText::_('EACH_VALUE_ONE_BIN');
    }elseif (isset($discretizationHint->EquidistantInterval)){
      return JText::_('EQUIDISTANT_INTERVAL');
    }
  }

	/**
	 * Display the view
	 */
	function display()
	{                          
    JHtml::stylesheet('izi.css','media/com_dbconnect/css/');
		                                              
		$document = & JFactory::getDocument();
		$document->setTitle( JText::_('PREPROCESSING') );
    
		parent::display();		
  }
}
?>
