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
jimport( 'joomla.error.log' );

require_once (JPATH_COMPONENT_ADMINISTRATOR . DS . 'helpers/jucene.php');

/**
 * Basic API class for using together with the Jucene Search Component and plugins
 * @author Lukáš Beránek
 *
 */

class JuceneControllerApi extends JController {
	
	var $action;
	
	/**
	 * 
	 */
	function __construct($config = array()) {
		$action = JRequest::getVar ( 'action', 'getDocuments' ); 
		$this->setAction ( $action );
		try {
			if(!method_exists($this, $action)){
				throw new Exception(JText::sprintf('Method "%s" does not exist.',$action));
			}
		} catch (Exception $e) {
			//print $e->getMessage();
		}
		parent::__construct($config);
		//$this->validateAccess();
	}
	/**
	 * This method controlles the display methods
	 */
	function display($tpl = null) {
		$view_class_name = strtolower(str_replace(CLASS_NAME,"",get_class($this)));
		
		$viewName = JRequest::getVar ( 'view', $view_class_name );
		$viewType = 'raw';
		
		$view = & $this->getView ( $viewName, $viewType );
		$action = $this->getAction();
		
		try {
			$view->assignRef ( 'value', $this->$action() );
			//throw new Exception(JText::sprintf('Method "%s" does not exist.',$action));
		} catch (Exception $e) {
			print $e->getMessage();
			$this->log("error",$e->getMessage());
		}
		
		$view->display ();
		//parent::display ($tpl);
	}
	public function __call($method, $args)
    {
        if(method_exists($this, $method)) {
          return call_user_func_array(array($this, $method), $args);
        }else{
          throw new Exception(sprintf('The required method "%s" does not exist for %s', $method, get_class($this)));
        }
    } 
    
	function validateAccess(){
		/*$key = JRequest::getVar ( 'key', NULL );
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
		}*/
	}
	function getAction() {
		return $this->action;
	}
	
	function setAction($action) {
		$this->action = $action;
	}	
	
	function getIndex() {
		return "Index";
	}
	
	function createIndex() {
	
	}
	
	function query() {
	
	}
	
	function addDocument($doc, $additional, $specific_index = NULL, $path = false) {
		return "<response></response>";
	}
	
