<?php

jimport( 'joomla.application.component.view' );
                                  
/**
 * @package Joomla
 * @subpackage Config
 */
class articlesViewMyarticles extends JView
{
	/**
	 * Display the view
	 */
	function display()
	{
		//DEVNOTE: set document title
		$document = & JFactory::getDocument();
		$document->setTitle( JText::_('MANAGE_REPORTS').' '.@$this->userGroup->title);

    JHtml::stylesheet('main.css','media/com_sewebar_vyuka/css/');
    JHTML::_('behavior.modal');
    $document->addScriptDeclaration("function closeSqueezeBox(){SqueezeBox.close();location.reload();}");
    
    $articlesModel=$this->getModel('Articles','sewebarModel');
    
    $mainframe=JFactory::getApplication();
    $params=$mainframe->getParams();           

    $reportsCategory=$params->get('reportsCategory');                                                                                                     
    $this->assignRef('reportsArticles',$articlesModel->getArticlesInCategory($reportsCategory,true));
    $this->assign('reportsCategory',$reportsCategory);
    $pmmlCategory=$params->get('pmmlCategory');
    $this->assignRef('pmmlArticles',$articlesModel->getArticlesInCategory($pmmlCategory,false,true));
    $this->assign('pmmlCategory',$pmmlCategory);
    
		//DEVNOTE:call parent display
    parent::display();		
  }
}
?>
