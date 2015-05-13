<?php
	
/**
 * Copyright (C) 2015 Christian Barkowsky
 * 
 * @author  Christian Barkowsky <hallo@christianbarkowsky.de>
 * @copyright Christian Barkowsky <http://christianbarkowsky.de>
 * @package tiny-compress-images
 * @license LGPL
 */


namespace Barkowsky;


class TinyCompressImages extends \System
{
	
	/**
	 *
	 */
	public function processPostUpload($arrFiles)
	{
		if (is_array($arrFiles)) {
			foreach($arrFiles as $file) {
				
				$input = TL_ROOT . '/' . $file;
				$output = TL_ROOT . '/' . $file;
				
				$request = curl_init();
				curl_setopt_array($request, array(
					CURLOPT_URL => "https://api.tinypng.com/shrink",
					CURLOPT_USERPWD => "api:" . $GLOBALS['TL_CONFIG']['tinypng_api_key'],
					CURLOPT_POSTFIELDS => file_get_contents($input),
					CURLOPT_BINARYTRANSFER => true,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_HEADER => true,
					//Uncomment below if you have trouble validating our SSL certificate.
					//Download cacert.pem from: http://curl.haxx.se/ca/cacert.pem
					// CURLOPT_CAINFO => __DIR__ . "/cacert.pem",
					CURLOPT_SSL_VERIFYPEER => false
				));
				
				$response = curl_exec($request);
				
				if (curl_getinfo($request, CURLINFO_HTTP_CODE) === 201) {
					// Compression was successful, retrieve output from Location header.
					$headers = substr($response, 0, curl_getinfo($request, CURLINFO_HEADER_SIZE));
					
					foreach (explode("\r\n", $headers) as $header) {
						if (strtolower(substr($header, 0, 10)) === "location: ") {
							$request = curl_init();
							curl_setopt_array($request, array(
								CURLOPT_URL => substr($header, 10),
								CURLOPT_RETURNTRANSFER => true,
								// Uncomment below if you have trouble validating our SSL certificate.
								// CURLOPT_CAINFO => __DIR__ . "/cacert.pem",
								CURLOPT_SSL_VERIFYPEER => false
							));
							
							file_put_contents($output, curl_exec($request));
							
							\Contao\System::log('Compression was successful. (File: ' . $file . ')', __METHOD__, TL_GENERAL);
						}
					}
					
					//\Contao\System::log('Compression was successful. (File: ' . $file . ')', __METHOD__, TL_GENERAL);
					
				} else {
					\Contao\System::log('Compression failed. (' . curl_error($request) . ') (File: ' . $file . ')', __METHOD__, TL_GENERAL);
				}
				
				/*
				$url = "https://api.tinypng.com/shrink";
				$options = array(
					"http" => array(
					"method" => "POST",
					"header" => array(
					"Content-type: image/png",
					"Authorization: Basic " . base64_encode("api:" . $GLOBALS['TL_CONFIG']['tinypng_api_key'] . "")
				),
				"content" => file_get_contents($input)
				),
				"ssl" => array(
					//Uncomment below if you have trouble validating our SSL certificate.
					//Download cacert.pem from: http://curl.haxx.se/ca/cacert.pem
					// "cafile" => __DIR__ . "/cacert.pem",
					"verify_peer" => true
					)
				);
				
				$result = fopen($url, "r", false, stream_context_create($options));
				
				if ($result) {
				// Compression was successful, retrieve output from Location header.
				foreach ($http_response_header as $header) {
					if (strtolower(substr($header, 0, 10)) === "location: ") {
						file_put_contents($output, fopen(substr($header, 10), "rb", false));
						
						\Contao\System::log('Compression was successful. (File: ' . $file . ')', __METHOD__, TL_GENERAL);
					}
				}
				} else {
					\Contao\System::log('Compression failed. (' . curl_error($request) . ') (File: ' . $file . ')', __METHOD__, TL_GENERAL);
				}
				*/
			}
		}
	}
}
