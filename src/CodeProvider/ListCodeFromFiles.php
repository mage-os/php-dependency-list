<?php declare(strict_types=1);

namespace MageOs\PhpDependencyList\CodeProvider;

use MageOs\PhpDependencyList\ListCode;
use MageOs\PhpDependencyList\RecursivelyReadCodeFromFiles;

class ListCodeFromFiles implements ListCode
{
    /**
     * @var string[] 
     */
    private array $filePaths;

    private $fileNamePattern;

    public function __construct($fileNamePattern, string ...$paths)
    {
        $this->fileNamePattern = $fileNamePattern;
        $this->filePaths       = $paths;
    }

    public function list(): \Iterator
    {
        return (new RecursivelyReadCodeFromFiles($this->fileNamePattern))->list($this->filePaths);
    }
}