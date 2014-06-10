<?php
/**
 * The WordPress Plugin Boilerplate.
 *
 * A foundation off of which to build well-documented WordPress plugins that
 * also follow WordPress Coding Standards and PHP best practices.
 *
 * @package   WP Notice
 * @author    Ran Bar-Zik <ran@bar-zik.com>
 * @license   GPL-2.0+
 * @link      http://internet-israel.com
 * @copyright 2014 Ran Bar-Zik
 *
 * @wordpress-plugin
 * Plugin Name:       WP Notice
 * Plugin URI:        http://internet-israel.com
 * Description:       Plugin to display notice and messages on posts
 * Version:           1.0.0
 * Author:            Ran Bar-Zik <ran@bar-zik.com>
 * Author URI:        http://internet-israel.com
 * Text Domain:       wp-notice
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/barzik/wp-notice
 * WordPress-Plugin-Boilerplate: v2.6.1
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

require_once( plugin_dir_path( __FILE__ ) . "public/class-wp-notice.php" );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 */
register_activation_hook( __FILE__, array( 'WP_notice', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'WP_notice', 'deactivate' ) );

add_action( 'plugins_loaded', array( 'WP_notice', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-wp-notice-admin.php' );
	add_action( 'plugins_loaded', array( 'WP_notice_Admin', 'get_instance' ) );

}
