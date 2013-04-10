<?php
/**
 * @version		$Id$
 * @package		com_kbi
 * @author		Andrej Hazucha
 * @copyright	Copyright (C) 2010 All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );

/**
 * Controller for sources administration.
 *
 * @package		com_kbi
 */
class KbiControllerSources extends JController
{
	private $com_kbi = 'com_kbi';

	/**
	 * Constructor
	 */
	function __construct( $config = array() )
	{
		parent::__construct($config);

		// Register Extra tasks
		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');
	}

	function display()
	{
		KbiHelpers::addSubmenu('sources');

		$app = JFactory::getApplication('administrator');
		$document = JFactory::getDocument();
		$view = $this->getView('sources', $document->getType());
		$model = $this->getModel('sources');

		$context			= 'com_kbi.sources.list';
		$filter_order		= $app->getUserStateFromRequest( $context.'filter_order',		'filter_order',		'name',	'cmd' );
		$filter_order_Dir	= $app->getUserStateFromRequest( $context.'filter_order_Dir',	'filter_order_Dir',	'',			'word' );
		$search				= $app->getUserStateFromRequest( $context.'search',			'search',			'',			'string' );
		$search				= JString::strtolower( $search );

		$limit		= $app->getUserStateFromRequest( 'global.list.limit',		'limit',		$app->getCfg('list_limit'), 'int' );
		$limitstart	= $app->getUserStateFromRequest( $context.'limitstart',	'limitstart',	0, 'int' );

		$orderby = ' ORDER BY '. $filter_order .' '. $filter_order_Dir .', id';

		$rows = $model->getList($total, $limitstart, $limit, $search, $orderby);

		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $limitstart, $limit );

		// table ordering
		$lists['order_Dir']	= $filter_order_Dir;
		$lists['order']		= $filter_order;

		// search filter
		$lists['search']= $search;

		$view->setLayout('default');

		$view->assignRef('rows', $rows);
		$view->assignRef('pageNav', $pageNav);
		$view->assignRef('lists', $lists);
		$view->display();
	}

	function edit()
	{
		$document = JFactory::getDocument();
		$view = $this->getView('source', $document->getType());

		// Get/Create the model
		if ($model = $this->getModel('sources')) {
			// Push the model into the view (as default)
			$view->setModel($model, true);
		}

		$view->setLayout('default');
		$view->display();
	}

	function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		$app = JFactory::getApplication();

		try
		{
			$this->setRedirect("index.php?option={$this->com_kbi}");

			$model = $this->getModel('sources');

			$data = JRequest::get('post');
			$data['dictionaryquery'] = JRequest::getVar('dictionaryquery', '','post', 'string', JREQUEST_ALLOWRAW);

			$table = $model->save($data);

			switch (JRequest::getCmd('task'))
			{
				case 'apply':
					$this->setRedirect("index.php?option={$this->com_kbi}&task=edit&id[]={$table->id}");
					break;
			}

			$this->setMessage(JText::_('Item Saved'));
		}
		catch(Exception $ex)
		{
			$app->enqueueMessage(JText::_('ERROR SAVING KBI SOURCE' ) . "<br />" . $ex->getMessage(), 'error');
		}
	}

	function remove()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		$this->setRedirect("index.php?option={$this->com_kbi}");

		$app = JFactory::getApplication();
		$ids = JRequest::getVar('cid', array(0), 'post', 'array');
		$n = count($ids);
		$sources = &$this->getModel('sources');

		for ($i = 0; $i < $n; $i++) {
			try {
				$id = (int) $ids[$i];
				$sources->remove($id);

				$app->enqueueMessage(JText::sprintf('SOURCE REMOVED (%d)', $id));
			} catch (Exception $ex) {
				$app->enqueueMessage(JText::_('ERROR SAVING KBI SOURCE' ) . "<br />" . $ex->getMessage(), 'error');
			}
		}
	}

	function cancel()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		$this->setRedirect("index.php?option={$this->com_kbi}");
	}

	function export()
	{
		JRequest::checkToken() or jexit('Invalid Token');

		$controller = JRequest::getVar('controller');
		$link = "index.php?option={$this->com_kbi}&controller=$controller&task=renderexport&format=json";

		foreach(JRequest::getVar( 'cid', array(0), 'post', 'array' ) as $id) {
			$link .= "&cid[]=$id";
		}

		$this->setRedirect($link);
	}

	function renderexport()
	{
		// Check for request forgeries
		$document =& JFactory::getDocument();
		$document->setMimeEncoding('application/json');

		$viewName = JRequest::getVar('view', 'export');
		$viewType = 'json';
		$view =& $this->getView($viewName, $viewType);
		$filename = $view->getName();

		$sources = array();

		if ($model = &$this->getModel('sources')) {
			$filename = $model->getName();
			$ids = JRequest::getVar('cid', array(), 'request', 'array');

			foreach ($ids as $id) {
				$sources[] = $model->getSource($id);
			}
		}

		$json = json_encode($sources);

		$view->assignRef('rows', $json);

		JResponse::setHeader('Content-Disposition', 'attachment; filename="'.$filename.'.json"');

		$view->display();
	}
}
