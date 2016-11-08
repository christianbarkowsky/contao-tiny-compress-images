<?php

/**
 * Copyright (C) 2015-2016 Christian Barkowsky
 * 
 * @author  Christian Barkowsky <hallo@christianbarkowsky.de>
 * @copyright Christian Barkowsky <http://christianbarkowsky.de>
 * @package tiny-compress-images
 * @license LGPL
 */


/**
 * Hook
 */
$GLOBALS['TL_HOOKS']['postUpload'][] = array('TinyCompressImages', 'processPostUpload');
