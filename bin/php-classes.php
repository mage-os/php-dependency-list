#!/usr/bin/env php
<?php declare(strict_types=1);

namespace MageOs\PhpDependencyList;

require_once __DIR__ . '/../vendor/autoload.php';

if (array_search('--help', $argv, true) || array_search('-h', $argv, true)) {
    fwrite(STDERR, <<<EOT
Usage:
    {$argv[0]} -f files.txt
    {$argv[0]} [files...]
    cat file.php | {$argv[0]}
    
If no files are specified, PHP Code is read from STDIN. Multiple code files can be separated by a zero byte.

EOT
    );
    exit(1);
}

$args = array_values(array_slice($argv, 1));
if (false !== ($idx = array_search('-f', $args))) {
    if (!isset($args[$idx + 1])) {
        fwrite(STDERR, "Missing file containing file-list after argument -f.\n");
        exit(1);
    }
    $args = explode("\n", file_get_contents($args[$idx + 1]));
}
$phpProvider = empty($args) ? new ListPhpFromStdin() : new ListPhpFromFiles(... $args);

foreach ($phpProvider->list() as $phpCode) {
    try {
        PrintClassNames::echo((new PHPReferencedClasses())->list($phpCode));
    } catch (\PhpParser\Error $exception) {
        // Ignore parse error exceptions in input
    } catch (\Exception $exception) {
        fwrite(STDERR, $exception->getMessage() . PHP_EOL);
        exit(1);
    }
}