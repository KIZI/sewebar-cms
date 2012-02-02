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
JLoader::import('LispMiner', JPATH_PLUGINS . DS . 'kbi' . DS . 'Integrators');

/**
 * Controller for sources administration.
 *
 * @package		com_kbi
 */
class KbiControllerRegisterlmserver extends JController
{
	/**
	 * Constructor
	 */
	function __construct( $config = array() )
	{
		parent::__construct( $config );
		// Register Extra tasks
		$this->registerTask( 'apply',	'save' );
	}

	function display()
	{
		$document =& JFactory::getDocument();
		$viewName = 'registerlmserver';
		$viewType = $document->getType();

		$view =& $this->getView($viewName, $viewType);

		// Get/Create the model
		if ($model = &$this->getModel('lmservers')) {
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
		$id = JRequest::getVar('id', array(0), 'method', 'array');
		$name = JRequest::getVar( 'name', '','post', 'string');
		$db_type = JRequest::getVar( 'type', '','post', 'string');
		$db_conf = JRequest::getVar( 'db', '','post', 'string', JREQUEST_ALLOWRAW );
		$dataDictionary = JRequest::getVar( 'dataDictionary', '','post', 'string', JREQUEST_ALLOWRAW );
		
		$this->setRedirect("index.php?option={$option}&controller=registerlmserver&id[]={$id[0]}");		

		try
		{	
			$model = &$this->getModel('lmservers');
			$server = $model->getLmserver($id[0]);
			
			$db_conf = json_decode($db_conf, true);
			$db_conf['type'] = $db_type;
			
			$miner = new LispMiner(array(
				'url' => $server->url,
			));
			
			$server_id = $miner->register($db_conf);

			// Import Data Dictionary
			$miner->importDataDictionary($dataDictionary, $server_id);
			
			$db		=& JFactory::getDBO();
			$table	=& JTable::getInstance('source', 'Table');			
			
			$table->name = $name;
			$table->url = $server->url;
			$table->type = 'LISPMINER';
			$table->method = 'POST';
			$table->params = json_encode(array(
				'miner_id' => $server_id
			));
			$table->dictionaryquery = '';
			
			//$table->dictionaryquery = 
			
			if (!$table->check()) {
				throw new Exception($table->getError());
			}
			if (!$table->store()) {
				throw new Exception($table->getError());
			}
			$table->checkin();

			$this->setRedirect("index.php?option={$option}&controller=sources");	
			$this->setMessage( JText::_( 'Server sucessfully registered.' ) );
		}
		catch (Exception $ex)
		{
			JFactory::getApplication ()->enqueueMessage ( JText::_( 'ERROR REGISTERING SERVER' ) . "<br />" . $ex->getMessage(), 'error' );
		}		
	}

	function cancel()
	{
		global $option;

		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$this->setRedirect( "index.php?option=$option&controller=lmservers" );
	}
}
