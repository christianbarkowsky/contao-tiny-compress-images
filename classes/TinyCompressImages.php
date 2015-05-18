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
	 * Compress images
	 */
	public function processPostUpload($arrFiles)
	{
		if (is_array($arrFiles) && $GLOBALS['TL_CONFIG']['tinypng_api_key'] != '') {
			
			$strUrl = 'https://api.tinypng.com/shrink';
			$strKey = $GLOBALS['TL_CONFIG']['tinypng_api_key'];
			$strAuthorization = 'Basic '.base64_encode("api:$strKey");
			
			foreach($arrFiles as $file) {
				
				$strFile = TL_ROOT . '/' . $file;
				
				$objRequest = new \Request();
				$objRequest->method = 'post';
				$objRequest->data = file_get_contents($strFile);
				$objRequest->setHeader('Content-type', 'image/png');
				$objRequest->setHeader('Authorization', $strAuthorization);
				$objRequest->send($strUrl);
				
				$arrResponse = json_decode($objRequest->response);
				
				if ($objRequest->code == 201) {
					file_put_contents($strFile, fopen($arrResponse->output->url, "rb", false));
					\Dbafs::addResource($file);
					\Contao\System::log('Compression was successful. (File: ' . $file . ')', __METHOD__, TL_GENERAL);
				} else {
					\Contao\System::log('Compression failed. (' . $arrResponse->message . ') (File: ' . $file . ')', __METHOD__, TL_GENERAL);
				}
			}
		}
	}
}
