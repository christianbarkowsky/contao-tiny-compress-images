<?php

/**
 * @copyright   Copyright (c) 2015-2021, Plenta.io & Christian Barkowsky
 * @author      Christian Barkowsky <hallo@plenta.io>
 * @package     tiny-compress-images
 * @license     LGPL
 */

use Barkowsky\TinyCompressImages;

$GLOBALS['TL_DCA']['tl_files']['list']['operations']['tinify'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_files']['tinify'],
    'icon' => 'bundles/tiny-compress-images/tinyfy.png',
    'button_callback' => [TinyCompressImages::class, 'addIcon']
];

$GLOBALS['TL_DCA']['tl_files']['fields']['compressed'] = [
    'sql' => "char(1) NOT NULL default ''"
];
