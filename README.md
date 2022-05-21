# List PHP Class Dependcies

A simple tool to list PHP classes referenced in a given set of files

## Usage

    ./php-classes.phar -f files.txt
    ./php-classes.phar [files...]
    cat file.php | ./php-classes.phar

 dd `--di.xml` to switch to parsing referenced PHP classes from Magento `di.xml` files instead of PHP.

If no files are specified, input code is read from STDIN. Multiple files via STDIN can be separated by a zero byte.  
This scenario can be used in cases where the code is not read from some database instead of the file system (for example a git repo without a working copy, a ZIP archive, ...).

## License

Copyright 2022 by Vinai Kopp.

This code is licensed under the BSD-3-Clause license (see LICENSE.txt for details).
