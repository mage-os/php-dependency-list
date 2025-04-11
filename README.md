# List PHP Class Dependcies

A simple tool to list PHP classes referenced in a given set of files

## Usage
```text
Usage:
    ./php-classes.phar -f files.txt
    ./php-classes.phar [files...]
    cat file.php | ./php-classes.phar

Input Options:
    Stdin       - If no files are specified, input code is read from STDIN. Multiple files via STDIN can be separated by a zero byte. 
                This scenario can be used in cases where the code is not read from some database instead of the file system (for 
                example a git repo without a working copy, a ZIP archive, ...).
    File list   - If -f is specified before a list of files, each file is assumed to be a list of files to scan
    Files       - If -f is NOT specified, each file specified is scanned
    Both "File List" and "Files" options will recursively scan any directories specified for appropriate files.

Parser Options:
    By default only PHP (.php/.phtml) files will be evaluated. Any non PHP files will be silently ignored.
    You can change this behaviour by specifying any of the following parsers: --php, --di.xml, --composer.json, --module.xml
    You can specify multiple parsers, if you wish to evaluate multiple file types in a single.
    You can also specify the --all option to enable all parsers
    For example: 
    ./php-classes.phar --di.xml --php [files...]    - parse all di.xml and php files in the supplied list
    ./php-classes.phar --all [files...]             - parse all files in the supplied list

Output Options
    --json-output               Output in JSON format
    --include-source-file       Include the source filepath of each class referenced in the output
    --include-module-names      Include the module name of each class referenced in the output

Version: 1.2.0
```

## License

Copyright 2022 by Vinai Kopp.

This code is licensed under the BSD-3-Clause license (see LICENSE.txt for details).


## Contributing

### Docker Image
To ensure testing / development is carried out against the correct environment a Dockerfile has been provided in the `build` directory.
This can be built with `./bin/build-docker-image`

You can then commands with the following:
```bash
docker run -t --volume $(pwd):/usr/src php-dependency-list-env:1.1.0 bash -c "COMMAND"
```

For example to test the "help" output
```bash
docker run -t --volume $(pwd):/usr/src php-dependency-list-env:1.1.0 bash -c "php bin/php-classes.php --help"
```

### Regression Testing
Regression tests can be ran with the shell script provided
```bash
./bin/run-regression-tests
```

### Building the Phar
The Phar can be built with the shell script provided
```bash
./bin/build-phar
```

### Coding standards
All code should conform to PSR12.
You can check this with: `./vendor/bin/phpcs --standard=PSR12 -- src`
Alot of the errors and warnings can be auto corrected with: `./vendor/bin/phpcbf --standard=PSR12 src`