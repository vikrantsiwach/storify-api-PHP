<?php

// Request an API key at http://dev.storify.com/request

class StorifyClient {
  	private $apiKey = '<EDIT THIS>';
	private $username;
	private $token;

	function __construct($username, $token) {
		if (!$token) {
			print "Call this->token(\$username, \$password) to get a token\n";
		}

		$this->username = $username;
		$this->token = $token;

		$this->curl = curl_init();
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->curl, CURLOPT_VERBOSE, true);
	}

	function exec() {
		$result = curl_exec($this->curl);
		$code = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);

		switch ($code) {
			case 200:
				return json_decode($result, true);

			default:
				print "Error $code\n";
				print_r($result, true);
				return null;
		}
	}

	function post($path, $params = array()) {
		$params['api_key'] = $this->apiKey;
		$params['username'] = $this->username;
		$params['_token'] = $this->token;

		$url = 'https://api.storify.com/v1' . $path;
		print "$url\n";

		curl_setopt($this->curl, CURLOPT_URL, $url);
		curl_setopt($this->curl, CURLOPT_POSTFIELDS, http_build_query($params));

		return $this->exec();
	}

	function token($username, $password) {
		$params = array(
			'api_key' => $this->apiKey,
			'username' => $username,
			'password' => $password,
		);

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, 'https://api.storify.com/v1/auth');
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_VERBOSE, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));

		$result = curl_exec($curl);
		$data = json_decode($result, true);

		return $data['content']['_token'];
	}
}
