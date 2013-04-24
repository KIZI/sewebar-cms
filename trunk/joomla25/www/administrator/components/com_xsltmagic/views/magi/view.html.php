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
require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_xsltmagic'.DS.'models'.DS.'xslts.php');
          

/**
 * Admin detail view for XSLT magic Menu.
 *
 * @package com_xsltmagic
 */
class XsltMagicViewMagi extends JView {
 
     /*!@function setToolbar
        @abstract - function setting toolbar for this view
     */
    function setToolbar(){
        $task = JRequest::getVar( 'task', '', 'method', 'string');

        JToolBarHelper::title( JText::_( 'Magic 2 Menu' ) . ($task == 'add' ? ': <small><small>[ '. JText::_( 'New' ) .' ]</small></small>' : ': <small><small>[ '. JText::_( 'Edit' ) .' ]</small></small>'), 'generic.png' );
        JToolBarHelper::save( 'save' );
        JToolBarHelper::apply('apply');
        JToolBarHelper::cancel( 'cancel' );
    }

	/**
	 * @function display
	 * abstract getting data for display item
	 */
	function display($tpl = NULL){
        global $option, $mainframe;
        $task = JRequest::getVar( 'task', '', 'method', 'string'); 
        self::setToolbar();

        JRequest::setVar( 'hidemainmenu', 1 );
        $id = JRequest::getVar('cid', array(0), 'method', 'array');

        $model =& $this->getModel();
        $user =& JFactory::getUser();
        if ($task != 'add'){
          $source = $model->getStyle($id[0]);
        }
        $this->assignRef('row', $source);
        $this->assignRef('option', $option);
        $this->assignRef('name', $user->name);
        $this->_xslts = new XsltmagicModelXslts;

        parent::display($tpl);
    }
}
?>