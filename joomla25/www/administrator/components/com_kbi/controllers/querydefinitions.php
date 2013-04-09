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
 * Controller for queries administration.
 *
 * @package com_kbi
 */
class KbiControllerQuerydefinitions extends JController
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
		KbiHelpers::addSubmenu('querydefinitions');

		global $option, $mainframe;
		$app = JFactory::getApplication('administrator');

		$document =& JFactory::getDocument();

		$view =& $this->getView(JRequest::getVar('controller', 'querydefinitions'), $document->getType());
		$model = &$this->getModel('querydefinitions');

		$user	=& JFactory::getUser();
		$context			= 'com_kbi.querydefinitions.list';
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

		$view =& $this->getView('queryDefinition', $document->getType());

		// Get/Create the model
		if ($model = &$this->getModel('querydefinitions')) {
			// Push the model into the view (as default)
			$view->setModel($model, true);
		}

		$model_sources = &$this->getModel('sources');
		$view->assignRef('sources', $model_sources->getAssocList());

		$model_queries = &$this->getModel('queries');
		$view->assignRef('queries', $model_queries->getAssocList());

		$model_xslts = &$this->getModel('xslts');
		$view->assignRef('xslts', $model_xslts->getAssocList());

		$view->setLayout('default');
		$view->display();
	}

	function save()
	{
		global $option;

		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$this->setRedirect( "index.php?option=$option&controller=querydefinitions" );

		// Initialize variables
		$db		=& JFactory::getDBO();
		$table	=& JTable::getInstance('queryDefinition', 'Table');

		if (!$table->bind( JRequest::get( 'post' ) )) {
			return JError::raiseWarning( 500, $table->getError() );
		}

		$table->name = JRequest::getVar( 'name', '','post', 'string' );
		$table->source = JRequest::getVar( 'sources', '','post', 'string', JREQUEST_ALLOWRAW );
		$table->query = JRequest::getVar( 'query', '','post', 'string', JREQUEST_ALLOWRAW );
		$table->icon = JRequest::getVar( 'icon', '','post', 'string', JREQUEST_ALLOWRAW );
		$table->xslt = JRequest::getVar( 'xslt', '','post', 'string', JREQUEST_ALLOWRAW );

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
				$this->setRedirect( "index.php?option=$option&controller=querydefinitions&task=edit&id[]={$table->id}" );
				break;
		}

		$this->setMessage( JText::_( 'Item Saved' ) );
	}

	function remove()
	{
		global $option;

		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$this->setRedirect( "index.php?option=$option&controller=querydefinitions" );

		// Initialize variables
		$db		=& JFactory::getDBO();
		$ids	= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$table	=& JTable::getInstance('query', 'Table');
		$n		= count( $ids );

		for ($i = 0; $i < $n; $i++)
		{
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

		$this->setRedirect( "index.php?option=$option&controller=querydefinitions" );

		// Initialize variables
		//$db			=& JFactory::getDBO();
		//$table		=& JTable::getInstance('bannerclient', 'Table');
		//$table->cid	= JRequest::getVar( 'cid', 0, 'post', 'int' );
		//$table->checkin();
	}

	function export()
	{
		global $option;
		$output_xml_file = realpath(dirname(__FILE__).'/../../../../xml/');
		$output_xml_file .= '/queries.xml';

		$queries = new SimpleXMLElement('<?xml version="1.0"?><queries></queries>');

		if ($model = &$this->getModel('querydefinitions')) {
			$ids = JRequest::getVar( 'cid', array(), 'request', 'array' );

			foreach ($ids as $id) {
				$qd = $model->getQuery($id);
				$query = $queries->addChild('query');
				$query->addAttribute('id', $qd->query);
				$query->addChild('name', $qd->name);
				$query->addChild('icon', $qd->icon);
				$query->addChild('source', '')
					->addAttribute('id', $qd->source);
				$query->addChild('xslt', '')
					->addAttribute('id', $qd->xslt);
			}
		}

		$queries->asXML($output_xml_file);

		$this->setMessage( JText::_( "Query definitions exported to $output_xml_file." ) );

		$this->setRedirect( "index.php?option=$option&controller=querydefinitions" );
	}
}
