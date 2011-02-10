<?php
/**
 * @version		$Id: query.php 1586 2010-10-24 22:32:27Z andrej $
 * @package		com_kbi
 * @author		Andrej Hazucha
 * @copyright	Copyright (C) 2010 All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Mapping to queries database table.
 *
 * @package com_kbi
 */
class TableQuery extends JTable
{
	/** @var int */
	var $id = null;
	/** @var string */
	var $name = '';
	/** @var string */
	var $query = '';
	/** @var string */
	var $delimiter = '';
	/** @var string */
	var $dictionaryquery = '';
	/** @var string */
	var $dictionaryqueryxsl = '';
	/** @var string */
	var $featurelist = '';

	function __construct( &$_db )
	{
		parent::__construct( '#__kbi_queries', 'id', $_db );
	}

}
?>
