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
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the WebLinks component
 *
 * @static
 * @package		Joomla
 * @subpackage	Weblinks
 * @since 1.0
 */
class JuceneViewJucene extends JView
{
	function display($tpl = null)
	{
		global $mainframe;

		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'jucene.php' );

		// Initialize some variables
		$pathway  =& $mainframe->getPathway();
		$uri      =& JFactory::getURI();

		$error	= '';
		$rows	= null;
		$total	= 0;

		// Get some data from the model
		$state 		= &$this->get('state');
		$query 		= $state->get('query');

		$params = &$mainframe->getParams();

		$menus	= &JSite::getMenu();
		$menu	= $menus->getActive();

		// because the application sets a default page title, we need to get it
		// right from the menu item itself
		if (is_object( $menu )) {
			$menu_params = new JParameter( $menu->params );
			if (!$menu_params->get( 'page_title')) {
				$params->set('page_title',	JText::_( 'Jucene Search' ));
			}
		} else {
			$params->set('page_title',	JText::_( 'Jucene Search' ));
		}

		$document	= &JFactory::getDocument();
		$document->setTitle( $params->get( 'page_title' ) );

		// Get the parameters of the active menu item
		$params	= &$mainframe->getParams();

		// built select lists
		//TODO find a solution how to implement query builder or ordering and search areas e.g. search fields
		$sorting = array();
		$sorting[] = JHTML::_('select.option',  'Default', JText::_( 'SORT_REGULAR ' ) );
		$sorting[] = JHTML::_('select.option',  'Numeric', JText::_( 'SORT_NUMERIC ' ) );
		$sorting[] = JHTML::_('select.option',  'String', JText::_( 'SORT_STRING ' ) );

		$ordering = array();
		$ordering[] = JHTML::_('select.option',  'Ascending', JText::_( 'SORT_ASC ' ) );
		$ordering[] = JHTML::_('select.option',  'Descending', JText::_( 'SORT_DESC ' ) );

		$lists = array();
		//TODO fix sorting problem
		$lists['sorting'] = JHTML::_('select.genericlist',   $sorting, 'sorting', 'class="inputbox" disabled="disabled"', 'value', 'text', $state->get('sorting') );

		$lists['ordering'] = JHTML::_('select.genericlist',   $ordering, 'ordering', 'class="inputbox" disabled="disabled"', 'value', 'text', $state->get('ordering') );

		$searchphrases 		= array();
		$searchphrases[] 	= JHTML::_('select.option',  'all', JText::_( 'All words' ) );
		$searchphrases[] 	= JHTML::_('select.option',  'any', JText::_( 'Any words' ) );
		$searchphrases[] 	= JHTML::_('select.option',  'exact', JText::_( 'Exact phrase' ) );
		$lists['searchphrase' ]= JHTML::_('select.radiolist',  $searchphrases, 'searchphrase', '', 'value', 'text', $state->get('match') );
		$fields 	= &$this->get('fields');
		sort($fields);
		//TODO rewrite accrording to JHTML class
		$field_list = "<ul><li class='parrent'><a href='#'>?</a><ul>";
			foreach($fields as $field){
				$field_list .= '<li> '.$field.' </li>';
			}
		$field_list .= "</ul></li></ul>";
		//TODO refactor this and move into the model and add some security measures - maybe they arent necessary, cause the search is used
		// by trusted users and Lucene sanitazes the query via the query parser
		$state->set('query', $query);



		//prepare query
		if(JuceneHelper::preprocessQuery($query)) {
			$error = JText::_( 'SHORTSTRING' );
		}

		if(!$error)
		{
			$results	= &$this->get('data' );
			$total		= &$this->get('total');
			$pagination	= &$this->get('pagination');

			$count 		= 0;

			for ($i=0; $i < count($results); $i++)
			{
				//TODO add Jucene Highlighting here
				$row = &$results[$i]->title;

				//TODO add content preparation here - security checks or prepare content for displaying
				//$row = SearchHelper::prepareSearchContent( $row, 200, $needle );

				$result =& $results[$i];
			    $result->count		= $i + 1;
			}

		}

		$this->result	= JText::sprintf( 'JUCENETOTALRESULTSFOUND', $total );

		$this->assignRef('pagination',  $pagination);
		$this->assignRef('results',		$results);
		$this->assignRef('lists',		$lists);
		$this->assignRef('params',		$params);

		$this->assign('ordering',		$state->get('ordering'));
		$this->assign('sorting',		$state->get('sorting'));
		$this->assign('query',			$state->get('query'));
		$this->assign('field_list',		$field_list);
		$this->assign('total',			$total);
		$this->assign('error',			$error);
		$this->assign('action', 	    $uri->toString());

		parent::display($tpl);
	}
}
