<?php
/**
 * @version		$Id:$
 * @package		com_xsltmagic
 * @author		David Fišer
 * @copyright	Copyright (C) 2011 All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.view' );

/**
 * Admin list view for XSLT magic menu.
 *
 * @package com_xsltmagic
 */
class XsltmagicViewMagic extends JView {
 
     /*!@function setToolbar
        @abstract - function setting toolbar for this view
     */
    function setToolbar(){
        JToolBarHelper::title( JText::_( 'Magic 2 Menu' ), 'generic.png' );
        JToolBarHelper::deleteList( '', 'remove' );
        JToolBarHelper::editListX( 'edit' );
        JToolBarHelper::addNewX( 'add' );
        JToolBarHelper::preferences( 'com_xsltmagic', '230', '570', 'Parameters' );
    }

	/**
	 * @function display
	 * abstract display list of rules
	 */
    function display($tpl = NULL){
        global $option;
        self::setToolbar();
        parent::display($tpl);
        $root =  'administrator/components/'.$option.'/';
        JHTML::stylesheet('default.css', $root.'css/' );
    }
}
?>