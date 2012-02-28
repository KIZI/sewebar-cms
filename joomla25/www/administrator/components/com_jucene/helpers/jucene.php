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

// Check to ensure this file is included in Joomla!
defined ( '_JEXEC' ) or die ( 'Restricted access' );

/**
 * Jucen heler class
 * @package		Joomla
 * @subpackage	Search
 */
class JuceneHelper {
	/**
	 *
	 */
	var $index;
	
	/**
	 * 
	 * @param $name
	 * index name can be specified
	 */
	function getIndex($name = NULL) {
		
		require_once ('Zend/Search/Lucene.php');
		
		$index_path = JuceneHelper::getIndexPath ( $name );
		
		if (! JFolder::exists ( $index_path )) {
			
			JFolder::create ( $index_path );
			
			$index = Zend_Search_Lucene::create ( $index_path );
		
		} else {
			try {
				$index = Zend_Search_Lucene::open ( $index_path );
			} catch ( Exception $ex ) {
				echo $ex->getMessage ();
			}
		}
		$component = JComponentHelper::getComponent ( 'com_jucene' );
		$params = &JComponentHelper::getParams ( $component->params );
		
		Zend_Search_Lucene_Analysis_Analyzer::setDefault ( new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8Num () );
		Zend_Search_Lucene::setResultSetLimit ( $params->get ( 'resuls_limit', 100 ) );
		
		return $index;
	
	}
	
	/**
	 * Method that removes currently used index. The index name is held in the 
	 * component parameters
	 */
	function removeIndex() {
		$remove_path = JuceneHelper::getIndexPath ();
		
		if (JFolder::exists ( $remove_path )) {
			
			if (JFolder::delete ( $remove_path )) {
				
				JFactory::getApplication ()->enqueueMessage ( JTEXT::_ ( "INDEXREMOVED" ) );
			
			} else {
				
				JFactory::getApplication ()->enqueueMessage ( JTEXT::_ ( "COULDNOTREMOVE" ), 'error' );
			
			}
		
		} else { //index doesnt exist
			JFactory::getApplication ()->enqueueMessage ( JTEXT::_ ( "NOTHINGTOREMOVE" ) );
		}
	}
	
	/**
	 * Delete selected record from the index. 
	 */
	function removeFromIndexByQuery($query) {
		$message = "";
		
		$index = JuceneHelper::getIndex ();
		$hits = $index->find ( $query );
		if (count ( $hits > 0 )) {
			foreach ( $hits as $hit ) {
				
				$index->delete ( $hit->id );
				$message .= "Deleted " . $hit->id;
				//$this->raiseMessage("removed: ".$hit->id,'error');
			}
		}
		JFactory::getApplication ()->enqueueMessage ( $message );
	}
	
	/**
	 * 
	 * @param $doc_id
	 */
	function removeFromIndexById($doc_id) {
		if(!is_numeric()){
			return false;
		}
		
		$message = "";
		
		$index = JuceneHelper::getIndex ();
		$hits = $index->find ( "pk:".$doc_id );
		if (count ( $hits > 0 )) {
			foreach ( $hits as $hit ) {				
				try {
					$index->delete ( $hit->id );
				} catch (Exception $e) {
					JFactory::getApplication ()->enqueueMessage ( $e->getMessage() );
				}
				$message .= "Deleted " . $hit->id;
				//$this->raiseMessage("removed: ".$hit->id,'error');
			}
		}
		JFactory::getApplication ()->enqueueMessage ( $message );
	}
	
	
	/**
	 * Get index path
	 * @param $index_name
	 * index name can be specified to open specific index
	 */
	function getIndexPath($index_name = NULL) {
		$params = &JComponentHelper::getParams ( 'com_jucene' );
		$dir_path = JPATH_SITE . DS . 'administrator' . DS . 'components' . DS . 'com_jucene' . DS . $params->get ( 'index_path', 'search_index' );
		$index_name = empty ( $index_name ) ? $params->get ( 'index_name', 'default_index' ) : $index_name;
		$index_path = $dir_path . DS . $index_name;
		return $index_path;
	}
	
	public function formatNumber($query) {
		
		return '1' . sprintf ( '%06d', round ( $query * 1000 ) );
	
	}
	
	/**
	 * Prepared number to format that can be handled by Lucene
	 * 
	 * @param unknown_type $strNumber
	 */
	public function prepareNumber($strNumber) {
		
		return preg_replace ( '/\d+(\.\d+)?/e', 'JuceneHelper::formatNumber(\\0)', $strNumber );
	
	}
	
