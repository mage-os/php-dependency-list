<?php

declare(strict_types=1);

namespace MageOs\PhpDependencyList;

class RecursivelyReadCodeFromFiles
{
    private $fileNamePattern;

    public function __construct($fileNamePattern)
    {
        $this->fileNamePattern = $fileNamePattern;
    }

    /**
     * @param  string[] $filePaths
     * @return \Iterator
     */
    public function list(array $filePaths): \Iterator
    {
        $finder = new FindFilesMatching($this->fileNamePattern);
        foreach ($filePaths as $path) {
            if (is_dir($path)) {
                foreach ((new self($this->fileNamePattern))->list($finder->list($path)) as $filePath => $code) {
                    yield $filePath => $code;
                }
            } elseif (file_exists($path) && is_readable($path)) {
                yield $path => file_get_contents($path);
            }
        }
    }
}
