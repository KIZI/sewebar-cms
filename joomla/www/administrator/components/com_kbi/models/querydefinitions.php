<?php
/**
 * @version		$Id: queryDefinitions.php 180 2011-04-12 09:33:58Z hazucha.andrej@gmail.com $
 * @package		com_kbi
 * @author		Andrej Hazucha
 * @copyright	Copyright (C) 2010 All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.model' );

/**
 * JModel for query definitions. Cooperates with TableQueryDefinition.
 *
 * @package com_kbi
 */
class KbiModelQuerydefinitions extends JModel
{
	var $_qds = null;
	var $_qd = null;

	public function getList(&$total, $limitstart, $limit, $search = '', $orderby = '', $where = array())
	{
		global $option;

		$orderby = '';

		if(!$this->_qds)
		{
			if ($search) {
				//$where[] = 'LOWER(name) LIKE '.$this->_db->Quote( '%'.$this->_db->getEscaped( $search, true ).'%', false );
			}

			$where		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

			// get the total number of records
			$query = "SELECT * FROM #__kbi_querydefinitions $where $orderby";

			$this->_db->setQuery( $query );
			$this->_db->query();
			$total = $this->_db->getNumRows();

			$this->_db->setQuery( $query, $limitstart, $limit );
			$this->_qds = $this->_db->loadObjectList();

			foreach($this->_qds as &$row)
			{
				$row->link = JRoute::_("index.php?option={$option}&id[]={$row->id}&task=edit&controller=queryDefinitions");
				$row->checked_out = false;
			}
		}

		return $this->_qds;
	}

	public function getQuery($id)
	{
		if(!$this->_qd || $this->_qd->id != $id)
		{
			$query = "SELECT * FROM #__kbi_querydefinitions WHERE id = '{$id}'";
			$this->_db->setQuery($query);
			$this->_qd = $this->_db->loadObject();
		}

		return $this->_qd;
	}

	public function getAssocList()
	{
		return $this->getList($total, 0, 0);
	}

}

?>