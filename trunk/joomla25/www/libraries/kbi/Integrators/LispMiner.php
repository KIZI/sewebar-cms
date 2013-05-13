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
	//region Getters

	public function getMethod()
	{
		return isset($this->config['method']) ? $this->config['method'] : 'POST';
	}

	public function getMinerId($default = null)
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

	//endregion

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
		$url = "$url/miners";

		$response = $this->requestPost($url, $db_cfg);

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

	public function unregister($server_id = null)
	{
		$url = trim($this->getUrl(), '/');
		$url = "$url/miners/{$this->getMinerId($server_id)}";

		$client = $this->getRestClient();

		$response = $client->delete($url);

		$xml_response = simplexml_load_string($response);

		if($xml_response['status'] == 'failure') {
			throw new Exception($xml_response->message);
		} else if($xml_response['status'] == 'success') {
			KBIDebug::log("Miner unregistered/removed");
			return;
		}

		throw new Exception(sprintf('Response not in expected format (%s)', htmlspecialchars($response)));
	}

	public function importDataDictionary($dataDictionary, $server_id = null)
	{
		$server_id = $server_id == null ? $this->getMinerId() : $server_id;

		if($server_id === null) {
			throw new Exception('LISpMiner ID was not provided.');
		}

		$client = $this->getRestClient();

		$url = trim($this->getUrl(), '/');
		$url = "$url/miners/{$server_id}";

		$response = $client->patch($url, $dataDictionary);

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
		$server_id = $this->getMinerId();

		if($server_id === null) {
			throw new Exception('LISpMiner ID was not provided.');
		}

		$client = $this->getRestClient();

		$url = trim($this->getUrl(), '/');
		$url = "$url/miners/{$server_id}/DataDictionary";

		$data = array(
			'matrix' => $this->getMatrixName(),
			'template' => (@$params['template']?$params['template']:'')
		);

		KBIDebug::info(array($url, $data), "getting DataDictionary");

		$dd = $client->get($url, $data);

		return trim($dd);
	}

	public function queryPost($query, $options)
	{
		// $options['export'] = '9741046ed676ec7470cb043db2881a094e36b554';
		// TODO: add user credentials to options

		$server_id = $this->getMinerId();

		if($this->getMinerId() === null) {
			throw new Exception('LISpMiner ID was not provided.');
		}

		$url = trim($this->getUrl(), '/');

		$data = array();
		$client = $this->getRestClient();

		if(isset($options['export'])) {
			$task = $options['export'];
			$url = "$url/miners/{$server_id}/tasks/{$task}";

			KBIDebug::info("Making just export of task '{$task}' (no generation).", 'LISpMiner');
		} else {
			$pooler = $this->getPooler();

			if(isset($options['pooler'])) {
				$pooler = $options['pooler'];
				KBIDebug::info("Using '{$pooler}' as pooler", 'LISpMiner');
			}

			switch($pooler) {
				case 'grid':
					$url = "$url/miners/{$server_id}/grid";
				break;
				case 'proc':
					$url = "$url/miners/{$server_id}/proc";
					break;
				case 'task':
				default:
					$url = "$url/miners/{$server_id}/task";
			}
		}

		if(isset($options['template'])) {
			$data['template'] = $options['template'];
			KBIDebug::info("Using LM exporting template {$data['template']}", 'LISpMiner');
		}

		KBIDebug::log(array('URL' => $url, 'GET' => $data, 'POST' => $query), 'LM Query');

		return $client->post("$url?{$client->encodeData($data)}", $query);
	}

	public function cancelTask($taskName)
	{
		$server_id = $this->getMinerId();

		if($this->getMinerId() === null) {
			throw new Exception('LISpMiner ID was not provided.');
		}

		$url = trim($this->getUrl(), '/');
		$client = $this->getRestClient();

		switch($this->getPooler()) {
			case 'grid':
				$url = "$url/miners/{$server_id}/grid/{$taskName}/cancel";
				break;
			case 'proc':
				$url = "$url/miners/{$server_id}/proc/{$taskName}/cancel";
			break;
			case 'task':
			default:
				$url = "$url/miners/{$server_id}/task/{$taskName}/cancel";
		}

		KBIDebug::info(array($url), 'Canceling task');

		return $client->post($url);
	}

	public function test()
	{
		try {
			$server_id = $this->getMinerId();

			if($server_id === null) {
				throw new Exception('LISpMiner ID was not provided.');
			}

			$client = $this->getRestClient();

			$url = trim($this->getUrl(), '/');
			$url = "$url/miners/{$server_id}";

			$response = $client->get($url);

			KBIDebug::log($response, "Test executed");

			$this->parseResponse($response, '');

			return true;
		}
		catch (Exception $ex)
		{
			return false;
		}
	}

	//region SewebarKey

	/**
	 * @param string $username
	 * @param string $password
	 * @return mixed
	 */
	public function registerUser($username, $password)
	{
		// TODO: credentials for given user (from session)
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

	/**
	 * @param string $username
	 * @param string $new_username
	 * @param string $new_password
	 * @param string $new_email
	 * @param string $email_from - e-mail address for "From:" field for confirmation e-mail
	 * @param string $email_link - link for user confirmation / string {code} should be on server replaced with security code value
	 */
	public function updateOtherUser($username, $new_username, $new_password,$new_email, $email_from, $email_link)
	{
		//TODO funkce pro změnu "nevlastního" uživatelského účtu
	}

	/**
	 * @param string $username
	 * @param string $password
	 * @param string $new_username
	 * @param string $new_password
	 * @param string $new_email
	 * @return mixed
	 */
	public function updateUser($username, $password, $new_username, $new_password, $new_email)
	{    //TODO přibyl sem nový parametr $new_email
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

	/**
	 * @param string $username
	 */
	public function deleteUser($username)
	{
		//TODO doplnit imlementaci smazání uživatele
	}

	/**
	 * @param string $username
	 * @param string $security_code
	 */
	public function confirmUserPasswordUpdate($username,$security_code)
	{
		//TODO uživatelem vyvolané potvrzení hesla
	}

	/**
	 * @param string $username
	 * @param string $password
	 * @param int $db_id
	 * @param string $db_password
	 * @return mixed
	 */
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

	/**
	 * @param string $username
	 * @param string $password
	 * @param int $db_id
	 * @return string
	 */
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

	public function setDatabasePassword($username, $password, $db_id, $old_password, $new_password)
	{
		// TODO:
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

	//endregion
}