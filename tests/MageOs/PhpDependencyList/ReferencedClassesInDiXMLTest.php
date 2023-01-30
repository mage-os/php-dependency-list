<?php declare(strict_types=1);

namespace MageOs\PhpDependencyList;

use MageOs\PhpDependencyList\Exception\ParseException;
use MageOs\PhpDependencyList\Parser\ReferencedClassesInDiXML;
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
        $sut->parse($nonXmlCode);
    }
    
    public function testParsesPreferences(): void
    {
        $xmlCode = <<<EOT
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:ObjectManager/etc/config.xsd">
    <preference for="DateTimeInterface" type="DateTimeImmutable" />
    <preference for="Just\Another\TypeInterface" type="This\Is\The\Implementation"/>

</config>
EOT;
        $sut = new ReferencedClassesInDiXML();
        
        $list = $sut->parse($xmlCode);
        $this->assertEquals([
            new Reference(\DateTimeImmutable::class), 
            new Reference(\This\Is\The\Implementation::class)
        ], $list);
    }
    
    public function testParsesObjectArguments(): void
    {
        $xmlCode = <<<EOT
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:ObjectManager/etc/config.xsd">
    <type name="This\Is\The\TargetType">
        <arguments>
            <argument name="dataTypeClassMap" xsi:type="array">
                <item name="foo" xsi:type="object">This\Is\A\Nested\ArrayType</item>
            </argument>
            <argument name="dataProcessor" xsi:type="object">This\Is\A\Direct\ArgumentType</argument>
        </arguments>
    </type>
</config>
EOT;
        $sut = new ReferencedClassesInDiXML();
        
        $list = $sut->parse($xmlCode);
        $this->assertEquals([
            new Reference(\This\Is\A\Nested\ArrayType::class), 
            new Reference(\This\Is\A\Direct\ArgumentType::class)
        ], $list);
    }
    
    public function testParsesVirtualTypes(): void
    {
        $xmlCode = <<<EOT
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:ObjectManager/etc/config.xsd">
    <virtualType name="interceptionConfigScope" type="Magento\Framework\Config\Scope">
        <arguments>
            <argument name="dataTypeClassMap" xsi:type="array">
                <item name="foo" xsi:type="object">This\Is\A\Nested\ArrayType</item>
            </argument>
            <argument name="dataProcessor" xsi:type="object">This\Is\A\Direct\ArgumentType</argument>
        </arguments>
    </virtualType>
</config>
EOT;
        $sut = new ReferencedClassesInDiXML();
        
        $list = $sut->parse($xmlCode);
        $this->assertEquals([
            new Reference(\This\Is\A\Nested\ArrayType::class),
            new Reference(\This\Is\A\Direct\ArgumentType::class),
            new Reference(\Magento\Framework\Config\Scope::class),
        ], $list);
    }
}