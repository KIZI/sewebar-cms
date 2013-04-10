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
 * JModel for data sources. Cooperates with TableSource.
 *
 * @package com_kbi
 */
class KbiModelSources extends JModel
{
	private $_sources = null;
	private $_source = null;
	private $com_kbi = 'com_kbi';

	/**
	 * @param $data
	 * @return mixed
	 * @throws Exception
	 */
	public function save($data)
	{
		$table = JTable::getInstance('source', 'Table');

		if (!$table->bind($data)) {
			throw new Exception($table->getError());
		}

		if (!$table->check()) {
			throw new Exception($table->getError());
		}

		if (!$table->store()) {
			throw new Exception($table->getError());
		}

		if (!$table->checkIn()) {
			throw new Exception($table->getError());
		}

		return $table;
	}

	public function getList(&$total, $limitstart, $limit, $search = '', $orderby = '', $where = array())
	{
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
				$row->link = JRoute::_("index.php?option={$this->com_kbi}&id[]={$row->id}&task=edit");
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

	/**
	 * @param $id
	 * @throws Exception
	 */
	public function remove($id)
	{
		$table	=& JTable::getInstance('source', 'Table');
		$id = (int) $id;
		$source = $this->getSource($id);

		// delete miner if it is LISpMiner
		if($source && $source->type == 'LISPMINER') {
			try {
				$config = get_object_vars($source);

				JLoader::import('KBIntegrator', JPATH_LIBRARIES . DS . 'kbi');

				$miner = KBIntegrator::create($config);
				$miner->unregister();
			} catch (Exception $ex) {
				// Just log it
				KBIDebug::log($ex->getMessage());
			}
		}

		if (!$table->delete($id))
		{
			throw new Exception($table->getError());
		}
	}
}