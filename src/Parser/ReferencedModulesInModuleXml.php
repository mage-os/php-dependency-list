<?php

declare(strict_types=1);

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

class ReferencedModulesInModuleXml implements ParserInterface
{
    public const CODE = 'module.xml';
    public const PATTERN = '/.*module\.xml$/';

    /**
     * @param  string $filePath
     * @return bool
     */
    public function canParse($filePath)
    {
        return (1 === preg_match(self::PATTERN, $filePath));
    }

    /**
     * @param  string $xml
     * @return []Reference
     */
    public function parse($xml)
    {
        $dom = new \DOMDocument();
        if (! $dom->loadXML($xml, LIBXML_NOERROR | LIBXML_NOWARNING)) {
            throw new ParseException(sprintf('Unable to parse XML input'));
        }

        $references = [];

        /**
 * @var $modules \DOMElement[]
*/
        $modules = (new \DOMXPath($dom))->query('/config/module/sequence/module');

        foreach ($modules as $module) {
            $references[] = new Reference(null, $module->getAttribute('name'));
        }

        return $references;
    }

    /**
     * @return string
     */
    public function getParsiblePattern()
    {
        return self::PATTERN;
    }
}
