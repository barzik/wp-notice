=== WordPress Notice ===
Contributors: barzik
Tags: notification, posts, obsolete, notice, messages, notice based on category,
Requires at least: 3.7
Tested up to: 4.3
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

WP Notice Plugin enables admin to put custom announcements in the beginning of posts based on date, categories or tags.

== Description ==

WP Notice plugin enable every admin to post announcement or messages  on top of posts based on tags, categories or date.
You can assign several messages for the several posts. There are several custom designs based on BootStrap styles.
Along with the styles, you can also add to every message one of hundreds icons based on Font Awesome repository.

Ideal for technical sites that need to post deprecated notice on old posts and any other sites that need to show
messages to users based on categories, tags or date.

WP Notice has fully automated testing environment based on WordPress PHPUnit and Travis CI and it is 100% compatible
to WordPress coding standards.
GitHub: https://github.com/barzik/wp-notice


== Installation ==

= Using The WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Search for 'wp-notice'
3. Click 'Install Now'
4. Activate the plugin on the Plugin dashboard

= Uploading in WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Navigate to the 'Upload' area
3. Select `wp-notice.zip` from your computer
4. Click 'Install Now'
5. Activate the plugin in the Plugin dashboard

= Using FTP =

1. Download `wp-notice.zip`
2. Extract the `wp-notice` directory to your computer
3. Upload the `wp-notice` directory to the `/wp-content/plugins/` directory
4. Activate the plugin in the Plugin dashboard


== Screenshots ==

1. The wp-notice plugin admin settings page.
2. The wp-notice plugin success message after setting up the first notice.
3. wp-notice example.

== Changelog ==

= 1.2.1 =
* Remove possible XSS issues.
* Complete Linting
* Removing deprecated code/
* Removing options in uninstall
* Fixing all code standards for WP-Notice classes
* Adding several PHPUnit tests, 88% coverage!

= 1.2.0 =
* Adding preview for messages
* Adding icons to messages
* Fixing security breach in admin interface

= 1.1.0 =
* Fixed issues in date.
* Allowing custom styling
* Admin interface UI improvements
* Better test coverage - more than 50%
* Adding Travis CI integration

= 1.0.2 =
* Adding WordPress PHPUnit integration.

= 1.0.1 =
* Coding conventions, minor bug fix made by Yakir Sitbon

= 1.0 =
* Initial version

== Upgrade Notice ==

= 1.2.1 =
Security issue fix.

= 1.2.0 =
A lot of features and improvements in this release. along with important security fix.

= 1.1.0 =
Updating a lot of issues in the admin UI, messages appearance and security testing.

== Automated testing ==

WP Tutorial maker can be tested by using PHPUnit with the official WordPress testing environment.

1. Install WordPress develop and PHPUnit. You can follow [these instructions](https://make.wordpress.org/core/handbook/testing/automated-testing/)
2. define local variable WP_TESTS_DIR with the location of WordPress develop phpunit folder.
for example, put `export WP_TESTS_DIR="/var/www/html/wordpress-develop/tests/phpunit"` in .bashrc (Linux)
3. Go to the plugin main folder and run `phpunit`.
4. Tests coverage report is being printed in HTML page to ./log/CodeCoverage.

== Translations ==

* English - default, always included
* Hebrew

