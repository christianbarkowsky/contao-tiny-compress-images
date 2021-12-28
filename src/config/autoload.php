<?php

/**
 * @copyright   Copyright (c) 2015-2021, Plenta.io & Christian Barkowsky
 * @author      Christian Barkowsky <hallo@plenta.io>
 * @package     tiny-compress-images
 * @license     LGPL
 */

\Contao\ClassLoader::addNamespace('Barkowsky');

/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
    'Barkowsky\TinyCompressImages' => 'system/modules/tiny-compress-images/classes/TinyCompressImages.php',
));