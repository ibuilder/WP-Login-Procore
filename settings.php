<?php
/**
 * Settings
 */

add_shortcode('procore-form', 'show_procore_form');
function show_procore_form(){
    global $wp;

    $general_settings = (array) get_option( 'procore_api_settings' );

	$auth_url = 'https://login.procore.com/oauth/authorize';
	$access_token = '';
	$client_id = $general_settings['client_id'];
	$client_secret = $general_settings['client_secret'];
	$current_url = site_url(add_query_arg(array(),$wp->request));
	$headless_url = 'urn:ietf:wg:oauth:2.0:oob';
    return '<a href="'.$auth_url.'?response_type=code&client_id='.$client_id.'&redirect_uri='. $current_url .'" class="btn btn-block btn-procore" title="Log in using your Procore account"><span class="text-divider-pad">|</span>Connect to Procore</a>';
}