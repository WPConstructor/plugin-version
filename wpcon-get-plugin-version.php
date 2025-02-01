<?php
/** 
 * WPConstructor Get Plugin Version include file.
 *
 * The WPConstructor Get Plugin Version code retrieves the version of a given plugin's
 * main file without triggering translation errors caused by calling get_plugin_data()
 * before the init hook. It also avoids the need to manually include plugin.php on the frontend.
 * This is particularly useful for plugins that need to access their own version number.
 * Usage:
 * ```php
 * $main_file = 'path/to/your/main_plugin_file.php';
 * $plugin_version = include 'path/to/wpcon-get-plugin-version.php';
 * echo $plugin_version; // Outputs the plugin version.
 * ```
 *
 * @package    WPCon_Get_Plugin_Version
 * @copyright  (c) 2025 by WPConstructor
 * @license    GPL-3.0+ http://www.gnu.org/licenses/gpl-3.0.txt
 * @version    1.0.0 
 * @since      1.0.0 
 */

/**
 * WPConstructor Get Plugin Version is free software: you can redistribute
 * it and/or modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * WPConstructor Get Plugin Version is distributed in the hope that it will
 * be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with WPConstructor Get Plugin Version. If not, see <https://www.gnu.org/licenses/>.
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die();
}

if ( ! isset( $main_file ) ) {
	wp_die( 'WPConstructor Get Plugin Version: You must define the main file using $main_file="path/to/your/main_plugin_file.php".' );
}

if ( ! is_string( $main_file ) ) {
	wp_die( 'WPConstructor Get Plugin Version: $main_file must be a string.' );
}

$plugin_version = null;

if ( file_exists( $main_file ) ) {
	if ( is_readable( $main_file ) ) {
		//phpcs:ignore
		$plugin_file_content = file_get_contents( $main_file );
		if ( preg_match( '/^Version:\s*(.+)$/mi', $plugin_file_content, $matches ) ) {
			$plugin_version = trim( $matches[1] );
		}
	} elseif ( defined( 'WP_DEBUG' ) && WP_DEBUG && defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
		//phpcs:ignore
		error_log( 'WPConstructor Get Plugin Version: The main file "' . $main_file . '" is not readable.' );
	}
} elseif ( defined( 'WP_DEBUG' ) && WP_DEBUG && defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
	//phpcs:ignore
	error_log( 'WPConstructor Get Plugin Version: The main file "' . $main_file . '" does not exist.' );
}

// Fallback if the version is not found.
if ( ! $plugin_version ) {
	$plugin_version = '1.0.0'; // Default version.
	if ( defined( 'WP_DEBUG' ) && WP_DEBUG && defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
		//phpcs:ignore
		error_log( 'WPConstructor Get Plugin Version: The plugin version of "' . $main_file . '" not found! Falled back to "1.0.0".' );
	}
}

return $plugin_version;
