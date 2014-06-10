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

?>




<div class="wrap">
    <?php print $message; ?>
	<h2><?php print $header; ?></h2>
<form id="wp_notice_form" class='form-horizontal' method="post">
    <h3><?php print $fieldset_header; ?></h3>
    <p><?php _e('Please fill in the notice text and the proper conditions that need to be fulfilled in order to make the notice to appear', 'wp-notice' ); ?></p>
    <p><?php _e('You can combine several conditions and create several notices', 'wp-notice' ); ?></p>

    <?php print $fieldsets; ?>
    <button class="btn btn-info" id="wp_notice_conditions_more" >+</button>
    <button class="btn btn-info" id="wp_notice_conditions_less" style="display: none">-</button>
    <button class="btn btn-primary" id="wp_notice_conditions_submit" ><?php _e('Submit', 'wp-notice' ); ?></button>
</form>


</div>
