<?php
/**
 * WP_notice.
 *
 * @package   WP_notice
 * @author    Ran Bar-Zik <ran@bar-zik.com>
 * @license   GPL-2.0+
 * @link      http://internet-israel.com
 * @copyright 2014 Ran Bar-Zik
 */

if ( ! defined( 'ABSPATH' ) ) die; // Exit if accessed directly

final class WP_notice {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const VERSION = '1.0.0';

	/**
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'wp-notice';
    protected $plugin_settings_information = 'wp_notice_settings_information';

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	private static $instance = null;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		// Load public-facing style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );

		/* Define custom functionality.
		 * Refer To http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
		 */
		add_action( '@TODO', array( $this, 'action_method_name' ) );
		add_filter( 'the_content', array( $this, 'wp_notice_add_message' ) );

	}

	/**
	 * Throw error on object clone
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @since 1.0.1
	 * @return void
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wp-notice' ), '1.0.1' );
	}

	/**
	 * Disable unserializing of the class
	 *
	 * @since 1.0.1
	 * @return void
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wp-notice' ), '1.0.1' );
	}

    /**
     * Return the plugin slug.
     *
     * @since 1.0.0
     *
     * @return string Plugin slug variable.
     */
    public function get_plugin_slug() {
        return $this->plugin_slug;
    }

    /**
     * Return the plugin option name
     *
     * @since 1.0.0
     *
     * @return string Plugin slug variable.
     */
    public function get_plugin_option_name() {
        return $this->plugin_settings_information;
    }


    /**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    WP_notice    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       activated on an individual blog.
	 */
	public static function activate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide  ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_activate();

					restore_current_blog();
				}

			} else {
				self::single_activate();
			}

		} else {
			self::single_activate();
		}

	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Deactivate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_deactivate();

					restore_current_blog();

				}

			} else {
				self::single_deactivate();
			}

		} else {
			self::single_deactivate();
		}

	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since    1.0.0
	 *
	 * @param    int    $blog_id    ID of the new blog.
	 */
	public function activate_new_site( $blog_id ) {

		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();

	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 * @since    1.0.0
	 *
	 * @return   array|false    The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {

		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

		return $wpdb->get_col( $sql );

	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since    1.0.0
	 */
	private static function single_activate() {
        add_option('wp_notice_information', '');
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 */
	private static function single_deactivate() {
        delete_option('wp_notice_information');
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );
		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );

	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'assets/css/public.css', __FILE__ ), array(), self::VERSION );
	}




	/**
	 * NOTE:  Filters are points of execution in which WordPress modifies data
	 *        before saving it or sending it to the browser.
	 *
	 *        Filters: http://codex.wordpress.org/Plugin_API#Filters
	 *        Reference:  http://codex.wordpress.org/Plugin_API/Filter_Reference
	 *
	 * @since    1.0.0
	 */
	public function wp_notice_add_message($content) {
        $messages_html = '';
        $messages_array = $this->wp_notice_options_decider();
        foreach ($messages_array as $key=> $message) {
            $messages_html .= $this->create_message_block($message, $key);
        }
        $content = $messages_html.$content;
        return $content;
	}


    /**
     *
     * Create single message block - HTML
     *
     * @param $text
     * @param $id
     * @return string
     */

    public function create_message_block($text, $id) {
        $message_html = <<<EOD
<div class="wp_notice_message" id="wp_notice_message-$id">$text</div>
EOD;
        return $message_html;
    }

    /**
     * decide which message will appear here.
     *
     * @return array
     */

    private function wp_notice_options_decider() {
        $option  = $this->get_plugin_option_name();
        $options = maybe_unserialize(get_option($option, array()));
        $messages = array();
        $current_categories = $this->get_the_category_id();
        $current_tags =  $this->get_the_tags_id();
        foreach($options as $key => $sort_option) {
            $found = false;
            foreach($sort_option['tag'] as $tag) {
                if(in_array($tag, $current_tags)) {
                    $messages[] = $sort_option['wp_notice_text'];
                    $found = true;
                    break;
                }
            }
            if($found == true) {
                continue;
            }
            foreach($sort_option['cat'] as $cat) {
                if(in_array($cat, $current_categories)) {
                    $messages[] = $sort_option['wp_notice_text'];
                    $found = true;
                    break;
                }
            }
            if($found == true) {
                continue;
            }

            if(!empty($sort_option['wp_notice_time'])) {
				$dt = DateTime::createFromFormat("d/m/Y", $options[$key]['wp_notice_time']);
				$ts = $dt->getTimestamp();
				if( $ts > get_the_time('U') ) {
					$messages[] = $sort_option['wp_notice_text'];
				}
			}

        }
        return $messages;

    }

    /**
     *
     * Get an array of current category ID
     *
     * @param bool $post
     * @return array
     */


    private function get_the_category_id($post = false) {
        $cats = array();
        $cats_raw = get_the_category($post);
        if(!$cats_raw) {
            return array();
        }
        foreach($cats_raw as $key => $cat) {
            $cats[] = $cat->term_id;
        }
        return $cats;
    }

    /**
     *
     * Get an array of current tags ID
     *
     * @param bool $post
     * @return array
     */

    private function get_the_tags_id($post = false) {
        $tags = array();
        $tags_raw = get_the_tags($post);
        if(!$tags_raw) {
            return array();
        }
        foreach($tags_raw as $key => $tag) {
            $tags[] = $tag->term_id;
        }
        return $tags;
    }

}
