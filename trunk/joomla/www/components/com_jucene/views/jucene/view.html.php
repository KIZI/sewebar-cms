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

jimport ( 'joomla.application.component.view' );

/**
 * HTML View class for the WebLinks component
 *
 * @static
 * @package		Joomla
 * @subpackage	Weblinks
 * @since 1.0
 */
class JuceneViewJucene extends JView {
	function display($tpl = null) {
		global $mainframe;
		
		require_once (JPATH_COMPONENT_ADMINISTRATOR . DS . 'helpers' . DS . 'jucene.php');
		
		// Initialize some variables
		$pathway = & $mainframe->getPathway ();
		$uri = & JFactory::getURI ();
		
		$error = '';
		$rows = null;
		$total = 0;
		
		$comParams = &JComponentHelper::getParams ( 'com_jucene' );
		$displayServiceLink = $comParams->get ( 'service_link', 1 );
		if ($displayServiceLink) {
			$service_link = 'Powered by Zend Lucene, created by <a href="http://www.drupaler.cz">drupaler.cz</a>, Lukáš Beránek';
		}
		// Get some data from the model
		$state = &$this->get ( 'state' );
		$query = $state->get ( 'query' );
		
		$params = &$mainframe->getParams ();
		
		$menus = &JSite::getMenu ();
		$menu = $menus->getActive ();
		
		// because the application sets a default page title, we need to get it
		// right from the menu item itself
		if (is_object ( $menu )) {
			$menu_params = new JParameter ( $menu->params );
			if (! $menu_params->get ( 'page_title' )) {
				$params->set ( 'page_title', JText::_ ( 'Jucene Search' ) );
			}
		} else {
			$params->set ( 'page_title', JText::_ ( 'Jucene Search' ) );
		}
		
		$document = &JFactory::getDocument ();
		$document->setTitle ( $params->get ( 'page_title' ) );
		//add search stylesheet and autocomplete jquery library, jquery_lib_min (dependency)
		$document->addScript ( 'components/com_jucene/js/jquery-1.4.4.min.js' );
		$document->addScript ( 'components/com_jucene/js/jquery-ui-1.8.10.custom.min.js' );
		$document->addStyleSheet ( 'components/com_jucene/css/jquery-ui-1.8.10.custom.css' );
		$document->addStyleSheet ( 'components/com_jucene/css/search.css' );
				
		// built select lists
		//TODO find a solution how to implement query builder or ordering and search areas e.g. search fields
		$sorting = array ();
		$sorting [] = JHTML::_ ( 'select.option', 'Default', JText::_ ( 'SORT_REGULAR ' ) );
		$sorting [] = JHTML::_ ( 'select.option', 'Numeric', JText::_ ( 'SORT_NUMERIC ' ) );
		$sorting [] = JHTML::_ ( 'select.option', 'String', JText::_ ( 'SORT_STRING ' ) );
		
		$ordering = array ();
		$ordering [] = JHTML::_ ( 'select.option', 'Ascending', JText::_ ( 'SORT_ASC ' ) );
		$ordering [] = JHTML::_ ( 'select.option', 'Descending', JText::_ ( 'SORT_DESC ' ) );
		
		$lists = array ();
		//TODO fix sorting problem
		$lists ['sorting'] = JHTML::_ ( 'select.genericlist', $sorting, 'sorting', 'class="inputbox"', 'value', 'text', $state->get ( 'sorting' ) );
		
		$lists ['ordering'] = JHTML::_ ( 'select.genericlist', $ordering, 'ordering', 'class="inputbox"', 'value', 'text', $state->get ( 'ordering' ) );
		
		$searchphrases = array ();
		$searchphrases [] = JHTML::_ ( 'select.option', 'all', JText::_ ( 'All words' ) );
		$searchphrases [] = JHTML::_ ( 'select.option', 'any', JText::_ ( 'Any words' ) );
		$searchphrases [] = JHTML::_ ( 'select.option', 'exact', JText::_ ( 'Exact phrase' ) );
		$lists ['searchphrase'] = JHTML::_ ( 'select.radiolist', $searchphrases, 'searchphrase', '', 'value', 'text', $state->get ( 'match' ) );
		$fields = &$this->get ( 'fields' );
		sort ( $fields );
		
		$field_list = "";
		foreach ( $fields as $field ) {
			$field_list .= '"' . $field . '",';
		}
		$document->addScriptDeclaration ( '
			
        	$(function() {
        		var availableTags = [' . $field_list . '];
        		function split( val ) {
        			return val.split( /:\s*/ );
				}
				function extractLast( term ) {
					return split( term ).pop();
				}
        		
				$( "#search_searchword" )// don\'t navigate away from the field on tab when selecting an item
			.bind( "keydown", function( event ) {
				if ( event.keyCode === $.ui.keyCode.TAB &&
						$( this ).data( "autocomplete" ).menu.active ) {
					event.preventDefault();
				}
			})
			.autocomplete({
				minLength: 0,
				source: function( request, response ) {
					// delegate back to autocomplete, but extract the last term
					response( $.ui.autocomplete.filter(
						availableTags, extractLast( request.term ) ) );
				},
				focus: function() {
					// prevent value inserted on focus
					return false;
				},
				select: function( event, ui ) {
					var terms = split( this.value );
					// remove the current input
					terms.pop();
					// add the selected item
					terms.push( ui.item.value );
					// add placeholder to get the comma-and-space at the end
					terms.push( "" );
					this.value = terms.join( ": " );
					return false;
				}
			});
				
			});      
			
        ' );
		
		//TODO refactor this and move into the model and add some security measures - maybe they arent necessary, cause the search is used
		// by trusted users and Lucene sanitazes the query via the query parser
		$state->set ( 'query', $query );
		
		//prepare query
		if (JuceneHelper::preprocessQuery ( $query )) {
			$error = JText::_ ( 'SHORTSTRING' );
		}
		
		if (! $error) {
			$results = &$this->get ( 'data' );			
			$total = &$this->get ( 'total' );
			$pagination = &$this->get ( 'pagination' );
			
			$count = 0;
			
			for($i = 0; $i < count ( $results ); $i ++) {
				//TODO add Jucene Highlighting here
				$row = &$results [$i]->title;
				
				//TODO add content preparation here - security checks or prepare content for displaying
				//$row = SearchHelper::prepareSearchContent( $row, 200, $needle );
				

				$result = & $results [$i];
				$result->count = $i + 1;
			}
		
		}
		
		$this->result = JText::sprintf ( 'JUCENETOTALRESULTSFOUND', $total );
		
		$this->assignRef ( 'pagination', $pagination );
		$this->assignRef ( 'results', $results );
		$this->assignRef ( 'lists', $lists );
		$this->assignRef ( 'params', $params );
		
		$this->assign ( 'service_link', $service_link );
		$this->assign ( 'ordering', $state->get ( 'ordering' ) );
		$this->assign ( 'sorting', $state->get ( 'sorting' ) );
		$this->assign ( 'query', $state->get ( 'query' ) );
		$this->assign ( 'field_list', $field_list );
		$this->assign ( 'total', $total );
		$this->assign ( 'error', $error );
		$this->assign ( 'action', $uri->toString () );
		
		parent::display ( $tpl );
	}
}
