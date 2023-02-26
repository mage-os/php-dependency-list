<?php

declare(strict_types=1);

namespace MageOs\PhpDependencyList\CodeProvider;

use MageOs\PhpDependencyList\ListCode;
use MageOs\PhpDependencyList\RecursivelyReadCodeFromFiles;

class ListCodeFromFileList implements ListCode
{
    /**
     * @var string[]
     */
    private array $filePaths;

    private $fileNamePattern;

    public function __construct($fileNamePattern, string $filePath)
    {
        $this->fileNamePattern = $fileNamePattern;
        $this->filePaths       = explode("\n", file_get_contents($filePath));
    }

    public function list(): \Iterator
    {
        return (new RecursivelyReadCodeFromFiles($this->fileNamePattern))->list($this->filePaths);
    }
}
