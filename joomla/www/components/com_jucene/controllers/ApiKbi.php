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

/**
 * Specific Jucene Api Controller made for KBI usage
 *
 *
 * @author bery
 *
 */
class JuceneControllerApiKbi extends JuceneControllerApi {
	
	/**
	 *
	 */
	var $_index = null;
	var $task;
	/**
	 *
	 * @param $conf
	 * @return unknown_type
	 */
	function __construct($conf = array()) {
		parent::__construct ( $conf );
	
	}
	/**
	 * This method controlles the display methods
	 */
	function display($tpl = null) {
		$viewName = JRequest::getVar ( 'view', 'apikbi' );
		$viewType = 'raw';
		$view = & $this->getView ( $viewName, $viewType );
		
		$task = JRequest::getVar ( 'task', 'getDocuments', 'post' );
		$this->setTask ( $task );
		$view->assignRef ( 'value', $this->$task () );
		$view->display ();
		//parent::display ($tpl);
	}
	/**
	 * Getter method for the task being performed
	 */
	function getTask() {
		return $this->task;
	}
	
	/**
	 * Setter method for the task being performed
	 * @param $task
	 */
	function setTask($task) {
		$this->task = $task;
	}
	/**
	 * PMML document and joomla document additional fields form jos_content
	 * (e.g. title id (known as pk [primary key], keywords, path alias).. etc.)
	 * 
	 * @param $joomla_doc
	 */
	function insertToIndexKbi($joomla_xml_doc, $additional, $specific_index = NULL, $path = false) {
		
		$xml_doc = $path ? file_get_contents ( $joomla_xml_doc ) : (substr ( $joomla_xml_doc ['fulltext'], 0, 5 ) != '<?xml') ? $joomla_xml_doc ['introtext'] : $joomla_xml_doc ['fulltext'];
		
		//prepare new Dom
		$dom = new DOMDocument ();
		
		//Make ready for the transformation
		$xslt = new DOMDocument ();
		
		$error = false;
		//load xslt stylesheet
		if (! @$xslt->load ( JPATH_SITE . DS . 'administrator' . DS . 'components' . DS . 'com_jucene' . DS . 'xslt/jucene.xsl' )) {
			$error = true;
			$this->raiseMessage ( "XSLTLOADERROR", 'error' );
		}
		
		$proc = new XSLTProcessor ();
		if (! $proc->importStylesheet ( $xslt )) {
			$error = true;
			$this->raiseMessage ( "XSLTIMPORTERROR", 'error' );
		}
		
		if ($dom->loadXML ( $xml_doc ) && ! $error) {
			
			//simplify the document - prepare it for the indexation process
			$xslOutput = $proc->transformToXml ( $dom );
			
			//create new DOM document to preserve output and transform the XML to the indexable one
			$transXml = new DOMDocument ();
			$transXml->preserveWhitespace = false;
			@$transXml->loadXML ( $xslOutput );
			//unset unneccessary variables
			unset ( $xslOutput );
			unset ( $dom );
			unset ( $xslt );
			
			//index every assoc rule as document with same credentials
			if (! $error) {
				
				$rules = $transXml->getElementsByTagName ( "AssociationRule" );
				$rulesCount = $rules->length;
				if ($rulesCount == 0) {
					$error = true;
					$this->raiseMessage ( 'XMLDOCUMENTNORULES', 'error' );
				}
				
				$rule_doc_position = 0;
				
				foreach ( $rules as $rule ) {
					$additional ['rating'] = 0;
					$additional ['position'] = $rule_doc_position;
					JPluginHelper::importPlugin ( 'content' );
					$dispatcher = & JDispatcher::getInstance ();
					$results = $dispatcher->trigger ( 'onIndexPmml', array ($rule, $additional ) );
					$rule_doc_position ++;
				}
			}
			return true;
		} else {
			return false;
		}
	}
	
	function deleteFromIndexKbi(array $doc_ids, $specific_index = NULL) {
		if (count ( $doc_ids ) > 0) {
			foreach ( $doc_ids as $id ) {
				JuceneHelper::removeFromIndexById ( $id );
			}
		}
	}
	
	/**
	 * 
	 * @param $doc_id
	 * @param $new_content
	 */
	function updateIndexDocumentKbi($doc_id, $new_content, $specific_index = NULL) {
		if (is_numeric ( $doc_id ) && JuceneHelper::stringContains ( $new_content, "<?xml", false )) {
			JuceneHelper::removeFromIndexById ( $doc_id );
			return $this->insertToIndexKbi ( $new_content, $specific_index );
		} else {
			return false;
		}
	}
	
