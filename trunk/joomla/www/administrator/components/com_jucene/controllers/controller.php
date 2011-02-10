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
 *
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

  function index() {

    $start = time ();
    $app = JFactory::getApplication ();

    set_time_limit ( 0 );

    if (@preg_match ( '/\pL/u', 'a' ) == 1) {

      $this->raiseMessage ( "PCRE unicode support is turned on.\n" );

    } else {

      $this->raiseMessage ( "PCRE unicode support is turned off.\n" );

    }

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

    //bind parameters
    //$params->merge ( new JParameter ( $row->params ) );

    //get the index through the helper, because we need to import it into the search plugin too
    $index = JuceneHelper::getIndex ();

    //retrieve actual document count in the index before indexation
    $documents = $index->numDocs ();
    $this->raiseMessage ( JText::sprintf ( 'INDEXRECORDCOUNT', $documents ) );

    //remove the document if it is already included it the index
    $hits = $index->find ( 'pk:' . $currId );
    //$this->raiseMessage("hits: ".count($hits),'error');
    if (count ( $hits ) > 0) {
      foreach ( $hits as $hit ) {

        $index->delete ( $hit->id );
        //$this->raiseMessage("removed: ".$hit->id,'error');
      }
    }
    $dom = new DOMDocument ();
    //decide which field contains PMML doc


    $xml_field = (substr ( $record ['fulltext'], 0, 5 ) != '<?xml') ? $record ['introtext'] : $record ['fulltext'];

    

    //first test is to decide which field contains the pmml doc. This is just a test to decide if it really is one:-). God help us.
    //if (substr ( $xml_field, 0, 5 ) == '<?xml'){


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
    $pmml = true;
    

    unset ( $record ['fulltext'] );
    unset ( $record ['introtext'] );

    if ($dom->loadXML ( $xml_field ) && ! $error) {

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
      if (! $error && $pmml) {
        
      	//@TODO remove if library is working require_once (JPATH_COMPONENT_ADMINISTRATOR . DS . 'lucene/Lucene/Document/Pmml.php');
        
      	$rules = $transXml->getElementsByTagName ( "AssociationRule" );
        $rulesCount = $rules->length;
        if ($rulesCount == 0) {
          $error = true;
          $this->raiseMessage ( 'XMLDOCUMENTNORULES', 'error' );
        }

        $rule_doc_position = 0;

        foreach ( $rules as $rule ) {
          $additional ['rating'] = rand(-1000,1000);
          $additional ['position'] = $rule_doc_position;
          $zendDoc = Zend_Search_Lucene_Document_Pmml::addPmml ( $rule, $additional, false );
          /*print'<pre>';
					var_dump($zendDoc);
					print '</pre>';
					die();*/
          $index->addDocument ( $zendDoc );
          $rule_doc_position ++;
        }
      } elseif (! $error) {
        $zendDoc = Zend_Search_Lucene_Document_Html::loadHTML ( $xml_field, false, 'UTF-8' );
        $index->addDocument ( $zendDoc );
      } else {

        $this->redirect ( $redir_url );
      }

    } else {

      $this->raiseMessage ( 'XMLDOCLOADERROR', 'error' );

    }
    $end = time ();

    //$index->size();
    $documents = $index->numDocs ();
    $size = $index->count ();

    /*TODO make the logging better $took = $end - $start;
    $indexFilesPath = JPATH_COMPONENT_ADMINISTRATOR . DS . "search_index" . DS . "index_pmml4";
    $xmlFilePath = JPATH_COMPONENT_ADMINISTRATOR . DS . "search_index" . DS . "xml" . DS . $currId . ".xml";
    if (is_file ( $xmlFilePath )) {
      $xmlSize = $this->sizeFormat ( filesize ( $xmlFilePath ) );
    } else {
      $xmlSize = 0;
    }
    $indexFileSize = $this->getDirectorySize ( $indexFilesPath );
    if ($rulesCount > 0) {
      $stats = "START: " . $start . " END: " . $end . " TOOK: " . $took . " DOCUMENTSNUM: " . $documents . " RULESFOUND: " . $rulesCount . " " . " FILESIZE: " . $xmlSize . " " . " INDEXSIZE: " . $indexFileSize ['size'] . " INDEXFILECOUNT: " . $indexFileSize ['count'] . "\r\n";

      $statsFile = JPATH_COMPONENT_ADMINISTRATOR . DS . "stats.txt";
      $statsCont = JFile::read ( $statsFile );
      JFile::write ( $statsFile, $statsCont . $stats );
    }*/

    if (is_numeric ( $recId )) {

      $this->raiseMessage ( JText::sprintf ( 'REMAININGRECORDS', $this->getRecordCount ( $currId ) ) );
      //$this->raiseMessage(JText::sprintf( 'INDEXRECORDCOUNT', $documents ));
      //sleep(0.5);
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
      if($userRating == -1 || $userRating == 1){
        $newRating = $ruleRating + $userRating;
        $zendDoc = Zend_Search_Lucene_Document_Pmml::addPmml ( $result, array(), false );
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
    require_once (JPATH_COMPONENT_ADMINISTRATOR . DS . 'lucene/Lucene/Document/Pmml.php');

    $index = JuceneHelper::getIndex ();

    $dom = new DOMDocument ();
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
        require_once (JPATH_COMPONENT_ADMINISTRATOR . DS . 'lucene/Lucene/Document/Pmml.php');
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

  function cron() {
    $this->raiseMessage ( 'Cron testing', 'error' );
  }

  /**
   *
   * @param string $message
   * @param string $type
   */
  function raiseMessage($message, $type = '') {

    JFactory::getApplication ()->enqueueMessage ( JText::_ ( $message ), $type );
    //if($type=='error'){die($message);}
  }

  /**
   *
   * @param $fieldName
   */
  function logSearchField($fieldName) {
    $db = JFactory::getDBO ();

    $query = "SELECT * from " . $db->nameQuote ( '#__jucene_fields' ) . " where " . $db->nameQuote ( 'fieldname' ) . "
				 	= " . $db->quote ( $fieldName );
    $db->setQuery ( $query );
    $db->query ();
    $num_rows = $db->getNumRows ();
    //$this->raiseMessage("Num rows: ".$num_rows);
    if ($num_rows == 0) {
      $fieldQuery = "INSERT INTO " . $db->nameQuote ( '#__jucene_fields' ) . " (fieldname)
							VALUES (" . $db->quote ( $fieldName ) . ")";
      //$this->raiseMessage("Field query: ".$fieldQuery);
      $db->setQuery ( $fieldQuery );
      if (! $db->query ()) {
        $this->raiseMessage ( $db->getErrorMsg (), 'error' );

      }
    }
  }

  /**
   *
   */
  function getRecord() {
    //get db object
    $db = JFactory::getDBO ();

    //construct query
    $query = "SELECT c.*,c.id as pk FROM " . $db->nameQuote ( '#__content' ) .' as c ';
		 			/*. " LEFT JOIN " . $db->nameQuote ( '#__jucene_synchronyze' ) . "
		 			. "AS j on " . $db->nameQuote ( 'c.id' ) . " = " . $db->nameQuote ( 'j.id_article' );*/
		 			//."WHERE (" . $db->nameQuote ( 'c.catid' ) . " = " . $db->quote ( '69' ) .
		 			//"OR " . $db->nameQuote ( 'c.catid' ) . " = " . $db->quote ( '86' ) . ")";

    $articleId = JRequest::getVar ( 'rid', 0, 'get', 'int' );

    if ($articleId != 0) {
      $query .= /*" AND " .*/ ' WHERE '. $db->nameQuote ( 'c.id' ) . "
			      	  = " . $db->quote ( $articleId );
    }

    //add order and limit, because we want obtain only one record
    $query .= " ORDER BY ID ASC LIMIT 1";

    $db->setQuery ( $query );

    $res = $db->loadAssoc ();

    return $res;
  }
  /**
   *
   * @param $currID
   */
  function getNextRecordId($currentId) {

    $db = JFactory::getDBO ();

    $query = "SELECT c.id FROM " . $db->nameQuote ( '#__content' ) . "
		 as c WHERE " . $db->nameQuote ( 'id' ) . " > " . $db->quote ( $currentId )
		 //."AND (" . $db->nameQuote ( 'catid' ) . " = " . $db->quote ( '69' ) . " OR " . $db->nameQuote ( 'catid' ) . " = " . $db->quote ( '86' ) . ")".
		 ." ORDER BY ID ASC LIMIT 1";

    $db->setQuery ( $query );

    return $db->loadResult ();
  }
  /**
   *
   * @param $id
   */
  function getRecordCount($id) {

    $db = JFactory::getDBO ();

    //construct query
    $query = "SELECT COUNT(c.id) FROM " . $db->nameQuote ( '#__content' ) . "
		 as c WHERE "
		 //."(" . $db->nameQuote ( 'catid' ) . " = " . $db->quote ( '69' )
		 //."OR  " . $db->nameQuote ( 'catid' ) . " = " . $db->quote ( '86' ) . ") AND
		 . $db->nameQuote ( 'id' ) . " > " . $db->quote ( $id ) . '';

    $db->setQuery ( $query );

    $remainingRecords = $db->loadResult ();

    return $remainingRecords;
  }
  /**
   *
   */
  function remove() {

    $index = JuceneHelper::removeIndex ();
    $this->redirect ( "index.php?option=com_jucene" );

  }
  /**
   *
   * @param $path
   */
  function getDirectorySize($path) {
    $totalsize = 0;
    $totalcount = 0;
    $dircount = 0;
    if ($handle = opendir ( $path )) {
      while ( false !== ($file = readdir ( $handle )) ) {
        $nextpath = $path . '/' . $file;
        if ($file != '.' && $file != '..' && ! is_link ( $nextpath )) {
          if (is_dir ( $nextpath )) {
            $dircount ++;
            $result = $this->getDirectorySize ( $nextpath );
            $totalsize += $result ['size'];
            $totalcount += $result ['count'];
            $dircount += $result ['dircount'];
          } elseif (is_file ( $nextpath )) {
            $totalsize += filesize ( $nextpath );
            $totalcount ++;
          }
        }
      }
    }
    closedir ( $handle );
    $total ['size'] = $this->sizeFormat ( $totalsize );
    $total ['count'] = $totalcount;
    $total ['dircount'] = $dircount;
    return $total;
  }

  function sizeFormat($size) {
    /*if($size<1024)
		 {
			return $size." bytes";
			}
			else if($size<(1024*1024))
			{*/
    $size = round ( $size / 1024, 1 );
    return $size . " KB";
    /*}
		 else if($size<(1024*1024*1024))
		 {
			$size=round($size/(1024*1024),1);
			return $size." MB";
			}
			else
			{
			$size=round($size/(1024*1024*1024),1);
			return $size." GB";
			}*/

  }

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