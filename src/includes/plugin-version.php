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
 * // Add to the main plugin file.
 * $main_file = __FILE__;
 * $plugin_version = include 'vendor/wpconstr/plugin-version/plugin-version.php';
 *
 * // PHP or WordPress version requirement not met.
 * if (false === $plugin_version){
 *    return;
 * }
 * // you can now use $plugin_version eg. to set a constant.
 * ```
 *
 * @package    WPConstr_Plugin_Version
 * @copyright  © 2025 by WPConstructor
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
		//phpcs:ignore
		$plugin_file_content = file_get_contents( $main_file, false, null, 0, 4096 );
		if ( preg_match( '/^Version:\s*(.+)$/mi', $plugin_file_content, $matches ) ) {
			$plugin_version = trim( $matches[1] );
		}
		$php_requires_ok = true;
		if ( preg_match( '/^Requires\s*PHP:\s*(.+)$/mi', $plugin_file_content, $matches ) ) {
			$php_requires = trim( $matches[1] );
			if ( version_compare( PHP_VERSION, $php_requires, '<' ) ) {
				$php_requires_ok = false;
			}
		}
		$wp_requires_ok = true;
		if ( preg_match( '/^Requires\s*at\s*least:\s*(.+)$/mi', $plugin_file_content, $matches ) ) {
			global $wp_version;
			$wp_requires = trim( $matches[1] );
			if ( version_compare( $wp_version, $wp_requires, '<' ) ) {
				$wp_requires_ok = false;
			}
		}
		if ( false === $wp_requires_ok || false === $php_requires_ok ) {
			$plugin_name = 'Unknown Plugin Name';
			if ( preg_match( '/^Plugin\s*Name:\s*(.+)$/mi', $plugin_file_content, $matches ) ) {
				$plugin_name = trim( $matches[1] );
			}
			if ( true === $wp_requires_ok ) {
				$msg = $plugin_name . ' requires PHP ' . $php_requires . ' or higher.';
			} elseif ( true === $php_requires_ok ) {
				$msg = $plugin_name . ' requires WordPress ' . $wp_requires . ' or higher.';
			} else {
				$msg = $plugin_name . ' requires PHP ' . $php_requires . ' or higher and WordPress ' . $wp_requires . ' or higher.';
			}
			if ( is_admin() && current_user_can( 'install_plugins' ) ) {
				add_action(
					'admin_notices',
					function () {
						echo '<div class="notice notice-error"><p>';
						echo esc_html( $msg );
						echo '</p></div>';
					}
				);
			}
			return false;
		}
	} elseif ( defined( 'WP_DEBUG' ) && WP_DEBUG && defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
		//phpcs:ignore
		error_log( 'WPConstructor Plugin Version: The main file "' . $main_file . '" is not readable.' );
	}
} elseif ( defined( 'WP_DEBUG' ) && WP_DEBUG && defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
	//phpcs:ignore
	error_log( 'WPConstructor Plugin Version: The main file "' . $main_file . '" does not exist.' );
}

// Fallback if the version is not found.
if ( ! $plugin_version ) {
	$plugin_version = '1.0.0'; // Default version.
	if ( defined( 'WP_DEBUG' ) && WP_DEBUG && defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
		//phpcs:ignore
		error_log( 'WPConstructor Plugin Version: The plugin version of "' . $main_file . '" not found! Falled back to "1.0.0".' );
	}
}

return $plugin_version;