	/**
	 * 
	 * @param $conditions
	 * array of conditions in form of fied => value
	 */
	function searchKbi(DOMDocument $ar_query, $specific_index = NULL) {
		if (! JuceneHelper::stringContains ( $ar_query, "<?xml" )) {
			return "";
		}
		
		$xslt = new DOMDocument ();
		
		if (! @$xslt->load ( JPATH_SITE . DS . 'administrator' . DS . 'components' . DS . 'com_jucene' . DS . 'xslt/ARQuery.xsl' )) {
			$error = true;
			$this->raiseMessage ( "XSLTLOADERROR", 'error' );
		
		}
		
		$xslt_proc = new XSLTProcessor ();
		
		if (! $xslt_proc->importStylesheet ( $xslt )) {
			$error = true;
			$this->raiseMessage ( "XSLTIMPORTERROR", 'error' );
		}
		
		$ar_query_dom = new DOMDocument ();
		
		if ($ar_query_dom->loadXML ( $ar_query )) {
			
			$query = $xslt_proc->transformToXml ( $ar_query );
		
		}
		
		$index = (is_null ( $specific_index )) ? JuceneHelper::getIndex () : JuceneHelper::getIndex ( $specific_index );
		
		try {
			$results = $index->find ( $query );
		} catch ( Exception $e ) {
			echo $e->getMessage ();
		}
		
		if (count ( $results ) > 0) {
			//TODO make the results XML
			$results_xml = new DOMDocument ();
			$results_xml->formatOutput = true;
			$search_result = $results_xml->appendChild ( 'SearchResult' );
			$hits = $search_result->appendChild ( 'Hits' );
			foreach ( $results as $field_name => $value ) {
				$hit = $hits->appendChild ( 'Hit' );
				$hitValue = $results_xml->createElement ( $field_name, $value );
				$hit->appendChild ( $hitValue );
			}
			$xml = $results_xml->saveXML ();
		}
		return $xml;
	
	}
	
	function modifyIndexKbi($op, $specific_index = NULL) {
		
		$op = strtolower ( $op );
		$op_allowed = array ('create', 'update', 'delete' );
		
		if (! in_array ( $op_allowed, $op )) {
			throw new Exception ( 'Operation must be one of create, delete, update', 1 );
		}
		switch ($op) {
			case "create" :
				break;
			case "delete" :
				break;
			case "update" :
				break;
			default :
				throw new Exception ( "Operation must be specified.", 1 );
				break;
		}
	}
	