	function deleteDocument() {
	
	}
	function getDocuments() {
		/*return "<result milisecs=\"39.0\">
<docs count=\"80\"><doc joomlaID=\"\" timestamp=\"Thu Jun 16 10:31:55 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s00-ClientAAILoanIMPLIEDCONDYear31-41.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 10:34:16 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s00-ClientAAILoanIMPLIEDCONDYear41-51.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 10:36:55 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s00-ClientAAILoanIMPLIEDCONDYear51-61.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 10:28:44 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s00-ClientAAILoanIMPLIEDCONDYear61-71.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 11:34:46 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s01-ClientAAILoanIMPLIEDCONDYear31-41.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 11:29:23 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s01-ClientAAILoanIMPLIEDCONDYear41-51.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 11:32:09 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s01-ClientAAILoanIMPLIEDCONDYear51-61.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 11:26:06 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s01-ClientAAILoanIMPLIEDCONDYear61-71.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 11:49:37 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s02-ClientAAILoanIMPLIEDCONDYear31-41.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 11:46:50 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s02-ClientAAILoanIMPLIEDCONDYear41-51.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 11:44:13 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s02-ClientAAILoanIMPLIEDCONDYear51-61.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 11:41:01 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s02-ClientAAILoanIMPLIEDCONDYear61-71.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 12:02:04 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s03-ClientAAILoanIMPLIEDCONDYear31-41.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 12:04:31 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s03-ClientAAILoanIMPLIEDCONDYear41-51.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 11:59:26 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s03-ClientAAILoanIMPLIEDCONDYear51-61.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 11:56:14 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s03-ClientAAILoanIMPLIEDCONDYear61-71.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 12:15:13 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s04-ClientAAILoanIMPLIEDCONDYear31-41.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 12:12:26 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s04-ClientAAILoanIMPLIEDCONDYear41-51.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 12:09:47 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s04-ClientAAILoanIMPLIEDCONDYear51-61.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 12:17:40 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s04-ClientAAILoanIMPLIEDCONDYear61-71.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 12:49:59 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s05-ClientAAILoanIMPLIEDCONDYear31-41.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 12:44:34 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s05-ClientAAILoanIMPLIEDCONDYear41-51.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 12:47:21 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s05-ClientAAILoanIMPLIEDCONDYear51-61.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 12:52:26 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s05-ClientAAILoanIMPLIEDCONDYear61-71.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 13:06:39 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s06-ClientAAILoanIMPLIEDCONDYear31-41.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 13:00:39 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s06-ClientAAILoanIMPLIEDCONDYear41-51.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 12:58:01 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s06-ClientAAILoanIMPLIEDCONDYear51-61.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 13:03:26 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s06-ClientAAILoanIMPLIEDCONDYear61-71.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 13:12:32 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s07-ClientAAILoanIMPLIEDCONDYear31-41.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 13:20:49 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s07-ClientAAILoanIMPLIEDCONDYear41-51.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 13:18:10 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s07-ClientAAILoanIMPLIEDCONDYear51-61.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 13:14:58 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s07-ClientAAILoanIMPLIEDCONDYear61-71.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 13:35:34 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s08-ClientAAILoanIMPLIEDCONDYear31-41.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 13:26:56 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s08-ClientAAILoanIMPLIEDCONDYear41-51.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 13:32:55 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s08-ClientAAILoanIMPLIEDCONDYear51-61.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 13:29:43 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s08-ClientAAILoanIMPLIEDCONDYear61-71.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 13:46:59 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s09-ClientAAILoanIMPLIEDCONDYear31-41.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 13:44:12 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s09-ClientAAILoanIMPLIEDCONDYear41-51.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 13:49:27 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s09-ClientAAILoanIMPLIEDCONDYear51-61.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 13:40:57 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s09-ClientAAILoanIMPLIEDCONDYear61-71.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 14:00:14 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s10-ClientAAILoanIMPLIEDCONDYear31-41.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 14:02:42 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s10-ClientAAILoanIMPLIEDCONDYear41-51.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 14:05:29 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s10-ClientAAILoanIMPLIEDCONDYear51-61.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 14:08:08 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s10-ClientAAILoanIMPLIEDCONDYear61-71.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 14:14:42 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s11-ClientAAILoanIMPLIEDCONDYear31-41.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 14:20:22 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s11-ClientAAILoanIMPLIEDCONDYear41-51.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 14:23:10 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s11-ClientAAILoanIMPLIEDCONDYear51-61.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 14:17:09 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s11-ClientAAILoanIMPLIEDCONDYear61-71.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 14:37:09 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s12-ClientAAILoanIMPLIEDCONDYear31-41.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 14:31:09 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s12-ClientAAILoanIMPLIEDCONDYear41-51.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 14:28:30 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s12-ClientAAILoanIMPLIEDCONDYear51-61.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 14:33:56 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s12-ClientAAILoanIMPLIEDCONDYear61-71.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 14:44:18 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s13-ClientAAILoanIMPLIEDCONDYear31-41.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 14:46:47 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s13-ClientAAILoanIMPLIEDCONDYear41-51.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 14:49:34 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s13-ClientAAILoanIMPLIEDCONDYear51-61.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 14:52:12 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s13-ClientAAILoanIMPLIEDCONDYear61-71.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 15:10:08 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s14-ClientAAILoanIMPLIEDCONDYear31-41.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 15:01:27 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s14-ClientAAILoanIMPLIEDCONDYear41-51.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 15:04:14 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s14-ClientAAILoanIMPLIEDCONDYear51-61.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 15:06:53 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s14-ClientAAILoanIMPLIEDCONDYear61-71.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 15:17:53 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s15-ClientAAILoanIMPLIEDCONDYear31-41.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 15:20:21 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s15-ClientAAILoanIMPLIEDCONDYear41-51.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 15:15:14 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s15-ClientAAILoanIMPLIEDCONDYear51-61.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 15:23:09 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s15-ClientAAILoanIMPLIEDCONDYear61-71.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 15:35:16 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s16-ClientAAILoanIMPLIEDCONDYear31-41.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 15:29:14 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s16-ClientAAILoanIMPLIEDCONDYear41-51.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 15:37:44 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s16-ClientAAILoanIMPLIEDCONDYear51-61.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 15:32:03 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s16-ClientAAILoanIMPLIEDCONDYear61-71.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 15:52:02 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s17-ClientAAILoanIMPLIEDCONDYear31-41.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 15:43:24 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s17-ClientAAILoanIMPLIEDCONDYear41-51.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 15:49:23 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s17-ClientAAILoanIMPLIEDCONDYear51-61.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 15:46:11 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s17-ClientAAILoanIMPLIEDCONDYear61-71.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 15:59:46 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s18-ClientAAILoanIMPLIEDCONDYear31-41.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 15:56:58 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s18-ClientAAILoanIMPLIEDCONDYear41-51.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 16:02:14 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s18-ClientAAILoanIMPLIEDCONDYear51-61.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 16:04:51 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s18-ClientAAILoanIMPLIEDCONDYear61-71.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 16:13:44 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s19-ClientAAILoanIMPLIEDCONDYear31-41.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 16:10:58 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s19-ClientAAILoanIMPLIEDCONDYear41-51.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 16:19:21 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s19-ClientAAILoanIMPLIEDCONDYear51-61.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 16:16:09 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s19-ClientAAILoanIMPLIEDCONDYear61-71.xml</doc></docs>

</result>";*/
		$db = & JFactory::getDBO ();
		
		$query = "SELECT * 
    			  FROM " . $db->nameQuote ( '#__jucene_documents' ).";";
		
		$db->setQuery($query);
		$row = $db->loadObjectList();
		$result = json_encode($row);
		
		return $result;
	}
	
	function log($type, $message, $code = null){
		$log = &JLog::getInstance('com_jucene.log.php');
		$log->addEntry(array('LEVEL' => $type,'COMMENT' => $message));
	}
}