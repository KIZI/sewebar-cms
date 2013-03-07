<?php
/**
 * @version		$Id$
 * @package		KBI
 * @author		Andrej Hazucha
 * @copyright	Copyright (C) 2010 All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

require_once dirname(__FILE__).'/../KBIntegrator.php';
require_once dirname(__FILE__).'/../IHasDataDictionary.php';

/**
 * IKBIntegrator implementation for LISp-Miner via SEWEBAR Connect web interface.
 *
 * @package KBI
 */
class LispMiner extends KBIntegrator implements IHasDataDictionary
{
	public function getMethod()
	{
		return isset($this->config['method']) ? $this->config['method'] : 'POST';
	}

	public function getMinerId($default = NULL)
	{
		return isset($this->config['miner_id']) ? $this->config['miner_id'] : $default;
	}

	public function getMatrixName()
	{
		return isset($this->config['matrix']) ? $this->config['matrix'] : 'Loans';
	}

	public function getPort()
	{
		return isset($this->config['port']) ? $this->config['port'] : 80;
	}

	public function getPooler()
	{
		return isset($this->config['pooler']) ? $this->config['pooler'] : 'task';
	}

	public function __construct($config)
	{
		parent::__construct($config);
	}

	protected function parseRegisterResponse($response)
	{
		$xml_response = simplexml_load_string($response);

		if($xml_response['status'] == 'failure') {
			throw new Exception($xml_response->message);
		} else if($xml_response['status'] == 'success') {
			return (string)$xml_response['id'];
		}

		throw new Exception(sprintf('Response not in expected format (%s)', htmlspecialchars($response)));
	}

	protected function parseImportResponse($response)
	{
		$xml_response = simplexml_load_string($response);

		if($xml_response['status'] == 'failure') {
			throw new Exception($xml_response->message);
		} else if($xml_response['status'] == 'success') {
			return (string)$xml_response->message;
		}

		throw new Exception('Response not in expected format');
	}

	public function register($db_cfg)
	{
		$url = trim($this->getUrl(), '/');

		$response = $this->requestPost("$url/Application/Register", $db_cfg);

		KBIDebug::log(array('config' => $db_cfg, 'response' => $response), "Miner registered");

		return $this->parseRegisterResponse($response);
	}

	public function unregister($server_id = NULL)
	{
		$url = trim($this->getUrl(), '/');

		$response = $this->requestCurl("$url/Application/Remove", array('guid' => $this->getMinerId($server_id)));

		$xml_response = simplexml_load_string($response);

		if($xml_response['status'] == 'failure') {
			throw new Exception($xml_response->message);
		} else if($xml_response['status'] == 'success') {
			KBIDebug::log("Miner unregistered/removed");
			return;
		}

		throw new Exception(sprintf('Response not in expected format (%s)', htmlspecialchars($response)));
	}

	public function importDataDictionary($dataDictionary, $server_id = NULL)
	{
		$server_id = $server_id == NULL ? $this->getMinerId() : $server_id;

		if($server_id === NULL) {
			throw new Exception('LISpMiner ID was not provided.');
		}

		$url = trim($this->getUrl(), '/');

		$data = array(
			'content' => $dataDictionary,
			'guid' => $server_id
		);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "$url/DataDictionary/Import");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->encodeData($data));
		curl_setopt($ch, CURLOPT_VERBOSE, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);

		$response = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);

		KBIDebug::log($response, "Import executed");

		return $this->parseRegisterResponse($response);
	}

	public function getDataDescription($params=null)
	{
		$url = trim($this->getUrl(), '/');
		$url = "$url/DataDictionary/Export";

		$data = array(
			'guid' => $this->getMinerId(),
			'matrix' => $this->getMatrixName(),
			'template' => (@$params['template']?$params['template']:'')
		);

		KBIDebug::info(array($url, $data));
		$dd = $this->requestCurlPost($url, $data);

		return trim($dd);
	}

	public function queryPost($query, $options)
	{
		// $options['export'] = '9741046ed676ec7470cb043db2881a094e36b554';

		if($this->getMinerId() === NULL) {
			throw new Exception('LISpMiner ID was not provided.');
		}

		$url = trim($this->getUrl(), '/');

		$data = array(
			'guid' => $this->getMinerId()
		);

		if(isset($options['export'])) {
			$task = $options['export'];
			$url = "$url/Task/Export";
                         
			$data['task'] = $task;

			KBIDebug::info("Making just export of task '{$task}' (no generation).", 'LISpMiner');
		} else {
			$pooler = $this->getPooler();

			if(isset($options['pooler'])) {
				$pooler = $options['pooler'];
				KBIDebug::info("Using '{$pooler}' as pooler", 'LISpMiner');
			}

			switch($pooler) {
				case 'grid':
					$url = "$url/TaskGen/GridPool";
				break;
				case 'proc':
					$url = "$url/TaskGen/ProcPool";
					break;
				case 'task':
				default:
					$url = "$url/TaskGen/TaskPool";
			}

			$data['content'] = $query;
		}

		if(isset($options['template'])) {
			$data['template'] = $options['template'];
			KBIDebug::info("Using LM exporting template {$data['template']}", 'LISpMiner');
		}

		KBIDebug::log(array('URL' => $url, 'POST' => $data), 'LM Query');

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);

		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->encodeData($data));
		curl_setopt($ch, CURLOPT_VERBOSE, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);

		// gain task results from LISpMiner
		$response = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);

		return $response;
	}

	public function cancelTask($taskName)
	{
		$url = trim($this->getUrl(), '/');

		switch($this->getPooler()) {
			case 'grid':
				$url = "$url/TaskGen/GridPool/Cancel";
				break;
			case 'proc':
				$url = "$url/TaskGen/ProcPool/Cancel";
			break;
			case 'task':
			default:
				$url = "$url/TaskGen/TaskPool/Cancel";
		}

		$data = array(
			'guid' => $this->getMinerId(),
			'taskName' => $taskName
		);

		KBIDebug::info(array($url, $data));

		$dd = $this->requestCurl($url, $data);

		return trim($dd);
	}
}