# WP Tutorial Maker

Wp Notice plugin enable every admin to post announcement (HTML or just text) on top of every WordPress post.
The admin can choose specific categories, tags or post date in order to point the correct message to the posts he wants.
Ideal for obsolete\deprecation messages for tutorials or technical posts.

# Installation and other information

For Installations, screenshots and logs, please refer to [WP tutorial maker WordPress.org page](https://wordpress.org/plugins/wp-notice/). 

# Automated testing

WP Tutorial maker can be tested by using PHPUnit with the official WordPress testing environment.

1. Install WordPress develop and PHPUnit. You can follow [those instructions](https://make.wordpress.org/core/handbook/testing/automated-testing/)
2. Define local variable WP_TESTS_DIR with the location of WordPress develop phpunit folder. for example, put
put `export WP_TESTS_DIR="/var/www/html/wordpress-develop/tests/phpunit"` in .bashrc (Linux)
3. Go to the plugin main folder and run `phpunit`.
4. Tests coverage report is being printed in HTML page to ./log/CodeCoverage.

