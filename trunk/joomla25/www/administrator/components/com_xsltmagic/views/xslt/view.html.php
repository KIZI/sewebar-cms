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
 * Admin detail view for XSLT Editor item - file.
 *
 * @package com_xsltmagic
 */
class XsltmagicViewXslt extends JView {
 
     /*!@function setToolbar
        @abstract - function setting toolbar for this view
     */	function setToolbar() {
        $task = JRequest::getVar( 'task', '', 'method', 'string');

        JToolBarHelper::title( JText::_( 'XSLT Editor File' ) . ($task == 'add' ? ': <small><small>[ '. JText::_( 'New' ) .' ]</small></small>' : ': <small><small>[ '. JText::_( 'Edit' ) .' ]</small></small>'), 'generic.png' );
        JToolBarHelper::save( 'save' );
        JToolBarHelper::apply('apply');
        JToolBarHelper::cancel( 'cancel' );
    }

	/**
	 * @function display
	 * abstract getting data for display item
	 */
    function display($tpl = NULL){
        global $mainframe;
        $option="com_xsltmagic";
        self::setToolbar();

        JRequest::setVar( 'hidemainmenu', 1 );
        $id = JRequest::getVar('id', array(0), 'method', 'array');

        $model =& $this->getModel();
        $user =& JFactory::getUser();
        $source = $model->getStyle($id[0]);

        $root =  'administrator/components/'.$option.'/';
        // declaration of CSS/JS for codemirror plugin
        JHTML::script( 'codemirror.js', $root.'js/' );
        JHTML::script( 'xml.js', $root.'js/');
        JHTML::stylesheet('xml.css', $root.'css/' );
        JHTML::stylesheet('codemirror.css', $root.'css/' );
  
        $this->assignRef('row', $source);
        $this->assignRef('option', $option);
        $this->assignRef('name', $user->name);

        parent::display($tpl);
    }
}
?>