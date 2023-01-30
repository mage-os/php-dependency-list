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


// default config
const OPTION_ENABLE_ALL_PARSERS = '--all';
$validParsers = [
    '--'.ReferencedClassesInPHP::CODE => ReferencedClassesInPHP::class,
    '--'.ReferencedClassesInDiXML::CODE => ReferencedClassesInDiXML::class,
    '--'.ReferencedModulesInComposerJson::CODE => ReferencedModulesInComposerJson::class,
    '--'.ReferencedModulesInModuleXml::CODE => ReferencedModulesInModuleXml::class,
];
$enabledParsers = [
    ReferencedClassesInPHP::class
];
$hasSpecifiedParser = false;

const OPTION_OUTPUT_JSON = '--output-json';
const OPTION_OUTPUT_INCLUDE_SOURCE_FILE = '--include-source-file';
const OPTION_OUTPUT_INCLUDE_MODULE_NAME = '--include-module-names';
$outputJson = false;
$outputIncludeSourceFile = false;
$outputIncludeModuleName = false;

const INPUT_TYPE_STDIN = 0;
const INPUT_TYPE_FILE_LIST = 1; // list of files in file
const INPUT_TYPE_FILES = 2; // list of files passed in as arguments
$inputType = null;

function printHelpEndExit(){
    global $argv, $validParsers;

    $validParserOptions = implode(', ', array_keys($validParsers));

    fwrite(STDERR, <<<EOT
Usage:
    {$argv[0]} -f files.txt
    {$argv[0]} [files...]
    cat file.php | {$argv[0]}

Input Options:
    Stdin       - If no files are specified, input code is read from STDIN. Multiple files via STDIN can be separated by a zero byte. 
    File list   - If -f is specified before a list of files, each file is assumed to be a list of files to scan
    Files       - If -f is NOT specified, each file specified is scanned
    Both "File List" and "Files" options will recursively scan any directories specified for appropriate files.

Parser Options:
    By default only PHP (.php/.phtml) files will be evaluated. Any non PHP files will be silently ignored.
    You can change this behaviour by specifying any of the following parsers: {$validParserOptions}
    You can specify multiple parsers, if you wish to evaluate multiple file types in a single.
    You can also specify the {OPTION_ENABLE_ALL_PARSERS} option to enable all parsers
    For example: 
    {$argv[0]} --di.xml --php [files...]    - parse all di.xml and php files in the supplied list
    {$argv[0]} --all [files...]             - parse all files in the supplied list

Output Options
    --json-output               Output in JSON format
    --include-source-file       Include the source filepath of each class referenced in the output
    --include-module-names      Include the module name of each class referenced in the output

EOT
    );
    exit(1);
}

// loop through an pull out valid options
$args = [];
if(count($argv) > 1){
    for($x=1; $x<count($argv); $x++){
        $option = $argv[$x];

        if(isset($validParsers[$option])){
            if(!$hasSpecifiedParser){
                $enabledParsers = [$validParsers[$option]];
                $hasSpecifiedParser = true;
            }else{
                $enabledParsers[] = $validParsers[$option];
            }
            continue;
        }

        switch($option){
            case OPTION_ENABLE_ALL_PARSERS:
                $enabledParsers = array_values($validParsers);
                $hasSpecifiedParser = true;
                break;
            case OPTION_OUTPUT_JSON:
                $outputJson = true;
                break;
            case OPTION_OUTPUT_INCLUDE_MODULE_NAME:
                $outputIncludeModuleName = true;
                break;
            case OPTION_OUTPUT_INCLUDE_SOURCE_FILE:
                $outputIncludeSourceFile = true;
                break;
            case '-h':
            case '?':
            case '--help':
                printHelpEndExit();
                break;
            default:
                $args[] = $option;
        }
    }
}
$enabledParsers = array_unique($enabledParsers);
$parsers = [];
$filePatterns = [];
foreach($enabledParsers as $enabledParser){
    $parser = new $enabledParser();
    $parsers[] = $parser;
    $filePatterns[] = $parser->getParsiblePattern();
}

// work out options specified.
if(count($args) == 0){
    // no arguments left, must be STDIN
    $inputType = INPUT_TYPE_STDIN;
    $codeProvider = new ListCodeFromStdin();
}elseif($args[0] == '-f'){
    $inputType = INPUT_TYPE_FILE_LIST;
    $codeProvider = new ListCodeFromFileList($filePatterns, ...array_slice($args, 1));
}else{
    $inputType = INPUT_TYPE_FILES;
    $codeProvider = new ListCodeFromFiles($filePatterns, ...$args);
}

if($outputJson){
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
                if($outputIncludeSourceFile || $outputIncludeModuleName){
                    foreach($references as $reference){
                        $sourceResolver->resolve($reference);
                    }
                }
                break;
            }
        }
        if(!$outputJson){
            $outputter->print($filePath, $references, $outputIncludeSourceFile, $outputIncludeModuleName);
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
if($outputJson){
    $outputter->print($report, $outputIncludeSourceFile, $outputIncludeModuleName);
}
exit(0);