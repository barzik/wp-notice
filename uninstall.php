<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package   WP_notice
 * @author    Ran Bar-Zik <ran@bar-zik.com>
 * @license   GPL-2.0+
 * @link      http://internet-israel.com
 * @copyright 2015 Ran Bar-Zik
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

delete_option( 'wp_notice_settings_information' );
