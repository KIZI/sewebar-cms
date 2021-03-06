<?php
/**
 * @version		$Id$
 * @package		com_kbi
 * @author		Andrej Hazucha
 * @copyright	Copyright (C) 2010 All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.model' );

/**
 * JModel for queries. Cooperates with TableQuery.
 *
 * @package com_kbi
 */
class KbiModelQueries extends JModel {

	var $_sources = null;
	var $_source = null;

	public function getList(&$total, $limitstart, $limit, $search = '', $orderby = '', $where = array())
	{
		global $option;

		if(!$this->_sources)
		{
			if ($search) {
				$where[] = 'LOWER(name) LIKE '.$this->_db->Quote( '%'.$this->_db->getEscaped( $search, true ).'%', false );
			}

			$where		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

			// get the total number of records
			$query = "SELECT * FROM #__kbi_queries $where $orderby";

			$this->_db->setQuery( $query );
			$this->_db->query();
			$total = $this->_db->getNumRows();

			$this->_db->setQuery( $query, $limitstart, $limit );
			$this->_sources = $this->_db->loadObjectList();

			foreach($this->_sources as &$row)
			{
				$row->link = JRoute::_("index.php?option={$option}&id[]={$row->id}&task=edit&controller=queries");
				$row->checked_out = false;
			}
		}

		return $this->_sources;
	}

	public function getQuery($id)
	{
		if(!$this->_source)
		{
			$query = "SELECT * FROM #__kbi_queries WHERE id = '{$id}'";
			$this->_db->setQuery($query);
			$this->_source = $this->_db->loadObject();
		}

		return $this->_source;
	}

	public function getAssocList()
	{
		return $this->getList($total, 0, 0);
	}

}

?>