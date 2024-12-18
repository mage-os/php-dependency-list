<?php

declare(strict_types=1);

namespace MageOs\PhpDependencyList;

interface ParserInterface
{
    /**
     * @param  string $filePath
     * @return bool
     */
    public function canParse($filePath);

    /**
     * @param  string $code
     * @return []Reference
     */
    public function parse($code);

    /**
     * @return string
     */
    public function getParsiblePattern();
}
