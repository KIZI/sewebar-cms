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

require_once /*JPATH_COMPONENT.'com_jucene'.DS.'controllers'.DS.*/'Api.php';

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
		return parent::getDocuments();
		
	}
}