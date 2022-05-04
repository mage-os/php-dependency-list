<?php declare(strict_types=1);

namespace MageOs\PhpDependencyList;

use MageOs\A;
use PhpParser\NodeDumper;

class TestClass extends NodeDumper
{
    public \MageOs\A $a;
    
    public string $b = \MageOs\G::FOO;
    
    public function __construct(array $options = [], \MageOs\B $listPhpFiles)
    {
        parent::__construct($options);
    }

    public function foo(\MageOs\C $listPhpFiles): \MageOs\D
    {
        if ($listPhpFiles->toString() === \MageOs\E::class) {
            return \in_array(\MageOs\F::class, []);
        }
        
        return new A;
    }

}