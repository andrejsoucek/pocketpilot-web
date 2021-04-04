<?php

declare(strict_types=1);

namespace PP;

use Nette\SmartObject;

/**
 * @author Andrej Souček
 */
class DirResolver
{
    use SmartObject;

    private string $appDir;

    private string $scriptsDir;

    private string $manifestDir;

    public function __construct(string $appDir, string $scriptsDir, string $manifestDir)
    {
        $this->appDir = $appDir;
        $this->scriptsDir = $scriptsDir;
        $this->manifestDir = $manifestDir;
    }

    public function getAppDir(): string
    {
        return $this->appDir;
    }

    /**
     * @return string
     */
    public function getScriptsDir(): string
    {
        return $this->scriptsDir;
    }

    /**
     * @return string
     */
    public function getManifestDir(): string
    {
        return $this->manifestDir;
    }
}
