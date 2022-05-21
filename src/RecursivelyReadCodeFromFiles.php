<?php declare(strict_types=1);

namespace MageOs\PhpDependencyList;

class RecursivelyReadCodeFromFiles
{
    private string $fileNamePattern;

    public function __construct(string $fileNamePattern)
    {
        $this->fileNamePattern = $fileNamePattern;
    }

    /**
     * @param string[] $filePaths
     * @return \Iterator
     */
    public function list(array $filePaths): \Iterator
    {
        $finder = new FindFilesMatching($this->fileNamePattern);
        foreach ($filePaths as $path) {
            if (is_dir($path)) {
                foreach ((new self($this->fileNamePattern))->list($finder->list($path)) as $code) {
                    yield $code;
                }
            } elseif (file_exists($path) && is_readable($path)) {
                yield file_get_contents($path);
            }
        }
    }
}