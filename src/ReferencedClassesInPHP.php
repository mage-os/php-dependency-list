<?php declare(strict_types=1);

namespace MageOs\PhpDependencyList;

use MageOs\PhpDependencyList\Exception\ParseException;
use PhpParser\Node;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\ParserFactory;

use function array_filter as filter;
use function array_map as map;
use function array_unique as unique;

class ReferencedClassesInPHP
{
    private static $exclude = [
        '\\true',
        '\\false',
        '\\null',
        '\\__',
        '\\stdClass',
    ];
    
    public function extractReferencedClassesFrom(string $phpCode): array
    {
        try {
            $parser        = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
            $nodeTraverser = new NodeTraverser;
            $nodeTraverser->addVisitor(new NameResolver);
            $stmts = $nodeTraverser->traverse($parser->parse($phpCode));
            $nodes = (new NodeFinder)->findInstanceOf($stmts, Node\Name\FullyQualified::class);
    
            $classesAndFunctions = map(fn(Node\Name\FullyQualified $class) => $class->toCodeString(), $nodes);
    
            return unique(filter($classesAndFunctions, function (string $name) {
                return !in_array($name, self::$exclude, true) && !function_exists($name) && !defined($name);
            }));
        } catch (\PhpParser\Error $exception) {
            throw new ParseException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}