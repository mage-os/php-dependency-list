<?php declare(strict_types=1);

namespace MageOs\PhpDependencyList;

class PrintClassNames
{
    public static function echo(array $classes)
    {
        foreach($classes as $class) {
            fwrite(STDOUT, $class . PHP_EOL);
        }
    }
}