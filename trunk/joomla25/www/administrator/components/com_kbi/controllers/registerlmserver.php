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
JLoader::import('LispMiner', JPATH_LIBRARIES . DS . 'kbi' . DS . 'Integrators');

/**
 * Controller for sources administration.
 *
 * @package		com_kbi
 */
class KbiControllerRegisterlmserver extends JController
{
	private $com_kbi = 'com_kbi';

	/**
	 * Constructor
	 */
	function __construct( $config = array() )
	{
		parent::__construct($config);

		// Register Extra tasks
		$this->registerTask('apply', 'save');
	}

	function display()
	{
		$document = JFactory::getDocument();
		$view = $this->getView('registerlmserver', $document->getType());

		// Get/Create the model
		if ($model = &$this->getModel('lmservers')) {
			// Push the model into the view (as default)
			$view->setModel($model, true);
		}

		$view->setLayout('default');
		$view->display();
	}

	function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$app = JFactory::getApplication();

		$id = JRequest::getVar('id', array(0), 'method', 'array');
		$name = JRequest::getVar( 'name', '','post', 'string');
		$db_type = JRequest::getVar( 'type', '','post', 'string');
		$db_conf = JRequest::getVar( 'db', '','post', 'string', JREQUEST_ALLOWRAW );
		$matrix_name = JRequest::getVar( 'matrix', '','post', 'string');
		$dataDictionary = JRequest::getVar( 'dataDictionary', '','post', 'string', JREQUEST_ALLOWRAW );
		
		$this->setRedirect("index.php?option={$this->com_kbi}&controller=registerlmserver&id[]={$id[0]}");

		try
		{	
			$model = $this->getModel('lmservers');
			$sources = $this->getModel('sources');
			$server = $model->getLmserver($id[0]);
			
			$db_conf = json_decode($db_conf, true);
			$db_conf['type'] = $db_type;
			
			$miner = new LispMiner(array(
				'url' => $server->url,
			));
			
			$server_id = $miner->register($db_conf);

			// Import Data Dictionary
			$miner->importDataDictionary($dataDictionary, $server_id);

			$data = array(
				'name' => $name,
				'url' => $server->url,
				'type' => 'LISPMINER',
				'method' => 'POST',
				'params' => json_encode(array(
					'miner_id' => $server_id,
					'matrix' => $matrix_name
				)),
				'dictionaryquery' => ''
			);
			
			$sources->save($data);

			$this->setRedirect("index.php?option={$this->com_kbi}&controller=sources");
			$this->setMessage(JText::_( 'Server sucessfully registered.'));
		}
		catch (Exception $ex)
		{
			$app->enqueueMessage(JText::_('ERROR REGISTERING SERVER' ) . "<br />" . $ex->getMessage(), 'error');

			// try to remove registered miner as it is in invalid state.
			if(isset($server_id) && isset($miner)) {
				try {
					$miner->unregister($server_id);
				}
				catch (Exception $innerException) {
					$app->enqueueMessage(JText::_('ERROR REMOVING REGISTERING SERVER') . "<br />" . $innerException->getMessage(), 'error');
				}
			}
		}		
	}

	function cancel()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$this->setRedirect("index.php?option={$this->com_kbi}&controller=lmservers");
	}
}
