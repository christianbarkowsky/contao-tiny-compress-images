<?php

declare(strict_types=1);

/**
 * @copyright   Copyright (c) 2015-2021, Plenta.io & Christian Barkowsky
 * @author      Christian Barkowsky <hallo@plenta.io>
 * @package     tiny-compress-images
 * @license     LGPL
 */

namespace Plenta\ContaoTinyCompressImages\EventListener;

use Contao\Image;
use Contao\FilesModel;

class DataContainerListener
{
    public function onTinyCompressImagesButtonCallback(array $row, ?string $href, string $label, string $title, ?string $icon, string $attributes)
    {
        if ('folder' === (string) $row['type']) {
            return '';
        }

        $model = FilesModel::findByPath(rawurldecode($row['id']));

        if (null !== $model && true === (bool) $model->compressed) {
            return Image::getHtml($icon, $label);
        }

        return '';
    }
}