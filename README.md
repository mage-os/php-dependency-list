# List PHP Class Dependcies

A simple tool to list PHP classes referenced in a given set of files

## Usage

bin/php-classes.php -f files.txt
bin/php-classes.php [files...]
cat file.php | bin/php-classes.php

If no files are specified, PHP Code is read from STDIN.  

In this case multiple code files can be separated by a zero byte. This scenario can be used in cases where the code is not read from some database instead of the file system (for example a git repo without a working copy, a ZIP archive, ...).

## License

Copyright 2022 by Vinai Kopp.

This code is licensed under the BSD-3-Clause license (see LICENSE.txt for details).
