<?php
//First start with information about the Plugin and yourself. For example:
/**
 * @version     $Id: jucene.php
 * @package     Joomla
 * @subpackage  Jucene
 * @copyright   Copyright (C) 2005 - 2010 Lukáš Beránek. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

//To prevent accessing the document directly, enter this code:
// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

//to prevent loading local zend framework and allow users to run Jucene without Zend installed
//require_once ('../administrator\components\com_jucene\lucene/Lucene.php');


//Now define the registerEvent and the language file. Replace 'nameofplugin' with the name of your plugin.


//$mainframe->registerEvent ( 'onAfterContentSave', 'plgContentContindexIndex' );
//$mainframe->registerEvent ( 'onAfterInitialise', 'plgContentContindexSetPath');
//JPlugin::loadLanguage ( 'plg_content_contindex' );
require_once (JPATH_SITE . DS . 'administrator' . DS . 'components' . DS . 'com_jucene' . DS . 'helpers' . DS . 'jucene.php');

class plgContentContindex extends JPlugin {
	
	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @param object $subject The object to observe
	 * @param object $params  The object that holds the plugin parameters
	 * @since 1.5
	 */
	function plgContindex(&$article, $params) {
		parent::__construct ( $article, $params );
		exit ();
	}
	/**
	 * Example after save content method
	 * Article is passed by reference, but after the save, so no changes will be saved.
	 * Method is called right after the content is saved
	 *
	 *
	 * @param   object      A JTableContent object
	 * @param   bool        If the content is just about to be created
	 * @return  void
	 * 
	 */
	function onAfterContentSave($article, $isNew) {
		$this->onIndexContent ( $article, $isNew );
	}
	/**
	 * 
	 * @param $article
	 * @param $additional
	 */
	function onIndexPmml($rule, array $additional) {
		
		$index = JuceneHelper::getIndex ();
		//var_dump($index);
		$pk = $additional ['pk'];
		
		JuceneHelper::removeFromIndex ( 'pk:' . $pk );
		
		$app = JFactory::getApplication ();
		try {
			$zendDoc = Zend_Search_Lucene_Document_Pmml::addPmml ( $rule, $additional, 'UTF-8' );
			$index->addDocument ( $zendDoc );
		} catch ( Exception $e ) {
			$app->enqueueMessage ( JText::_ ( $e->getMessage () ), 'error' );
		
		}
	
	}
	
	/**
	 * 
	 * @param $article
	 * @param $isNew
	 */
	function onIndexContent($article, $isNew = false) {
		//FIXME move the content type tests and following transformations to the helper
		global $mainframe;
		$pk = $article->id;
		if (! $isNew) {
			JuceneHelper::removeFromIndex ( 'pk:' . $pk );
		}
		$index = JuceneHelper::getIndex ();
		$xml_field = (substr ( $article->fulltext, 0, 5 ) != '<?xml') ? $article->introtext : $article->fulltext;
		
		if (substr ( $xml_field, 0, 5 ) == '<?xml') {
			$dom = new DOMDocument ();
			$pmml = true;
			
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
			
			unset ( $artcile->fulltext );
			unset ( $record->introtext );
			
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
			}		
		} else {
			$zendDoc = Zend_Search_Lucene_Document_Html::loadHTML ( $article->fulltext, false, UTF - 8 );
			$index->addDocument ( $zendDoc );
		}
	}
}