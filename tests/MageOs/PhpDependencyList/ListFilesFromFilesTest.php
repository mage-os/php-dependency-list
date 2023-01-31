<?php declare(strict_types=1);

namespace MageOs\PhpDependencyList;

use MageOs\PhpDependencyList\CodeProvider\ListCodeFromFiles;
use MageOs\PhpDependencyList\Parser\ReferencedClassesInDiXML;
use MageOs\PhpDependencyList\Parser\ReferencedClassesInPHP;
use MageOs\PhpDependencyList\Parser\ReferencedModulesInComposerJson;
use MageOs\PhpDependencyList\Parser\ReferencedModulesInModuleXml;
use PHPUnit\Framework\TestCase;

class ListFilesFromFilesTest extends TestCase
{
    const MOCK_DIRECTORY = 'mock_dir';

    /**
     * @dataProvider correctFilePathsAreReturnedData
     */
    public function testCorrectFilePathsAreReturned(
        $directoryStructure,
        $patterns,
        $args,
        $expectedFiles
    ): void
    {
        shell_exec('rm -rf '.self::MOCK_DIRECTORY);
        foreach($directoryStructure as $filePath){
            $filePath = self::MOCK_DIRECTORY.'/'.$filePath;
            if(!is_dir(dirname($filePath))){
                mkdir(dirname($filePath), 0777, true);
            }
            if(!is_file($filePath)){
                file_put_contents($filePath, '');
            }
        }

        $sut = new ListCodeFromFiles($patterns, ...$args);

        $this->assertEquals($expectedFiles, iterator_to_array($sut->list()));
        shell_exec('rm -rf '.self::MOCK_DIRECTORY);
    }

    public function correctFilePathsAreReturnedData()
    {
        $basicDirectoryStructure = [
            'Test.php',
            'Test.xml',
            'composer.json',
            'di.xml',
            'module.xml',
            'a/Test.php',
            'a/Test.xml',
            'a/composer.json',
            'a/di.xml',
            'a/module.xml',
        ];

        return [
            'Test Non existent filepath' => [
                'directory_structure' => $basicDirectoryStructure,
                'patterns' => [ReferencedClassesInPHP::PATTERN],
                'args' => ['a_directory_that_doesnt_exist'],
                'expected_files' => [],
            ],
            'Specify same directory twice' => [
                'directory_structure' => $basicDirectoryStructure,
                'patterns' => [ReferencedClassesInPHP::PATTERN],
                'args' => [self::MOCK_DIRECTORY, self::MOCK_DIRECTORY],
                'expected_files' => [
                    self::MOCK_DIRECTORY.'/Test.php' => '',
                    self::MOCK_DIRECTORY.'/a/Test.php' => '',
                ],
            ],
            'Just look for PHP files recursivley' => [
                'directory_structure' => $basicDirectoryStructure,
                'patterns' => [ReferencedClassesInPHP::PATTERN],
                'args' => [self::MOCK_DIRECTORY],
                'expected_files' => [
                    self::MOCK_DIRECTORY.'/Test.php' => '',
                    self::MOCK_DIRECTORY.'/a/Test.php' => '',
                ],
            ],
            'Just look for PHP files' => [
                'directory_structure' => $basicDirectoryStructure,
                'patterns' => [ReferencedClassesInPHP::PATTERN],
                'args' => [self::MOCK_DIRECTORY.'/a'],
                'expected_files' => [
                    self::MOCK_DIRECTORY.'/a/Test.php' => '',
                ],
            ],
            'Just look for di.xml files' => [
                'directory_structure' => $basicDirectoryStructure,
                'patterns' => [ReferencedClassesInDiXML::PATTERN],
                'args' => [self::MOCK_DIRECTORY],
                'expected_files' => [
                    self::MOCK_DIRECTORY.'/di.xml' => '',
                    self::MOCK_DIRECTORY.'/a/di.xml' => '',
                ],
            ],
            'Just look for composer.json files' => [
                'directory_structure' => $basicDirectoryStructure,
                'patterns' => [ReferencedModulesInComposerJson::PATTERN],
                'args' => [self::MOCK_DIRECTORY],
                'expected_files' => [
                    self::MOCK_DIRECTORY.'/composer.json' => '',
                    self::MOCK_DIRECTORY.'/a/composer.json' => '',
                ],
            ],
            'Just look for module.xml files' => [
                'directory_structure' => $basicDirectoryStructure,
                'patterns' => [ReferencedModulesInModuleXml::PATTERN],
                'args' => [self::MOCK_DIRECTORY],
                'expected_files' => [
                    self::MOCK_DIRECTORY.'/module.xml' => '',
                    self::MOCK_DIRECTORY.'/a/module.xml' => '',
                ],
            ],
            'Just look for all files' => [
                'directory_structure' => $basicDirectoryStructure,
                'patterns' => [
                    ReferencedModulesInModuleXml::PATTERN,
                    ReferencedModulesInComposerJson::PATTERN,
                    ReferencedClassesInDiXML::PATTERN,
                    ReferencedClassesInPHP::PATTERN
                ],
                'args' => [self::MOCK_DIRECTORY],
                'expected_files' => [
                    self::MOCK_DIRECTORY.'/Test.php' => '',
                    self::MOCK_DIRECTORY.'/composer.json' => '',
                    self::MOCK_DIRECTORY.'/di.xml' => '',
                    self::MOCK_DIRECTORY.'/module.xml' => '',
                    self::MOCK_DIRECTORY.'/a/Test.php' => '',
                    self::MOCK_DIRECTORY.'/a/composer.json' => '',
                    self::MOCK_DIRECTORY.'/a/di.xml' => '',
                    self::MOCK_DIRECTORY.'/a/module.xml' => '',
                ],
            ],
        ];
    }
}