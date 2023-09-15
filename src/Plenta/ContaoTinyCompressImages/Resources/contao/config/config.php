<?php

declare(strict_types=1);

/**
 * @copyright   Copyright (c) 2015-2023, Plenta.io & Christian Barkowsky
 * @author      David Greminger <https://github.com/bytehead>
 * @package     tiny-compress-images
 * @license     LGPL
 */

use Contao\CoreBundle\ContaoCoreBundle;

if (version_compare(ContaoCoreBundle::getVersion(), '5.0', '>=')) {
    $GLOBALS['BE_MOD']['content']['files'] = array_merge($GLOBALS['BE_MOD']['content']['files'], [
        'tinify' => ['plenta.contao_tiny_compress_images.listener.data_container', 'onCompress']
    ]);
} else {
    $GLOBALS['BE_MOD']['system']['files'] = array_merge($GLOBALS['BE_MOD']['system']['files'], [
        'tinify' => ['plenta.contao_tiny_compress_images.listener.data_container', 'onCompress']
    ]);
}
