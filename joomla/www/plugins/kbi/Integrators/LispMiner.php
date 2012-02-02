<?php
/**
 * @version		$Id$
 * @package		KBI
 * @author		Andrej Hazucha
 * @copyright	Copyright (C) 2010 All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

require_once dirname(__FILE__).'/../KBIntegrator.php';

/**
 * IKBIntegrator implementation for LISp-Miner via SEWEBAR Connect web interface.
 *
 * @package KBI
 */
class LispMiner extends KBIntegrator
{
	public function getMethod()
	{
		return isset($this->config['method']) ? $this->config['method'] : 'POST';
	}

	public function getMinerId()
	{
		return isset($this->config['miner_id']) ? $this->config['miner_id'] : NULL;
	}
	
	public function __construct($config)
	{
		parent::__construct($config);
	}
	
	protected function parseResponse($response)
	{
		$xml_response = simplexml_load_string($response);
		
		if($xml_response['status'] == 'failure') {
			throw new Exception($xml_response->message);
		} else if($xml_response['status'] == 'success') {
			return (string)$xml_response['id'];
		}
		
		throw new Exception('Response not in expected format');
	}

	public function register($db_cfg)
	{
		$url = trim($this->getUrl(), '/');
		
		$response = $this->requestGet("$url/Register.ashx", $db_cfg);

		KBIDebug::log($response, "Miner registered");
		
		return $this->parseResponse($response);
	}
	
	public function importDataDictionary($dataDictionary, $server_id = NULL, $cookieFile = NULL)
	{
		$url = trim($this->getUrl(), '/');
		
		$data = array(
			'content' => $dataDictionary,
		);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "$url/Import.ashx");
		
		if ($server_id)
		{
			$data['guid'] = $server_id;
		}
		else if($cookieFile)
		{
			curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
			curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
		}
		else
		{
			throw new Exception('Miner ID or session cookie is required'); 
		}
		
		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->encodeData($data));
		curl_setopt($ch, CURLOPT_VERBOSE, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		
		$response = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);
		
		KBIDebug::log($response, "Import executed");
		
		return $this->parseResponse($response);
	}

	public function queryPost($query)
	{
		$url = trim($this->getUrl(), '/');

		KBIDebug::log($url, 'trimed');
		KBIDebug::log($this->getUrl(), 'orig');
		
		$data = array(
			'content' => $query,
		);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "$url/Task.ashx");
				
		if($this->getMinerId())
		{
			//default data dictionary
			$dataDictionary = file_get_contents('/Volumes/Data/svn/sewebar/trunk/joomla/www/components/com_arbuilder/assets/barboraForLMImport.pmml'); 
			
			// session based
			if(session_id() === '') {
				session_start();
			}
	
			// ulozeni session id pro komunikace s LISpMiner-em
			$ckfile = JPATH_CACHE . "/cookie_".session_id();
	
			// Pokus session s LISpMiner-em jeste nezacla tak posleme data pro inicializaci
			if(!file_exists($ckfile)) {
				$this->importDataDictionary($dataDictionary, NULL, $ckfile);
			}
			
			curl_setopt($ch, CURLOPT_COOKIEFILE, $ckfile);
			curl_setopt($ch, CURLOPT_COOKIEJAR, $ckfile);			
		}
		else
		{
			$data['guid'] = $this->getMinerId();
		}
		
		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->encodeData($data));
		curl_setopt($ch, CURLOPT_VERBOSE, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);

		// ziskani vysledku tasku z LISpMiner-a
		$response = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);

		return $response;
	}
}