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
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.model');
/**
 * Search Component Search Model
 *
 * @package		Joomla
 * @subpackage	Search
 * @since 1.5
 */
class JuceneModelJucene extends JModel
{
	/**
	 * Search data array
	 *
	 * @var array
	 */
	var $_data = null;
	
	/**
	 * Indexed fields array
	 *
	 * @var array
	 */
	var $_fields = null;
	
	/**
	 * Search total
	 *
	 * @var integer
	 */
	var $_total = null;

	/**
	 * Pagination object
	 *
	 * @var object
	 */
	var $_pagination = null;

	/**
	 * Constructor
	 *
	 * @since 1.5
	 */
	function __construct()
	{
		parent::__construct();

		global $mainframe;
		
		//Get configuration
		$config = JFactory::getConfig();

		// Get the pagination request variables
		$this->setState('limit', $mainframe->getUserStateFromRequest('com_jucene.limit', 'limit', $config->getValue('config.list_limit'), 'int'));
		$this->setState('limitstart', JRequest::getVar('limitstart', 0, '', 'int'));

		// Set the search query
		$query			= urldecode(JRequest::getString('searchword'));		
		//TODO find a solution how to implement sorting
		$sorting		= urldecode(JRequest::getString('sorting', SORT_REGULAR));
		//TODO find a solution how to implement ordering
		$ordering		= urldecode(JRequest::getString('ordering',SORT_ASC));
		$this->setSearch($query,$sorting,$ordering);

	}

	/**
	 * Method to set the search parameters
	 *
	 * @access	public
	 * @param string search string
 	 * @param string mathcing option, exact|any|all
 	 * @param string ordering option, newest|oldest|popular|alpha|category
	 */
	function setSearch($query, $sorting = SORT_REGULAR, $ordering = SORT_ASC)
	{
		if(isset($query)) {
			$this->setState('query', $query);
		}
		if(isset($sorting)) {
			$this->setState('sorting', $sorting);
		}
		if(isset($ordering)) {
			$this->setState('ordering', $ordering);
		}
	}

	
	/**
	 * Method to get weblink item data for the category
	 *
	 * @access public
	 * @return array
	 */
	function getData($arQuery = null)
	{
		// Lets load the content if it doesn't already exist
		
		if (empty($this->_data))
		{
			
            $query = $this->getState('query');
			//TODO add here a error message - missing plugin
			
			if(substr ( $query, 0, 5 ) == '<?xml' && $arQuery != null){
			     $query = $this->_prepareARQuery($arQuery);
		    }
		    JPluginHelper::importPlugin( 'jucene');
        	$dispatcher =& JDispatcher::getInstance();
        	
			$results = $dispatcher->trigger( 'onJuceneSearch', array($query));

			$rows = array();
			foreach($results AS $result) {
				$rows = array_merge( (array) $rows, (array) $result);
			}

			$this->_total	= count($rows);
			if($this->getState('limit') > 0) {
				$this->_data    = array_splice($rows, $this->getState('limitstart'), $this->getState('limit'));
			} else {
				$this->_data = $rows;
			}
		}
		
		return $this->_data;
	}
	
	
	
	function _prepareARQuery($query){
	       $dom = new DOMDocument ();
        //decide which field contains PMML doc
        

        
        //first test is to decide which field contains the pmml doc. This is just a test to decide if it really is one:-). God help us.
        //if (substr ( $xml_field, 0, 5 ) == '<?xml'){
        

        $xslt = new DOMDocument ();
        
        $error = false;
        //load xslt stylesheet
        if (! @$xslt->load ( JPATH_COMPONENT . DS . 'xslt/arquery.xsl' )) {
            $error = true;
            $this->raiseMessage ( "XSLTLOADERROR", 'error' );
        
        }
        
        $proc = new XSLTProcessor ();
        if (! $proc->importStylesheet ( $xslt )) {
            $error = true;
            $this->raiseMessage ( "XSLTIMPORTERROR", 'error' );
        }
        
        /*}else{
            $xml_field = $record ['fulltext'].$record ['introtext'];
        }*/
        
           
        
        if ($dom->loadXML ( $query ) && ! $error) {
            
            //simplify the document - prepare it for the indexation process
            $xslOutput = $proc->transformToXml ( $dom );
            
            //create new DOM document to preserve output and transform the XML to the indexable one
            $transXml = new DOMDocument ();
            $transXml->preserveWhitespace = false;
            
            //unset unneccessary variables
            unset ( $xslOutput );
            unset ( $dom );
            unset ( $xslt );
            
            return @$transXml->loadXML ( $xslOutput );
        }
	}
	
	
	/**
	 * 
	 */
	function getFields(){
		if (empty($this->_fields))
		{
			
		    $index = JuceneHelper::getIndex();
		    $results = $index->getFieldNames();
			$this->_fields = $results;
		}
		
		return $this->_fields;
	}
	/**
	 * Method to get the total number of hits
	 *
	 * @access public
	 * @return integer
	 */
	function getTotal()
	{
		return $this->_total;
	}

	/**
	 * Method to get a pagination object of the weblink items for the category
	 *
	 * @access public
	 * @return integer
	 */
	function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}

	
}
