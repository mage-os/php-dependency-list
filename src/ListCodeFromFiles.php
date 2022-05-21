<?php declare(strict_types=1);

namespace MageOs\PhpDependencyList;

class ListCodeFromFiles implements ListCode
{
    /**
     * @var string[] 
     */
    private array $filePaths;

    private string $fileNamePattern;

    public function __construct(string $fileNamePattern, string ...$paths)
    {
        $this->fileNamePattern = $fileNamePattern;
        $this->filePaths       = $paths;
    }

    public function list(): \Iterator
    {
        return (new RecursivelyReadCodeFromFiles($this->fileNamePattern))->list($this->filePaths);
    }
}