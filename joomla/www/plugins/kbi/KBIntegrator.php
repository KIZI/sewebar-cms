<?php
/**
 * @version		$Id$
 * @package		KBI
 * @author		Andrej Hazucha
 * @copyright	Copyright (C) 2010 All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

require_once 'IKBIntegrator.php';
require_once 'Query.php';

/**
 * Generic implementation for IKBIntegrator.
 *
 * @package KBI
 */
class KBIntegrator implements IKBIntegrator
{
	public static function create(Array $config)
	{
		$type = strtoupper(isset($config['type']) ? $config['type'] : 'GENERIC');

		switch($type){
			case 'ONTOPIA':
				require_once 'Integrators/Ontopia.php';
				return new Ontopia($config);
			break;
			case 'SPARQL':
				require_once 'Integrators/Semsol.php';
				return new Semsol($config);
			break;
			case 'XQUERY':
				require_once 'Integrators/XQuery.php';
				return new XQuery($config);
				break;
			case 'JUCENE':
				require_once 'Integrators/Jucene.php';
				return new Jucene($config);
				break;
			case 'LISPMINER':
				require_once 'Integrators/LispMiner.php';
				return new LispMiner($config);
				break;
            case 'JOOMLA':
                require_once 'Integrators/Joomla.php';
                return new JoomlaKBIIntegrator($config);
                break;
			case 'GENERIC':
			default:
				return new KBIntegrator($config);
			break;
		}
	}

	/** @var string */
	protected $config;

	public function getName()
	{
		return isset($this->config['name']) ? $this->config['name'] : '';
	}

	public function getUrl()
	{
		return isset($this->config['url']) ? $this->config['url'] : '';
	}

	public function setUrl($value)
	{
		$this->config['url'] = $value;
	}

	public function getMethod()
	{
		return isset($this->config['method']) ? $this->config['method'] : '';
	}

	public function setMethod($value)
	{
		$this->config['method'] = $value;
	}

	public function getPort()
	{
		return isset($this->config['port']) ? $this->config['port'] : 8081;
	}

	public function setPort($value)
	{
		$this->config['port'] = $value;
	}

	public function __construct(Array $config = array())
	{
		if(isset($config['params']) && !empty($config['params'])) {
			$params = $this->parseNameValues($config['params']);
			unset($config['params']);

			$config = array_merge($config, $params);
		}

		$this->config = $config;
	}

	/**
	 * Implements the query execution. If remote source returned well-formed XML and XSLT is set the transformation is performed.
	 *
	 * @param KBIQuery | string Query
	 * @param string XSLT
     * @return The result of query execution.
	 */
	public function query($query, $xsl = '')
	{
		$options = array();

		if($query instanceof KBIQuery) {
			$query = $query->proccessQuery($options);
		}

		$method = strtoupper($this->getMethod());

		switch($method) {
			case 'POST':
				$xml_data = $this->queryPost($query, $options);
				break;
			case 'SOAP':
				$xml_data = $this->querySoap($query);
				break;
			default:
			case 'GET':
				$xml_data = $this->queryGet($query);
				break;
		}

		KBIDebug::log(array($xml_data), 'Raw result');

		if(empty($xsl)){
			return $xml_data;
		}

		$xml = new DOMDocument();
		if($xml->loadXML($xml_data)) {
			// Create XSLT document
			$xsl_document = new DOMDocument();
			$xsl_document->loadXML($xsl, LIBXML_NOCDATA);

			// Process XSLT
			$xslt = new XSLTProcessor();
			$xslt->importStylesheet($xsl_document);

			KBIDebug::info('Applying post-query transformation.');

			return $xslt->transformToXML($xml);
		} else {
			return $xml_data;
		}
	}

	protected function queryGet($query)
	{
		$class = get_class($this);
		throw new Exception("Source type ({$class}) does not support this method (GET).");
	}

	protected function queryPost($query, $options)
	{
		$class = get_class($this);
		throw new Exception("Source type ({$class}) does not support this method (POST).");
	}

	protected function querySoap($query)
	{
		$class = get_class($this);
		throw new Exception("Source type ({$class}) does not support this method (SOAP).");
	}

	function requestGet($url, $_data)
	{
		$data = array();
	    while(list($n,$v) = each($_data)){
	        $data[] = "$n=" . urlencode($v);
	    }
	    $data = implode('&', $data);

		$p = file_get_contents("$url?$data");

		KBIDebug::info("$url?$data", 'GET');

		return $p;
	}

	function requestCurl($url, $_data)
	{
		$data = array();
	    while(list($n,$v) = each($_data)){
	        $data[] = "$n=" . urlencode($v);
	    }
	    $data = implode('&', $data);

		KBIDebug::info("$url?$data");

		$ch = curl_init("$url?$data");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$response = curl_exec($ch);

		//optionally you can check the response and see what the HTTP Code returned was
		$response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

    	return $response;
	}

	protected function encodeData($array)
	{
		$data = "";
		foreach ($array as $key=>$value) $data .= "{$key}=".urlencode($value).'&';
		return $data;
	}

	function requestCurlPost($url, $postdata)
	{
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->encodeData($postdata));
		curl_setopt($ch, CURLOPT_VERBOSE, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);

		$response = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);

		return $response;
	}

	function requestPost($url, $_data, $referer = NULL)
	{
	    // convert variables array to string:
	    $data = array();
	    while(list($n,$v) = each($_data)){
	        $data[] = "$n=$v";
	    }
	    $data = implode('&', $data);
	    // format --> test1=a&test2=b etc.

	    // parse the given URL
	    $url = parse_url($url);
	    if ($url['scheme'] != 'http') {
	        die('Only HTTP request are supported !');
	    }

	    // extract host and path:
	    $host = $url['host'];
	    $path = $url['path'];

	    // open a socket connection on port 80
	    $fp = fsockopen($host, $this->getPort(), $errno, $errstr);

        if(!$fp) {
            throw new Exception("Communication error: $errno $errstr ($host).");
        }

	    // send the request headers:
	    fputs($fp, "POST $path HTTP/1.1\r\n");
	    fputs($fp, "Host: $host\r\n");
	    if(!empty($referer)) fputs($fp, "Referer: $referer\r\n");
	    fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
	    fputs($fp, "Content-length: ". strlen($data) ."\r\n");
	    fputs($fp, "Connection: close\r\n\r\n");
	    fputs($fp, $data);

	 	if($fp)
	 	{
			$result = fgets($fp);

			while(!feof($fp))
			{
				// receive the results of the request
				$result .= fgets($fp, 128);
			}
		}

	    // close the socket connection:
	    fclose($fp);

	    // split the result header from the content
	    $result = explode("\r\n\r\n", $result, 2);
	    $header = isset($result[0]) ? $result[0] : '';
	    $content = isset($result[1]) ? $result[1] : '';

	    // return as array:
	    return $content;
	}

	function parseNameValues($text)
	{
		if(is_array($text)) return $text;

		$values = json_decode($text, true);

		if($values == NULL) {
			$values = array();
			$matches = array();
			if (preg_match_all('/([^:\s]+)[\s]*:[\s]*("(?P<value1>[^"]+)"|' . '\'(?P<value2>[^\']+)\'|(?P<value3>.+?)\b)/', $text, $matches, PREG_SET_ORDER))
				foreach ($matches as $match)
					$values[trim($match[1])] = @$match['value1'] . @$match['value2'] . trim(@$match['value3']);
		}

		return $values;
	}
}
