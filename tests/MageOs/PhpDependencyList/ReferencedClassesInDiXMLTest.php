<?php declare(strict_types=1);

namespace MageOs\PhpDependencyList;

use MageOs\PhpDependencyList\Exception\ParseException;
use PHPUnit\Framework\TestCase;

class ReferencedClassesInDiXMLTest extends TestCase
{
    public function testThrowsExceptionOnNonXmlCode(): void
    {
        $nonXmlCode = <<<EOT
<?php class Qux {
    public function moo(Buz \$buz) {}
EOT;
        $sut = new ReferencedClassesInDiXML();
        
        $this->expectException(ParseException::class);
        $sut->extractReferencedClassesFrom($nonXmlCode);
    }
    
    public function testParsesPreferences(): void
    {
        $xmlCode = <<<EOT
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:ObjectManager/etc/config.xsd">
    <preference for="DateTimeInterface" type="DateTimeImmutable" />
</config>
EOT;
        $sut = new ReferencedClassesInDiXML();
        
        $list = $sut->extractReferencedClassesFrom($xmlCode);
        $this->assertSame([\DateTimeImmutable::class], $list);
    }
}