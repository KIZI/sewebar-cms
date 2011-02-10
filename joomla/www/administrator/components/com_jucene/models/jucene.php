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

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.helper');
jimport( 'joomla.application.component.model');

/**
 * Content Component Article Model
 *
 * @package		Joomla
 * @subpackage	Content
 * @since		1.5
 */
class JuceneModelJucene extends JModel
{
	var $_data = null;
	/**
	 * php exec time etc...
	 */
	function getStatus(){
	
	}
	
	function getIndexInfo(){
	
	
	}
	
	function getData(){
		
		if(empty($this->_data)){
			
			$arr = array();
			$arr['max_execution_time'] = ini_get('max_execution_time');
			$arr['memory_limit'] = ini_get('memory_limit');
			
			$this->_data = $arr;
			
		}
		return $this->_data;
	}



}