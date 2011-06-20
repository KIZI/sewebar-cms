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
defined ( '_JEXEC' ) or die ( 'Restricted acces' );
define ( 'JUCENE_ENCODING', 'UTF-8' );
DEFINE('API_VERSION', 1.0);
DEFINE('CLASS_NAME', 'JuceneController');

jimport ( 'joomla.application.component.controller' );

require_once (JPATH_COMPONENT_ADMINISTRATOR . DS . 'helpers/jucene.php');

/**
 * Basic API class for using together with the Jucene Search Component and plugins
 * @author Lukáš Beránek
 *
 */

class JuceneControllerApi extends JController {
	
	var $task;
	
	/**
	 * 
	 */
	function __construct() {
		$this->validateAccess();
	}
	
	function validateAccess(){
		$key = JRequest::getVar ( 'key', NULL );
		$ip = $_SERVER["HTTP_CLIENT_IP"];
		
		$db = & JFactory::getDBO ();
		$query = "SELECT * 
    FROM " . $db->nameQuote ( '#__jucene_api' )."
    WHERE" . $db->quote('ip') . "=" . $ip . "
    AND"   . $db->quote('key'). "=" . $key;
		$db->setQuery($query);
		$row = $db->loadRowList();
		if(count($row)!=1){
			$msg = "unrestricted acces or ambiguous name/host/username";
			$this->log("access", $msg);
			die($msg);
		}
	}
	function getTask() {
		return $this->task;
	}
	
	function setTask($task) {
		$this->task = $task;
	}
	
	function display($tpl = null) {
		$view_class_name = strtolower(str_replace(CLASS_NAME,"",get_class($this)));
		
		$viewName = JRequest::getVar ( 'view', $view_class_name );
		$viewType = 'raw';
		$view = & $this->getView ( $viewName, $viewType );
		
		$task = JRequest::getVar ( 'task', 'getDocuments', 'post' );
		$this->setTask ( $task );
		$view->assignRef ( 'value', $this->$task () );
		$view->display ();
		//parent::display ($tpl);
	}
	
	
	function getIndex() {
	
	}
	
	function createIndex() {
	
	}
	
	function query() {
	
	}
	
	function indexDocument() {
	
	}
	
	function deleteDocument() {
	
	}
	function getDocuments() {
		$db = & JFactory::getDBO ();
		$query = "SELECT * 
    FROM " . $db->nameQuote ( '#__jucene_documents' ).";";
		$db->setQuery($query);
		$row = $db->loadRowList();
		$result = json_encode($row);
	}
	
	function log($type, $message, $code = null){
		
	}
}