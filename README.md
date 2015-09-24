[![Build Status](https://travis-ci.org/barzik/wp-notice.svg)](https://travis-ci.org/barzik/wp-notice)

# WP Notice

WP Notice plugin enable every admin to post animated announcement or messages on top of posts based on tags, categories or date.
You can assign several messages for the several posts. There are several custom designs based on BootStrap styles.
Along with the styles, you can also add to every message one of hundreds icons based on Font Awesome repository and
also choose animation type, duration and number of repetition.

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

# Generate auto prefix for animation

Make the changed in source_css and then run `grunt postcss` to output the CSS to public.css.