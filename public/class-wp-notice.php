<?php
/**
 * WP_notice.
 *
 * @package   WP_notice
 * @author    Ran Bar-Zik <ran@bar-zik.com>
 * @license   GPL-2.0+
 * @link      http://internet-israel.com
 * @copyright 2015 Ran Bar-Zik
 */

if ( ! defined( 'ABSPATH' ) ) {
	die; // Exit if accessed directly.
}

/**
 * Class WP_notice
 */
final class WP_notice
{

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

	/**
	 * The slug
	 * @var string
	 */
	protected $plugin_slug = 'wp-notice';

	/**
	 * The option name
	 * @var string
	 */
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
		// Load plugin text domain.
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Activate plugin when new blog is added.
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		// Load public-facing style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );

		/*
		Define custom functionality.
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
		// Cloning instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, esc_html( __( 'Cheatin&#8217; huh?' , 'wp-notice' ) ), '1.0.1' );
	}

	/**
	 * Disable unserializing of the class
	 *
	 * @since 1.0.1
	 * @return void
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, esc_html( __( 'Cheatin&#8217; huh?' , 'wp-notice' ) ), '1.0.1' );
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
		if ( null === self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
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
		load_plugin_textdomain( $domain, false, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );

	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'assets/css/public.css', __FILE__ ), array(), self::VERSION );
		wp_enqueue_style( $this->plugin_slug . '-fonts-awsome-plugin-styles', plugins_url( 'assets/css/font-awesome.min.css', __FILE__ ), array(), self::VERSION );
	}
	/**
	 * Add message to post
	 *
	 * @param string $content -The post content.
	 *
	 * @return string
	 */
	public function wp_notice_add_message( $content ) {
		$messages_html = '';
		$messages_array = $this->wp_notice_options_decider();
		foreach ( $messages_array as $key => $message ) {
			$messages_html .= $this->create_message_block( $message['text'], $key, $message['style'], $message['font'], $message['animation'] );
			switch ( $message['position'] ) {
				case 'after':
					$content = $content.$messages_html;
					break;
				case 'both':
					$content = $messages_html.$content.$messages_html;
					break;
				default:
					$content = $messages_html.$content;
					break;
			}
		}

		return $content;
	}


	/**
	 *
	 * WP notice create message block
	 *
	 * @param string $text - The text of the message.
	 * @param int    $id - The ID of the message, 0-n.
	 * @param string $style - The style name.
	 * @param string $font - The fontawsome name.
	 * @param array  $animation - The animation details.
	 *
	 * @return string
	 */
	public function create_message_block( $text, $id, $style = 'wp-notice-regular', $font = 'none', $animation = array() ) {
		if ( 'none' !== $font ) {
			$fa_included = 'fa_included';
		} else {
			$fa_included = '';
		}

		if ( ! empty( $animation ) && ! empty( $animation['type'] ) &&
		    ! empty( $animation['duration'] ) && ! empty( $animation['repeat'] ) &&
				'none' !== $animation['type'] ) {
			if ( -1 === $animation['repeat'] ) {
				$animation['repeat'] = 'infinite';
			}

			$animation['duration'] = $animation['duration'].'s';

			$animation_string = "{$animation['type']} {$animation['duration']} {$animation['repeat']}";
			$animation_style = "-webkit-animation: $animation_string; animation: $animation_string;";
		} else {
			$animation_style = '';
		}

		$message_html = <<<EOD
<div style="$animation_style" class="wp_notice_message $style $fa_included" id="wp_notice_message-$id"><i class="fa $font fa-4x"></i>$text</div>
EOD;
		return $message_html;
	}

	/**
	 * Decide which message will appear here.
	 *
	 * @return array
	 */
	private function wp_notice_options_decider() {
		$option  = $this->get_plugin_option_name();
		$options = maybe_unserialize( get_option( $option, array() ) );
		$messages = array();
		$current_categories = $this->get_the_category_id();
		$current_tags = $this->get_the_tags_id();
		foreach ( $options as $key => $sort_option ) {
			$found = false;
			if ( isset( $sort_option['tag'] ) && ! empty( $sort_option['tag'] ) ) {
				foreach ( $sort_option['tag'] as $tag ) {
					if ( in_array( $tag, $current_tags, true ) ) {
						$temp_message = array(
							'text' => $sort_option['wp_notice_text'],
							'style' => $sort_option['style'],
							'font' => $sort_option['font'],
							'animation' => $sort_option['animation'],
						);
						if ( isset( $sort_option['position'] ) ) {
							$temp_message['position'] = $sort_option['position'];
						} else {
							$temp_message['position'] = 'before';
						}
						$messages[] = $temp_message;
						$found = true;
						break;
					}
				}
			}

			if ( true === $found ) {
				continue;
			}

			if ( isset( $sort_option['cat'] ) && ! empty( $sort_option['cat'] ) ) {
				foreach ( $sort_option['cat'] as $cat ) {
					if ( in_array( $cat, $current_categories, true ) ) {
						$temp_message = array(
							'text' => $sort_option['wp_notice_text'],
							'style' => $sort_option['style'],
							'font' => $sort_option['font'],
							'animation' => $sort_option['animation'],
						);
						if ( isset( $sort_option['position'] ) ) {
							$temp_message['position'] = $sort_option['position'];
						} else {
							$temp_message['position'] = 'before';
						}
						$messages[] = $temp_message;
						$found = true;
						break;
					}
				}
			}

			if ( true === $found ) {
				continue;
			}

			if ( ! empty( $sort_option['wp_notice_time'] ) ) {
				$dt = DateTime::createFromFormat( 'd/m/Y', $options[ $key ]['wp_notice_time'] );
				$ts = $dt->getTimestamp();
				if ( $ts > get_the_time( 'U' ) ) {
					$temp_message = array(
						'text' => $sort_option['wp_notice_text'],
						'style' => $sort_option['style'],
						'font' => $sort_option['font'],
						'animation' => $sort_option['animation'],
					);
					if ( isset( $sort_option['position'] ) ) {
						$temp_message['position'] = $sort_option['position'];
					} else {
						$temp_message['position'] = 'before';
					}
					$messages[] = $temp_message;
				}
			}
		}

		return $messages;
	}

	/**
	 *
	 * Get an array of current category ID
	 *
	 * @param bool $post  the post object.
	 * @return array
	 */
	private function get_the_category_id( $post = false ) {
		$cats = array();
		$cats_raw = get_the_category( $post );
		if ( ! $cats_raw ) {
			return array();
		}
		foreach ( $cats_raw as $key => $cat ) {
			$cats[] = $cat->term_id;
		}
		return $cats;
	}

	/**
	 *
	 * Get an array of current tags ID
	 *
	 * @param bool $post  post object.
	 * @return array
	 */
	private function get_the_tags_id( $post = false ) {
		$tags = array();
		$tags_raw = get_the_tags( $post );
		if ( ! $tags_raw ) {
			return array();
		}
		foreach ( $tags_raw as $key => $tag ) {
			$tags[] = $tag->term_id;
		}
		return $tags;
	}
}
