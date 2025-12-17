<?php
/** 
 * WPConstructor Plugin Version include file.
 *
 * The WPConstructor Plugin Version code retrieves the version of a given plugin's
 * main file without triggering translation errors caused by calling get_plugin_data()
 * before the init hook. It also avoids the need to manually include plugin.php on the frontend.
 * This is particularly useful for plugins that need to access their own version number.
 *
 * It also checks if the current PHP version and WordPress version meet the requirements.
 * If not a admin notice is added in the admin area for users with the 'install_plugins' capability.
 *
 * Usage:
 * ```php
 * $main_file      = __FILE__;
 * $plugin_version = require __DIR__ . '/vendor/wpconstructor/plugin-version/dist/includes/plugin-version.php';
 *
 * // False when PHP or WordPress requirements are not satisfied.
 * if ( false === $plugin_version ) {
 *     return; // Stop execution if requirements are not met.
 * }
 * // Use the plugin version, e.g., define a constant.
 * define( 'MY_PLUGIN_VERSION', $plugin_version );
 * ```
 *
 * @package    WPConstructor_Plugin_Version
 * @copyright  (c) 2026 by WPConstructor
 * @author     WPConstructor <https://wpconstructor.com/contact>
 * @license    MIT (https://opensource.org/licenses/MIT)
 * @link       https://wpconstructor.com/code/wpconstr-plugin-version
 * @version    1.0.0 
 * @since      1.0.0 
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die();
}

$use_default = false;

// Checks if $main_file has been set valid.

if ( ! isset( $main_file ) ) {
	$use_default = true;
	if ( defined( 'WP_DEBUG' ) && WP_DEBUG && defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
		//phpcs:ignore
		error_log( 'WPConstructor Plugin Version: You must define the main file using $main_file="path/to/your/main_plugin_file.php". Using version "1.0.0".' );
	}
}

if ( ! is_string( $main_file ) ) {
	$use_default = true;
	if ( defined( 'WP_DEBUG' ) && WP_DEBUG && defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
		//phpcs:ignore
		error_log( 'WPConstructor Plugin Version: $main_file must be a string. Using version "1.0.0".' );
	}
}

if ( $use_default ) {
	return '1.0.0';
}

$plugin_version = null;

if ( file_exists( $main_file ) ) {
	if ( is_readable( $main_file ) ) {

		// Gets contents of main plugin file.
		$plugin_file_content = file_get_contents( $main_file, false, null, 0, 4096 ); //phpcs:ignore

		// Define Regex template to just replace the plugin tag name.
		$regex_template = '/^\s*\*?\s*{TAG}:\s*(.+)$/mi';

		// Extracts the plugin version.
		if ( preg_match( str_replace( '{TAG}', 'Version', $regex_template ), $plugin_file_content, $matches ) ) {
			$plugin_version = trim( $matches[1] );
		}

		// Extracts and checks required PHP version.
		$php_requires_ok = true;
		if ( preg_match( str_replace( '{TAG}', 'Requires PHP', $regex_template ), $plugin_file_content, $matches ) ) {
			$php_requires = trim( $matches[1] );
			if ( version_compare( PHP_VERSION, $php_requires, '<' ) ) {
				$php_requires_ok = false;
			}
		}

		// Extract and checks the WordPress required version.
		$wp_requires_ok = true;
		if ( preg_match( str_replace( '{TAG}', 'Requires\s*at\s*least', $regex_template ), $plugin_file_content, $matches ) ) {
			global $wp_version;
			$wp_requires = trim( $matches[1] );
			if ( version_compare( $wp_version, $wp_requires, '<' ) ) {
				$wp_requires_ok = false;
			}
		}

		// Handle not met requirements (PHP or WordPress or both).
		if ( false === $wp_requires_ok || false === $php_requires_ok ) {

			// Extract plugin name to add to message later.
			$plugin_name = 'Unknown Plugin Name';
			if ( preg_match( str_replace( '{TAG}', 'Plugin\s*Name', $regex_template ), $plugin_file_content, $matches ) ) {
				$plugin_name = trim( $matches[1] );
			}

			// Defines the message temapltes.
			if ( true === $wp_requires_ok ) {
				$msg_template = '<strong>{pluginName}</strong> plugin could not be activated. It requires <strong>PHP {phpVersion} or higher</strong>. Please update your environment to use this plugin.';
			} elseif ( true === $php_requires_ok ) {
				$msg_template = '<strong>{pluginName}</strong> plugin could not be activated. It requires <strong>WordPress {wordPressVersion} or higher</strong>. Please update your environment to use this plugin.';
			} else {
				$msg_template = '<strong>{pluginName}</strong> plugin could not be activated. It requires <strong>PHP {phpVersion} or higher</strong> and <strong>WordPress {wordPressVersion} or higher</strong>. Please update your environment to use this plugin.';
			}

			// Replace placeholders in template.
			$msg = str_replace( '{pluginName}', $plugin_name, $msg_template );
			$msg = str_replace( '{phpVersion}', $php_requires, $msg );
			$msg = str_replace( '{wordPressVersion}', $wp_requires, $msg );

			// Add the admin notice.
			add_action(
				'admin_notices',
				function () use ( $msg ) {

					// Run only in admin for administrators.
					if ( is_admin() && current_user_can( 'install_plugins' ) ) {
						echo '<div class="notice notice-error"><p>';
						echo wp_kses_post( $msg );
						echo '</p></div>';
					}
				}
			);
			return false;
		}
	} elseif ( defined( 'WP_DEBUG' ) && WP_DEBUG && defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
		// Error: Main file not readable.

		//phpcs:ignore
		error_log( 'WPConstructor Plugin Version: The main file "' . $main_file . '" is not readable.' );
	}
} elseif ( defined( 'WP_DEBUG' ) && WP_DEBUG && defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
	// Error: Main file does not exist.

	//phpcs:ignore
	error_log( 'WPConstructor Plugin Version: The main file "' . $main_file . '" does not exist.' );
}

// Fallback if the plugin version is not found.
if ( ! $plugin_version ) {
	$plugin_version = '1.0.0'; // Default version.
	if ( defined( 'WP_DEBUG' ) && WP_DEBUG && defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
		//phpcs:ignore
		error_log( 'WPConstructor Plugin Version: The plugin version of "' . $main_file . '" not found! Falled back to "1.0.0".' );
	}
}

return $plugin_version;
