
php-classes.phar: box.phar bin/php-classes.php $(shell find src -type f)
	./box.phar compile; \
	ls -l php-classes.phar

box.phar:
	curl -L https://github.com/box-project/box/releases/download/4.2.0/box.phar -o box.phar; \
	chmod +x box.phar; \
	./box.phar --version
