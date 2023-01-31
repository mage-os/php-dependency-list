<?php declare(strict_types=1);

namespace MageOs\PhpDependencyList\CodeProvider;

use MageOs\PhpDependencyList\ListCode;

class ListCodeFromStdin implements ListCode
{
    /**
     * @var resource
     */
    private $stream;

    public function __construct($stream = null)
    {
        $this->stream = $stream ?? STDIN;
    }

    private function readChunk(): string
    {
        $chunk = '';
        while (($next = fgetc($this->stream)) !== "\0" && $next !== false) {
            $chunk .= $next;
        }
        
        return $chunk;
    }

    /**
     * Read null byte separated PHP file contents from STDIN  
     * 
     * @return \Generator
     */
    public function list(): \Iterator
    {
        $i = 0;
        while (! feof($this->stream)) {
            $chunk = $this->readChunk();
            if ($chunk === '') {
                continue;
            }
            yield $i => $chunk;
            $i++;
        }
    }
}