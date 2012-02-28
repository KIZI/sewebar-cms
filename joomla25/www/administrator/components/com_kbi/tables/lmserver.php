<?php
/**
 * @version		$Id: source.php 147 2011-03-28 09:19:11Z hazucha.andrej@gmail.com $
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
class TablelmServer extends JTable
{
	/** @var int */
	var $id = null;
	/** @var string */
	var $name = '';
	/** @var string */
	var $url = '';

	function __construct( &$_db )
	{
		parent::__construct( '#__kbi_lmservers', 'id', $_db );
	}
}
?>
