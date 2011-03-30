<?php
/**
 * @version		$Id$
 * @package		KBI
 * @author		Andrej Hazucha
 * @copyright	Copyright (C) 2010 All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

require_once dirname(__FILE__) . '/../KBIntegratorSynchronable.php';
//require_once dirname(__FILE__).'/../../administrator/components/com_jucene/controllers/controller.php';

/**
 * IKBIntegrator implementation for jucene (Joomla + Lucene).
 *
 * @package KBI
 */
class Jucene extends KBIntegratorSynchronable
{
	public function getMethod()
	{
		return isset($this->config['method']) ? $this->config['method'] : 'POST';
	}

	public function __construct(Array $config)
	{
		parent::__construct($config);
	}

	public function queryPost($query)
	{
		$server = 'http://'.$_SERVER['SERVER_NAME'];
		//$server = 'http://joomla.drupaler.cz';
		//$url = 'http://joomla.drupaler.cz/component/jucene/DistrictR-Praha/?sorting=SORT_STRING&ordering=';
		$url = "$server/index.php?option=com_jucene&task=arsearch&format=raw";

		//$post['sorting']	= JRequest::getWord('sorting', 'SORT_STRING', 'post');
		//$post['ordering']	= JRequest::getWord('ordering', null, 'post');
		//$post['limit']  = JRequest::getInt('limit', null, 'post');

		$postdata = array(
			'searchword' => $query,
			'sorting' => 'SORT_STRING',
			'ordering' => ''
		);

		return $this->requestCurlPost($url, $postdata);
	}

	/**
	 *
	 * @todo Implement
	 * @see ISynchronable::getDocuments()
	 */
	public function getDocuments()
	{
		$documents = array();

		return $documents;
	}

	/*
	 *
	 * @see http://dtbaker.com.au/random-bits/uploading-a-file-using-curl-in-php.html
	 */
	public function addDocument($id, $document, $path = true)
	{
		$jucene = new JuceneController();

		$jucene->kbiInsertToIndex($path ? file_get_contents($document) : $document);
	}

	/**
	 *
	 * @todo Implement
	 * @see ISynchronable::getDocument()
	 */
	public function getDocument($id)
	{
	}

	/**
	 *
	 * @todo Implement
	 * @see ISynchronable::deleteDocument()
	 */
	public function deleteDocument($id)
	{
	}

	/**
	 * @todo Implement
	 * @see ISynchronable::getDataDescription()
	 */
	public function getDataDescription()
	{
		return '';
	}
}