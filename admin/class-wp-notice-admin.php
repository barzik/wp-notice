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

final class WP_notice_Admin {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;
    protected static $option = array();

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

    private $styles = array('wp-notice-regular' => 'Regular style',
        'wp-notice-success' => 'Success',
        'wp-notice-info' => 'Info',
        'wp-notice-warning' => 'Warning',
        'wp-notice-danger' => 'Danger'
        );

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		$plugin = WP_notice::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();

        //create one time message on plugin activation
        add_action('admin_notices', array( $this, 'plugin_activation_message' ) );

        // Add the options page and menu item.
        add_action('admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Load admin style sheet and JavaScript.
		add_action('admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action('admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( realpath( dirname( __FILE__ ) ) ) . $this->plugin_slug . '.php' );
		add_filter('plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );
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
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    WP_notice_Admin    A single instance of this class.
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

    /**
     * When plugin is being activated - show message
     */

    public function plugin_activation_message() {
//        $message  = '<a href="'.admin_url( 'options-general.php?page=' . $this->plugin_slug ).'">'.__( 'Please set up WP Notice settings.', 'WP-NOTICE' ).'</a>';
//        $html = "<div id='message' class='updated'><p>$message</p></div>";
//        print $html;
    }


	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {

		if ( 'wp-notice' === $_GET['page'] ) {
			wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), WP_notice::VERSION );
            wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'public/assets/css/public.css', 'wp-notice/public/' ), array(), WP_notice::VERSION );
            wp_enqueue_style('jquery-ui-css', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
        }

	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {

        if ( 'wp-notice' === $_GET['page'] ) {
            wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery' ), WP_notice::VERSION );
            wp_enqueue_script('jquery-ui-datepicker');

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
                // get the start of week from WP general setting
                'firstDay'          => get_option( 'start_of_week' ),
                // is Right to left language? default is false
                'isRTL'             => $wp_locale->is_rtl,
            );

            // Pass the localized array to the enqueued JS
            wp_localize_script( $this->plugin_slug . '-admin-script', 'objectL10n', $aryArgs );

        }

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
        $header = esc_html(get_admin_page_title());
        $fieldset_header = __('Insert the notice and the conditions', $this->plugin_slug );

        if($_POST) {
            $wp_notice_options = $this->prepare_wp_notice_post();
            $result = $this->set_wp_notice_settings($wp_notice_options);
            unset($_POST);
            if($result['status'] == '200') {
                $message = "<div class='alert alert-success'>{$result['text']}</div>";
            } else {
                $message = "<div class='alert alert-danger'>{$result['text']}</div>";
            }


        } else {
            $wp_notice_options = $this->get_wp_notice_settings();
        }

        $fieldsets = $this->get_all_fieldsets($wp_notice_options);

