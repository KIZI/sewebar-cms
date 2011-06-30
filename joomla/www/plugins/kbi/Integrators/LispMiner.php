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

	public function __construct($config)
	{
		parent::__construct($config);
	}

	public function queryPost($query)
	{
		$url = trim($this->getUrl(), '/');

		KBIDebug::log($url, 'trimed');
		KBIDebug::log($this->getUrl(), 'orig');

		if(session_id() === '') {
			session_start();
		}

		// ulozeni session id pro komunikace s LISpMiner-em
		$ckfile = JPATH_CACHE . "/cookie_".session_id();

		// Pokus session s LISpMiner-em jeste nezacla tak posleme data pro inicializaci
		if(!file_exists($ckfile)) {
			$data = array(
				'content' => file_get_contents('/Volumes/Data/svn/sewebar/trunk/joomla/www/components/com_arbuilder/assets/barboraForLMImport.pmml'),
			);

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "$url/Import.ashx");
			//curl_setopt($ch, CURLOPT_URL, "http://146.102.66.141/SewebarConnect/Import.ashx");
			curl_setopt($ch, CURLOPT_COOKIEFILE, $ckfile);
			curl_setopt($ch, CURLOPT_COOKIEJAR, $ckfile);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $this->encodeData($data));
			curl_setopt($ch, CURLOPT_VERBOSE, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, true);

			$response = curl_exec($ch);
			$info = curl_getinfo($ch);
			curl_close($ch);

			KBIDebug::log($response, "Import executed");
		}

		// dotaz/task pro LISpMiner
		$data = array(
			'content' => $query,
		);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "$url/Task.ashx");
		//curl_setopt($ch, CURLOPT_URL, "http://146.102.66.141/SewebarConnect/Task.ashx");
		curl_setopt($ch, CURLOPT_COOKIEFILE, $ckfile);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $ckfile);
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