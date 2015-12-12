<?php
/**
 * WP Notice - enable admin to posts messages based on tags, categories or date
 *
 * @package   WP Notice
 * @author    Ran Bar-Zik <ran@bar-zik.com>
 * @license   GPL-2.0+
 * @link      http://internet-israel.com
 * @copyright 2015 Ran Bar-Zik
 *
 * @wordpress-plugin
 * Plugin Name:       WP Notice
 * Plugin URI:        http://internet-israel.com
 * Description:       WP Notice Plugin enables admin to put custom animated announcements in the beginning of posts based on date, categories or tags.
 * Version:           1.3.4
 * Author:            Ran Bar-Zik <ran@bar-zik.com>
 * Author URI:        http://internet-israel.com
 * Text Domain:       wp-notice
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/barzik/wp-notice
 * WordPress-Plugin-Boilerplate: v2.6.1
 */

if ( ! defined( 'ABSPATH' ) ) { die; // Exit if accessed directly.
}

/*
 ----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------
 */

require_once( plugin_dir_path( __FILE__ ) . 'public/class-wp-notice.php' );

add_action( 'plugins_loaded', array( 'WP_notice', 'get_instance' ) );

/*
 ----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------
 */

if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-wp-notice-admin.php' );
	add_action( 'plugins_loaded', array( 'WP_notice_Admin', 'get_instance' ) );

}
