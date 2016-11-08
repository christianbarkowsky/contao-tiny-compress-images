<?php

/**
 * Copyright (C) 2015-2016 Christian Barkowsky
 *
 * @author  Christian Barkowsky <hallo@christianbarkowsky.de>
 * @copyright Christian Barkowsky <http://christianbarkowsky.de>
 * @package tiny-compress-images
 * @license LGPL
 */


\Contao\ClassLoader::addNamespace('Barkowsky');


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
    'Barkowsky\TinyCompressImages' => 'system/modules/tiny-compress-images/classes/TinyCompressImages.php',
));