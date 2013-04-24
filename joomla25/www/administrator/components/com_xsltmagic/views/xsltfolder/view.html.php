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
 * Admin detail view for XSLT Editor - folder.
 *
 * @package com_xsltmagic
 */
class XsltmagicViewXsltfolder extends JView {
 
     /*!@function setToolbar
        @abstract - function setting toolbar for this view
     */
    function setToolbar(){
        $task = JRequest::getVar( 'task', '', 'method', 'string');

        JToolBarHelper::title( JText::_( 'XSLT Editor Folder' ) . ($task == 'addNew' ? ': <small><small>[ '. JText::_( 'New' ) .' ]</small></small>' : ': <small><small>[ '. JText::_( 'Edit' ) .' ]</small></small>'), 'generic.png' );
        JToolBarHelper::save( 'saveF' );
        JToolBarHelper::cancel( 'cancel' );
    }

	/**
	 * @function display
	 * abstract getting data for display item
	 */
	function display($tpl = NULL){
        global $option, $mainframe;
        self::setToolbar();

        JRequest::setVar( 'hidemainmenu', 1 );
        $id = JRequest::getVar('id', array(0), 'method', 'array');

        $model =& $this->getModel();
        $user =& JFactory::getUser();
        $source = $model->getStyle($id[0]);

        $this->assignRef('row', $source);
        $this->assignRef('option', $option);
        $this->assignRef('name', $user->name);

        parent::display($tpl);
	}
}
?>