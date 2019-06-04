<?php
/**
 * Register Settings
 *
 * @package     Procore API
 * @subpackage  Admin/Settings
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Get Settings
 *
 * Retrieves all plugin settings
 *
 * @since 0.1
 * @return array ALLIOC settings
 */
function procore_api_settings() {

	$settings = get_option( 'procore_api_settings' );

	if( empty( $settings ) ) {

		// Update old settings with new single option

		$settings = is_array( get_option( 'procore_api_settings' ) )    ? get_option( 'procore_api_settings' )    : array();


		update_option( 'allioc_settings', $settings );
	}

	return apply_filters( 'procore_api_settings', $settings );
}

/**
 * Reister settings
 */
function procore_api_register_settings() {

	register_setting( 'procore_api_settings', 'procore_api_settings', '' );

	add_settings_section( 'procore_api_section', null, null, 'Procore API' );

	$setting_fields = array(		
			'client_id' => array(
					'title' 	=> __( 'Client ID', 'Procore API' ),
					'callback' 	=> 'procore_api_field_input',
					'page' 		=> 'Procore API',
					'section' 	=> 'procore_api_section',
					'args' => array(
							'option_name' 	=> 'procore_api_settings',
							'setting_id' 	=> 'client_id',
							'label' 		=> __( '', 'Procore API' ),
							'placeholder'	=> __( 'Enter Client ID...', 'Procore API' ),
							'required'		=> true
					)
			),

			'client_secret' => array(
				'title' 	=> __( 'Client Secret', 'Procore API' ),
				'callback' 	=> 'procore_api_field_input',
				'page' 		=> 'Procore API',
				'section' 	=> 'procore_api_section',
				'args' => array(
						'option_name' 	=> 'procore_api_settings',
						'setting_id' 	=> 'client_secret',
						'label' 		=> __( '', 'Procore API' ),
						'placeholder'	=> __( 'Enter Client Secret...', 'Procore API' ),
						'required'		=> true
				)
		),

	);

	foreach ( $setting_fields as $setting_id => $setting_data ) {
		// $id, $title, $callback, $page, $section, $args
		add_settings_field( $setting_id, $setting_data['title'], $setting_data['callback'], $setting_data['page'], $setting_data['section'], $setting_data['args'] );
	}
}


/**
 * Set default settings if not set
 */
function procore_api_default_settings() {

	$general_settings = (array) get_option( 'procore_api_settings' );

	$general_settings = array_merge( array(
			'client_id'		=> '',
			'client_secret' => '',
	), $general_settings );

	update_option( 'procore_api_settings', $general_settings );

}

if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
	add_action( 'admin_init', 'procore_api_default_settings', 10, 0 );
	add_action( 'admin_init', 'procore_api_register_settings' );
}
