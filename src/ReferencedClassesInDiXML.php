<?php declare(strict_types=1);

namespace MageOs\PhpDependencyList;

use MageOs\PhpDependencyList\Exception\ParseException;

use function array_merge as merge;

class ReferencedClassesInDiXML
{
    public function extractReferencedClassesFrom(string $xml)
    {
        $dom = new \DOMDocument();
        if (! $dom->loadXML($xml, LIBXML_NOERROR | LIBXML_NOWARNING)) {
            throw new ParseException(sprintf('Unable to parse XML input'));
        }
        
        $preferences = $this->extractPreferences($dom);
        $arguments = $this->extractArguments($dom);
        
        return merge($preferences, $arguments);
    }

    private function extractPreferences(\DOMDocument $dom): array
    {
        /** @var $preferences \DOMElement[] */
        $preferences = (new \DOMXPath($dom))->query('/config/preference');

        $classes = [];
        foreach ($preferences as $preference) {
            $classes[] = $preference->getAttribute('type');
        }
        
        return $classes;
    }

    private function extractArguments(\DOMDocument $dom): array
    {
        /** @var $arguments \DOMElement[] */
        $xpath  = new \DOMXPath($dom);
        $arguments = $xpath->query("/config/type/arguments//argument|item[@xsi:type='object']");
        $classes = [];
        foreach ($arguments as $argument) {
            $classes[] = trim($argument->textContent);
        }
        
        return $classes;
    }
}