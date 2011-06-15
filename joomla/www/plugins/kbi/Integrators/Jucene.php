<?php
/**
 * @version		$Id$
 * @package		KBI
 * @author		Andrej Hazucha
 * @copyright	Copyright (C) 2010 All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

require_once dirname(__FILE__) . '/../KBIntegratorSynchronable.php';

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

	/**
	 * Method called when querying with POST data. Service should return XML with query results.
	 *
	 * @return string  XML with query results
	 */
	public function queryPost($query)
	{
		$server = $this->getUrl();
		//$server = 'http://joomla.drupaler.cz';
		//$url = 'http://joomla.drupaler.cz/component/jucene/DistrictR-Praha/?sorting=SORT_STRING&ordering=';
		$url = "$server/index.php?option=com_jucene&controller=ApiKbi";

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
	 * @return string  XML with indexed documents
	 */
	public function getDocuments()
	{
		$documents = array();

		return $documents;
	}

	/**
	 *
	 * Sends PMML document to be indexed to service
	 *
	 * @see http://dtbaker.com.au/random-bits/uploading-a-file-using-curl-in-php.html
	 */
	public function addDocument($id, $document, $path = true)
	{
		/*$jucene = new JuceneControllerApiKbi();

		var_dump($jucene->insertToIndexKbi($document));*/
	}

	/**
	 *
	 * @return string  indexed PMML document
	 */
	public function getDocument($id)
	{
	}

	/**
	 *
	 * Trigers document to be deleted from service.
	 */
	public function deleteDocument($id)
	{
	}

	/**
	 *
	 * @return string  data description from PMML documents if possible
	 */
	public function getDataDescription()
	{
		return '';
	}
}