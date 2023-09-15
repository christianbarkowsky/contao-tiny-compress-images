<?php

declare(strict_types=1);

/**
 * @copyright   Copyright (c) 2015-2023, Plenta.io & Christian Barkowsky
 * @author      Christian Barkowsky <hallo@plenta.io>
 * @package     tiny-compress-images
 * @license     LGPL
 */

namespace Plenta\ContaoTinyCompressImages\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Plenta\ContaoTinyCompressImages\PlentaContaoTinyCompressImagesBundle;

class Plugin implements BundlePluginInterface
{
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(PlentaContaoTinyCompressImagesBundle::class)
                ->setLoadAfter([
                    ContaoCoreBundle::class,
                ]),
        ];
    }
}
