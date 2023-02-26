<?php declare(strict_types=1);

namespace MageOs\PhpDependencyList;

use MageOs\PhpDependencyList\Exception\ParseException;
use MageOs\PhpDependencyList\Parser\ReferencedModulesInModuleXml;
use PHPUnit\Framework\TestCase;

class ReferencedClassesInModuleXmlTest extends TestCase
{
    public function testThrowsExceptionOnInvalidJson()
    {
        $phpCode = <<<EOT
<?php class Qux {
    public function moo(Buz \$buz) {}
EOT;
        $sut = new ReferencedModulesInModuleXml();
        
        $this->expectException(ParseException::class);
        $sut->parse($phpCode);
    }

    /**
     * @dataProvider correctModulesAreExtractedData
     */
    public function testCorrectModulesAreExtracted($moduleXml, $expectedModules)
    {
        $sut = new ReferencedModulesInModuleXml();
        $list = $sut->parse($moduleXml);

        $this->assertEquals($expectedModules, $list);
    }

    public function correctModulesAreExtractedData()
    {
        $noRequirements = <<<EOT
<?xml version="1.0"?>
<!--
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
-->
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:Module/etc/module.xsd">
    <module name="Magento_Catalog" setup_version="2.0.3" />
</config>
EOT;

    $singleRequirement = <<<EOT
<?xml version="1.0"?>
<!--
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
-->
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:Module/etc/module.xsd">
    <module name="Magento_Catalog" setup_version="2.0.3">
        <sequence>
            <module name="Magento_Eav"/>
        </sequence>
    </module>
</config>
EOT;

    $twoRequirements = <<<EOT
<?xml version="1.0"?>
<!--
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
-->
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:Module/etc/module.xsd">
    <module name="Magento_Catalog" setup_version="2.0.3">
        <sequence>
            <module name="Magento_Eav"/>
            <module name="Magento_Cms"/>
        </sequence>
    </module>
</config>
EOT;

        return [
            'no requirements' => [
                'composer_json' => $noRequirements,
                'expected_modules' => [],
            ],
            'single requirement' => [
                'composer_json' => $singleRequirement,
                'expected_modules' => [
                    new Reference(null, 'Magento_Eav')
                ],
            ],
            'two requirements' => [
                'composer_json' => $twoRequirements,
                'expected_modules' => [
                    new Reference(null, 'Magento_Eav'),
                    new Reference(null, 'Magento_Cms'),
                ],
            ],
        ];
    }
}