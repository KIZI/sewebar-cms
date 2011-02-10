<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.view');
jimport('joomla.html.pagination');

class KbiViewDocumentsLocal extends JView
{
	function display($tpl = null)
	{
		JToolBarHelper::title(JText::_('PMML Documents') . " <small>[<em>local</em>]</small>", 'article.png');

		$doc = & JFactory::getDocument();
		if (JPATH_BASE!=JPATH_ADMINISTRATOR){
			$doc->addStyleSheet('components/com_ginclude/css/general.css');
			$doc->addStyleSheet('components/com_ginclude/css/component.css');
		}

		global $mainframe;
		$model = $this->getModel();

		$orderDir=JRequest::getCmd('filter_order_Dir','asc');
		if ($orderDir=='asc'){$orderDir2='desc';}else{$orderDir2='asc';}

		$limit = JRequest::getVar('limit',$mainframe->getCfg(list_limit));
		$limitstart=JRequest::getVar('limitstart',0);

		/*kontrola, jestli je v session nastavena sekce a kategorie -> pouze při prvním zobrazení*/
		if ((JRequest::getInt('section',-1)==-1)&&($_SESSION['ginclude']['section']>0)){
			JRequest::setVar('section',$_SESSION['ginclude']['section']);
			$_SESSION['ginclude']['section']=-1;
		}
		if ((JRequest::getInt('categorie',-1)==-1)&&($_SESSION['ginclude']['categorie']>0)){
			JRequest::setVar('categorie',$_SESSION['ginclude']['categorie']);
			$_SESSION['ginclude']['categorie']=-1;
		}
		/**/

		$articles = $model->getArticles(JRequest::getInt('section',-1),JRequest::getInt('categorie',-1),JRequest::getString('filter',''),JRequest::getCmd('filter_order','title'),JRequest::getCmd('filter_order_Dir','asc'),$limitstart,$limit);
		$this->assignRef('articles', $articles);

		$total=$model->getArticlesCount(JRequest::getInt('section',-1),JRequest::getInt('categorie',-1),JRequest::getString('filter',''));
		$this->assignRef('total', $total);

		$pageNav = new JPagination($total,$limitstart,$limit);
		$this->assignRef('pageNav', $pageNav);

		$sections = $model->getSections();
		$this->assignRef('sections', $sections);

		$categories = $model->getCategories(JRequest::getInt('section',-1));
		$this->assignRef('categories', $categories);

		parent::display($tpl);
	}
}

?>