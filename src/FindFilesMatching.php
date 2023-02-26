<?php

declare(strict_types=1);

namespace MageOs\PhpDependencyList;

class FindFilesMatching
{
    private $fileNamePattern;

    public function __construct($fileNamePattern)
    {
        $this->fileNamePattern = $fileNamePattern;
    }

    public function list(string $dir): array
    {
        $files = [];
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir));
        /** @var \DirectoryIterator $file */
        foreach ($iterator as $file) {
            if (is_array($this->fileNamePattern)) {
                foreach ($this->fileNamePattern as $pattern) {
                    if (preg_match($pattern, $file->getFilename())) {
                        $files[] = $file->getPathname();
                        break;
                    }
                }
            } else {
                if (preg_match($this->fileNamePattern, $file->getFilename())) {
                    $files[] = $file->getPathname();
                }
            }
        }
        return $files;
    }
}
