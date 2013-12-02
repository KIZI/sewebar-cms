<?php

jimport( 'joomla.application.component.view' );
                                  
/**
 * @package Joomla
 * @subpackage Config
 */
class articlesViewArticleslist extends JView
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
    
    
    $adminModel=$this->getModel('Admin','sewebarModel');  
                                        
    $userGroups=$adminModel->getUserGroups($this->parentUserGroupId,false); 
    $this->assignRef('userGroups',$userGroups); 		
    
    
    $articlesModel=$this->getModel('Articles','sewebarModel');
    
    $mainframe=JFactory::getApplication();
    $params=$mainframe->getParams();           

    
    $reportsCategory=$params->get('reportsCategory');
                                                                                                         
    $this->assignRef('reportsArticles',$articlesModel->getArticlesInCategoryByUsergroup($this->usergroupId,$reportsCategory,true));
    $this->assign('reportsCategory',$reportsCategory);
    $pmmlCategory=$params->get('pmmlCategory');
    $this->assignRef('pmmlArticles',$articlesModel->getArticlesInCategoryByUsergroup($this->usergroupId,$pmmlCategory,false,true));
    $this->assign('pmmlCategory',$pmmlCategory);
    
		//DEVNOTE:call parent display
    parent::display();		
  }
}
?>
