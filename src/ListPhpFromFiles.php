<?php declare(strict_types=1);

namespace MageOs\PhpDependencyList;

class ListPhpFromFiles implements PhpCodeProvider
{
    /**
     * @var string[] 
     */
    private array $files;
    
    public function __construct(string ...$files)
    {
        $this->files = $files;
    }

    public function list(): \Iterator
    {
        foreach ($this->files as $file) {
            if (is_dir($file)) {
                foreach ((new self(...(new FindPhpFiles($file))->list()))->list() as $php) {
                    yield $php;
                }
            } elseif (file_exists($file) && is_readable($file)) {
                yield file_get_contents($file);
            }
        }
    }
}