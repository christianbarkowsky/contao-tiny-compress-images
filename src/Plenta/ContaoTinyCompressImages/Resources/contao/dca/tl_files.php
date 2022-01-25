<?php

declare(strict_types=1);

/**
 * @copyright   Copyright (c) 2015-2021, Plenta.io & Christian Barkowsky
 * @author      Christian Barkowsky <hallo@plenta.io>
 * @package     tiny-compress-images
 * @license     LGPL
 */

$GLOBALS['TL_DCA']['tl_files']['list']['operations']['tinify'] = [
    'icon' => 'bundles/plentacontaotinycompressimages/tinify.png',
    'button_callback' => ['plenta.contao_tiny_compress_images.listener.data_container', 'onTinyCompressImagesButtonCallback']
];

$GLOBALS['TL_DCA']['tl_files']['fields']['compressed'] = [
    'sql' => "char(1) NOT NULL default ''"
];
