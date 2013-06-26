<?php

class RESTClientResponse {
	protected $xml;
	protected $body;
	protected $info;

	public function getBody() {
		return $this->body;
	}

	public function getBodyAsXml() {
		if ($this->xml == null) {
			$this->xml = simplexml_load_string($this->getBody());
		}

		return $this->xml;
	}

	public function getStatusCode() {
		return $this->info['http_code'];
	}

	public function __construct($body, $info) {
		$this->body = $body;
		$this->info = $info;
	}
}