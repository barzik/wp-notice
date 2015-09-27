<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   Plugin_Name
 * @author    Your Name <email@example.com>
 * @license   GPL-2.0+
 * @link      http://example.com
 * @copyright 2014 Your Name or Company Name
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;    // Exit if accessed directly.
}
?>

<div class="wrap">
	<h2><?php print esc_html( $header ); ?></h2>
<form id="wp_notice_form" class='form-horizontal' method="post">
    <?php
    // @codingStandardsIgnoreStart
    // This variable contain nonce That should appear there. It should not be escaped.
    print wp_nonce_field('submit_notice', 'wp_notice');
    // @codingStandardsIgnoreEnd
	?>
    <h3><?php print esc_html( $fieldset_header ); ?></h3>
    <p><?php esc_html_e( 'Please fill in the notice text and the proper conditions that need to be fulfilled in order to make the notice to appear', 'wp-notice' ); ?></p>
    <p><?php esc_html_e( 'You can combine several conditions and create several notices', 'wp-notice' ); ?></p>

    <?php
    // @codingStandardsIgnoreStart
    // This variable contain HTML That should appear there. It should not be escaped.
    print $fieldsets;
    // @codingStandardsIgnoreEnd
	?>
    <button class="btn btn-info" id="wp_notice_conditions_more" >+</button>
    <button class="btn btn-info" id="wp_notice_conditions_less" style="display: none">-</button>
    <button class="btn btn-primary" id="wp_notice_conditions_submit" ><?php esc_html_e( 'Submit', 'wp-notice' ); ?></button>
</form>

<div><small>Font Awesome by Dave Gandy - <a href="http://fontawesome.io">http://fontawesome.io</a></small></div>
</div>
