<?php

namespace Helpers;

class Curl
{
	var $callback = false;


	# ------------------------------------------------------------------ #
	function setCallback($func_name)
	{
		$this->callback = $func_name;
	}


	# ------------------------------------------------------------------ #
	public static function doRequest($method, $url, $vars = null, $headers = null)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 900);
		curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
		curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt');

		if ($method == 'POST') {
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);
			if ($headers) curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		} else if ($method == 'POSTXML') {
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);
		}

		$rough_content = curl_exec($ch);
		if ($rough_content === false) {
			echo 'Curl error: '.curl_error($ch);
			echo 'Curl errorno: '.curl_errno($ch);
		}
		$err    = curl_errno($ch);
		$errmsg = curl_error($ch);
		$header = curl_getinfo($ch);
		curl_close($ch);

		$header_content = substr($rough_content, 0, $header['header_size']);
		$body_content   = trim(str_replace($header_content, '', $rough_content));
		$pattern        = "#Set-Cookie:\\s+(?<cookie>[^=]+=[^;]+)#m";
		preg_match_all($pattern, $header_content, $matches);
		$cookiesOut = implode("; ", $matches['cookie']);

		$header['errno']   = $err;
		$header['errmsg']  = $errmsg;
		$header['headers'] = $header_content;
		$header['content'] = $body_content;
		$header['cookies'] = $cookiesOut;

		return $header;
	}


	# ------------------------------------------------------------------ #
	function get($url)
	{
		return $this->doRequest('GET', $url, 'NULL');
	}


	# ------------------------------------------------------------------ #
	function post($url, $vars)
	{
		return $this->doRsequest('POST', $url, $vars);
	}
}