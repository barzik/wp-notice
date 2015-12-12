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

if ( ! defined( 'ABSPATH' ) ) { die; // Exit if accessed directly.
}

/**
 * Class WP_notice_Admin
 */
final class WP_notice_Admin
{

	/**
	 * The instance.
	 *
	 * @var null
	 */
	protected static $instance = null;
	/**
	 * Options variable.
	 *
	 * @var array
	 */
	protected static $option = array();
	/**
	 * Awsome fonts array.
	 *
	 * @var array
	 */
	protected $fonts = array();
	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * The messages styles array.
	 *
	 * @var array
	 */
	private $styles = array(
		'wp-notice-regular' => 'Regular style',
		'wp-notice-success' => 'Success',
		'wp-notice-info' => 'Info',
		'wp-notice-warning' => 'Warning',
		'wp-notice-danger' => 'Danger',
	);

	/**
	 * The animation type
	 *
	 * @var array
	 */
	private $animation_types = array(
		'pulse',
		'rubberBand',
		'jello',
		'flash',
		'bounce',
		'shake',
		'swing',
		'tada',
		'wobble',
		'flip',
	);

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {
		require( 'fonts.php' );
		$this->fonts = return_font_array();

		$plugin = WP_notice::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( realpath( dirname( __FILE__ ) ) ) . $this->plugin_slug . '.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );
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
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    WP_notice_Admin    A single instance of this class.
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null === self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since     1.0.0
	 */
	public function enqueue_admin_styles() {
		if ( ! isset( $_GET['page'] ) || 'wp-notice' !== sanitize_text_field( wp_unslash( $_GET['page'] ) ) ) { // Input var okay.
			return;
		}
		wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), WP_notice::VERSION );
		wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'public/assets/css/public.css', 'wp-notice/public/' ), array(), WP_notice::VERSION );
		wp_enqueue_style( $this->plugin_slug . 'fonts-awsome-plugin-styles', plugins_url( 'public/assets/css/font-awesome.min.css', 'wp-notice/public/' ), array(), WP_notice::VERSION );
		wp_enqueue_style( 'jquery-ui-css', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css' );

	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since     1.0.0
	 */
	public function enqueue_admin_scripts() {
		if ( ! isset( $_GET['page'] ) || 'wp-notice' !== sanitize_text_field( wp_unslash( $_GET['page'] ) ) ) { // Input var okay.
			return;
		}
		wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery' ), WP_notice::VERSION );
		wp_enqueue_script( 'jquery-ui-datepicker' );

		global $wp_locale;
		$aryArgs = array(
			'closeText'         => __( 'Done', $this->plugin_slug ),
			'currentText'       => __( 'Today', $this->plugin_slug ),
			'monthNames'        => $this->strip_array_indices( $wp_locale->month ),
			'monthNamesShort'   => $this->strip_array_indices( $wp_locale->month_abbrev ),
			'monthStatus'       => __( 'Show a different month', $this->plugin_slug ),
			'dayNames'          => $this->strip_array_indices( $wp_locale->weekday ),
			'dayNamesShort'     => $this->strip_array_indices( $wp_locale->weekday_abbrev ),
			'dayNamesMin'       => $this->strip_array_indices( $wp_locale->weekday_initial ),
			// Get the start of week from WP general setting.
			'firstDay'          => get_option( 'start_of_week' ),
			// Is Right to left language? default is false.
			'isRTL'             => $wp_locale->is_rtl(),
		);

		// Pass the localized array to the queued JS.
		wp_localize_script( $this->plugin_slug . '-admin-script', 'objectL10n', $aryArgs );
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {
		/*
		 * Add a settings page for this plugin to the Settings menu.
		 *
		 */
		$this->plugin_screen_hook_suffix = add_options_page(
			__( 'WP Notice Setting page', $this->plugin_slug ),
			__( 'WP Notice', $this->plugin_slug ),
			'manage_options',
			$this->plugin_slug,
			array( $this, 'display_plugin_admin_page' )
		);

	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page() {
		global $current_user;
		if ( ! user_can( $current_user, 'manage_options' ) ) {
			return;
		}
		$header = esc_html( get_admin_page_title() );
		$fieldset_header = __( 'Insert the notice and the conditions', $this->plugin_slug );
		// @codingStandardsIgnoreStart
		// It I skip Coding Standard here because of "Detected usage of a non-validated input variable" error.
		$nonce_key = sanitize_text_field( wp_unslash( $_POST['wp_notice'] ) ); // Input var okay.
		// @codingStandardsIgnoreEnd
		if ( wp_verify_nonce( $nonce_key, 'submit_notice' ) ) {
			$wp_notice_options = $this->prepare_wp_notice_post();
			$this->set_wp_notice_settings( $wp_notice_options );
			unset( $_POST ); // Input var okay.
		} else {
			$wp_notice_options = $this->get_wp_notice_settings();
		}
		$fieldsets = $this->get_all_fieldsets( $wp_notice_options );

		include_once( 'views/admin.php' );
	}

	/**
	 * Take the $_POST and prepare it to something that we can work with
	 * @return array
	 */
	private function prepare_wp_notice_post() {

		// @codingStandardsIgnoreStart
		// I skip Coding Standard here because of "Detected usage of a non-validated input variable" error.
		if ( isset (  $_POST['wp_notice'] ) ) {
			$nonce_key = sanitize_text_field( wp_unslash( $_POST['wp_notice'] ) ); // Input var okay.
		} else {
			$nonce_key = false;
		}
		// @codingStandardsIgnoreEnd
		if ( false === wp_verify_nonce( $nonce_key, 'submit_notice' ) ) {
			return array();
		}
		$wp_notice_options_raw = $_POST; // Input var okay.
		if ( isset( $wp_notice_options_raw['delete_all'] ) && 1 === $wp_notice_options_raw['delete_all'] && ! isset( $wp_notice_options_raw['wp_notice_text'] ) ) {
			return array();
		}

		$wp_notice_options = array();
		$wp_notice_options_raw_count = count( $wp_notice_options_raw );
		for ( $i = 0; $i < $wp_notice_options_raw_count; $i++ ) {
			if ( empty( $wp_notice_options_raw['wp_notice_text'][ $i ] ) ) {
				continue;
			}
			$wp_notice_options[ $i ]['wp_notice_text'] = balanceTags( wp_unslash( $wp_notice_options_raw['wp_notice_text'][ $i ] ), true );
			if ( isset( $wp_notice_options_raw['style'][ $i ] ) ) {
				$wp_notice_options[ $i ]['style'] = sanitize_text_field( $wp_notice_options_raw['style'][ $i ][0] );
			} else {
				$wp_notice_options[ $i ]['style'] = 'wp-notice-regular';
			}
			if ( isset( $wp_notice_options_raw['font'][ $i ] ) ) {
				$wp_notice_options[ $i ]['font'] = sanitize_text_field( $wp_notice_options_raw['font'][ $i ][0] );
			} else {
				$wp_notice_options[ $i ]['font'] = 'none';
			}

			if ( isset( $wp_notice_options_raw['cat'][ $i ] ) ) {
				$wp_notice_options[ $i ]['cat'] = array_map( 'absint', $wp_notice_options_raw['cat'][ $i ] );
			} else {
				$wp_notice_options[ $i ]['cat'] = '';
			}
			if ( isset( $wp_notice_options_raw['tag'][ $i ] ) ) {
				$wp_notice_options[ $i ]['tag'] = array_map( 'absint', $wp_notice_options_raw['tag'][ $i ] );
			} else {
				$wp_notice_options[ $i ]['tag'] = '';
			}
			if ( isset( $wp_notice_options_raw['wp_notice_time'][ $i ] ) ) {
				$wp_notice_options[ $i ]['wp_notice_time'] = preg_replace( '([^0-9/])', '', $wp_notice_options_raw['wp_notice_time'][ $i ] );
			} else {
				$wp_notice_options[ $i ]['wp_notice_time'] = '';
			}
			if ( isset( $wp_notice_options_raw['animation'][ $i ] ) && is_array( $wp_notice_options_raw['animation'][ $i ] ) &&
			     isset( $wp_notice_options_raw['animation'][ $i ]['type'] )
			     && isset( $wp_notice_options_raw['animation'][ $i ]['duration'] )
			     && isset( $wp_notice_options_raw['animation'][ $i ]['repeat'] ) ) {
				$wp_notice_options[ $i ]['animation']['type'] = sanitize_text_field( $wp_notice_options_raw['animation'][ $i ]['type'] );
				$wp_notice_options[ $i ]['animation']['duration'] = absint( $wp_notice_options_raw['animation'][ $i ]['duration'] );
				$wp_notice_options[ $i ]['animation']['repeat'] = intval( $wp_notice_options_raw['animation'][ $i ]['repeat'] );
			} else {
				$wp_notice_options_raw['animation'][ $i ]['type'] = 'none';
				$wp_notice_options_raw['animation'][ $i ]['duration'] = '';
				$wp_notice_options_raw['animation'][ $i ]['repeat'] = '';
			}
			if ( isset( $wp_notice_options_raw['position'][ $i ] ) ) {
				$wp_notice_options[ $i ]['position'] = sanitize_text_field( $wp_notice_options_raw['position'][ $i ] );
			} else {
				$wp_notice_options[ $i ]['position'] = 'before';
			}
		}
		return $wp_notice_options;
	}

	/**
	 * Get the settings from the DB.
	 *
	 * @return mixed
	 */
	public static function get_wp_notice_settings() {

		$plugin = WP_notice::get_instance();
		$option  = $plugin->get_plugin_option_name();
		return maybe_unserialize( get_option( $option, array() ) );
	}

	/**
	 *
	 * Set the settings to the DB.
	 *
	 * @param array $wp_notice_settings settings array.
	 *
	 * @return array
	 */
	private function set_wp_notice_settings( $wp_notice_settings = array() ) {
		$plugin = WP_notice::get_instance();
		$option  = $plugin->get_plugin_option_name();
		if ( empty( $wp_notice_settings ) || ! is_array( $wp_notice_settings ) ) {
			delete_option( $option );
		}
		/* Some validation */
		foreach ( $wp_notice_settings as $i => $fieldset ) {
			// No associative here.
			if ( ! is_numeric( $i ) ) {
				unset( $wp_notice_settings[ $i ] );
			}

			// Removing notices without text.
			if ( empty( $wp_notice_settings[ $i ]['wp_notice_text'] ) ) {
				unset( $wp_notice_settings[ $i ] );
			}
			// No un-numeric tags.
			if ( ! empty( $wp_notice_settings[ $i ]['tag'] ) ) {
				foreach ( $wp_notice_settings[ $i ]['tag'] as $key => $tag ) {
					if ( ! is_numeric( $tag ) ) {
						unset( $wp_notice_settings[ $i ]['tag'][ $key ] );
					}
				}
			}
			// No un-numeric categories.
			if ( ! empty( $wp_notice_settings[ $i ]['cat'] ) ) {
				foreach ( $wp_notice_settings[ $i ]['cat'] as $key => $cat ) {
					if ( ! is_numeric( $cat ) ) {
						unset( $wp_notice_settings[ $i ]['cat'][ $key ] );
					}
				}
			}
		}
		$new_value = maybe_serialize( $wp_notice_settings );
		$result = update_option( $option, $new_value );
		if ( $result ) {
			return array( 'status' => 200, 'text' => __( 'WP Notice Settings Updated', $this->plugin_slug ) );
		} else {
			return array( 'status' => 500, 'text' => __( 'WP Notice Error! You did change the form, right? please try again.', $this->plugin_slug ) );
		}
	}

	/**
	 *
	 * Creating the fieldset array from wp_notice options.
	 *
	 * @param array $wp_notice_options  The options array.
	 * @return string
	 */
	private function get_all_fieldsets( $wp_notice_options ) {
		$html = '';
		if ( empty( $wp_notice_options ) ) {
			$html .= $this->build_fieldset();
			return $html;
		}

		foreach ( $wp_notice_options as $key => $wp_notice_option ) {
			$html .= $this->build_fieldset(
				$key, $wp_notice_option['tag'], $wp_notice_option['cat'],
				$wp_notice_option['wp_notice_time'], $wp_notice_option['wp_notice_text'], $wp_notice_option['style'],
				$wp_notice_option['font'], $wp_notice_option['animation'], $wp_notice_option['position']
			);
		}
		return $html;
	}

	/**
	 *
	 * Generating the category list for the fieldset in the admin options menu.
	 *
	 * @param int   $number The serial ID of the category.
	 * @param array $selected_category  Selected categories array to be marked as selected.
	 * @return string
	 */
	private function generate_category_list( $number = 0, $selected_category = array() ) {
		if ( empty( $selected_category ) || 0 === $selected_category || '0' === $selected_category[0] ) {
			$all = 'selected="selected"';
		} else {
			$all = '';
		}
		$category_list = '';
		if ( $categories = get_categories( array( 'orderby' => 'name' ) ) ) {
			$category_list .= "<label for='cat_$number'>".__( 'Show in all posts that belongs to : ', $this->plugin_slug ).'</label>';
			$category_list .= "<select id='cat_$number' name='cat[$number][]' multiple='multiple' class='wp_notice_tag'>";
			$category_list .= "<option $all value='0'>".__( 'Do not use categories', $this->plugin_slug ).'</option>';
			foreach ( $categories as $cat ) {
				if ( is_array( $selected_category ) && ! empty( $selected_category ) && in_array( $cat->term_id, $selected_category, true ) ) {
					$selected = $cat->term_id;
				} else {
					$selected = '';
				}
				$category_list .= '<option '.selected( $selected, $cat->term_id, false ).' value="'.$cat->term_id .'">'.$cat->name.'</option>';
			}
			$category_list .= '</select> ';
		}
		return $category_list;
	}

	/**
	 *
	 * Generating the tag list for the fieldset in the admin options menu.
	 *
	 * @param int   $number The serial ID of the tag.
	 * @param array $selected_tag Selected tags array to be marked as selected.
	 * @return string
	 */
	private function generate_tag_list( $number = 0, $selected_tag = array() ) {
		$all_selected = '';
		if ( empty( $selected_tag ) || 0 === $selected_tag || '0' === $selected_tag[0] ) {
			$all_selected = 'selected="selected"';
		}
		$tag_list = '';
		if ( $tags = get_tags( array( 'orderby' => 'name' ) ) ) {
			$tag_list .= "<label for='tag_{$number}'>" . __( 'Show in all posts that belongs to : ', $this->plugin_slug ) . '</label>';
			$tag_list .= "<select id='tag_{$number}' name='tag[{$number}][]' multiple='multiple' class='wp_notice_tag'>";
			$tag_list .= "<option {$all_selected} value='0'>" . __( 'Do not use tags', $this->plugin_slug ) . '</option>';
			foreach ( $tags as $tag ) {
				$selected = '';
				if ( is_array( $selected_tag ) && ! empty( $selected_tag ) && in_array( $tag->term_id, $selected_tag, true ) ) {
					$selected = $tag->term_id;
				}
				$tag_list .= '<option ' . selected( $selected, $tag->term_id, false ) . ' value="' . $tag->term_id . '">' . $tag->name . '</option>';
			}
			$tag_list .= '</select> ';
		}

		return $tag_list;
	}

	/**
	 *
	 * Generating the style selector list for the fieldset in the admin options menu
	 *
	 * @param int    $number   The serial ID of the fieldset.
	 * @param string $selected_style The string of the selected style.
	 * @return string
	 */
	private function generate_style_list( $number = 0, $selected_style = 'wp-notice-regular' ) {
		$style_list = '';
		$style_list .= "<label for='style_{$number}'>" . __( 'Select the style of the notice : ', $this->plugin_slug ) . '</label>';
		$style_list .= "<select id='style_{$number}' name='style[{$number}][]' class='wp_notice_style'>";
		foreach ( $this->styles as $style_value => $style_name ) {
			$style_list .= '<option ' . selected( $selected_style, $style_value, false ) . ' value="' . $style_value . '">' . $style_name . '</option>';
		}
		$style_list .= '</select> ';

		return $style_list;
	}
	/**
	 *
	 * Generating the font list for the fieldset in the admin options menu
	 *
	 * @param int    $number The serial ID of the fieldset.
	 * @param string $selected_font The font name of the fontwAwsome.
	 * @return string
	 */
	private function generate_fonts_list( $number = 0, $selected_font = 'none' ) {

		$font_list = '';
		$font_list .= "<label for='font_{$number}'>" . __( 'Select icon for the notice : ', $this->plugin_slug ) . '</label>';
		$font_list .= "<select id='font_{$number}' name='font[{$number}][]' class='wp_notice_font'>";
		$font_list .= '<option ' . selected( $selected_font, 'none', false ) . " value='none'>" . __( 'Do not use font', $this->plugin_slug ) . '</option>';
		foreach ( $this->fonts as $font ) {
			$font_list .= '<option ' . selected( $selected_font, $font, false ) . ' value="' . $font . '">' . $font . '</option>';
		}
		$font_list .= '</select> ';

		return $font_list;
	}

	/**
	 *
	 * Generating the position list for the fieldset in the admin options menu
	 *
	 * @param int    $number The serial ID of the fieldset.
	 * @param string $selected_position The position for the notice.
	 * @return string
	 */
	private function generate_position( $number = 0, $selected_position = 'befote' ) {

		$position_list = '';
		$position_list .= "<label for='position_{$number}'>" . __( 'Select position for the notice : ', $this->plugin_slug ) . '</label>';
		$position_list .= "<select id='position_{$number}' name='position[{$number}]' class='wp_notice_position'>";
		$position_list .= '<option ' . selected( $selected_position, 'before', false ) . ' value="before">'. __( 'Before', $this->plugin_slug ) . '</option>';
		$position_list .= '<option ' . selected( $selected_position, 'after', false ) . ' value="after">'. __( 'After', $this->plugin_slug ) . '</option>';
		$position_list .= '<option ' . selected( $selected_position, 'both', false ) . ' value="both">'. __( 'Both', $this->plugin_slug ) . '</option>';
		$position_list .= '</select> ';

		return $position_list;
	}

	/**
	 *
	 * Generating the animation for the fieldset in the admin options menu
	 *
	 * @param int   $number The serial ID of the fieldset.
	 * @param array $selected_animation The animation array with the information regarding the animation.
	 * @return string
	 */
	private function generate_animation( $number = 0, $selected_animation = array() ) {
		if ( ! isset( $selected_animation['type'] ) || empty( $selected_animation['type'] ) ) {
			$selected_animation['type'] = 'none';
		}
		if ( ! isset( $selected_animation['duration'] ) ) {
			$selected_animation['duration'] = '';
		}
		if ( ! isset( $selected_animation['repeat'] ) ) {
			$selected_animation['repeat'] = '';
		}

		$animation_list = '';
		$animation_list .= '<span>';
		$animation_list .= "<label for='animation_type_{$number}'>" . __( 'Select animation type : ', $this->plugin_slug ) . '</label>';
		$animation_list .= "<select id='animation_type_{$number}' name='animation[{$number}][type]' class='wp_notice_animation_type'>";
		$animation_list .= '<option ' . selected( $selected_animation['type'], 'none', false ) . " value='none'>" . __( 'None', $this->plugin_slug ) . '</option>';
		foreach ( $this->animation_types as $animation_type ) {
			$animation_list .= '<option ' . selected( $selected_animation['type'], $animation_type, false ) . ' value="' . $animation_type . '">' . $animation_type . '</option>';
		}
		$animation_list .= '</select> ';
		$animation_list .= '</span>';
		$animation_list .= '<span>';
		$animation_list .= "<label for='animation_duration_{$number}'>" . __( 'Select animation duration (seconds) : ', $this->plugin_slug ) . '</label>';
		$animation_list .= "<input type='number' min='0.1' max='999' step='0.1' class='wp_notice_animation_duration' value='{$selected_animation['duration']}' id='animation_duration_$number' name='animation[{$number}][duration]'>";
		$animation_list .= '</span>';
		$animation_list .= '<span>';
		$animation_list .= "<label for='animation_repeat_{$number}'>" . __( 'Select animation repetition, -1 for infinite : ', $this->plugin_slug ) . '</label>';
		$animation_list .= "<input type='number' min='-1' max='999' step='1' class='wp_notice_animation_repeat' value='{$selected_animation['repeat']}' id='animation_repeat_$number' name='animation[{$number}][repeat]'>";
		$animation_list .= '</span>';

		return $animation_list;
	}

	/**
	 * Create the fieldset that should appear in the admin options menu
	 *
	 * @param int    $number  The ID of the fieldset.
	 * @param array  $selected_tag  The array of the tags selected.
	 * @param array  $selected_category The array of the categories selected.
	 * @param null   $time  string that contain the date of the message.
	 * @param string $text  The text of the message.
	 * @param string $selected_style  The style string.
	 * @param string $selected_font  The font string for fontAwsome.
	 * @param array  $selected_animation  Array for fonts.
	 * @param string $selected_position - The position of the notice.
	 * @return string
	 */
	private function build_fieldset( $number = 0, $selected_tag = array(), $selected_category = array(), $time = null,
		$text = '', $selected_style = 'wp-notice-regular', $selected_font = 'none', $selected_animation = array(),
		$selected_position = 'before' ) {

			$category_list = $this->generate_category_list( $number, $selected_category );
			$tag_list = $this->generate_tag_list( $number, $selected_tag );
			$style_list = $this->generate_style_list( $number, $selected_style );
			$fonts_list = $this->generate_fonts_list( $number, $selected_font );
			$animation = $this->generate_animation( $number, $selected_animation );
			$position = $this->generate_position( $number, $selected_position );
			$text_label = __( 'The Notice', $this->plugin_slug );
			$time_label = __( 'Show in all posts that were created before:', $this->plugin_slug );
			$text_place_holder = __( 'Insert the text of the notice here. It can be HTML or text string', $this->plugin_slug );
			$time_place_holder = __( 'DD/MM/YYYY', $this->plugin_slug );
			$fieldset = <<<EOD
<fieldset class="wp_notice" rel="$number">
        <div class="form-group">
            <label for="wp_notice_text_$number">$text_label</label>
            <textarea placeholder="$text_place_holder" id="wp_notice_text_$number" name="wp_notice_text[$number]" class="form-control wp_notice_text">$text</textarea>
        </div>
        <div class="wp_notice_conditions form-group">
            $category_list
            $tag_list
            <label for="wp_notice_time_$number">$time_label</label>
            <input placeholder="$time_place_holder" class="wp_notice_time" value="$time" id="wp_notice_time_$number" name="wp_notice_time[$number]">
        </div>
        <div class="wp_notice_style form-group">
            $style_list
            $fonts_list
        </div>
        <div class="wp_notice_animation form-group">
						$animation
        </div>
        <div class="wp_notice_position form-group">
						$position
        </div>
        <div class="wp_notice_mock_example">
            <div class="wp_notice_message" id="wp_notice_message-$number"></div>
        </div>
</fieldset>

EOD;

			return $fieldset;
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @param string $links  The action links.
	 * @return array
	 */
	public function add_action_links( $links ) {
		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>',
			),
			$links
		);
	}

	/**
	 * Format array for the datepicker
	 *
	 * WordPress stores the locale information in an array with a alphanumeric index, and
	 * the datepicker wants a numerical index. This function replaces the index with a number
	 *
	 * @param array $array_to_strip  array with alphanumeric index.
	 * @return array
	 */
	private function strip_array_indices( $array_to_strip ) {

		$new_array = array();
		foreach ( $array_to_strip as $obj_array_item ) {
			$new_array[] = $obj_array_item;
		}

		return $new_array;
	}
}
