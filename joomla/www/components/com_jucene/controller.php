<?php
/**
 * @version		$Id: jucene.php
 * @package		Joomla
 * @subpackage	Jucene
 * @copyright	Copyright (C) 2005 - 2010 Lukáš Beránek. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.controller');

/**
 * Search Component Controller
 *
 * @package		Joomla
 * @subpackage	Search
 * @since 1.5
 */
class JuceneController extends JController
{
	/**
	 * Method to show the search view
	 *
	 * @access	public
	 * @since	1.5
	 */
	function display()
	{
		JRequest::setVar('view','jucene'); //
		parent::display();
	}

	function search()
	{
		$searchword = trim(JRequest::getString('searchword', null, 'post'));


		$post['searchword'] = $searchword;
		$post['sorting']	= JRequest::getWord('sorting', 'SORT_STRING', 'post');
		$post['ordering']	= JRequest::getWord('ordering', null, 'post');
		$post['limit']  = JRequest::getInt('limit', null, 'post');
		if($post['limit'] === null) unset($post['limit']);

		//from com_search No need to guess Itemid if it's already present in the URL
		if (JRequest::getInt('Itemid') > 0) {
			$post['Itemid'] = JRequest::getInt('Itemid');
		} else {

			// set Itemid id for links
			$menu = &JSite::getMenu();
			$items	= $menu->getItems('link', 'index.php?option=com_jucene&view=jucene');

			if(isset($items[0])) {
				$post['Itemid'] = $items[0]->id;
			}

		}

		unset($post['task']);
		unset($post['submit']);

		$uri = JURI::getInstance();
		$uri->setQuery($post);
		$uri->setVar('option', 'com_jucene');


		$this->setRedirect(JRoute::_('index.php'.$uri->toString(array('query', 'fragment')), false));
	}

	function arsearch()
	{
		$viewName = JRequest::getVar('view', 'arsearch');
		$viewType = 'raw';

		$view =& $this->getView($viewName, $viewType);

		/*$config = array(
			'source' => JRequest::getVar('source', NULL),
			'query' => JRequest::getVar('query', NULL),
			'xslt' => JRequest::getVar('xslt', NULL),
			'parameters' => JRequest::getVar('parameters', NULL)
		);

		$model = new KbiModelTransformator($config);
		$view->assignRef('value', $model->transform());*/


		$searchword = JRequest::getVar('searchword', '', 'post', 'string', JREQUEST_ALLOWRAW);

		require '/Volumes/Data/svn/svn.rewko.eu/sewebar/www/components/com_jucene/models/jucene.php';

		$jucene = new JuceneModelJucene();
		$result = $jucene->getData($searchword);

		$view->assignRef('results', $result);

		$view->display();
	}
}
