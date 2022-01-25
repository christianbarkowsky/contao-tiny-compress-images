<?php

declare(strict_types=1);

/**
 * @copyright   Copyright (c) 2015-2021, Plenta.io & Christian Barkowsky
 * @author      Christian Barkowsky <hallo@plenta.io>
 * @package     tiny-compress-images
 * @license     LGPL
 */

namespace Plenta\ContaoTinyCompressImages\EventListener;

use Plenta\ContaoTinyCompressImages\TinyPNG\Compressor;
use Contao\CoreBundle\ServiceAnnotation\Hook;

/**
 * @Hook("postUpload")
 */
class PostUploadListener
{
    private Compressor $compressor;

    public function __construct(Compressor $compressor)
    {
        $this->compressor = $compressor;
    }

    public function __invoke(array $files): void
    {
        foreach ($files as $file) {
            $this->compressor->compress($file);
        }
    }
}
