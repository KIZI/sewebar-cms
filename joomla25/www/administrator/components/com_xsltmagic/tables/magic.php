<?php
/**
 * @version		$Id:$
 * @package		com_xsltmagic
 * @author		David Fišer
 * @copyright	Copyright (C) 2011 All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Mapping to magic database table.
 *
 * @package com_xsltmagic
 */
class TableMagic extends JTable
{
    /** @var int */
    var $id	= null;
    /** @var string */
    var $name = '';
    /** @var string */
    var $rule = '';
    /** @var string */
    var $source = '';
    /** @var date */
    var $modified = '';
	
    function __construct( &$_db ){
        parent::__construct( '#__xslt_magic', 'id', $_db );
    }
}
?>
