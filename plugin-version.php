<?php
/**
 * WPConstructor Plugin Version
 *
 * This file retrieves the version of a WordPress plugin from its main file.
 *
 * @package    WPConstr_Plugin_Version
 * @version    0.1.0
 * @author     WPConstructor <https://wpconstructor.com/contact>
 * @license    MIT (https://opensource.org/licenses/MIT)
 * @link       https://wpconstructor.com/code/wpconstr-plugin-version
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die();
}

// Gets plugin version.
if ( ! function_exists( 'get_plugin_data' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}
$plugin_data    = \get_plugin_data( __FILE__ );
$plugin_version = $plugin_data['Version'];
return $plugin_version;
