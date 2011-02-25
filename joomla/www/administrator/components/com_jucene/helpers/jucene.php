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
 * @package		Joomla
 * @subpackage	Search
 */
class JuceneHelper {
	/**
	 *
	 */
	var $index;

	function getIndex() {
		//get params


		require_once ('Zend/Search/Lucene.php');
		//require_once (JPATH_COMPONENT_ADMINISTRATOR . DS . 'helpers/LuceneDigitAnalyzer.php');
		$index_path = JuceneHelper::getIndexPath ();


		if (! JFolder::exists ( $index_path )) {

			JFolder::create ( $index_path );

			$index = Zend_Search_Lucene::create ( $index_path );

		} else {
			try{
			$index = Zend_Search_Lucene::open ( $index_path );
			}catch (Exception $ex)
			{
				echo $ex->getMessage();
			}
		}
		Zend_Search_Lucene_Analysis_Analyzer::setDefault ( new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8Num () );
		Zend_Search_Lucene::setResultSetLimit ( 100 );

		return $index;

	}

	function removeIndex() {
		$remove_path = JuceneHelper::getIndexPath ();
		JuceneHelper::removeDBData ();

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
	 *
	 */
	function removeDBData() {

		$db = JFactory::getDBO ();

		$query = "DELETE FROM " . $db->nameQuote ( '#__jucene_fields' ) . ";";
		$db->setQuery ( $query );
		$db->query ();

		$query2 = "DELETE FROM " . $db->nameQuote ( '#__jucene_synchronyze' ) . ";";
		$db->setQuery ( $query2 );
		$db->query ();
	}
	/**
	 *
	 */
	function getIndexPath() {

		$params = &JComponentHelper::getParams ( 'com_jucene' );

		//bind parameters
		//$params->merge ( new JParameter ( $params->params ) );

		$dir_path = JPATH_COMPONENT_ADMINISTRATOR . DS . $params->get ( 'index_path' );
		$index_name = $params->get ( 'index_name' );
		$index_path = $dir_path . DS . $index_name;

		return $index_path;
	}

	public function formatNumber($query) {
		return '1'.sprintf ( '%06d', round ( $query * 1000 ) );
	}

	public function prepareNumber($strNumber) {
		return preg_replace ( '/\d+(\.\d+)?/e', 'JuceneHelper::formatNumber(\\0)', $strNumber );
	}
	function sanitizeFieldName($name) {

		$replace_chars = array (' ', '\\', '+', '-', '\&', '\|', '!', '(', ')', '{', '}', '[', ']', '^', '\"', '~', '*', '?', ':' );
		$name = str_replace ( $replace_chars, '', $name );
		$name = JuceneHelper::removeCzechspecialChars ( $name );
		$name = strtolower ( $name );
		return $name;
	}

	function preprocessQuery($query) {
		$short = false;

		if (strlen ( $query ) < 3) {
			$short = true;
		}
		return $short;
	}
	/**
	 *
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
	function sanitizeRuleValue($value) {
		$value = strtolower ( JuceneHelper::removeCzechSpecialChars ( $value ) );
		return $value;
	}
	/**
	 *
	 * @param $number
	 */
	function prepareNumericValue($number) {

		$number = number_format ( $number, 3, ',', '' );
		if ($number == "0,000") {
			return "";
		} else {
			return $number;
		}

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
	function removeCzechspecialChars($czech) {

		$char_table = Array ('ä' => 'a', 'Ä' => 'A', 'á' => 'a', 'Á' => 'A', 'à' => 'a', 'À' => 'A', 'ã' => 'a', 'Ã' => 'A', 'â' => 'a', 'Â' => 'A', 'č' => 'c', 'Č' => 'C', 'ć' => 'c', 'Ć' => 'C', 'ď' => 'd', 'Ď' => 'D', 'ě' => 'e', 'Ě' => 'E', 'é' => 'e', 'É' => 'E', 'ë' => 'e', 'Ë' => 'E', 'è' => 'e', 'È' => 'E', 'ê' => 'e', 'Ê' => 'E', 'í' => 'i', 'Í' => 'I', 'ï' => 'i', 'Ï' => 'I', 'ì' => 'i', 'Ì' => 'I', 'î' => 'i', 'Î' => 'I', 'ľ' => 'l', 'Ľ' => 'L', 'ĺ' => 'l', 'Ĺ' => 'L', 'ń' => 'n', 'Ń' => 'N', 'ň' => 'n', 'Ň' => 'N', 'ñ' => 'n', 'Ñ' => 'N', 'ó' => 'o', 'Ó' => 'O', 'ö' => 'o', 'Ö' => 'O', 'ô' => 'o', 'Ô' => 'O', 'ò' => 'o', 'Ò' => 'O', 'õ' => 'o', 'Õ' => 'O', 'ő' => 'o', 'Ő' => 'O', 'ř' => 'r', 'Ř' => 'R', 'ŕ' => 'r', 'Ŕ' => 'R', 'š' => 's', 'Š' => 'S', 'ś' => 's', 'Ś' => 'S', 'ť' => 't', 'Ť' => 'T', 'ú' => 'u', 'Ú' => 'U', 'ů' => 'u', 'Ů' => 'U', 'ü' => 'u', 'Ü' => 'U', 'ù' => 'u', 'Ù' => 'U', 'ũ' => 'u', 'Ũ' => 'U', 'û' => 'u', 'Û' => 'U', 'ý' => 'y', 'Ý' => 'Y', 'ž' => 'z', 'Ž' => 'Z', 'ź' => 'z', 'Ź' => 'Z' );
		$text = strtr ( $czech, $char_table );

		return $text;
		/*setlocale(LC_CTYPE, "cs_CZ.utf-8");
	$url = $czech;
    $url = preg_replace('~[^\\pL0-9_.,]+~u', '-', $url);
    $url = trim($url, "-");
    $url = iconv("utf-8", "us-ascii//TRANSLIT", $url);
    $url = strtolower($url);
    $url = preg_replace('~[^-a-z0-9_]+~', '', $url);
    return $url;*/

	}

}