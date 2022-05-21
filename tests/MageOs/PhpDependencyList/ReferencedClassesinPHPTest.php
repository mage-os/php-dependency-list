<?php declare(strict_types=1);

namespace MageOs\PhpDependencyList;

use MageOs\PhpDependencyList\Exception\ParseException;
use PHPUnit\Framework\TestCase;

class ReferencedClassesinPHPTest extends TestCase
{
    public function testThrowsExceptionOnInvalidPhp()
    {
        $phpCode = <<<EOT
<?php class Qux {
    public function moo(Buz \$buz) {}
EOT;
        $sut = new ReferencedClassesInPHP();
        
        $this->expectException(ParseException::class);
        $sut->extractReferencedClassesFrom($phpCode);

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
        $sut = new ReferencedClassesInPHP();
        
        $result = $sut->extractReferencedClassesFrom($xmlCode);
        
        $this->assertSame([], $result);
    }

}