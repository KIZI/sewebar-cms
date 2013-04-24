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
 * Admin list view for XSLT Editor.
 *
 * @package com_xsltmagic
 */
class XsltmagicViewXslts extends JView {

     /*!@function setToolbar
        @abstract - function setting toolbar for this view
     */
	function setToolbar(){
        JToolBarHelper::title( JText::_( 'XSLT Editor' ), 'generic.png' );      
        JToolBarHelper::addNew('import', 'Import File');
        JToolBarHelper::deleteList( '', 'remove');
        JToolBarHelper::editListX( 'edit' );
        JToolBarHelper::addNewX( 'add', 'New Folder' );
        JToolBarHelper::preferences( 'com_xsltmagic', '230', '570', 'Parameters' );
    }

	/**
	 * @function display
	 * abstract display list of folders and files, importing css
	 */
	function display($tpl = NULL){
        $option="com_xsltmagic";
        self::setToolbar();
        parent::display($tpl);

        $root =  'administrator/components/'.$option.'/';
        JHTML::stylesheet('default.css', $root.'css/' );
	}

	/**
	 * @function checkedOutRadio
	 * abstract a little edited default function, just display radio buttons instead checkboxes
	 */
	function checkedOutRadio( &$row, $i, $identifier = 'id'){
	
        $user   =& JFactory::getUser();
        $userid = $user->get('id');

        $result = false;
        
        if(is_a($row, 'JTable')){
            $result = $row->isCheckedOut($userid);
        }else{
            $result = JTable::isCheckedOut($userid, $row->checked_out);
        }

        $checked = '';
		
        if ( $result ) {
            $checked = JHTMLGrid::_checkedOut( $row );
        }else{
            echo $this->idRadio($i, $row->name);
        }
        
        return $checked;
    }

	/**
	 * @function idRadio
	 * abstract a little edited default function, just display radio buttons instead checkboxes
	 */
   	function idRadio( $rowNum, $recId, $checkedOut=false, $name='cid'){ 
        if ( $checkedOut ) {
            return '';
        }else{
            return '<input type="radio" id="cb'.$rowNum.'" name="'.$name.'[]" value="'.$recId.'" onclick="isChecked(this.checked);" />';
        }
    } 
}
?>