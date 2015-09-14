<?php

/**
 * Tests to test that that testing framework is testing tests. Meta, huh?
 *
 * @package wordpress-plugins-tests
 */
class WP_Test_WPnotice_Plugin_Tests extends WP_UnitTestCase {

	/**
	 * Ensure that the plugin has been installed and activated.
	 */
	function test_plugin_activated() {
		$this->assertTrue( is_plugin_active( 'wp-notice/wp-notice.php' ) );
	}

    /**
     * testing the slug name
     */

    function test_plugin_shortname() {
        $plugin = WP_notice::get_instance();
        $result = $plugin->get_plugin_slug();
        $this->assertEquals('wp-notice', $result);
    }

    /**
     * CSS file is being asserted
     */

    function test_css_is_there() {
        $plugin = WP_notice::get_instance();
        $public_css_file = $plugin->get_plugin_slug().'-plugin-styles';
        $this->assertEquals('wp-notice-plugin-styles', $public_css_file);
    }



}
