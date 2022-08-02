<?php

declare(strict_types=1);

/**
 * @copyright   Copyright (c) 2015-2022, Plenta.io & Christian Barkowsky
 * @author      Christian Barkowsky <https://github.com/plenta>
 * @author      David Greminger <https://github.com/bytehead>
 * @package     tiny-compress-images
 * @license     LGPL
 */

namespace Plenta\ContaoTinyCompressImages\TinyPNG;

use Contao\Config;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\FilesModel;
use Contao\Message;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Webmozart\PathUtil\Path;

class Compressor
{
    public const EXTENSIONS = [
        'png', 'jpg', 'jpeg', 'webp'
    ];

    private ContaoFramework $framework;
    private TranslatorInterface $translator;
    private LoggerInterface $logger;
    private HttpClientInterface $httpClient;
    private string $projectDir;

    private string $apiKey;
    private string $auth;

    public function __construct(
        ContaoFramework $framework,
        TranslatorInterface $translator,
        LoggerInterface $logger,
        HttpClientInterface $httpClient,
        string $projectDir
    ) {
        $this->framework = $framework;
        $this->translator = $translator;
        $this->logger = $logger;
        $this->httpClient = $httpClient;
        $this->projectDir = $projectDir;

        /** @var Config $config */
        $config = $framework->getAdapter(Config::class);

        $this->apiKey = (string) $config->get('tinypng_api_key');

        if ('' !== $this->apiKey) {
            $this->auth = 'Basic ' . base64_encode(sprintf('api:%s', $this->apiKey));
        } else {
            $this->showApiKeyWarning();
        }
    }

    public function compress(string $filename): ?FilesModel
    {
        if ('' === $this->apiKey) {
            return null;
        }

        /** @var FilesModel $fileAdapter */
        $fileAdapter = $this->framework->getAdapter(FilesModel::class);

        $file = $fileAdapter->findByPath($filename);

        if (!$file instanceof FilesModel) {
            $this->showError($filename);

            return null;
        }

        if (!\in_array($file->extension, self::EXTENSIONS, true)) {
            $this->showError($filename, sprintf('File extension %s is not allowed', $file->extension));

            return null;
        }

        $filePath = Path::join($this->projectDir, $file->path);

        try {
            $response = $this->httpClient->request('POST','https://api.tinypng.com/shrink', [
                'body' => fopen($filePath, 'rb'),
                'headers' => [
                    'Authorization' => $this->auth
                ]
            ]);

            $content = $response->getContent();
            $compressionCount = $response->getHeaders()['compression-count'];
        } catch (ExceptionInterface $exception) {
            $this->showError($file->path, $exception->getMessage());

            return null;
        }

        try {
            $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $jsonException) {
            $this->showError($file->path, $jsonException->getMessage());

            return null;
        }

        file_put_contents($filePath, fopen($content->output->url, 'rb', false));

        $file->tstamp = time();
        $file->hash = md5_file($filePath);
        $file->compressed = true;
        $file->save();

        $this->showSuccess($file->path);

        if (null !== $compressionCount) {
            $this->showCompressionCount((int) $compressionCount[0]);
        }

        return $file;
    }

    private function showError(string $file, string $error = null): void
    {
        /** @var Message $message */
        $message = $this->framework->getAdapter(Message::class);

        $message->addError(
            $this->translator->trans('MSC.TINYCOMPRESSIMAGES.failed', [$file, $error], 'contao_default')
        );

        $this->logger->error(
            $this->translator->trans('MSC.TINYCOMPRESSIMAGES.failed', [$file, $error], 'contao_default'),
            ['contao' => new ContaoContext(__METHOD__, ContaoContext::FILES)]
        );
    }

    private function showSuccess(string $path): void
    {
        /** @var Message $message */
        $message = $this->framework->getAdapter(Message::class);

        $message->addInfo(
            $this->translator->trans('MSC.TINYCOMPRESSIMAGES.successful', [$path], 'contao_default')
        );

        $this->logger->info(
            $this->translator->trans('MSC.TINYCOMPRESSIMAGES.successful', [$path], 'contao_default'),
            ['contao' => new ContaoContext(__METHOD__, ContaoContext::FILES)]
        );
    }

    private function showCompressionCount(int $compressionCount): void
    {
        /** @var Message $message */
        $message = $this->framework->getAdapter(Message::class);

        $message->addInfo(
            $this->translator->trans('MSC.TINYCOMPRESSIMAGES.count', [$compressionCount], 'contao_default')
        );

        $this->logger->info(
            $this->translator->trans('MSC.TINYCOMPRESSIMAGES.count', [$compressionCount], 'contao_default'),
            ['contao' => new ContaoContext(__METHOD__, ContaoContext::GENERAL)]
        );
    }

    private function showApiKeyWarning(): void
    {
        $message = $this->framework->getAdapter(Message::class);

        $message->addError(
            $this->translator->trans('MSC.TINYCOMPRESSIMAGES.apikey', [], 'contao_default')
        );

        $this->logger->info(
            $this->translator->trans('MSC.TINYCOMPRESSIMAGES.apikey', [], 'contao_default'),
            ['contao' => new ContaoContext(__METHOD__, ContaoContext::ERROR)]
        );
    }
}
