<?php
//First start with information about the Plugin and yourself. For example:
/**
 * @version		$Id: jucene.php
 * @package		Joomla
 * @subpackage	Jucene
 * @copyright	Copyright (C) 2005 - 2010 LukĂˇĹˇ BerĂˇnek. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

//To prevent accessing the document directly, enter this code:
// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );
//jimport( 'joomla.factory' );


//to prevent loading local zend framework and allow users to run Jucene without Zend installed


//Now define the registerEvent and the language file. Replace 'nameofplugin' with the name of your plugin.
$mainframe->registerEvent ( 'onJuceneSearch', 'plgSearchJucene' );
JPlugin::loadLanguage ( 'plg_search_jucene' );

/**
 * This method is used to generate search results field that are passed to the view
 * for displaying to the end-user
 * @param $query
 * @param $ordering
 */
function plgSearchJucene($query, $ordering = '') {
	//import helpers
	require_once (JPATH_SITE . DS . 'administrator' . DS . 'components' . DS . 'com_jucene' . DS . 'helpers' . DS . 'jucene.php');
	require_once (JPATH_SITE . DS . 'components' . DS . 'com_content' . DS . 'helpers' . DS . 'route.php');
	
	//set up some variables
	global $mainframe;
	$user = &JFactory::getUser ();
	
	$time = time ();
	try {
		//retrieve the Lucene Index if exists	
		$index = & JuceneHelper::getIndex ();
	} catch ( Exception $e ) {
		JFactory::getApplication ()->enqueueMessage ( JText::_ ( $e->getMessage () ), 'error' );
	}
	//It is time to define the parameters! First get the right plugin; 'search' (the group), 'nameofplugin'.
	$plugin = & JPluginHelper::getPlugin ( 'search', 'jucene' );
	
	//load the parameters of the plugin
	$pluginParams = new JParameter ( $plugin->params );
	
	$limit = $pluginParams->def ( 'search_limit', 0 );
	
	//TODO log search query
	

	//Set query
	

	try {
		$query = JuceneHelper::prepareNumber ( $query );
	} catch ( Exception $e ) {
	}
	
	Zend_Search_Lucene_Search_QueryParser::setDefaultEncoding ( 'UTF-8' );
	try {
		Zend_Search_Lucene_Search_QueryParser::parse ( $query );
	} catch ( Exception $e ) {
		JFactory::getApplication ()->enqueueMessage ( JText::_ ( $e->getMessage () ), 'error' );
	}
	
	$query = str_replace ( '-', ':', $query );
	if (JDEBUG) {
		JFactory::getApplication ()->enqueueMessage ( JText::_ ( $query, 'error' ) );
	}
	//query time
	$_SESSION ['jucene_timer'] = time ();
	
	try {
		$results = $index->find ( $query, 'score', SORT_NUMERIC, SORT_DESC, 'rating', SORT_NUMERIC, SORT_DESC );
	} catch ( Exception $e ) {
		JFactory::getApplication ()->enqueueMessage ( JText::_ ( $e->getMessage () ), 'error' );
	}
	
	//TODO find better solution to create links, perhaps to create router like it the com_content
	

	// Create a user access object for the current user
	$access = new stdClass ();
	$access->canEdit = $user->authorize ( 'com_content', 'edit', 'content', 'all' );
	$access->canEditOwn = $user->authorize ( 'com_content', 'edit', 'content', 'own' );
	$access->canPublish = $user->authorize ( 'com_content', 'publish', 'content', 'all' );
	
	// Check to see if the user has access to view the full article
	$aid = $user->get ( 'aid' );
	
	//pk is the value of the primary key of the record, because we cannot call id - lucene would return id WITHIN index.. not database primary key
	foreach ( $results as $key => $val ) {
		
		if ($val->access <= $aid) {
			$results [$key]->href = JRoute::_ ( ContentHelperRoute::getArticleRoute ( $val->alias, $val->catid, $val->sectionid ) );
			;
			//ContentHelperRoute::getArticleRoute ( $val->pk );
		

		} else {
			$params = &JComponentHelper::getParams ( 'com_jucene' );
			$leave_out = $params->get ( 'leave_out', 1 );
			// Create login URL or leave out private content
			if ($leave_out == 1) {
				unset ( $results [$key] );
			} else {
				$uri = JFactory::getURI ();
				$return = $uri->toString ();
				
				$url = 'index.php?option=com_user&view=login';
				$url .= '&return=' . base64_encode ( $return );
				
				$results [$key]->href = $url;
			}
		}
		$results [$key]->count = '';
		$results [$key]->time = $time;
	}
	
	//Return the search results in an array
	return $results;
}
	
	
