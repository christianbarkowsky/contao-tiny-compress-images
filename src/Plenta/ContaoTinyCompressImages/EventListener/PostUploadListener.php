<?php

declare(strict_types=1);

/**
 * @copyright   Copyright (c) 2015-2021, Plenta.io & Christian Barkowsky
 * @author      Christian Barkowsky <hallo@plenta.io>
 * @package     tiny-compress-images
 * @license     LGPL
 */

namespace Plenta\ContaoTinyCompressImages\EventListener;

use Contao\Message;
use Contao\Request;
use Contao\FilesModel;
use Psr\Log\LoggerInterface;
use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\CoreBundle\ServiceAnnotation\Hook;

/**
 * @Hook("postUpload")
 */
class PostUploadListener
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @var array|string[]
     */
    protected array $extensions = [
        'png', 'jpg', 'jpeg', 'webp'
    ];

    public function __invoke(array $files): void
    {
        if (count($files) > 0 && !empty($GLOBALS['TL_CONFIG']['tinypng_api_key'])) {
            $strUrl = 'https://api.tinypng.com/shrink';
            $strKey = $GLOBALS['TL_CONFIG']['tinypng_api_key'];
            $strAuthorization = 'Basic '.base64_encode("api:$strKey");
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

                        $this->logger->info(
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

                        $this->logger->error(
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

                $this->logger->info(
                    sprintf($GLOBALS['TL_LANG']['MSC']['TINYCOMPRESSIMAGES']['count'], $compressionCount),
                    ['contao' => new ContaoContext(__METHOD__, ContaoContext::GENERAL)]
                );
            }
        }
    }
}
