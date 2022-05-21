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
        $virtualTypes = $this->extractVirtualTypes($dom);
        
        return merge($preferences, $arguments, $virtualTypes);
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
        $arguments = $xpath->query("//argument[@xsi:type='object']|//item[@xsi:type='object']");
        $classes = [];
        foreach ($arguments as $argument) {
            $classes[] = trim($argument->nodeValue);
        }
        
        return $classes;
    }

    private function extractVirtualTypes(\DOMDocument $dom): array
    {
        /** @var $virtualTypes \DOMElement[] */
        $xpath  = new \DOMXPath($dom);
        $virtualTypes = $xpath->query("/config/virtualType[@type]");
        $classes = [];
        foreach ($virtualTypes as $virtualType) {
            $classes[] = trim($virtualType->getAttribute('type'));
        }
        
        return $classes;
    }
}