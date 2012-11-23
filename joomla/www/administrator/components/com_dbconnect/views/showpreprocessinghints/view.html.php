<?php
/**
* @package helloworld02
* @version 1.1
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* Joomla! is free software and parts of it may contain or be derived from the
* GNU General Public License or other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

jimport( 'joomla.application.component.view' );
                                  
/**
 * @package Joomla
 * @subpackage Config
 */
class dbconnectViewShowPreprocessingHints extends JView
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
		//DEVNOTE: set document title
		$document = & JFactory::getDocument();
		$document->setTitle( JText::_('PREPROCESSING') );

    //TOOLBAR, CSS
    JHtml::stylesheet('main.css','media/com_dbconnect/css/');

    $bkefModel=$this->getModel('Bkef','dbconnectModel');
    
    //$bkefModel=$this->getModel('Bkef','dbconnectModel');
		
    $this->assignRef('bkefModel',$bkefModel);
    
    $this->assignRef('preprocessingHints',$bkefModel->getPreprocessingHints($this->maName,$this->formatName));
    
		//DEVNOTE:call parent display
    parent::display();		
  }
}
?>
