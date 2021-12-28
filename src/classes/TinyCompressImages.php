<?php

declare(strict_types=1);

/**
 * @copyright   Copyright (c) 2015-2021, Plenta.io & Christian Barkowsky
 * @author      Christian Barkowsky <hallo@plenta.io>
 * @package     tiny-compress-images
 * @license     LGPL
 */

namespace Barkowsky;

use Contao\Image;
use Contao\System;
use Contao\Message;
use Contao\Request;
use Contao\FilesModel;
use Contao\StringUtil;
use Contao\Controller;
use Contao\CoreBundle\Monolog\ContaoContext;

/**
 * Hook "postUpload".
 */
class TinyCompressImages extends System
{
    /**
     * @var array|string[]
     */
    protected array $extensions = [
        'png', 'jpg', 'jpeg', 'webp'
    ];

    /**
     * Compress images.
     * @param array $files
     * @return void
     */
    public function processPostUpload(array $files): void
    {
        if (count($files) > 0 && !empty($GLOBALS['TL_CONFIG']['tinypng_api_key'])) {
            $strUrl = 'https://api.tinypng.com/shrink';
            $strKey = $GLOBALS['TL_CONFIG']['tinypng_api_key'];
            $strAuthorization = 'Basic '.base64_encode("api:$strKey");
            $logger = System::getContainer()->get('monolog.logger.contao');
            $compressionCount = null;

            foreach ($files as $file) {
                $objFile = FilesModel::findByPath($file);

                if (in_array($objFile->extension, $this->extensions, true)) {
                    $strFile = TL_ROOT.'/'.$objFile->path;

                    $objRequest = new Request();
                    $objRequest->method = 'post';
                    $objRequest->data = file_get_contents($strFile);
                    $objRequest->setHeader('Content-type', 'image/png');
                    $objRequest->setHeader('Authorization', $strAuthorization);
                    $objRequest->send($strUrl);

                    $compressionCount = $objRequest->headers['Compression-Count'];
                    $arrResponse = json_decode($objRequest->response);

                    if (201 == $objRequest->code) {
                        file_put_contents($strFile, fopen($arrResponse->output->url, 'r', false));

                        $objFile->tstamp = time();
                        $objFile->path = $file;
                        $objFile->hash = md5_file(TL_ROOT.'/'.$objFile->path);
                        $objFile->compressed = true;
                        $objFile->save();

                        Message::addInfo(
                            sprintf($GLOBALS['TL_LANG']['MSC']['TINYCOMPRESSIMAGES']['successful'], $objFile->path)
                        );

                        $logger->addInfo(
                            sprintf($GLOBALS['TL_LANG']['MSC']['TINYCOMPRESSIMAGES']['successful'], $objFile->path),
                            ['contao' => new ContaoContext(__METHOD__, ContaoContext::FILES)]
                        );
                    } else {
                        Message::addError(
                            sprintf(
                                $GLOBALS['TL_LANG']['MSC']['TINYCOMPRESSIMAGES']['failed'],
                                $objFile->path,
                                $arrResponse->message
                            )
                        );

                        $logger->addError(
                            sprintf(
                                $GLOBALS['TL_LANG']['MSC']['TINYCOMPRESSIMAGES']['failed'],
                                $objFile->path,
                                $arrResponse->message
                            ),
                            ['contao' => new ContaoContext(__METHOD__, ContaoContext::FILES)]
                        );
                    }
                }
            }

            if (null !== $compressionCount) {
                Message::addInfo(
                    sprintf($GLOBALS['TL_LANG']['MSC']['TINYCOMPRESSIMAGES']['count'], $compressionCount)
                );

                $logger->addNotice(
                    sprintf($GLOBALS['TL_LANG']['MSC']['TINYCOMPRESSIMAGES']['count'], $compressionCount),
                    ['contao' => new ContaoContext(__METHOD__, ContaoContext::GENERAL)]
                );
            }
        }
    }

    public function addIcon(array $row, ?string $href, string $label, string $title, ?string $icon, string $attributes)
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
