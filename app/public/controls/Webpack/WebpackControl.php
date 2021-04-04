<?php

declare(strict_types=1);

namespace PP\Controls;

use LogicException;
use PP\DirResolver;

/**
 * @author Andrej Souček
 */
class WebpackControl extends BaseControl
{
    private DirResolver $resolver;

    public function __construct(DirResolver $resolver)
    {
        $this->resolver = $resolver;
    }

    public function render(string $entry): void
    {
        $this->template->setFile(__DIR__ . '/webpackControl.latte');
        $this->template->paths = $this->resolvePaths($entry);
        $this->template->render();
    }

    private function resolvePaths(string $entry): array
    {
        if (empty($entry)) {
            throw new LogicException('Missing $entry string.');
        }
        $manifest = file_get_contents($this->resolver->getManifestDir() . '/manifest.json');
        if (!$manifest) {
            throw new LogicException('Unable to read manifest.json in %wwwDir%/dist/.');
        }
        $json = json_decode($manifest, true);
        if (isset($json['entrypoints'][$entry]) && isset($json['entrypoints'][$entry]['js'])) {
            return array_map(function ($s) {
                return "{$this->resolver->getScriptsDir()}/$s";
            }, $json['entrypoints'][$entry]['js']);
        }

        throw new LogicException("Unable to find entrypoint '$entry' in manifest.json.");
    }
}

interface WebpackControlFactory
{
    public function create(): WebpackControl;
}
