<?php

/**
 * @copyright   Copyright (c) 2015-2021, Plenta.io & Christian Barkowsky
 * @author      Christian Barkowsky <hallo@plenta.io>
 * @package     tiny-compress-images
 * @license     LGPL
 */

$GLOBALS['TL_HOOKS']['postUpload'][] = ['TinyCompressImages', 'processPostUpload'];
