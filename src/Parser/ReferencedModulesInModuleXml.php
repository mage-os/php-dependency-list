<?php declare(strict_types=1);

namespace MageOs\PhpDependencyList\Parser;

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

class ReferencedModulesInModuleXml implements ParserInterface
{
    const CODE = 'module.xml';
    const PATTERN = '/.*module\.xml$/';

    /**
     * @param string $filePath
     * @return bool
     */
    public function canParse($filePath)
    {
        return 1 === preg_match(
            self::PATTERN,
            $filePath
        );
    }

    /**
     * @param string $code
     * @return []Reference
     */
    public function parse($phpCode)
    {
        return [];
    }

    /**
     * @return string
     */
    public function getParsiblePattern()
    {
        return self::PATTERN;
    }
}