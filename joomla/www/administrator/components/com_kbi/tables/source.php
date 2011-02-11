<?php
/**
 * @version		$Id$
 * @package		com_kbi
 * @author		Andrej Hazucha
 * @copyright	Copyright (C) 2010 All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Mapping to sources database table.
 *
 * @package com_kbi
 */
class TableSource extends JTable
{
	/** @var int */
	var $id = null;
	/** @var string */
	var $name = '';
	/** @var string */
	var $url = ''; 
	/** @var string */
	var $type;
	/** @var string */
	var $method;
	/** @var string */
	var $params;	

	function __construct( &$_db )
	{
		parent::__construct( '#__kbi_sources', 'id', $_db );
	}
}
?>
