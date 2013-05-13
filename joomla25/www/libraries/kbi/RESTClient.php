<?php

class RESTClient {
	private function getData($_data)
	{
		$data = $_data;

		// convert variables array to string:
		if (is_array($_data)) {
			// format --> test1=a&test2=b etc.
			$data = $this->encodeData($data);
		} else if (is_string($_data)) {
			$data = $_data;
		}

		return $data;
	}

	public function encodeData(Array $array)
	{
		// foreach ($array as $key=>$value) $data .= "{$key}=".urlencode($value).'&';

		$data = array();

		while(list($n,$v) = each($array)) {
			$data[] = "{$n}=" . urlencode($v);
		}

		return implode('&', $data);
	}

	public function get($url, $_data = array())
	{
		$data = $this->getData($_data);

		KBIDebug::info("$url?$data");

		$ch = curl_init("$url?$data");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$response = curl_exec($ch);

		//optionally you can check the response and see what the HTTP Code returned was
		$response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		return $response;
	}

	public function post($url, $_data = array())
	{
		$data = $this->getData($_data);

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_VERBOSE, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);

		$response = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);

		return $response;
	}

	public function patch($url, $_data)
	{
		$data = $this->getData($_data);

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_VERBOSE, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');

		$response = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);

		return $response;
	}

	/**
	 * @param $url
	 * @return string
	 */
	public function delete($url)
	{
		return '';
	}
}