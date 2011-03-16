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

jimport ( 'joomla.application.component.controller' );
jimport ( 'joomla.application.component.controller' );
jimport ( 'joomla.filesystem.folder' );
jimport ( 'joomla.filesystem.file' );

require_once (JPATH_COMPONENT_ADMINISTRATOR . DS . 'helpers/jucene.php');
/**
 * Basic Jucene index controller
 *
 *
 * @author bery
 *
 */
class JuceneController extends JController {
	
	/**
	 *
	 */
	var $_index = null;
	
	/**
	 *
	 * @param $conf
	 * @return unknown_type
	 */
	function __construct($conf = array()) {
		parent::__construct ( $conf );
		$this->registerTask ( 'index', 'display' );
		$this->registerTask ( 'about', 'display' );
		$this->registerTask ( 'finish', 'display' );
		$this->registerTask ( 'update', 'update' );
	
	}
	/**
	 * This method controlles the display methods
	 */
	function display() {
		global $mainframe;
		
		switch ($this->getTask ()) {
			case 'jucene_about' :
				JRequest::setVar ( 'hidemainmenu', 0 );
				JRequest::setVar ( 'layout', 'jucene_about' );
				JRequest::setVar ( 'view', 'jucene' );
				break;
			case 'index' :
				JRequest::setVar ( 'hidemainmenu', 0 );
				JRequest::setVar ( 'layout', 'jucene_index' );
				JRequest::setVar ( 'view', 'jucene' );
				$this->index ();
				break;
			case 'continue' :
				JRequest::setVar ( 'hidemainmenu', 0 );
				JRequest::setVar ( 'layout', 'jucene_continue' );
				JRequest::setVar ( 'view', 'jucene' );
				JRequest::setVar ( 'edit', true );
				break;
			default :
				JRequest::setVar ( 'hidemainmenu', 0 );
				JRequest::setVar ( 'layout', 'default' );
				JRequest::setVar ( 'view', 'jucene' );
				JRequest::setVar ( 'edit', true );
				break;
		}
		
		parent::display ();
	}
	/**
	 * Core index method used to bulk index the database content
	 */
	function index() {
		
		$start = time ();
		$app = JFactory::getApplication ();
		
		set_time_limit ( 0 );
		
		$record = $this->getRecord ();
		
		//remove row id and use returned pk instead because of collision with Zend - it uses id as the primary
		//key in the index
		unset ( $record ['id'] );
		
		//set basic information about current document
		$currId = $record ['pk'];
		$title = $record ['title'];
		$category = $record ['catid'];
		
		$recId = $this->getNextRecordId ( $currId );
		
		$additional = $record;
		unset ( $additional ['introtext'] );
		unset ( $additional ['fulltext'] );
		//prepare redir url
		$redir_url = "index2.php?option=com_jucene&task=index&view=jucene_index&rid=" . $recId;
		
		$params = &JComponentHelper::getParams ( 'com_jucene' );
		
		//get the index through the helper, because we need to import it into the search plugin too
		$index = JuceneHelper::getIndex ();
		
		//retrieve actual document count in the index before indexation
		$documents = $index->numDocs ();
		$this->raiseMessage ( JText::sprintf ( 'INDEXRECORDCOUNT', $documents ) );
		
		//TODO move the removal to helper class 
		JuceneHelper::removeFromIndex ( 'pk:' . $currId );
		
		$pmml = false;
		//TODO devide this into index methods based on content types- JUCENE helper!!! - no reason to try to transform it if it' HTML document
		$xml_field = (substr ( $record ['fulltext'], 0, 5 ) != '<?xml') ? $record ['introtext'] : $record ['fulltext'];
		
		//html or PMML?
		if (substr ( $xml_field, 0, 5 ) == '<?xml') {
			$dom = new DOMDocument ();
			$pmml = true;
			
			$xslt = new DOMDocument ();
			
			$error = false;
			//load xslt stylesheet
			if (! @$xslt->load ( JPATH_COMPONENT_ADMINISTRATOR . DS . 'xslt/jucene.xsl' )) {
				$error = true;
				$this->raiseMessage ( "XSLTLOADERROR", 'error' );
			
			}
			
			$proc = new XSLTProcessor ();
			if (! $proc->importStylesheet ( $xslt )) {
				$error = true;
				$this->raiseMessage ( "XSLTIMPORTERROR", 'error' );
			}
			
			unset ( $record ['fulltext'] );
			unset ( $record ['introtext'] );
			
			if ($dom->loadXML ( $xml_field ) && ! $error && $pmml) {
				
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
			} else {
				
				$this->redirect ( $redir_url );
			}
		
		} else {
			$this->raiseMessage ( 'XMLDOCLOADERROR', 'error' );
			JPluginHelper::importPlugin ( 'content' );
			$dispatcher = & JDispatcher::getInstance ();
			$results = $dispatcher->trigger ( 'onIndexContent', array ($record['fulltext'], $additional ) );
		
		}
		$end = time ();
		
		$documents = $index->numDocs ();
		$size = $index->count ();
		
		$allDocs = $this->getAllRecordsCount ();
		$remainingDocs = $this->getRecordCount ( $currId );
		
		$percent = $allDocs / 100;
		$doneDocs = $allDocs - $remainingDocs;
		$percentDone = $doneDocs / $percent;
		$document = &JFactory::getDocument ();
		
		if (is_numeric ( $recId )) {
			if (JDEBUG) {
				$this->raiseMessage ( JText::sprintf ( 'REMAININGRECORDS', $remainingDocs ) );
				$this->raiseMessage ( JText::sprintf ( 'INDEXRECORDCOUNT', $documents ) );
			}
			$index->commit ();
			$this->redirect ( $redir_url );
		
		} else {
			
			$index->optimize ();
			$this->raiseMessage ( 'DONEINDEXING', 'error' );
			$this->redirect ( "index.php?option=com_jucene" );
		
		}
	
	}
	/**
	 * Function to update rating of the association rule by the Sewebar domain experts. It should be
	 * in the format +/- 1.
	 * @param $docId reference of the document id - e.g. db primary key id, pk field of the lucene record
	 * @param $rating value of the rating +/- 1
	 * @param $rulePosition position in the document
	 */
	function updateRating($docId, $userRating, $rulePosition) {
		$index = JuceneHelper::getIndex ();
		$results = $index->find ( "pk:'.$docId.' AND position:" . $rulePosition );
		if (count ( $results ) > 0) {
			foreach ( $results as $result ) {
				$del = $index->delete ( $result->id );
			}
			$ruleRating = $result->rating;
			if ($userRating == - 1 || $userRating == 1) {
				$newRating = $ruleRating + $userRating;
				$zendDoc = Zend_Search_Lucene_Document_Pmml::addPmml ( $result, array (), false );
				$index->addDocument ( $zendDoc );
			}
		}
	
	}
	
