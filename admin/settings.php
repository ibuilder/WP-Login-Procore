<?php
/**
 * Admin Options Page
 *
 * @package     Procore API
 * @subpackage  Admin/Settings
 * @copyright   Copyright (c) 2019 StarGroup
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Options Page
 *
 * Renders the options page contents.
 *
 * @since 1.0
 * @return void
 */
function procore_options_page() {
	?>
	<div class="wrap">
		<h1><?php _e( 'Procore API Settings', 'Procore API' ); ?></h1>
		<p class="description">Insert procore access token here.  To obtain one register at the <a href="https://developers.procore.com/">Procore Developer Portal</a></p>

		<form method="post" action="options.php">
			<?php
			wp_nonce_field( 'update-options' );
            settings_fields( 'procore_api_settings' );
            do_settings_sections('Procore API');
			submit_button( null, 'primary', 'submit', true, null );
			?>
		</form>

	</div>
	<?php
}

/**
 * General settings section
 */

 /**
 * Field input setting
 */
function procore_api_field_input( $args ) {
	$settings = (array) get_option( $args['option_name' ] );
	$class = isset( $args['class'] ) ? $args['class'] : 'regular-text';
	$type = isset( $args['type'] ) ? $args['type'] : 'text';
	$min = isset( $args['min'] ) && is_numeric( $args['min'] ) ? intval( $args['min'] ) : null;
	$max = isset( $args['max'] ) && is_numeric( $args['max'] ) ? intval( $args['max'] ) : null;
	$step = isset( $args['step'] ) && is_numeric( $args['step'] ) ? floatval( $args['step'] ) : null;
	$readonly = isset( $args['readonly'] ) && $args['readonly'] ? ' readonly' : '';
	$placeholder = isset( $args['placeholder'] ) ? $args['placeholder'] : '';
	$required = isset( $args['required'] ) && $args['required'] === true ? 'required' : '';
	?>
	<input class="<?php echo $class; ?>" type="<?php echo $type; ?>" name="<?php echo $args['option_name']; ?>[<?php echo $args['setting_id']; ?>]"
			value="<?php echo esc_attr( $settings[$args['setting_id']] ); ?>" <?php if ( $min !== null ) { echo ' min="' . $min . '"'; } ?>
			<?php if ( $max !== null) { echo ' max="' . $max . '"'; } echo $readonly; ?>
			<?php if ( $step !== null ) { echo ' step="' . $step . '"'; } ?>
			placeholder="<?php echo $placeholder; ?>" <?php echo $required; ?> />
	<?php
	if ( isset( $args['label'] ) ) { ?>
		<label><?php echo $args['label']; ?></label>
	<?php }
}
