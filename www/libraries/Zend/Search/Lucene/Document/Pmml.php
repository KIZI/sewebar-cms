<?php

require_once 'Zend/Search/Lucene/Document.php';
jimport ( 'joomla.filesystem.file' );
define ( 'JUCENE_ENCODING', 'UTF-8' );
/**
 * The Pmml document is so special that we have to build a special class for indexing it.
 * It is neccessary to add a new Zend Search Lucene Document for each and every one rule
 * that is to be found in the Pmml doc. 
 *
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage Document
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Search_Lucene_Document_Pmml extends Zend_Search_Lucene_Document {
	
	private function __construct($rule, $additional, $storeContent) {
		/*$dom = new DOMDocument ();
		$dom->preserveWhiteSpace = false;
		$dom->loadXML ( $rule );*/
		//walk through the association rule and index it
		//$this->_parse ( $rule, 1 );
		$quantifiers = $rule->childNodes;
		foreach ( $quantifiers as $quantifier ) {
			if ($quantifier->nodeName == '#text' || $quantifier->nodeValue == '') {
				continue;
			}
			//print $quantifier->nodeName.' : '. trim($quantifier->nodeValue).'<br/>';
			$val = trim ( $quantifier->nodeValue );
			if (is_numeric ( $val )) {
				
				$val = JuceneHelper::prepareNumber ( $val );
			
			} else {
				$val = ( string ) $val;
				$val = str_replace("-","",$val);
			}
			if ($quantifier->nodeName == 'Text') {
				$type = 'Unindexed';
			} else {
				$type = 'Text';
			}
			$this->addField ( Zend_Search_Lucene_Field::$type ( $quantifier->nodeName, $val, JUCENE_ENCODING ) );
		
		}
		foreach ( $additional as $field => $value ) {
			if (is_numeric ( $value )) {
				$val = JuceneHelper::prepareNumber ( $value );
			}
			$this->addField ( Zend_Search_Lucene_Field::Keyword ( 'service_'.$field, $value, JUCENE_ENCODING ) );
		}
	
	}
	
	/**
	 * 
	 * @param $pmml_rule
	 * @param $additional
	 * @param $storeContent
	 */
	
	public static function addPmml(DOMElement $pmml_rule, $additional, $storeContent = false) {
		$pmml = (is_readable ( $pmml )) ? JFile::read ( $pmml_rule ) : $pmml_rule;
		if (! $pmml) {
			require_once 'Zend/Lucene/Document/Exception.php';
			throw new Zend_Search_Lucene_Document_Exception ( 'Provided Pmml document \'' . $pmml . '\' is neither a file nor a XML.' );
		
		}
		
		return new Zend_Search_Lucene_Document_Pmml ( $pmml, $additional, $storeContent );
	}
}

?>