	/**
	 * There should be even the ID of the document to get this working properly
	 *
	 * @param $pmmlDoc PMML document XML string representation
	 */
	public function kbiInsertToIndex($pmmlDoc) {
		
		$index = JuceneHelper::getIndex ();
		
		$dom = new DOMDocument ();
		$xslt = new DOMDocument ();
		
		$error = false;
		//load xslt stylesheet
		if (! @$xslt->load ( JPATH_SITE . 'administrator' . DS . 'components' . DS . 'com_jucene' . DS . 'xslt/jucene.xsl' )) {
			$error = true;
			$this->raiseMessage ( "XSLTLOADERROR", 'error' );
		
		}
		
		$proc = new XSLTProcessor ();
		if (! $proc->importStylesheet ( $xslt )) {
			$error = true;
			$this->raiseMessage ( "XSLTIMPORTERROR", 'error' );
		}
		
		if ($dom->loadXML ( $pmmlDoc ) && ! $error) {
			
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
					$additional ['position'] = $rule_doc_position;
					$zendDoc = Zend_Search_Lucene_Document_Pmml::addPmml ( $rule, $additional, false );
					/*print'<pre>';
                    var_dump($zendDoc);
                    print '</pre>';
                    die();*/
					$index->addDocument ( $zendDoc );
					$rule_doc_position ++;
				}
				return true;
			} else {
				
				return (false);
			}
		
		} else {
			
			$this->raiseMessage ( 'XMLDOCLOADERROR', 'error' );
		}
	
	}
	
		
	/**
	 *	Just use of the JFactory method to save some code writing
	 *
	 * @param string $message
	 * @param string $type
	 */
	function raiseMessage($message, $type = '') {
		
		JFactory::getApplication ()->enqueueMessage ( JText::_ ( $message ), $type );
		//if($type=='error'){die($message);}
	}
	
	
	
	/**
	 * Get Joomla database article record
	 *
	 */
	function getRecord() {
		//get db object
		$db = JFactory::getDBO ();
		
		//construct query
		$query = "SELECT c.*,c.id as pk FROM " . $db->nameQuote ( '#__content' ) . ' as c ';
		
		$articleId = JRequest::getVar ( 'rid', 0, 'get', 'int' );
		
		if ($articleId != 0) {
			$query .= /*" AND " .*/ ' WHERE ' . $db->nameQuote ( 'c.id' ) . "
			      	  = " . $db->quote ( $articleId );
		}
		
		//add order and limit, because we want obtain only one record
		$query .= " ORDER BY ID ASC LIMIT 1";
		
		$db->setQuery ( $query );
		
		$res = $db->loadAssoc ();
		
		return $res;
	}
	/**
	 * Get the record id of the article which is to be indexed next
	 * @param $currID
	 */
	function getNextRecordId($currentId) {
		
		$db = JFactory::getDBO ();
		
		$query = "SELECT c.id FROM " . $db->nameQuote ( '#__content' ) . "
		 as c WHERE " . $db->nameQuote ( 'id' ) . " > " . $db->quote ( $currentId ) . //."AND (" . $db->nameQuote ( 'catid' ) . " = " . $db->quote ( '69' ) . " OR " . $db->nameQuote ( 'catid' ) . " = " . $db->quote ( '86' ) . ")".
" ORDER BY ID ASC LIMIT 1";
		
		$db->setQuery ( $query );
		
		return $db->loadResult ();
	}
	/**
	 * Number of records that wait to be indexed
	 *
	 * @param $id
	 */
	function getRecordCount($id) {
		
		$db = JFactory::getDBO ();
		
		//construct query
		$query = "SELECT COUNT(c.id) FROM " . $db->nameQuote ( '#__content' ) . "
		 as c WHERE " . $db->nameQuote ( 'id' ) . " > " . $db->quote ( $id ) . '';
		
		$db->setQuery ( $query );
		
		$remainingRecords = $db->loadResult ();
		
		return $remainingRecords;
	}
	
	/**
	 * Number of all records in the jos_content table. This method will be used
	 * in next releas to show bulk index progressbar
	 * @param $id
	 */
	function getAllRecordsCount() {
		
		$db = JFactory::getDBO ();
		
		//construct query
		$query = "SELECT COUNT(c.id) FROM " . $db->nameQuote ( '#__content' ) . "
		 as c";
		$db->setQuery ( $query );
		
		$remainingRecords = $db->loadResult ();
		
		return $remainingRecords;
	}
	/**
	 * Remove index method
	 */
	function remove() {
		
		$index = JuceneHelper::removeIndex ();
		$this->redirect ( "index.php?option=com_jucene" );
	
	}
	
	
	/**
	 * Crucial redirect method is used when indexing content. After each article
	 * is indexes it redirects to next one and continues. It prevents the process from
	 * php timeouts.
	 * @param $redirect_url
	 */
	function redirect($redirect_url) {
		?>
<form method="post" name="redirForm"
	action="<?php
		echo $redirect_url;
		?>"></form>
<script language="JavaScript">
			document.redirForm.submit();
		</script>
<?php
	}
}