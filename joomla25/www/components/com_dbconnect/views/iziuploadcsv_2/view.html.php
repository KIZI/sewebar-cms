<?php
jimport( 'joomla.application.component.view' );
                       
/**
 * @package Joomla
 * @subpackage Config
 */
class iziViewIziUploadCSV_2 extends JView
{
	/**
	 * Display the view
	 */
	function display()
	{
		JHtml::stylesheet('izi.css','media/com_dbconnect/css/');
    
		$document = & JFactory::getDocument();
		$document->setTitle( JText::_('UPLOAD_CSV_FILE') );
                                                                
    JHtml::script('','',true);
    
    $document->addScriptDeclaration("
        var lang={'LOADING':'Loading'};
        var ajaxUrl='".JRoute::_('index.php?option=com_dbconnect&controller=izi&format=raw&task=uploadCSV_getData&file='.$this->fileData->id,false)."';");    
		JHtml::script('iziuploadcsv_2.js','media/com_dbconnect/js/');
    
    
    parent::display();		
  }
}
?>
