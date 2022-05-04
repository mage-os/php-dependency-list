<?php declare(strict_types=1);

namespace MageOs\PhpDependencyList;

interface PhpCodeProvider
{
    public function list(): \Iterator;
}