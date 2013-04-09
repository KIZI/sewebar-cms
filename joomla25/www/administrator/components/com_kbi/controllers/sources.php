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
	/**
	 * Constructor
	 */
	function __construct( $config = array() )
	{
		parent::__construct( $config );
		// Register Extra tasks
		$this->registerTask( 'add',		'edit' );
		$this->registerTask( 'apply',	'save' );
	}

	function display()
	{
		KbiHelpers::addSubmenu('sources');

		global $option, $mainframe;
		$app = JFactory::getApplication('administrator');

		$document =& JFactory::getDocument();
		$viewName = JRequest::getVar('controller', 'sources');
		$viewType = $document->getType();
		$view =& $this->getView($viewName, $viewType);
		$model = &$this->getModel('sources');

		$user	=& JFactory::getUser();
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
		$document =& JFactory::getDocument();
		$viewName = 'source';
		$viewType = $document->getType();

		$view =& $this->getView($viewName, $viewType);

		// Get/Create the model
		if ($model = &$this->getModel('sources')) {
			// Push the model into the view (as default)
			$view->setModel($model, true);
		}


		//var_dump($view);

		/*$model = NULL;

		if(!JError::isError($model)) {
			$view->setModel($model, true);
		}*/

		$view->setLayout('default');
		$view->display();
	}

	function save()
	{
		global $option;

		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$this->setRedirect( "index.php?option=$option" );

		// Initialize variables
		$db		=& JFactory::getDBO();
		$table	=& JTable::getInstance('source', 'Table');

		if (!$table->bind( JRequest::get( 'post' ) )) {
			return JError::raiseWarning( 500, $table->getError() );
		}

		$table->dictionaryquery = JRequest::getVar( 'dictionaryquery', '','post', 'string', JREQUEST_ALLOWRAW );

		if (!$table->check()) {
			return JError::raiseWarning( 500, $table->getError() );
		}
		if (!$table->store()) {
			return JError::raiseWarning( 500, $table->getError() );
		}
		$table->checkin();

		switch (JRequest::getCmd( 'task' ))
		{
			case 'apply':
				$this->setRedirect( "index.php?option=$option&task=edit&id[]={$table->id}" );
				break;
		}

		$this->setMessage( JText::_( 'Item Saved' ) );
	}

	function remove()
	{
		global $option;

		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$this->setRedirect( "index.php?option=$option" );

		// Initialize variables
		$db		=& JFactory::getDBO();
		$ids	= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$table	=& JTable::getInstance('source', 'Table');
		$n		= count( $ids );

		for ($i = 0; $i < $n; $i++)
		{
			$sources = &$this->getModel('sources');
			$source = $sources->getSource((int) $ids[$i]);

			// delete miner if it is LISpMiner
			if($source && $source->type == 'LISPMINER') {
				try {
					$config = get_object_vars($source);

					JLoader::import('KBIntegrator', JPATH_PLUGINS . DS . 'kbi');

					$miner = KBIntegrator::create($config);
					$miner->unregister();
				} catch (Exception $ex) {
					// Just log it
					KBIDebug::log($ex->getMessage());
				}
			}

			if (!$table->delete( (int) $ids[$i] ))
			{
				return JError::raiseWarning( 500, $table->getError() );
			}
		}

		$this->setMessage( JText::sprintf( 'Items removed', $n ) );
	}

	function cancel()
	{
		global $option;

		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$this->setRedirect( "index.php?option=$option" );

		// Initialize variables
		//$db			=& JFactory::getDBO();
		//$table		=& JTable::getInstance('bannerclient', 'Table');
		//$table->cid	= JRequest::getVar( 'cid', 0, 'post', 'int' );
		//$table->checkin();
	}

	function export()
	{
		global $option;

		JRequest::checkToken() or jexit( 'Invalid Token' );

		$controller = JRequest::getVar('controller');
		$link = "index.php?option=$option&controller=$controller&task=renderexport&format=json";
		foreach(JRequest::getVar( 'cid', array(0), 'post', 'array' ) as $id)
			$link .= "&cid[]=$id";

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

		JResponse::setHeader( 'Content-Disposition', 'attachment; filename="'.$filename.'.json"' );

		$view->display();
	}
}