	function getDocuments() {
		
		return $result;
		/*return "<result milisecs=\"39.0\">
<docs count=\"80\"><doc joomlaID=\"\" timestamp=\"Thu Jun 16 10:31:55 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s00-ClientAAILoanIMPLIEDCONDYear31-41.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 10:34:16 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s00-ClientAAILoanIMPLIEDCONDYear41-51.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 10:36:55 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s00-ClientAAILoanIMPLIEDCONDYear51-61.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 10:28:44 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s00-ClientAAILoanIMPLIEDCONDYear61-71.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 11:34:46 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s01-ClientAAILoanIMPLIEDCONDYear31-41.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 11:29:23 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s01-ClientAAILoanIMPLIEDCONDYear41-51.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 11:32:09 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s01-ClientAAILoanIMPLIEDCONDYear51-61.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 11:26:06 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s01-ClientAAILoanIMPLIEDCONDYear61-71.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 11:49:37 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s02-ClientAAILoanIMPLIEDCONDYear31-41.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 11:46:50 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s02-ClientAAILoanIMPLIEDCONDYear41-51.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 11:44:13 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s02-ClientAAILoanIMPLIEDCONDYear51-61.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 11:41:01 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s02-ClientAAILoanIMPLIEDCONDYear61-71.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 12:02:04 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s03-ClientAAILoanIMPLIEDCONDYear31-41.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 12:04:31 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s03-ClientAAILoanIMPLIEDCONDYear41-51.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 11:59:26 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s03-ClientAAILoanIMPLIEDCONDYear51-61.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 11:56:14 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s03-ClientAAILoanIMPLIEDCONDYear61-71.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 12:15:13 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s04-ClientAAILoanIMPLIEDCONDYear31-41.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 12:12:26 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s04-ClientAAILoanIMPLIEDCONDYear41-51.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 12:09:47 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s04-ClientAAILoanIMPLIEDCONDYear51-61.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 12:17:40 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s04-ClientAAILoanIMPLIEDCONDYear61-71.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 12:49:59 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s05-ClientAAILoanIMPLIEDCONDYear31-41.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 12:44:34 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s05-ClientAAILoanIMPLIEDCONDYear41-51.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 12:47:21 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s05-ClientAAILoanIMPLIEDCONDYear51-61.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 12:52:26 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s05-ClientAAILoanIMPLIEDCONDYear61-71.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 13:06:39 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s06-ClientAAILoanIMPLIEDCONDYear31-41.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 13:00:39 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s06-ClientAAILoanIMPLIEDCONDYear41-51.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 12:58:01 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s06-ClientAAILoanIMPLIEDCONDYear51-61.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 13:03:26 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s06-ClientAAILoanIMPLIEDCONDYear61-71.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 13:12:32 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s07-ClientAAILoanIMPLIEDCONDYear31-41.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 13:20:49 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s07-ClientAAILoanIMPLIEDCONDYear41-51.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 13:18:10 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s07-ClientAAILoanIMPLIEDCONDYear51-61.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 13:14:58 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s07-ClientAAILoanIMPLIEDCONDYear61-71.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 13:35:34 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s08-ClientAAILoanIMPLIEDCONDYear31-41.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 13:26:56 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s08-ClientAAILoanIMPLIEDCONDYear41-51.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 13:32:55 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s08-ClientAAILoanIMPLIEDCONDYear51-61.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 13:29:43 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s08-ClientAAILoanIMPLIEDCONDYear61-71.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 13:46:59 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s09-ClientAAILoanIMPLIEDCONDYear31-41.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 13:44:12 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s09-ClientAAILoanIMPLIEDCONDYear41-51.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 13:49:27 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s09-ClientAAILoanIMPLIEDCONDYear51-61.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 13:40:57 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s09-ClientAAILoanIMPLIEDCONDYear61-71.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 14:00:14 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s10-ClientAAILoanIMPLIEDCONDYear31-41.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 14:02:42 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s10-ClientAAILoanIMPLIEDCONDYear41-51.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 14:05:29 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s10-ClientAAILoanIMPLIEDCONDYear51-61.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 14:08:08 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s10-ClientAAILoanIMPLIEDCONDYear61-71.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 14:14:42 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s11-ClientAAILoanIMPLIEDCONDYear31-41.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 14:20:22 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s11-ClientAAILoanIMPLIEDCONDYear41-51.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 14:23:10 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s11-ClientAAILoanIMPLIEDCONDYear51-61.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 14:17:09 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s11-ClientAAILoanIMPLIEDCONDYear61-71.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 14:37:09 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s12-ClientAAILoanIMPLIEDCONDYear31-41.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 14:31:09 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s12-ClientAAILoanIMPLIEDCONDYear41-51.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 14:28:30 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s12-ClientAAILoanIMPLIEDCONDYear51-61.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 14:33:56 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s12-ClientAAILoanIMPLIEDCONDYear61-71.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 14:44:18 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s13-ClientAAILoanIMPLIEDCONDYear31-41.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 14:46:47 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s13-ClientAAILoanIMPLIEDCONDYear41-51.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 14:49:34 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s13-ClientAAILoanIMPLIEDCONDYear51-61.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 14:52:12 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s13-ClientAAILoanIMPLIEDCONDYear61-71.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 15:10:08 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s14-ClientAAILoanIMPLIEDCONDYear31-41.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 15:01:27 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s14-ClientAAILoanIMPLIEDCONDYear41-51.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 15:04:14 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s14-ClientAAILoanIMPLIEDCONDYear51-61.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 15:06:53 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s14-ClientAAILoanIMPLIEDCONDYear61-71.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 15:17:53 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s15-ClientAAILoanIMPLIEDCONDYear31-41.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 15:20:21 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s15-ClientAAILoanIMPLIEDCONDYear41-51.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 15:15:14 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s15-ClientAAILoanIMPLIEDCONDYear51-61.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 15:23:09 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s15-ClientAAILoanIMPLIEDCONDYear61-71.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 15:35:16 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s16-ClientAAILoanIMPLIEDCONDYear31-41.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 15:29:14 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s16-ClientAAILoanIMPLIEDCONDYear41-51.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 15:37:44 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s16-ClientAAILoanIMPLIEDCONDYear51-61.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 15:32:03 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s16-ClientAAILoanIMPLIEDCONDYear61-71.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 15:52:02 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s17-ClientAAILoanIMPLIEDCONDYear31-41.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 15:43:24 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s17-ClientAAILoanIMPLIEDCONDYear41-51.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 15:49:23 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s17-ClientAAILoanIMPLIEDCONDYear51-61.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 15:46:11 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s17-ClientAAILoanIMPLIEDCONDYear61-71.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 15:59:46 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s18-ClientAAILoanIMPLIEDCONDYear31-41.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 15:56:58 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s18-ClientAAILoanIMPLIEDCONDYear41-51.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 16:02:14 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s18-ClientAAILoanIMPLIEDCONDYear51-61.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 16:04:51 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s18-ClientAAILoanIMPLIEDCONDYear61-71.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 16:13:44 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s19-ClientAAILoanIMPLIEDCONDYear31-41.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 16:10:58 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s19-ClientAAILoanIMPLIEDCONDYear41-51.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 16:19:21 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s19-ClientAAILoanIMPLIEDCONDYear51-61.xml</doc><doc joomlaID=\"\" timestamp=\"Thu Jun 16 16:16:09 CEST 2011\" reportUri=\"\" database=\"LM LMBarbora.mdb MB\" table=\"Loans\">s19-ClientAAILoanIMPLIEDCONDYear61-71.xml</doc></docs>

</result>";
*/	}
}