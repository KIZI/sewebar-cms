<?php
/**
 * @version		$Id:$
 * @package		com_xsltmagic
 * @author		David Fišer
 * @copyright	Copyright (C) 2011 All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );

/**
 * Controller for XSTL magic menu.
 *
 * @package		com_xsltmagic
 */
class XsltMagicControllerMagic extends JController
{
    /**
     * Constructor
	 */
    function __construct( $config = array() ){
        parent::__construct( $config );
        // Register Extra tasks
        $this->registerTask( 'add',		'edit' );
        $this->registerTask( 'apply',	'save' );
    }

    /*! @function display
        @abstract display list of rules from database, basic view
    */
    function display(){
        $mainframe = JFactory::getApplication();
        $option="com_xsltmagic";
        $document =& JFactory::getDocument();
        $viewName = JRequest::getVar('controller', 'magic');
        $viewType = $document->getType();

        $view =& $this->getView($viewName, $viewType);
        $model = &$this->getModel('magic');
        
        // sorting and searching options
        $user =& JFactory::getUser();
        $context = 'com_xsltmagic.magic.list';
        $filter_order		= $mainframe->getUserStateFromRequest( $context.'filter_order',		'filter_order',		'name',	'cmd' );
        $filter_order_Dir	= $mainframe->getUserStateFromRequest( $context.'filter_order_Dir',	'filter_order_Dir',	'',			'word' );
        $search	= $mainframe->getUserStateFromRequest( $context.'search',			'search',			'',			'string' );
        $search	= JString::strtolower( $search );

        $limit = $mainframe->getUserStateFromRequest( 'global.list.limit',		'limit',		$mainframe->getCfg('list_limit'), 'int' );
        $limitstart	= $mainframe->getUserStateFromRequest( $context.'limitstart',	'limitstart',	0, 'int' );

        $orderby = ' ORDER BY '. $filter_order .' '. $filter_order_Dir .', id';
        $rows = $model->getList($total, $limitstart, $limit, $search, $orderby);

        jimport('joomla.html.pagination');
        $pageNav = new JPagination( $total, $limitstart, $limit );

        // table ordering
        $lists['order_Dir'] = $filter_order_Dir;
        $lists['order']	= $filter_order;

        // search filter
        $lists['search']= $search;

        $view->setLayout('default');

        $view->assignRef('rows', $rows);
        $view->assignRef('pageNav', $pageNav);
        $view->assignRef('lists', $lists);
        $view->display();
    }

    /*! @function edit
        @abstract function switch to detailed view (magi) initizializing data for edit
    */
    function edit(){
        $document =& JFactory::getDocument();
        $viewName = 'magi';
        $viewType = $document->getType();

        $view =& $this->getView($viewName, $viewType);

        // Get/Create the model
        if ($model = &$this->getModel('magic')) {
            // Push the model into the view (as default)
            $view->setModel($model, true);
        }

        $view->setLayout('default');
        $view->display();
	}

    /*! @function save
        @abstract Save edited/new data to database
    */
    function save(){
        $option="com_xsltmagic";

        // Check for request forgeries
        JRequest::checkToken() or jexit( 'Invalid Token' );

        $this->setRedirect( "index.php?option=$option&controller=magic" );

        // Initialize variables
        $db =& JFactory::getDBO();
        $table =& JTable::getInstance('magic', 'Table');

        if (!$table->bind( JRequest::get( 'post' ) )) {
            return JError::raiseWarning( 500, $table->getError() );
        }

        $table->rule = JRequest::getVar( 'rule', '','post', 'string', JREQUEST_ALLOWRAW );
        $table->source = JRequest::getVar( 'source', '','post', 'string', JREQUEST_ALLOWRAW );
    
        if (!$table->check()) {
            return JError::raiseWarning( 500, $table->getError() );
        }

        if (!$table->store()) {
            return JError::raiseWarning( 500, $table->getError() );
        }

        $table->checkin();

        switch (JRequest::getCmd( 'task' )){
            case 'apply':
                $this->setRedirect( "index.php?option=$option&controller=magic&task=edit&cid[]={$table->id}" );
            break;
        }

        $this->setMessage( JText::_( 'Item Saved' ) );
    }

    /*! @function remove
        @abstract remove selected rule(s) from database 
    */
	function remove(){
        $option="com_xsltmagic";

        // Check for request forgeries      
        JRequest::checkToken() or jexit( 'Invalid Token' );

        $this->setRedirect("index.php?option=$option&controller=magic");

        // Initialize variables
        $db	=& JFactory::getDBO();
        $ids = JRequest::getVar( 'cid', array(0), 'post', 'array' );
        $table =& JTable::getInstance('magic', 'Table');
        $n = count( $ids );

        for ($i = 0; $i < $n; $i++){
            if (!$table->delete( (int) $ids[$i] )){
                return JError::raiseWarning( 500, $table->getError() );
            }
        }

        $this->setMessage( JText::sprintf( 'Items removed', $n ) );
    }

    /*! @function cancel
        @abstract "back" function from detailed view, return to previous page
    */
	function cancel(){
        $option="com_xsltmagic";

        // Check for request forgeries
        JRequest::checkToken() or jexit( 'Invalid Token' );

        $this->setRedirect( "index.php?option=$option&controller=magic" );
    }
}
