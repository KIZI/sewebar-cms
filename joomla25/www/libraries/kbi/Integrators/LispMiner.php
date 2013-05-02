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

	protected function parseResponse($response, $message)
	{
		$xml_response = simplexml_load_string($response);

		if($xml_response['status'] == 'failure') {
			throw new Exception($xml_response->message);
		} else if($xml_response['status'] == 'success') {
			// everything is OK
			return $message;
		}

		throw new Exception(sprintf('Response not in expected format (%s)', htmlspecialchars($response)));
	}

	public function register($db_cfg)
	{
		$url = trim($this->getUrl(), '/');

		$response = $this->requestPost("$url/Application/Register", $db_cfg);

		KBIDebug::log(array('config' => $db_cfg, 'response' => $response, 'url' => $url), "Miner registered");

		return $this->parseRegisterResponse($response);
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

		return $this->parseImportDataDictionaryResponse($response);
	}

	protected function parseImportDataDictionaryResponse($response)
	{
		$xml_response = simplexml_load_string($response);

		if($xml_response['status'] == 'failure') {
			throw new Exception($xml_response->message);
		} else if($xml_response['status'] == 'success') {
			return (string)$xml_response->message;
		}

		throw new Exception('Response not in expected format');
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

	public function test()
	{
		try {
			$server_id = $this->getMinerId();

			if($server_id === NULL) {
				throw new Exception('LISpMiner ID was not provided.');
			}

			$url = trim($this->getUrl(), '/');

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "$url/Application/Miner?guid=$server_id");
			curl_setopt($ch, CURLOPT_VERBOSE, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Accept: application/xml'
			));

			$response = curl_exec($ch);
			curl_close($ch);

			KBIDebug::log($response, "Test executed");

			$this->parseResponse($response, '');

			return true;
		}
		catch (Exception $ex)
		{
			return false;
		}
	}

	public function registerUser($username, $password)
	{
		$url = trim($this->getUrl(), '/');
		$url = "$url/Users/Register";

		$data = array(
			'name' => $username,
			'password' => $password
		);

		$response = $this->requestPost($url, $data);

		KBIDebug::log(array('data' => $data, 'response' => $response, 'url' => $url), "User registered");

		return $this->parseResponse($response, 'User successfully registered.');
	}

	public function updateUser($username, $password, $new_username, $new_password)
	{    //TODO tady nemohu předávat původní heslo...
		$url = trim($this->getUrl(), '/');
		$url = "$url/Users/Update";

		$data = array(
			'name' => $username,
			'password' => $password,
			'new_name' => $new_username,
			'new_password' => $new_password
		);

		$response = $this->requestCurlPost($url, $data);

		KBIDebug::log(array('data' => $data, 'response' => $response, 'url' => $url), "User updated");

		return $this->parseResponse($response, 'User successfully updated.');
	}

  public function deleteUser($username){
    //TODO doplnit imlementaci smazání uživatele
  }

	public function registerUserDatabase($username, $password, $db_id, $db_password)
	{
		$url = trim($this->getUrl(), '/');
		$url = "$url/Users/Register";

		$data = array(
			'name' => $username,
			'password' => $password,
			'db_id' => $db_id,
			'db_password' => $db_password
		);

		$response = $this->requestPost($url, $data);

		KBIDebug::log(array('data' => $data, 'response' => $response, 'url' => $url), "User registered");

		return $this->parseResponse($response, 'User successfully registered.');
	}

	public function getDatabasePassword($username, $password, $db_id)
	{
		$url = trim($this->getUrl(), '/');
		$url = "$url/Users/Get";

		$data = array(
			'name' => $username,
			'password' => $password,
			'db_id' => $db_id
		);

		$response = $this->requestGet($url, $data);

		KBIDebug::log(array('data' => $data, 'response' => $response, 'url' => $url), "Password retring");

		return $this->parseDatabasePassword($response);
	}

	protected function parseDatabasePassword($response)
	{
		$xml_response = simplexml_load_string($response);

		if($xml_response['status'] == 'failure') {
			throw new Exception($xml_response->message);
		} else if($xml_response['status'] == 'success') {
			return (string)$xml_response->database['password'];
		}

		throw new Exception('Response not in expected format');
	}
}