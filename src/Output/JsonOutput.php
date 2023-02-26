<?php

declare(strict_types=1);

namespace MageOs\PhpDependencyList\Output;

use MageOs\PhpDependencyList\Reference;

class JsonOutput
{
    public function print($report)
    {
        fwrite(STDOUT, json_encode($report));
    }
}
