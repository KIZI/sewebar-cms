<?php
/**
 * @version		$Id:$
 * @package		com_xsltmagic
 * @author		David Fišer
 * @copyright	Copyright (C) 2011 All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.model' );

/**
 * JModel for XSLT Magic menu. Cooperates with TableMagic.
 *
 * @package com_xsltmagic
 */
class XsltmagicModelMagic extends JModel {

    var $_sources = null;
    var $_source = null;

    /*! @function getList
        @abstract function generating list of rules, from database
        @param total int - num rows  
        @param limitstart int - number of items per page
        @param limit int - item count per page
        @param search string - filter search string
        @param orderby string - order by collumn 
        @return Array - list of rules
    */
    public function getList(&$total, $limitstart, $limit, $search = '', $orderby = ''){
        $option="com_xsltmagic";
      
        if(!$this->_sources){
            if ($search){
                $where[] = 'LOWER(name) LIKE '.$this->_db->Quote( '%'.$this->_db->getEscaped( $search, true ).'%', false );
            }
            
            $where = ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

            // get the total number of records
            $query = "SELECT * FROM #__xslt_magic $where $orderby";

            $this->_db->setQuery( $query, $limitstart, $limit );
            $this->_sources = $this->_db->loadObjectList();

            foreach($this->_sources as &$row){
                $row->link = JRoute::_("index.php?option={$option}&cid[]={$row->id}&task=edit&controller=magic");        
                $row->checked_out = false;
            }
        }

        return $this->_sources;
    }

     /*!@function getStyle
        @param id int - unique identifier
        @return array - DB object source
     */
     public function getStyle($id){
        if(!$this->_source){
            $query = "SELECT * FROM #__xslt_magic WHERE id = '{$id}'";
            $this->_db->setQuery($query);
            $this->_source = $this->_db->loadObject();
        }

        return $this->_source;
    }

     /*!@function getAssocList
        @abstract fuunction useful for edit item
        @return List 
     */
    public function getAssocList(){
        return $this->getList($total, 0, 0);
    }

     /*!@function getListMagic
        @abstract fuunction thats make specail output 
        @return specialized array for content plugin XSLT magic
     */
    public function getListMagic(){
        $query = "SELECT * FROM #__xslt_magic";

        $this->_db->setQuery( $query );
        $this->_db->query();
        $total = $this->_db->getNumRows();
        
        $this->_db->setQuery( $query);
        $this->_sources = $this->_db->loadObjectList();

        $config=array();
        $i=0;
  
        foreach ($this->_sources as $ole){
            $i++;
            $config[]=$ole->rule;
            $config[]=$ole->source;
  
            $config[]=$ole->modified;
  
            if ($i!==count($this->_sources)){
                $config[]='';
            }  
        }

        return $config;
    }
}
?>