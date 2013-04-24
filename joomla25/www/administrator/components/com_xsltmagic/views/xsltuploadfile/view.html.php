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
 * Admin detail view for uploading file.
 *
 * @package com_kbi
 */
class XsltmagicViewXsltuploadfile extends JView {

     /*!@function setToolbar
        @abstract - function setting toolbar for this view
     */
    function setToolbar(){
        $task = JRequest::getVar( 'task', '', 'method', 'string');

        JToolBarHelper::title( JText::_( 'XSLT Editor Upload File' ) . ($task == 'addNew' ? ': <small><small>[ '. JText::_( 'New' ) .' ]</small></small>' : ': <small><small>[ '. JText::_( 'Edit' ) .' ]</small></small>'), 'generic.png' );
        JToolBarHelper::save( 'importFile', 'Upload File' );
        JToolBarHelper::cancel( 'cancel' );
    }

	/**
	 * @function display
	 * abstract initializing view
	 */
	function display($tpl = NULL){
        global $option, $mainframe;
        self::setToolbar();

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