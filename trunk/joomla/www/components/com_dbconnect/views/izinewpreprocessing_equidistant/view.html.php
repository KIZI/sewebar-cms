<?php
jimport( 'joomla.application.component.view' );
                                  
class iziViewIziNewPreprocessing_Equidistant extends JView
{
	/**
	 * Display the view
	 */
	function display()
	{
    JHtml::stylesheet('izi.css','media/com_dbconnect/css/');
    
		$document = & JFactory::getDocument();
		$document->setTitle( JText::_('NEW_PREPROCESSING') );
    
    $document->addScriptDeclaration("
      function cleanNumber(number){
        number=number.replace(',','.');
        return number.replace(' ','');
      }
      function is_numeric(value){
        if (value==''){return false}
        return !isNaN(value);
      }
      function equidistantInputCheck(){
        start=cleanNumber($('start').value);
        $('start').value=start;
        if (!is_numeric(start)){
          alert('".JText::_('START_VALUE_IS_NOT_NUMBER')."');
          return false;
        }
        end=cleanNumber($('end').value);
        $('end').value=end;
        if (!is_numeric(end)){
          alert('".JText::_('END_VALUE_IS_NOT_NUMBER')."');
          return false;
        }
        step=cleanNumber($('step').value);
        $('step').value=step;
        if ((!is_numeric(step))||(step<=0)){
          alert('".JText::_('STEP_VALUE_IS_NOT_NUMBER')."');
          return false;
        }
        if ((step>(end-start)){
          alert('".JText::_('STEP_VALUE_IS_TOO_BIG')."');
          return false;
        }
        return true;
      }
    ");
    
    parent::display();		
  }
}
?>
