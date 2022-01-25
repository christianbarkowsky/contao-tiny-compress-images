<?php

declare(strict_types=1);

/**
 * @copyright   Copyright (c) 2015-2021, Plenta.io & Christian Barkowsky
 * @author      David Greminger <https://github.com/bytehead>
 * @package     tiny-compress-images
 * @license     LGPL
 */

$GLOBALS['BE_MOD']['system']['files'] = array_merge($GLOBALS['BE_MOD']['system']['files'], [
    'tinify' => ['plenta.contao_tiny_compress_images.listener.data_container', 'onCompress']
]);
