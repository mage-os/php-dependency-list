#!/usr/bin/env php
<?php declare(strict_types=1);

namespace MageOs\PhpDependencyList;

require_once __DIR__ . '/../vendor/autoload.php';

use MageOs\PhpDependencyList\Exception\ParseException;
use MageOs\PhpDependencyList\Parser\ReferencedClassesInPHP;
use MageOs\PhpDependencyList\Parser\ReferencedClassesInDiXML;
use MageOs\PhpDependencyList\Parser\ReferencedModulesInComposerJson;
use MageOs\PhpDependencyList\Parser\ReferencedModulesInModuleXml;
use MageOs\PhpDependencyList\CodeProvider\ListCodeFromFileList;
use MageOs\PhpDependencyList\CodeProvider\ListCodeFromFiles;
use MageOs\PhpDependencyList\CodeProvider\ListCodeFromStdin;

$validParserTypes = [
    '--'.ReferencedClassesInPHP::CODE,
    '--'.ReferencedClassesInDiXML::CODE,
    '--'.ReferencedModulesInComposerJson::CODE,
    '--'.ReferencedModulesInModuleXml::CODE,
];
$validParserTypesOptions = implode('|', $validParserTypes);
$defaultParser = '--'.ReferencedClassesInPHP::CODE;

const JSON_OUTPUT = '--json-output';
const INCLUDE_SOURCE_FILE = '--include-source-file';
const INCLUDE_MODULE_NAME = '--include-module-names';
$optionalArguments = '['.JSON_OUTPUT.'] ['.INCLUDE_SOURCE_FILE.'] ['.INCLUDE_MODULE_NAME.']';

const INPUT_TYPE_STDIN = 0;
const INPUT_TYPE_FILE = 1; // list of files in file
const INPUT_TYPE_FILES = 2; // list of files passed in as arguments

if (array_search('--help', $argv, true) || array_search('-h', $argv, true)) {
    fwrite(STDERR, <<<EOT
Usage:
    {$argv[0]} {$optionalArguments} -f files.txt
    {$argv[0]} {$optionalArguments} -- [files...]
    cat file.php | {$argv[0]} {$optionalArguments} [{$validParserTypesOptions}] 
    (When passing code directly, default parser type is {$defaultParser})

    --json-output               Output in JSON format
    --include-source-file       If the source file each class referenced found should be included in the output
    --include-module-names      If the module name of each class referenced found should be included

If no files are specified, input code is read from STDIN. Multiple files via STDIN can be separated by a zero byte.  

EOT
    );
    exit(1);
}

$parsers = [
    ReferencedClassesInPHP::CODE => new ReferencedClassesInPHP(),
    ReferencedClassesInDiXML::CODE => new ReferencedClassesInDiXML(),
    ReferencedModulesInComposerJson::CODE => new ReferencedModulesInComposerJson(),
    ReferencedModulesInModuleXml::CODE =>  new ReferencedModulesInModuleXml(),
];
// parse all the cli options
$args = array_values(array_slice($argv, 1));
$isJsonOutput = false;
$includeSourceFile = false;
$includeModuleName = false;
$inputType = null;
foreach($args as $index => $arg){
    switch($arg){
        case '--'.ReferencedClassesInPHP::CODE:
        case '--'.ReferencedClassesInDiXML::CODE:
        case '--'.ReferencedModulesInComposerJson::CODE:
        case '--'.ReferencedModulesInModuleXml::CODE:
            $inputType = INPUT_TYPE_STDIN;
            break 2;
        case JSON_OUTPUT:
            $isJsonOutput = true;
            break;
        case INCLUDE_SOURCE_FILE:
            $includeSourceFile = true;
            break;
        case INCLUDE_MODULE_NAME:
            $includeModuleName = true;
            break;
        case '--':
            $inputType = INPUT_TYPE_FILES;
            break 2;
        case '-f':
            $inputType = INPUT_TYPE_FILE;
            break 2;
    }
}
$filePatterns = [];
foreach($parsers as $parser){
    $filePatterns[] = $parser->getParsiblePattern();
}

switch($inputType){
    case INPUT_TYPE_STDIN:
        $parsers = [$parsers[substr($arg, 2)]];
        $codeProvider = new ListCodeFromStdin();
        break;
    case INPUT_TYPE_FILE:
        $codeProvider = new ListCodeFromFileList($filePatterns, $args[($index + 1)]);
        break;
    case INPUT_TYPE_FILES:
        $codeProvider = new ListCodeFromFiles($filePatterns, ...array_slice($args, $index + 1));
        break;
    case null:
        $parsers = [$parsers[ReferencedClassesInPHP::CODE]];
        $codeProvider = new ListCodeFromStdin();
        break;
}

if($isJsonOutput){
    $outputter = new Output\JsonOutput();
}else{
    $outputter = new Output\StdOutOutput();
}

$sourceResolver = new SourceResolver();

$report = [];
foreach ($codeProvider->list() as $filePath => $code) {
    try {
        /** @var ParserInterface $parser */
        foreach($parsers as $parser){
            if($parser->canParse($filePath)){
                $references = $parser->parse($code);
                if($includeModuleName || $includeSourceFile){
                    foreach($references as $reference){
                        $sourceResolver->resolve($reference);
                    }
                }
                break;
            }
        }
        if(!$isJsonOutput){
            $outputter->print($filePath, $references, $includeSourceFile, $includeModuleName);
        }else{
            $report[$filePath] = $references;
        }
    } catch (ParseException $exception) {
        // Ignore parse error exceptions in input
    } catch (\Exception $exception) {
        fwrite(STDERR, $exception->getMessage() . PHP_EOL);
        exit(1);
    }
}
if($isJsonOutput){
    $outputter->print($report, $includeSourceFile, $includeModuleName);
}
exit(0);