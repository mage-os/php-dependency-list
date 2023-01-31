<?php declare(strict_types=1);

namespace MageOs\PhpDependencyList\Parser;

use MageOs\PhpDependencyList\CodeProvider\ListCodeFromStdin;
use MageOs\PhpDependencyList\Exception\ParseException;
use PhpParser\Node;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\ParserFactory;
use MageOs\PhpDependencyList\ParserInterface;
use MageOs\PhpDependencyList\Reference;

use function array_filter as filter;
use function array_map as map;
use function array_unique as unique;

class ReferencedClassesInPHP implements ParserInterface
{
    const CODE = 'php';
    const PATTERN = '/.*\.(?:php|phtml)$/';

    private static $exclude = [
        '\\true',
        '\\false',
        '\\null',
        '\\__',
        '\\stdClass',
    ];

    /**
     * @param string $filePath
     * @return bool
     */
    public function canParse($filePath)
    {
        return (1 === preg_match(self::PATTERN, $filePath));
    }

    /**
     * @param string $code
     * @return []Reference
     */
    public function parse($phpCode)
    {
        $classes = $this->extractReferencedClassesFrom($phpCode);
        return array_map(function($class){
            return new Reference($class);
        }, $classes);
    }
    
    public function extractReferencedClassesFrom(string $phpCode): array
    {
        try {
            $parser        = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
            $nodeTraverser = new NodeTraverser;
            $nodeTraverser->addVisitor(new NameResolver);
            $stmts = $nodeTraverser->traverse($parser->parse($phpCode));
            $nodes = (new NodeFinder)->findInstanceOf($stmts, Node\Name\FullyQualified::class);
    
            $classesAndFunctions = map(fn(Node\Name\FullyQualified $class) => $class->toCodeString(), $nodes);
            
            return array_values(unique(filter($classesAndFunctions, function (string $name) {
                return !in_array($name, self::$exclude, true) && !function_exists($name) && !defined($name);
            })));
        } catch (\PhpParser\Error $exception) {
            throw new ParseException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * @return string
     */
    public function getParsiblePattern()
    {
        return self::PATTERN;
    }
}