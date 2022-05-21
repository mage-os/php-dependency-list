#!/usr/bin/env php
<?php declare(strict_types=1);

namespace MageOs\PhpDependencyList;

use MageOs\PhpDependencyList\Exception\ParseException;

require_once __DIR__ . '/../vendor/autoload.php';

if (array_search('--help', $argv, true) || array_search('-h', $argv, true)) {
    fwrite(STDERR, <<<EOT
Usage:
    {$argv[0]} -f files.txt
    {$argv[0]} [files...]
    cat file.php | {$argv[0]}
    Add --di.xml to switch to parsing referenced PHP classes from Magento di.xml files instead of PHP

If no files are specified, input code is read from STDIN. Multiple files via STDIN can be separated by a zero byte.  

EOT
    );
    exit(1);
}

const FILE_NAME_PATTERN_MAP = [
        'php' => '/\.(?:php|phtml)$/',
        'di.xml' => '/^di\.xml$/',
];

if ($isXmlMode = array_search('--di.xml', $argv, true)) {
    array_splice($argv, $isXmlMode, 1);
}

$args = array_values(array_slice($argv, 1));
if (false !== ($idx = array_search('-f', $args))) {
    if (!isset($args[$idx + 1])) {
        fwrite(STDERR, "Missing file containing file-list after argument -f.\n");
        exit(1);
    }
    $args = explode("\n", file_get_contents($args[$idx + 1]));
}

// -----

$targetCodeType = $isXmlMode ? 'di.xml' : 'php';

$codeProvider = empty($args)
    ? new ListCodeFromStdin()
    : new ListCodeFromFiles(FILE_NAME_PATTERN_MAP[$targetCodeType], ...$args);

$parser = $targetCodeType === 'php'
    ? new ReferencedClassesInPHP()
    : new ReferencedClassesInDiXML();

foreach ($codeProvider->list() as $code) {
    try {
        PrintClassNames::echo($parser->extractReferencedClassesFrom($code));
    } catch (ParseException $exception) {
        // Ignore parse error exceptions in input
    } catch (\Exception $exception) {
        fwrite(STDERR, $exception->getMessage() . PHP_EOL);
        exit(1);
    }
}