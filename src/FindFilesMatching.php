<?php declare(strict_types=1);

namespace MageOs\PhpDependencyList;

class FindFilesMatching
{
    private string $fileNamePattern;

    public function __construct(string $fileNamePattern)
    {
        $this->fileNamePattern = $fileNamePattern;
    }

    public function list(string $dir): array
    {
        $files = [];
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir));
        /** @var \DirectoryIterator $file */
        foreach ($iterator as $file) {
            if (preg_match($this->fileNamePattern, $file->getFilename())) {
                $files[] = $file->getPathname();
            }
        }
        return $files;
    }
}