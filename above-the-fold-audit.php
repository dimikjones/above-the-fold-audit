<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://example.com
 * @since             1.0.0
 * @package           Above_The_Fold_Audit
 *
 * @wordpress-plugin
 * Plugin Name: Above The Fold Audit
 * Plugin URI:  https://github.com/dimikjones/above-the-fold-audit
 * Description: Shows which hyperlinks were seen above the fold when someone opens homepage over the past 7 days.
 * Version:     1.0.0
 * Author:      Marko Dimitrijevic
 * Author URI:  https://www.linkedin.com/in/diwebdeveloper/
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: above-the-fold-audit
 * Domain Path: /languages
 */

/**
 * Developer note: updating minimum PHP, WordPress and WooCommerce versions.
 *
 * When updating any version metadata above and below please ensure to update these files:
 * - `phpcs.xml`
 */

namespace Above_The_Fold_Audit;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Define constants.
const VERSION     = '1.0.0';
const PLUGIN_FILE = __FILE__;


/**
 * Return error data
 *
 * @return array
 */
function get_error() {
	return array(
		/* translators: 1: composer command. 2: plugin directory */
		'message'   => esc_html__( 'Your installation of WordPress Above The Fold Audit plugin is incomplete. Please run %1$s within the %2$s directory.', 'above-the-fold-audit' ),
		'command'   => 'composer install',
		'directory' => esc_html( str_replace( ABSPATH, '', __DIR__ ) ),
	);
}


/**
 * Autoload packages.
 *
 * The package autoloader includes version information which prevents classes in this feature plugin
 * conflicting with WooCommerce core.
 *
 * We want to fail gracefully if `composer install` has not been executed yet, so we are checking for the autoloader.
 * If the autoloader is not present, let's log the failure and display a nice admin notice.
 */
$autoloader = __DIR__ . '/vendor/autoload.php';

if ( ! is_readable( $autoloader ) ) {

	if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		$composer_error = get_error();
		error_log( sprintf( $composer_error['message'], '`' . $composer_error['command'] . '`', '`' . $composer_error['directory'] . '`' ) ); // phpcs:ignore
	}

	/**
	 * Outputs an admin notice if composer install has not been ran.
	 */
	add_action(
		'admin_notices',
		function() {
			$composer_error = get_error();
			?>
			<div class="notice notice-error">
				<p>
					<?php printf( $composer_error['message'], '<code>' . $composer_error['command'] . '</code>', '<code>' . $composer_error['directory'] . '</code>' ); // phpcs:ignore ?>
				</p>
			</div>
			<?php
		}
	);

	return;
}

require $autoloader;

Main::bootstrap();
