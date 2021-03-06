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
JPluginHelper::importPlugin('jfirephp', 'system');

/**
 * JModel for data sources. Cooperates with TableSource.
 *
 * @package com_kbi
 */
class KbiModelSources extends JModel {

	var $_sources = null;
	var $_source = null;

	public function getList(&$total, $limitstart, $limit, $search = '', $orderby = '', $where = array())
	{
		global $option;

		if(!$this->_sources)
		{
			if (!empty($search))
			{
				$where[] = 'LOWER(name) LIKE '.$this->_db->Quote( '%'.$this->_db->getEscaped( $search, true ).'%', false );
			}

			$where = count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '';

			// get the total number of records
			$query = "SELECT * FROM #__kbi_sources $where $orderby";

			$this->_db->setQuery( $query );
			$this->_db->query();
			$total = $this->_db->getNumRows();

			$this->_db->setQuery( $query, $limitstart, $limit );
			$this->_sources = $this->_db->loadObjectList();

			foreach($this->_sources as &$row)
			{
				$row->link = JRoute::_("index.php?option={$option}&id[]={$row->id}&task=edit");
				$row->checked_out = false;
			}
		}

		return $this->_sources;
	}

	public function getSource($id)
	{
		$query = "SELECT * FROM #__kbi_sources WHERE id = '{$id}'";
		$this->_db->setQuery($query);
		$this->_source = $this->_db->loadObject();

		return $this->_source;
	}

	public function getAssocList()
	{
		return $this->getList($total, 0, 0);
	}

}

?>