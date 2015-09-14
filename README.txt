=== WordPress Notice ===
Contributors: barzik
Tags: notification, posts, obsolete, notice, messages, notice based on category,
Requires at least: 3.5.1
Tested up to: 4.3
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

WP Notice Plugin enable admin to put announcements in the beginning of posts based on date, categories or tags.

== Description ==

Wp Notice plugin enable every admin to post announcement (HTML or just text) on top of every WordPress post.
The admin can choose specific categories, tags or post date in order to point the correct message to the posts he wants.
Ideal for obsolete\deprecation messages for tutorials or technical posts.

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

= 1.0.2 =
* Adding wordpress PHPUnit integration.

= 1.0.1 =
* Coding conventions, minor bug fix made by Yakir Sitbon

= 1.0 =
* Initial version


== Translation ==

This plugin is multi-language plugin and can be translated using standard po-mo methods.

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

