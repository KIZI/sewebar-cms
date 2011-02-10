<?php
/**
 * @version		$Id: xslt.php 1586 2010-10-24 22:32:27Z andrej $
 * @package		com_kbi
 * @author		Andrej Hazucha
 * @copyright	Copyright (C) 2010 All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Mapping to xslts database table.
 *
 * @package com_kbi
 */
class TableXslt extends JTable
{
	/** @var int */
	var $id	= null;
	/** @var string */
	var $name = '';
	/** @var string */
	var $style = '';
	
	function __construct( &$_db )
	{
		parent::__construct( '#__kbi_xslts', 'id', $_db );
	}

}
?>
