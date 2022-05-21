<?php declare(strict_types=1);

namespace MageOs\PhpDependencyList;

use MageOs\PhpDependencyList\Exception\ParseException;

class ReferencedClassesInDiXML
{
    public function extractReferencedClassesFrom(string $xml)
    {
        $dom = new \DOMDocument();
        if (! $dom->loadXML($xml, LIBXML_NOERROR | LIBXML_NOWARNING)) {
            throw new ParseException(sprintf('Unable to parse XML input'));
        }
        /** @var $preferences \DOMElement[] */
        $preferences = (new \DOMXPath($dom))->query('/config/preference');
        
        $classes = [];
        foreach ($preferences as $preference) {
            $classes[] = $preference->getAttribute('type');
        }
        return $classes;
    }
}