<?php declare(strict_types=1);

namespace MageOs\PhpDependencyList;

use MageOs\PhpDependencyList\Exception\ParseException;
use MageOs\PhpDependencyList\Parser\ReferencedModulesInComposerJson;
use PHPUnit\Framework\TestCase;

class ReferencedClassesInComposerJsonTest extends TestCase
{
    public function testThrowsExceptionOnInvalidJson()
    {
        $phpCode = <<<EOT
<?php class Qux {
    public function moo(Buz \$buz) {}
EOT;
        $sut = new ReferencedModulesInComposerJson();
        
        $this->expectException(ParseException::class);
        $sut->parse($phpCode);
    }

    /**
     * @dataProvider correctModulesAreExtractedData
     */
    public function testCorrectModulesAreExtracted($composerJson, $expectedModules)
    {
        $sut = new ReferencedModulesInComposerJson();
        $list = $sut->parse($composerJson);

        $this->assertEquals($expectedModules, $list);
    }

    public function correctModulesAreExtractedData()
    {
        $noRequirements = <<<EOT
{
    "name": "mage-os/php-dependency-list",
    "description": "Determine PHP class source dependencies file within a project",
    "license": "BSD-3-Clause",
    "autoload": {
        "psr-4": {
            "MageOs\\\\PhpDependencyList\\\\": "src/"
        }
    },
    "authors": [
        {
            "name": "Vinai Kopp",
            "email": "vinai@netzarbeiter.com"
        }
    ],
    "require-dev": {
        "phpunit/phpunit": "^9.5"
    },
    "autoload-dev": {
        "psr-4": {
            "MageOs\\\\PhpDependencyList\\\\": "tests/MageOs/PhpDependencyList/"
        }
    },
    "bin": [
        "php-classes.phar"
    ]
}
EOT;

    $singleRequirement = <<<EOT
{
    "name": "mage-os/php-dependency-list",
    "description": "Determine PHP class source dependencies file within a project",
    "require": {
        "nikic/php-parser": "^4.13"
    },
    "license": "BSD-3-Clause",
    "autoload": {
        "psr-4": {
            "MageOs\\\\PhpDependencyList\\\\": "src/"
        }
    },
    "authors": [
        {
            "name": "Vinai Kopp",
            "email": "vinai@netzarbeiter.com"
        }
    ],
    "require-dev": {
        "phpunit/phpunit": "^9.5"
    },
    "autoload-dev": {
        "psr-4": {
            "MageOs\\\\PhpDependencyList\\\\": "tests/MageOs/PhpDependencyList/"
        }
    },
    "bin": [
        "php-classes.phar"
    ]
}    
EOT;

    $twoRequirements = <<<EOT
{
    "name": "mage-os/php-dependency-list",
    "description": "Determine PHP class source dependencies file within a project",
    "require": {
        "nikic/php-parser": "^4.13",
        "php": ">=7.4"
    },
    "license": "BSD-3-Clause",
    "autoload": {
        "psr-4": {
            "MageOs\\\\PhpDependencyList\\\\": "src/"
        }
    },
    "authors": [
        {
            "name": "Vinai Kopp",
            "email": "vinai@netzarbeiter.com"
        }
    ],
    "require-dev": {
        "phpunit/phpunit": "^9.5"
    },
    "autoload-dev": {
        "psr-4": {
            "MageOs\\\\PhpDependencyList\\\\": "tests/MageOs/PhpDependencyList/"
        }
    },
    "bin": [
        "php-classes.phar"
    ]
}    
EOT;

    $phpExtensionRequirment = <<<EOT
{
    "name": "mage-os/php-dependency-list",
    "description": "Determine PHP class source dependencies file within a project",
    "require": {
        "php-xml": "^4.13"
    },
    "license": "BSD-3-Clause",
    "autoload": {
        "psr-4": {
            "MageOs\\\\PhpDependencyList\\\\": "src/"
        }
    },
    "authors": [
        {
            "name": "Vinai Kopp",
            "email": "vinai@netzarbeiter.com"
        }
    ],
    "require-dev": {
        "phpunit/phpunit": "^9.5"
    },
    "autoload-dev": {
        "psr-4": {
            "MageOs\\\\PhpDependencyList\\\\": "tests/MageOs/PhpDependencyList/"
        }
    },
    "bin": [
        "php-classes.phar"
    ]
}    
EOT;

        return [
            'no requirements' => [
                'composer_json' => $noRequirements,
                'expected_modules' => [],
            ],
            'single requirement' => [
                'composer_json' => $singleRequirement,
                'expected_modules' => [
                    new Reference(null, 'nikic/php-parser')
                ],
            ],
            'two requirements' => [
                'composer_json' => $twoRequirements,
                'expected_modules' => [
                    new Reference(null, 'nikic/php-parser'),
                    new Reference(null, 'php'),
                ],
            ],
            'php extension requirement' => [
                'composer_json' => $phpExtensionRequirment,
                'expected_modules' => [
                    new Reference(null, 'php-xml'),
                ],
            ],
        ];
    }
}