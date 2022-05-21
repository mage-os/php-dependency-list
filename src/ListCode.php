<?php declare(strict_types=1);

namespace MageOs\PhpDependencyList;

interface ListCode
{
    public function list(): \Iterator;
}