	/**
	 * Remove bad chars from field name
	 * 
	 * @param $name
	 */
	function sanitizeFieldName($name) {
		
		$replace_chars = array (' ', '\\', '+', '-', '\&', '\|', '!', '(', ')', '{', '}', '[', ']', '^', '\"', '~', '*', '?', ':' );
		$name = str_replace ( $replace_chars, '', $name );
		$name = JuceneHelper::removeCzechspecialChars ( $name );
		$name = strtolower ( $name );
		return $name;
	}
	/**
	 * Check whether the query is not too short. This is the place where additional
	 * security checks should be.
	 * 
	 * @param $query
	 */
	function preprocessQuery($query) {
		$short = false;
		
		if (strlen ( $query ) < 3) {
			$short = true;
		}
		return $short;
	}
	
	/**
	 * Remove bad or harmfull chars from indexed field's value
	 * @param $value
	 */
	function sanitizeFieldValue($value) {
		
		if (is_numeric ( $value ) || is_float ( $value ) || is_int ( $value )) {
			
			$value = JuceneHelper::prepareNumericValue ( $value );
		
		} else {
			
			$value = strtolower ( JuceneHelper::removeCzechSpecialChars ( $value ) );
		
		}
		$replace_chars = array (';', '<', '>', '\\', '+', '-', '\&', '\|', '!', '(', ')', '{', '}', '[', ']', '^', '\"' );
		$value = str_replace ( $replace_chars, ' ', $value );
		
		return $value;
	}
	
	/**
	 * Same as for field value
	 */
	function sanitizeRuleValue($value) {
		$value = strtolower ( JuceneHelper::removeCzechSpecialChars ( $value ) );
		return $value;
	}
	
	function sanitizeQueryValue($value) {
		
		if (is_numeric ( $value ) || is_float ( $value ) || is_int ( $value )) {
			
			$value = JuceneHelper::prepareNumericValue ( $value );
		
		} else {
			
			$value = JuceneHelper::removeCzechSpecialChars ( $value );
		
		}
		$replace_chars = array (';', '<', '>', '\\', '+', '-', '\&', '\|', '!', '(', ')', '{', '}', '[', ']', '\"' );
		$value = str_replace ( $replace_chars, ' ', $value );
		$replace_dot = array ('.' );
		$value = str_replace ( $replace_dot, ',', $value );
		return $value;
	}
	
	/**
	 * Remove czech language special chars
	 * 
	 * @param $czech
	 */
	function removeCzechspecialChars($czech) {
		
		$char_table = Array ('ä' => 'a', 'Ä' => 'A', 'á' => 'a', 'Á' => 'A', 'à' => 'a', 'À' => 'A', 'ã' => 'a', 'Ã' => 'A', 'â' => 'a', 'Â' => 'A', 'č' => 'c', 'Č' => 'C', 'ć' => 'c', 'Ć' => 'C', 'ď' => 'd', 'Ď' => 'D', 'ě' => 'e', 'Ě' => 'E', 'é' => 'e', 'É' => 'E', 'ë' => 'e', 'Ë' => 'E', 'è' => 'e', 'È' => 'E', 'ê' => 'e', 'Ê' => 'E', 'í' => 'i', 'Í' => 'I', 'ï' => 'i', 'Ï' => 'I', 'ì' => 'i', 'Ì' => 'I', 'î' => 'i', 'Î' => 'I', 'ľ' => 'l', 'Ľ' => 'L', 'ĺ' => 'l', 'Ĺ' => 'L', 'ń' => 'n', 'Ń' => 'N', 'ň' => 'n', 'Ň' => 'N', 'ñ' => 'n', 'Ñ' => 'N', 'ó' => 'o', 'Ó' => 'O', 'ö' => 'o', 'Ö' => 'O', 'ô' => 'o', 'Ô' => 'O', 'ò' => 'o', 'Ò' => 'O', 'õ' => 'o', 'Õ' => 'O', 'ő' => 'o', 'Ő' => 'O', 'ř' => 'r', 'Ř' => 'R', 'ŕ' => 'r', 'Ŕ' => 'R', 'š' => 's', 'Š' => 'S', 'ś' => 's', 'Ś' => 'S', 'ť' => 't', 'Ť' => 'T', 'ú' => 'u', 'Ú' => 'U', 'ů' => 'u', 'Ů' => 'U', 'ü' => 'u', 'Ü' => 'U', 'ù' => 'u', 'Ù' => 'U', 'ũ' => 'u', 'Ũ' => 'U', 'û' => 'u', 'Û' => 'U', 'ý' => 'y', 'Ý' => 'Y', 'ž' => 'z', 'Ž' => 'Z', 'ź' => 'z', 'Ź' => 'Z' );
		$text = strtr ( $czech, $char_table );
		
		return $text;
	}
	
	function stringContains($str, $content, $ignorecase = true) {
		$retval = false;
		
		if ($ignorecase) {
			$str = strtolower ( $str );
			$content = strtolower ( $content );
		}
		
		// php type system sucks so we may need a "special check"...
		$_strpos = strpos ( $str, $content );
		if ($_strpos === 0 || $_strpos > 0) {
			$retval = true;
		}
		return $retval;
	}

}