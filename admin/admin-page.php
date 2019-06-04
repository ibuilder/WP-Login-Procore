<?php
/**
 * Admin Pages
 *
 * @package     Procore API
 * @subpackage  Admin/Pages
 * @copyright   Copyright (c) 2019, StarGroup
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Creates an options page for plugin settings and links it to a global variable
 *
 * @since 0.1
 * @return void
 */
function proccore_login() {
	global $proccore_settings_page;

	$proccore_settings_page      = 	add_options_page( __( 'Procore API', 'Procore API' ), __( 'Procore API', 'Procore API' ), 'manage_options', 'Procore API', 'procore_options_page');
	
}
add_action( 'admin_menu', 'proccore_login', 10 );