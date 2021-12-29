<?php

declare(strict_types=1);

/**
 * @copyright   Copyright (c) 2015-2021, Plenta.io & Christian Barkowsky
 * @author      Christian Barkowsky <hallo@plenta.io>
 * @package     tiny-compress-images
 * @license     LGPL
 */

use Contao\CoreBundle\DataContainer\PaletteManipulator;

$GLOBALS['TL_DCA']['tl_settings']['fields']['tinypng_api_key'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_settings']['tinypng_api_key'],
    'exclude' => true,
    'inputType' => 'text',
    'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
];

PaletteManipulator::create()
    ->addLegend('tiny_compress_images_legend', 'chmod_legend', PaletteManipulator::POSITION_AFTER)
    ->addField('tinypng_api_key', 'tiny_compress_images_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('default', 'tl_settings')
;
