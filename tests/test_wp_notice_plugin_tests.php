<?php
/**
 * Tests to test that that testing framework is testing tests. Meta, huh?
 *
 * @package wordpress-plugins-tests
 */

class WP_Test_WPnotice_Plugin_Tests extends WP_UnitTestCase
{

	protected $plugin;
	protected $plugin_admin;


	function __construct() {

			$this->plugin = WP_notice::get_instance();
			$this->plugin_admin = WP_notice_Admin::get_instance();

	}

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
			$this->assertEquals( 'wp-notice', $result );
	}

		/**
		 * CSS file is being asserted
		 */

	function test_css_is_there() {
			$plugin = WP_notice::get_instance();
			$public_css_file = $plugin->get_plugin_slug().'-plugin-styles';
			$this->assertEquals( 'wp-notice-plugin-styles', $public_css_file );
	}

	function test_wp_notice_has_action() {

			$tag = 'admin_menu';
			$this->plugin_admin->get_instance();

			global $wp_filter;
			$menu_is_there = false;
			$wp_filter_admin_menu = $wp_filter['admin_menu'][10];
		foreach ( $wp_filter_admin_menu as $item ) {
			if ( 'add_plugin_admin_menu' == $item['function'][1] ) {
					$menu_is_there = true;
			}
		}

			$this->assertTrue( $menu_is_there );

	}


	function test_plugin_load_textdomain() {
			add_filter( 'locale', array( $this, '_set_locale_to_hebrew' ) );

			$this->plugin->load_plugin_textdomain();
			$this->assertTrue( is_textdomain_loaded( 'wp-notice' ) );

	}

	public function _set_locale_to_hebrew() {
			return 'he_IL';
	}

	function test_plugin_public_css() {

		$plugin = WP_notice::get_instance();
		global $wp_styles;
		set_current_screen( '/' );
		$ver = $plugin::VERSION;

		$result = $plugin->enqueue_styles();
		$this->assertArrayHasKey( 'wp-notice-plugin-styles', $wp_styles->registered );
		$this->assertEquals( $wp_styles->registered['wp-notice-plugin-styles']->ver, $ver );
		$this->assertArrayHasKey( 'wp-notice-fonts-awsome-plugin-styles', $wp_styles->registered );
		$this->assertEquals( $wp_styles->registered['wp-notice-plugin-styles']->ver, $ver );
	}

	function test_plugin_admin_css() {
			$user = new WP_User( $this->factory->user->create( array( 'role' => 'administrator' ) ) );
			wp_set_current_user( $user->ID );
			$plugin = WP_notice::get_instance();
			global $wp_styles;

			set_current_screen( '/options-general.php?page=wp-notice' );

			$ver = $plugin::VERSION;

			$_GET['page'] = 'wp-notice';

			$result = $this->plugin_admin->enqueue_admin_styles();
			$this->assertArrayHasKey( 'wp-notice-admin-styles', $wp_styles->registered );
			$this->assertEquals( $wp_styles->registered['wp-notice-admin-styles']->ver, $ver );
	}


	function test_plugin_admin_js() {
			$user = new WP_User( $this->factory->user->create( array( 'role' => 'administrator' ) ) );
			wp_set_current_user( $user->ID );

			$plugin = WP_notice::get_instance();
			set_current_screen( '/options-general.php?page=wp-notice' );
			$_GET['page'] = 'wp-notice';
			global $wp_locale;
			$wp_locale = new WP_Locale;
			$wp_locale->is_rtl = false;
			$result = $this->plugin_admin->enqueue_admin_scripts();
			$ver = $plugin::VERSION;
			global $wp_scripts;
			$this->assertArrayHasKey( 'wp-notice-admin-script', $wp_scripts->registered );
			$this->assertEquals( $wp_scripts->registered['wp-notice-admin-script']->ver, $ver );
			$this->assertObjectHasAttribute( 'extra', $wp_scripts->registered['wp-notice-admin-script'] );
	}

	function test_plugin_create_options_for_category() {
			$user = new WP_User( $this->factory->user->create( array( 'role' => 'administrator' ) ) );
			wp_set_current_user( $user->ID );

			// fetching options, validate it is empty
			$wp_notice_settings = get_option( 'wp_notice_settings_information', array() );
			$this->assertEmpty( $wp_notice_settings );

			// create category
			$cat_id = $this->generate_category();
			$i = 0;
			// generate $_POST data
			$this->create_valid_post_data( $i, $cat_id );

			// checking the admin page with $_POST - should insert it to the options
			$this->plugin_admin->display_plugin_admin_page();

			// fetching options, not it is not empty
			$wp_notice_settings = get_option( 'wp_notice_settings_information', array() );

			$this->assertNotEmpty( $wp_notice_settings );

			$options_data = unserialize( $wp_notice_settings );
			$this->assertInternalType( 'array', $options_data[ $i ] );
			$this->assertInternalType( 'array', $options_data[ $i ]['cat'] );
			$this->assertEquals( $options_data[ $i ]['cat'][0], $cat_id );
			$this->assertEquals( $options_data[ $i ]['wp_notice_text'], 'This is notice #'.$i.' message' );
			$this->assertEquals( $options_data[ $i ]['style'], 'wp-notice-regular' );
			$this->assertEquals( $options_data[ $i ]['font'], 'none' );

			// now test it with real post that related to this category
			// create one post
			$post_id = $this->factory->post->create( array( 'post_type' => 'post', 'post_status' => 'publish', 'post_title' => 'POST1', 'post_date' => date( 'Y-m-d H:i:s', time() ) ) );
			// assign it to category
			wp_set_post_categories( $post_id, $cat_id );
			// go to this post
			$this->go_to( get_permalink( $post_id ) );
			// fetch the content
			$post_content_with_notice = get_echo( 'the_content' );

			$this->assertRegExp( '/This is notice #'.$i.' message/', $post_content_with_notice );
			$this->assertRegExp( '/<div style=\"\" class=\"wp_notice_message wp-notice-regular \" id=\"wp_notice_message-'.$i.'\">/', $post_content_with_notice );
			$this->assertRegExp( '/<i class=\"fa none fa\-4x\"><\/i>/', $post_content_with_notice );

			// make sure that post that has no relation to this category IS NOT having the notice
			// create non related post
			$unrelated_post_id = $this->factory->post->create( array( 'post_type' => 'post', 'post_status' => 'publish', 'post_title' => 'POST1', 'post_date' => date( 'Y-m-d H:i:s', time() ) ) );
			// create non related category
			$un_related_cat_id = $this->generate_category();
			// assign this post to this category
			wp_set_post_categories( $unrelated_post_id, $un_related_cat_id );
			// go to this post
			$this->go_to( get_permalink( $unrelated_post_id ) );
			$post_content_without_notice = get_echo( 'the_content' );
			$this->assertNotRegExp( '/This is notice #'.$i.' message/', $post_content_without_notice );

	}

	function test_plugin_create_options_for_tag() {
			$user = new WP_User( $this->factory->user->create( array( 'role' => 'administrator' ) ) );
			wp_set_current_user( $user->ID );

			// fetching options, validate it is empty
			$wp_notice_settings = get_option( 'wp_notice_settings_information', array() );

			$this->assertEmpty( $wp_notice_settings );

			// create category
			$term = $this->generate_tag();
			$term_id = $term['term_id'];
			$term_name = $term['name'];

			$i = 0;
			// generate $_POST data
			$this->create_valid_post_data( $i, 0, $term_id );

			// checking the admin page with $_POST - should insert it to the options
			$this->plugin_admin->display_plugin_admin_page();

			// fetching options, not it is not empty
			$wp_notice_settings = get_option( 'wp_notice_settings_information', array() );

			$this->assertNotEmpty( $wp_notice_settings );

			$options_data = unserialize( $wp_notice_settings );
			$this->assertInternalType( 'array', $options_data[ $i ] );
			$this->assertInternalType( 'array', $options_data[ $i ]['tag'] );
			$this->assertEquals( $options_data[ $i ]['tag'][0], $term_id );
			$this->assertEquals( $options_data[ $i ]['wp_notice_text'], 'This is notice #'.$i.' message' );
			$this->assertEquals( $options_data[ $i ]['style'], 'wp-notice-regular' );
			$this->assertEquals( $options_data[ $i ]['font'], 'none' );

			// now test it with real post that related to this tag
			// create one post
			$post_id = $this->factory->post->create( array( 'post_type' => 'post', 'post_status' => 'publish', 'post_title' => 'POST1', 'post_date' => date( 'Y-m-d H:i:s', time() ) ) );
			// assign it to tag
			wp_set_post_tags( $post_id, $term_name );

			// go to this post
			$this->go_to( get_permalink( $post_id ) );
			// fetch the content
			$post_content_with_notice = get_echo( 'the_content' );

			$this->assertRegExp( '/This is notice #'.$i.' message/', $post_content_with_notice );
			$this->assertRegExp( '/<div style=\"\" class=\"wp_notice_message wp-notice-regular \" id=\"wp_notice_message-'.$i.'\">/', $post_content_with_notice );
			$this->assertRegExp( '/<i class=\"fa none fa\-4x\"><\/i>/', $post_content_with_notice );

			// make sure that post that has no relation to this category IS NOT having the notice
			// create non related post
			$unrelated_post_id = $this->factory->post->create( array( 'post_type' => 'post', 'post_status' => 'publish', 'post_title' => 'POST1', 'post_date' => date( 'Y-m-d H:i:s', time() ) ) );
			// create non related category
			$un_related_term = $this->generate_tag();
			$un_related_term_name = $un_related_term['name'];
			// assign this post to this category
			wp_set_post_categories( $unrelated_post_id, $un_related_term_name );
			// go to this post
			$this->go_to( get_permalink( $unrelated_post_id ) );
			$post_content_without_notice = get_echo( 'the_content' );
			$this->assertNotRegExp( '/This is notice #'.$i.' message/', $post_content_without_notice );

	}

	function test_plugin_create_options_for_timestamp() {
			$user = new WP_User( $this->factory->user->create( array( 'role' => 'administrator' ) ) );
			wp_set_current_user( $user->ID );
			// fetching options, validate it is empty
			$wp_notice_settings = get_option( 'wp_notice_settings_information', array() );

			$this->assertEmpty( $wp_notice_settings );
			$date[0] = time() - 5000000;
			$date[1] = time() - 2500000;
			$date[2] = time();

			$i = 0;
			// generate $_POST data
			$this->create_valid_post_data( $i, 0, 0, date( 'd/m/Y', $date[1] ) );

			// checking the admin page with $_POST - should insert it to the options
			$this->plugin_admin->display_plugin_admin_page();

			// fetching options, not it is not empty
			$wp_notice_settings = get_option( 'wp_notice_settings_information', array() );

			$this->assertNotEmpty( $wp_notice_settings );

			$options_data = unserialize( $wp_notice_settings );
			$this->assertInternalType( 'array', $options_data[ $i ] );
			$this->assertInternalType( 'string', $options_data[ $i ]['wp_notice_time'] );
			$this->assertEquals( $options_data[ $i ]['wp_notice_time'], date( 'd/m/Y', $date[1] ) );
			$this->assertEquals( $options_data[ $i ]['wp_notice_text'], 'This is notice #'.$i.' message' );
			$this->assertEquals( $options_data[ $i ]['style'], 'wp-notice-regular' );
			$this->assertEquals( $options_data[ $i ]['font'], 'none' );

			// now test it with real post that related to this tag
			// create one post, the date is older than the notice date
			$post_id = $this->factory->post->create( array( 'post_type' => 'post', 'post_status' => 'publish', 'post_title' => 'POST1', 'post_date' => date( 'Y-m-d H:i:s', $date[0] ) ) );

			// go to this post
			$this->go_to( get_permalink( $post_id ) );
			// fetch the content
			$post_content_with_notice = get_echo( 'the_content' );

			$this->assertRegExp( '/This is notice #'.$i.' message/', $post_content_with_notice );
			$this->assertRegExp( '/<div style=\"\" class=\"wp_notice_message wp-notice-regular \" id=\"wp_notice_message-'.$i.'\">/', $post_content_with_notice );
			$this->assertRegExp( '/<i class=\"fa none fa\-4x\"><\/i>/', $post_content_with_notice );

			// create non related post with date that is later than the notice date
			$unrelated_post_id = $this->factory->post->create( array( 'post_type' => 'post', 'post_status' => 'publish', 'post_title' => 'POST1', 'post_date' => date( 'Y-m-d H:i:s', $date[2] ) ) );

			// go to this post
			$this->go_to( get_permalink( $unrelated_post_id ) );
			$post_content_without_notice = get_echo( 'the_content' );
			$this->assertNotRegExp( '/This is notice #'.$i.' message/', $post_content_without_notice );

	}


	function test_plugin_create_options_for_animation() {

		//Lets create some notice with date and assign to it animation
		$user = new WP_User( $this->factory->user->create( array( 'role' => 'administrator' ) ) );
		wp_set_current_user( $user->ID );
		// fetching options, validate it is empty
		$wp_notice_settings = get_option( 'wp_notice_settings_information', array() );

		$this->assertEmpty( $wp_notice_settings );
		$date = time() - 5000000;

		$i = 0;
		// generate $_POST data
		$this->create_valid_post_data( $i, 0, 0, date( 'd/m/Y', $date ), 'wp-notice-regular', 'none', array('duration' => '4', 'repeat' => '-1', 'type' => 'wiggle') );

		// checking the admin page with $_POST - should insert it to the options
		$this->plugin_admin->display_plugin_admin_page();

		// fetching options, not it is not empty
		$wp_notice_settings = get_option( 'wp_notice_settings_information', array() );

		$this->assertNotEmpty( $wp_notice_settings );

		$options_data = unserialize( $wp_notice_settings );
		$this->assertInternalType( 'array', $options_data[ $i ] );
		$this->assertInternalType( 'string', $options_data[ $i ]['wp_notice_time'] );
		$this->assertEquals( $options_data[ $i ]['wp_notice_time'], date( 'd/m/Y', $date ) );
		$this->assertEquals( $options_data[ $i ]['wp_notice_text'], 'This is notice #'.$i.' message' );
		$this->assertEquals( $options_data[ $i ]['style'], 'wp-notice-regular' );
		$this->assertEquals( $options_data[ $i ]['font'], 'none' );
		$this->assertInternalType( 'array', $options_data[ $i ]['animation'] );
		$this->assertEquals( $options_data[ $i ]['animation']['duration'], 4 );
		$this->assertEquals( $options_data[ $i ]['animation']['repeat'], -1 );
		$this->assertEquals( $options_data[ $i ]['animation']['type'], 'wiggle' );

		// now test it with real post that related to this tag
		// create one post, the date is older than the notice date
		$post_id = $this->factory->post->create( array( 'post_type' => 'post', 'post_status' => 'publish', 'post_title' => 'POST1', 'post_date' => date( 'Y-m-d H:i:s', $date[0] ) ) );

		// go to this post
		$this->go_to( get_permalink( $post_id ) );
		// fetch the content
		$post_content_with_notice = get_echo( 'the_content' );

		$this->assertRegExp( '/This is notice #'.$i.' message/', $post_content_with_notice );
		$this->assertRegExp( '/<div style=\"-webkit-animation: wiggle 4s infinite; animation: wiggle 4s infinite;\" class=\"wp_notice_message wp-notice-regular \" id=\"wp_notice_message-'.$i.'\">/', $post_content_with_notice );
		$this->assertRegExp( '/<i class=\"fa none fa\-4x\"><\/i>/', $post_content_with_notice );


	}

	function test_plugin_delete_all_options() {
			$user = new WP_User( $this->factory->user->create( array( 'role' => 'administrator' ) ) );
			wp_set_current_user( $user->ID );

			update_option( 'wp_notice_settings_information', 'mock_sata' );

			// fetching options, not it is not empty
			$wp_notice_settings = get_option( 'wp_notice_settings_information', array() );

			$this->assertNotEmpty( $wp_notice_settings );

			// fetching options, validate it is empty
			global $_POST;
			$_POST = array();
			$_POST['wp_notice'] = wp_create_nonce( 'submit_notice' );
			$_POST['delete_all'] = 1;

			// checking the admin page with $_POST - should insert it to the options
			$this->plugin_admin->display_plugin_admin_page();

			// fetching options, not it is not empty
			$wp_notice_settings = get_option( 'wp_notice_settings_information', array() );
			$wp_notice_settings = unserialize( $wp_notice_settings );

			$this->assertEmpty( $wp_notice_settings );

	}

	public function test_get_wp_notice_settings() {
			$user = new WP_User( $this->factory->user->create( array( 'role' => 'administrator' ) ) );
			wp_set_current_user( $user->ID );

			$options_object = $this->plugin_admin->get_wp_notice_settings();

			$this->assertInternalType( 'array', $options_object );
	}

	public function test_get_fontawsome_array() {
		$fonts = return_font_array();
		$fonts_count = count( $fonts );
		$this->assertInternalType( 'array', $fonts );
		$this->assertGreaterThan(0, $fonts_count);
	}


	function create_valid_post_data( $i = 0, $cat_id = 0, $term_id = 0, $date = '', $style = 'wp-notice-regular', $font = 'none', $animation = array('duration' => '', 'repeat' => '', 'type' => 'none') ) {
			global $_POST;

			$_POST['wp_notice'] = wp_create_nonce( 'submit_notice' );

			$_POST['wp_notice_text'][ $i ] = 'This is notice #'.$i.' message';

			$_POST['cat'][ $i ] = array( $cat_id );

			$_POST['tag'][ $i ] = array( $term_id );

			$_POST['wp_notice_time'][ $i ] = $date;

			$_POST['style'][ $i ] = array( $style );

			$_POST['font'][ $i ] = array( $font );

			$_POST['animation'][ $i ] = $animation;

	}

	public function generate_category() {
			// create Test Categories and Array Representations
			$testcat_array = array(
				'slug' => rand_str(),
				'name' => rand_str(),
				'description' => rand_str(),
			);
			$testcat = $this->factory->category->create_and_get( $testcat_array );
			return $testcat->term_id;
	}

	public function generate_tag() {
			$string = rand_str();

			$term = wp_insert_term( $string, 'post_tag' );
			$term['name'] = $string;

			return $term;
	}
}
