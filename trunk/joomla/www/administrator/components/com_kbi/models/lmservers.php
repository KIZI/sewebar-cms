<?php
/**
 * @version		$Id: sources.php 15 2011-02-11 00:57:01Z hazucha.andrej@gmail.com $
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
class KbiModelLmservers extends JModel {

	var $_servers = null;
	var $_server = null;

	public function getList(&$total, $limitstart, $limit, $search = '', $orderby = '', $where = array())
	{
		global $option;

		if(!$this->_servers)
		{
			if (!empty($search))
			{
				$where[] = 'LOWER(name) LIKE '.$this->_db->Quote( '%'.$this->_db->getEscaped( $search, true ).'%', false );
			}

			$where = count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '';

			// get the total number of records
			$query = "SELECT * FROM #__kbi_lmservers $where $orderby";

			$this->_db->setQuery( $query );
			$this->_db->query();
			$total = $this->_db->getNumRows();

			$this->_db->setQuery( $query, $limitstart, $limit );
			$this->_servers = $this->_db->loadObjectList();

			foreach($this->_servers as &$row)
			{
				$row->link = JRoute::_("index.php?option={$option}&id[]={$row->id}&task=edit&controller=lmservers");
				$row->checked_out = false;
			}
		}

		return $this->_servers;
	}

	public function getLmserver($id)
	{
		$query = "SELECT * FROM #__kbi_lmservers WHERE id = '{$id}'";
		$this->_db->setQuery($query);
		$this->_server = $this->_db->loadObject();

		return $this->_server;
	}

	public function getAssocList()
	{
		return $this->getList($total, 0, 0);
	}

}

?>