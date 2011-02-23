<?php
/**
 * @version		$Id$
 * @package		KBI
 * @author		Andrej Hazucha
 * @copyright	Copyright (C) 2010 All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

require_once 'KBIntegratorSynchronable.php';

/**
 * IKBIntegrator implementation for XQuery database.
 *
 * @package KBI
 */
class XQuery extends KBIntegratorSynchronable
{
	public function getMethod()
	{
		return isset($this->config['method']) ? $this->config['method'] : 'POST';
	}

	public function getAction()
	{
		return isset($this->config['action']) ? $this->config['action'] : 'directQuery';
	}

	public function setAction($value)
	{
		$this->config['action'] = $value;
	}

	public function getVariable()
	{
		return isset($this->config['variable']) ? $this->config['variable'] : '';
	}

	public function setVariable($value)
	{
		$this->config['variable'] = $value;
	}

	public function __construct(Array $config)
	{
		parent::__construct($config);
	}

	public function queryPost($query) {
		$postdata = array(
			'action' => $this->getAction(),
			'variable' => $this->getVariable(),
			'content' => $query,
		);

		return $this->requestCurlPost($this->getUrl(), $postdata);
	}

	public function getDocuments()
	{
		$ch = curl_init();
		$documents = array();

		$data = array(
			'action' => 'getDocsNames',
			'variable' => '',
			'content' => '',
		);

		curl_setopt($ch, CURLOPT_URL, $this->getUrl());
		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->encodeData($data));
		curl_setopt($ch, CURLOPT_VERBOSE, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);

		$response = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);

		if($info['http_code'] != '200')
		{
			throw new Exception('Error in communication');
		}
		else
		{
			$xml = simplexml_load_string($response);

			foreach($xml->children() as $doc)
			{
				$document = new stdClass;
				//$document->id = $doc->__toString();
				//http://bugs.php.net/bug.php?id=44484
				$document->id = $doc;
				$document->name = $doc;
				$documents[] = $document;
			}
		}

		return $documents;
	}

	/*
	 *
	 * @see http://dtbaker.com.au/random-bits/uploading-a-file-using-curl-in-php.html
	 */
	public function addDocument($id, $document, $path = true)
	{
		$ch = curl_init();

		$data = array(
			'action' => 'addDocument',
			'variable' => $id,
			'content'=> $path ? file_get_contents($document) : $document,
		);

		curl_setopt($ch, CURLOPT_URL, $this->getUrl());
		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->encodeData($data));
		curl_setopt($ch, CURLOPT_VERBOSE, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);

		$response = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);

		FB::log($info);
		FB::log($response);

		if($info['http_code'] != '200')
		{
			throw new Exception('Error in communication');
		}
	}

	public function getDocument($id)
	{
		$ch = curl_init();

		$data = array(
			'action' => 'getDocument',
			'variable' => $id,
			'content'=> '',
		);

		curl_setopt($ch, CURLOPT_URL, $this->getUrl());
		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->encodeData($data));
		curl_setopt($ch, CURLOPT_VERBOSE, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);

		$response = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);

		if($info['http_code'] != '200')
		{
			throw new Exception('Error in communication');
		}

		return $response;
	}

	public function deleteDocument($id)
	{
		$ch = curl_init();

		$data = array(
			'action' => 'deleteDocument',
			'variable' => $id,
			'content'=> '',
		);

		curl_setopt($ch, CURLOPT_URL, $this->getUrl());
		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->encodeData($data));
		curl_setopt($ch, CURLOPT_VERBOSE, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);

		$response = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);

		if($info['http_code'] != '200')
		{
			throw new Exception('Error in communication');
		}
	}
}