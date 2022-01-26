<?php

declare(strict_types=1);

/**
 * @copyright   Copyright (c) 2015-2021, Plenta.io & Christian Barkowsky
 * @author      Christian Barkowsky <hallo@plenta.io>
 * @package     tiny-compress-images
 * @license     LGPL
 */

namespace Plenta\ContaoTinyCompressImages\EventListener;

use Contao\Backend;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\DataContainer;
use Contao\FilesModel;
use Contao\Image;
use Contao\StringUtil;
use Plenta\ContaoTinyCompressImages\TinyPNG\Compressor;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class DataContainerListener
{
    private Compressor $compressor;
    private ContaoFramework $framework;
    private RouterInterface $router;
    private TranslatorInterface $translator;

    public function __construct(Compressor $compressor, ContaoFramework $framework, RouterInterface $router, TranslatorInterface $translator)
    {
        $this->compressor = $compressor;
        $this->framework = $framework;
        $this->router = $router;
        $this->translator = $translator;
    }

    public function onTinyCompressImagesButtonCallback(array $row, ?string $href, string $label, string $title, ?string $icon, string $attributes): string
    {
        if ('folder' === (string) $row['type']) {
            return '';
        }

        /** @var FilesModel $filesAdapter */
        $filesAdapter = $this->framework->getAdapter(FilesModel::class);

        $model = $filesAdapter->findByPath(rawurldecode($row['id']));

        if (null !== $model && true === (bool) $model->compressed) {
            $label = $this->translator->trans('tl_files.tinified', [], 'contao_default');

            return Image::getHtml($icon, $label, 'title="' . $label . '"');
        }

        /** @var FilesModel $fileAdapter */
        $fileAdapter = $this->framework->getAdapter(FilesModel::class);

        $file = $fileAdapter->findByPath(rawurldecode($row['id']));

        if (!$file instanceof FilesModel) {
            return '';
        }

        if (!\in_array($file->extension, Compressor::EXTENSIONS, true)) {
            return '';
        }

        return '<a href="' . Backend::addToUrl($href . '&amp;key=tinify&amp;id=' . $row['id']) . '" title="' . StringUtil::specialchars($title) . '"' . $attributes . '>' . Image::getHtml(preg_replace('/\.png$/i', '_.png', $icon), $this->translator->trans('tl_files.tinify', [], 'contao_default')) . '</a> ';
    }

    public function onCompress(DataContainer $dataContainer): Response
    {
        $this->compressor->compress((string) $dataContainer->id);

        return new RedirectResponse($this->router->generate('contao_backend', ['do' => 'files']));
    }
}