		include_once( 'views/admin.php' );
	}

    /**
     * Take the $_POST and prepare it to something that we can work with
     * @return array
     */

    private function prepare_wp_notice_post() {
        global $_POST;
        $wp_notice_options_raw = $_POST;
        if( isset( $_POST['delete_all'] ) && $_POST['delete_all'] == 1 && !isset($_POST['wp_notice_text'] ) ) {
            return array();
        }


        $wp_notice_options = array();
        for ($i = 0; $i < count($wp_notice_options_raw); $i++) {
            if(empty($_POST['wp_notice_text'][$i])) {
                continue;
            }
            $wp_notice_options[$i]['wp_notice_text'] = $_POST['wp_notice_text'][$i];
            if( isset( $_POST['style'][$i] ) ) {
                $wp_notice_options[$i]['style'] = $_POST['style'][$i][0];
            } else {
                $wp_notice_options[$i]['style'] = 'wp-notice-regular';
            }
            if(isset($_POST['cat'][$i])) {
                $wp_notice_options[$i]['cat'] = $_POST['cat'][$i];
            } else {
                $wp_notice_options[$i]['cat'] = '';
            }
            if(isset($_POST['tag'][$i])) {
                $wp_notice_options[$i]['tag'] = $_POST['tag'][$i];
            } else {
                $wp_notice_options[$i]['tag'] = '';
            }
            if(isset($_POST['wp_notice_time'][$i])) {
                $wp_notice_options[$i]['wp_notice_time'] = $_POST['wp_notice_time'][$i];
            } else {
                $wp_notice_options[$i]['wp_notice_time'] = '';

            }

        }
        return $wp_notice_options;
    }

    /**
     * get the settings from the DB
     *
     * @return mixed
     */

    public static function get_wp_notice_settings() {
        $plugin = WP_notice::get_instance();
        $option  = $plugin->get_plugin_option_name();
        return maybe_unserialize(get_option($option, array()));
    }

    /**
     *
     * set the settings to the DB
     *
     * @param array $wp_notice_settings
     * 
     * @return array
     */

    private function set_wp_notice_settings($wp_notice_settings = array()) {
        $plugin = WP_notice::get_instance();
        $option  = $plugin->get_plugin_option_name();
        if(empty($wp_notice_settings) || !is_array($wp_notice_settings)) {
            delete_option($option);
        }
        /* Some validation */
        foreach ($wp_notice_settings as $i => $fieldset) {
            //no asspciative here
            if(!is_numeric($i)) {
                unset($wp_notice_settings[$i]);
            }

            //removing notices without text
            if(empty($wp_notice_settings[$i]['wp_notice_text'])) {
                unset($wp_notice_settings[$i]);
            }
            // no un-numeric tags
            if(!empty($wp_notice_settings[$i]['tag'])) {
                foreach($wp_notice_settings[$i]['tag'] as $key => $tag) {
                    if(!is_numeric($tag)) {
                        unset($wp_notice_settings[$i]['tag'][$key]);
                    }
                }
            }
            // no un-numeric categories
            if(!empty($wp_notice_settings[$i]['cat'])) {
                foreach($wp_notice_settings[$i]['cat'] as $key => $cat) {
                    if(!is_numeric($cat)) {
                        unset($wp_notice_settings[$i]['cat'][$key]);
                    }
                }
            }
            //no strange dates here
            $test_date = $wp_notice_settings[$i]['wp_notice_time'];

            //no strange styles
            $wp_notice_settings[$i]['style'] = $wp_notice_settings[$i]['style'];

        }
        $new_value = maybe_serialize($wp_notice_settings);
        $result = update_option( $option, $new_value );
	    if ( $result ) {
		    return array( 'status' => '200', 'text' => __( 'WP Notice Settings Updated', $this->plugin_slug ) );
	    } else {
		    return array( 'status' => '500', 'text' => __( 'WP Notice Error! You did change the form, right? please try again.', $this->plugin_slug ) );
	    }
    }

    /**
     *
     * Creating the fieldset array from wp_notice options
     *
     * @param $wp_notice_options
     * @return string
     */
    private function get_all_fieldsets($wp_notice_options) {
        $html = '';
        if(empty($wp_notice_options)) {
            $html .= $this->build_fieldset();
            return $html;
        }

        foreach ($wp_notice_options as $key => $wp_notice_option) {
            $html .= $this->build_fieldset($key, $wp_notice_option['tag'], $wp_notice_option['cat'], $wp_notice_option['wp_notice_time'],$wp_notice_option['wp_notice_text'],$wp_notice_option['style']);
        }
       return $html;
    }

    /**
     *
     * Generating the category list for the fieldset in the admin options menu
     *
     * @param int $number
     * @param array $selected_category
     * @return string
     */
    private function generate_category_list($number = 0, $selected_category = array()) {
        if(empty($selected_category) || 0 === $selected_category || '0' === $selected_category[0] ) {
            $all = 'selected="selected"';
        }
        $category_list = '';
        if ($categories = get_categories( array('orderby' => 'name') )) {
            $category_list .= "<label for='cat_$number'>".__( 'Show in all posts that belongs to : ', $this->plugin_slug )."</label>";
            $category_list .= "<select id='cat_$number' name='cat[$number][]' multiple='multiple' class='wp_notice_tag'>";
            $category_list .="<option $all value='0'>".__('Do not use categories', $this->plugin_slug )."</option>";
            foreach ($categories as $cat) {
                if(is_array($selected_category) && !empty($selected_category) && in_array($cat->term_id, $selected_category)) {
                    $selected = $cat->term_id;
                }
                $category_list .= '<option '.selected($selected,$cat->term_id, false).' value="'.$cat->term_id .'">'.$cat->name.'</option>';
            }
            $category_list .= '</select> ';
        }
        return $category_list;
    }

    /**
     *
     * Generating the tag list for the fieldset in the admin options menu
     *
     * @param int $number
     * @param array $selected_tag
     * @return string
     */
	private function generate_tag_list( $number = 0, $selected_tag = array() ) {
		$all_selected = '';
		if ( empty( $selected_tag ) || 0 === $selected_tag || '0' === $selected_tag[0]) {
			$all_selected = 'selected="selected"';
		}
		$tag_list = '';
		if ( $tags = get_tags( array( 'orderby' => 'name' ) ) ) {
			$tag_list .= "<label for='tag_{$number}'>" . __( 'Show in all posts that belongs to : ', $this->plugin_slug ) . "</label>";
			$tag_list .= "<select id='tag_{$number}' name='tag[{$number}][]' multiple='multiple' class='wp_notice_tag'>";
			$tag_list .= "<option {$all_selected} value='0'>" . __( 'Do not use tags', $this->plugin_slug ) . "</option>";
			foreach ( $tags as $tag ) {
				$selected = '';
				if ( is_array( $selected_tag ) && ! empty( $selected_tag ) && in_array( $tag->term_id, $selected_tag ) ) {
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
     * Generating the tag list for the fieldset in the admin options menu
     *
     * @param int $number
     * @param array $selected_tag
     * @return string
     */
    private function generate_style_list( $number = 0, $selected_style = 'wp-notice-regular' ) {

        $style_list = '';
        $style_list .= "<label for='style_{$number}'>" . __( 'Select the style of the notice : ', $this->plugin_slug ) . "</label>";
        $style_list .= "<select id='style_{$number}' name='style[{$number}][]' class='wp_notice_style'>";
        foreach ($this->styles as $style_value => $style_name ) {
            $style_list .= '<option ' . selected( $selected_style, $style_value, false ) . ' value="' . $style_value . '">' . $style_name . '</option>';
        }
        $style_list .= '</select> ';


        return $style_list;
    }


    /**
     * Create the fieldset that should appear in the admin options menu
     *
     * @param int $number
     * @param array $selected_tag
     * @param array $selected_category
     * @param null $time
     * @param string $text
     * @return string
     */


    private function build_fieldset($number = 0, $selected_tag = array(), $selected_category = array(), $time = null, $text = '', $selected_style='wp-notice-regular') {
        $category_list = $this->generate_category_list($number, $selected_category);
        $tag_list = $this->generate_tag_list($number ,$selected_tag);
        $style_list = $this->generate_style_list( $number, $selected_style );
        $text_label = __('The Notice', $this->plugin_slug );
        $time_label = __('Show in all posts that were created before:', $this->plugin_slug );
        $text_place_holder = __('Insert the text of the notice here. It can be HTML or text string', $this->plugin_slug );
        $time_place_holder = __('DD/MM/YYYY', $this->plugin_slug );
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
	 * @since    1.0.0
	 */
	public function add_action_links( $links ) {
		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>'
			),
			$links
		);
	}

    /**
     * Format array for the datepicker
     *
     * WordPress stores the locale information in an array with a alphanumeric index, and
     * the datepicker wants a numerical index. This function replaces the index with a number
     */
	private function strip_array_indices( $array_to_strip ) {
		$new_array = array();
		foreach ( $array_to_strip as $obj_array_item ) {
			$new_array[] = $obj_array_item;
		}

		return $new_array;
	}


}
