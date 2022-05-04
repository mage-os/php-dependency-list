<?php declare(strict_types=1);

namespace MageOs\PhpDependencyList;

class FindPhpFiles
{
    private string $dir;

    public function __construct(string $dir)
    {
        $this->dir = $dir;
    }

    public function list(): array
    {
        $files = [];
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->dir));
        foreach ($iterator as $file) {
            if ($file->getExtension() === 'php' || $file->getExtension() === 'phtml') {
                $files[] = $file->getPathname();
            }
        }
        return $files;
    }
}