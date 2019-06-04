<?php
/**
 * Settings
 */

add_shortcode('procore-form', 'show_procore_form');
function show_procore_form(){
    global $wp;

    $general_settings = (array) get_option( 'procore_api_settings' );

	$auth_url = 'https://login.procore.com/oauth/authorize';
	$access_token = ''; //'51836e36-9655-4abf-8da5-2ded95b05fb6';
	$client_id = $general_settings['client_id']; //'53fd022d2c92310f12350a4660914405895bfb33770a52dfa9e99f433f350f55'; //'bc1d2d378d6c0bc0aa2b5d0f172e0152c0868e2439e04adf12e46691ce04d65f';
	$client_secret = $general_settings['client_secret'];//'86373967e6b314b3a4ad2b8c9f033a6f1491460ccee4bf15603de5c05510c0fc';
	$current_url = site_url(add_query_arg(array(),$wp->request));
	$headless_url = 'urn:ietf:wg:oauth:2.0:oob';
    return '<a href="'.$auth_url.'?response_type=code&client_id='.$client_id.'&redirect_uri='. $current_url .'" class="btn btn-block btn-procore" title="Log in using your Procore account"><span class="text-divider-pad">|</span>Connect to Procore</a>';
}

/**
 * Tell WP we use a setting - and where.
 */
add_action( 'admin_init', 'procore_register_setting' );
function procore_register_setting()
{
    add_settings_section(
        'procore_api_id',
        'Procore Settings',
        'procore_description',
        'general'
    );

    // Register a callback
    register_setting(
        'general',
        'procore-token',
        'trim'
    );
    // Register the field for the "avatars" section.
    add_settings_field(
        'ads',
        'Procore Access Token',
        'procore_access_settings',
        'general',
        'procore_api_id',
        array ( 'label_for' => 'procore_api_id' )
    );
}

/**
 * Print the text before our field.
 */
function procore_description()
{
    echo '<p class="description">Insert procore access token here.  To obtain one '.do_shortcode('[procore-form]').'</p>';
}

/**
 * Show our field.
 *
 * @param array $args
 */

 if (!function_exists('procore_access_settings')) {
    function procore_access_settings( $args )
    {
        $data = esc_attr( get_option( 'procore-token', '' ) );
    
        printf(
            '<input type="text" name="procore-token" value="%1$s" id="%2$s" />',
            $data,
            $args['label_for']
        );
    }
 }
