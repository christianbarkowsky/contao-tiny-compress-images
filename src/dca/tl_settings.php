<?php
	
/**
 * Copyright (C) 2015 Christian Barkowsky
 * 
 * @author  Christian Barkowsky <hallo@christianbarkowsky.de>
 * @copyright Christian Barkowsky <http://christianbarkowsky.de>
 * @package tiny-compress-images
 * @license LGPL
 */


/**
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] = $GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] . ';{tiny_compress_images_legend},tinypng_api_key';


/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_settings']['fields']['tinypng_api_key'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['tinypng_api_key'],
	'exclude'                 => true,
	'inputType'               => 'text',
	'eval'                    => array('maxlength'=>255, 'tl_class'=>'w50')
);
