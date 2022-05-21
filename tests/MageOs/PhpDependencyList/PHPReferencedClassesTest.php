<?php declare(strict_types=1);

namespace MageOs\PhpDependencyList;

use PHPUnit\Framework\TestCase;

class PHPReferencedClassesTest extends TestCase
{
    public function testThrowsExceptionOnInvalidPhp()
    {
        $phpCode = <<<EOT
<?php class Qux {
    public function moo(Buz \$buz) {}
EOT;
        $sut = new PHPReferencedClasses();
        
        $this->expectException(\PhpParser\Error::class);
        $sut->list($phpCode);

    }

    public function testThrowsNoExceptionOnXml()
    {
        $xmlCode = <<<EOT
<?xml version="1.0"?>
<!--
/**
 * Copyright © Foo, Inc. All rights reserved.
 */
-->
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:ObjectManager/etc/config.xsd">
    <preference for="DateTimeInterface" type="DateTime" />
</config>
EOT;
        $sut = new PHPReferencedClasses();
        
        $result = $sut->list($xmlCode);
        
        $this->assertSame([], $result);
    }

}