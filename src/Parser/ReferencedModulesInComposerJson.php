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

class ReferencedModulesInComposerJson implements ParserInterface
{
    public const CODE = 'composer.json';
    public const PATTERN = '/.*composer\.json$/';

    /**
     * @param string $filePath
     * @return bool
     */
    public function canParse($filePath)
    {
        return (1 === preg_match(self::PATTERN, $filePath));
    }

    /**
     * @param string $content
     * @return []Reference
     */
    public function parse($content)
    {
        $composerJson = json_decode($content, true);
        if ($composerJson === null) {
            throw new ParseException(json_last_error_msg(), json_last_error());
        }

        $references = [];
        if (isset($composerJson['require'])) {
            foreach ($composerJson['require'] as $module => $version) {
                $references[] = new Reference(null, $module);
            }
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
