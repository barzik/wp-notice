[![Build Status](https://travis-ci.org/barzik/wp-notice.svg)](https://travis-ci.org/barzik/wp-notice)

# WP Notice

Wp Notice plugin enable every admin to post announcement (HTML or just text) on top of every WordPress post.
The admin can choose specific categories, tags or post date in order to point the correct message to the posts he wants.
Ideal for obsolete\deprecation messages for tutorials or technical posts.

# Installation and other information

For Installations, screen shots and logs, please refer to [WP Notice WordPress.org page](https://wordpress.org/plugins/wp-notice/). 

# Automated testing

WP Notice can be tested by using PHPUnit with the official WordPress testing environment.

1. Install WordPress develop and PHPUnit. You can follow [those instructions](https://make.wordpress.org/core/handbook/testing/automated-testing/)
2. Define local variable WP_TESTS_DIR with the location of WordPress develop phpunit folder. for example, put
put `export WP_TESTS_DIR="/var/www/html/wordpress-develop/tests/phpunit"` in .bashrc (Linux)
3. Go to the plugin main folder and run `phpunit`.
4. Tests coverage report is being printed in HTML page to ./log/CodeCoverage.
5. You may run phpunit tests with Grunt by typing `grunt phpunit`.

# Automated coding standards tests

WP Notice is following [WordPress Code Standards](https://codex.wordpress.org/WordPress_Coding_Standards). 
The validation is done by [PHP Code Sniffer](https://github.com/squizlabs/PHP_CodeSniffer) Along with [Code Sniffer WordPress extension](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards).
In order to use it:

1. Install PHP Code Sniffer.
2. Install Code Sniffer WordPress extension.
3. Install Grunt modules by typing `npm install`.
4. Run `grunt phpcs` and observe results.

