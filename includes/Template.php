<?php
/**
 * Contains template related methods.
 *
 * @class       Template
 * @version     1.0.0
 * @package     Above_The_Fold_Audit/Classes/
 */

namespace Above_The_Fold_Audit;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Template Class.
 */
final class Template {

	/**
	 * Get template part.
	 *
	 * @param mixed  $slug Slug of the template to get.
	 * @param string $name (default: '') Template name (sub-slug if you will).
	 *
	 * @return void
	 */
	public static function get_part( $slug, $name = '' ) {

		$template = '';

		// Look in yourtheme/slug-name.php and yourtheme/above-the-fold-audit/slug-name.php .
		if ( $name ) {
			$template = locate_template( array( "{$slug}-{$name}.php", Utils::template_path() . "{$slug}-{$name}.php" ) );
		}

		// Get default slug-name.php .
		if ( ! $template && $name && file_exists( Utils::plugin_path() . "/templates/{$slug}-{$name}.php" ) ) {
			$template = Utils::plugin_path() . "/templates/{$slug}-{$name}.php";
		}

		// If template file doesn't exist, look in yourtheme/slug.php and yourtheme/above-the-fold-audit/slug.php .
		if ( ! $template ) {
			$template = locate_template( array( "{$slug}.php", Utils::template_path() . "{$slug}.php" ) );
		}

		// Allow 3rd party plugins to filter template file from their plugin.
		$template = apply_filters( 'above_the_fold_audit_get_template_part', $template, $slug, $name );

		if ( $template ) {
			load_template( $template, false );
		}
	}


	/**
	 * Get other templates passing attributes and including the file.
	 *
	 * @param string $template_name Filename to locate.
	 * @param array<string,mixed> $args (default: array()) Args to send to template.
	 * @param string $template_path (default: '') Path to look the template into.
	 * @param string $default_path (default: '') Default path to fallback to.
	 * @param bool $no_extract
	 *
	 * @return void
	 */
	public static function get( $template_name, $args = array(), $template_path = '', $default_path = '', $no_extract = true ) {

		if ( ! empty( $args ) && is_array( $args ) && $no_extract ) {
			// phpcs:ignore WordPress.PHP.DontExtract
			extract( $args );
		}

		$located = self::locate( $template_name, $template_path, $default_path );

		if ( ! file_exists( $located ) ) {
			_doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $located ), '1.0.0' ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			return;
		}

		// Allow 3rd party plugin filter template file from their plugin.
		$located = apply_filters( 'above_the_fold_audit_get_template', $located, $template_name, $args, $template_path, $default_path );

		// Perform other actions before template part is included.
		do_action( 'above_the_fold_audit_before_template_part', $template_name, $template_path, $located, $args );

		include $located;

		// Perform other actions after template part is included.
		do_action( 'above_the_fold_audit_after_template_part', $template_name, $template_path, $located, $args );
	}


	/**
	 * Like get, but returns the HTML instead of outputting.
	 *
	 * @since 2.5.0
	 * @param string              $template_name Filename to locate.
	 * @param array<string,mixed> $args (default: array()) Args to send to template.
	 * @param string              $template_path (default: '') Path to look the template into.
	 * @param string              $default_path (default: '') Default path to fallback to.
	 * @return string
	 */
	public static function get_html( $template_name, $args = array(), $template_path = '', $default_path = '' ) {

		ob_start();

		self::get( $template_name, $args, $template_path, $default_path );

		$ret = ob_get_clean();

		return is_bool( $ret ) ? '' : $ret;
	}


	/**
	 * Locate a template and return the path for inclusion.
	 *
	 * This is the load order:
	 *
	 *      yourtheme       /   $template_path  /   $template_name
	 *      yourtheme       /   $template_name
	 *      $default_path   /   $template_name
	 *
	 * @param string $template_name Filename to locate.
	 * @param string $template_path (default: '') Path to look the template into.
	 * @param string $default_path (default: '') Default path to fallback to.
	 * @return string
	 */
	public static function locate( $template_name, $template_path = '', $default_path = '' ) {

		if ( ! $template_path ) {
			$template_path = Utils::template_path();
		}

		if ( ! $default_path ) {
			$default_path = Utils::plugin_path() . '/templates/';
		}

		// Look within passed path within the theme - this is priority.
		$template = locate_template(
			array(
				trailingslashit( $template_path ) . $template_name,
				$template_name,
			)
		);

		// Get default template.
		if ( ! $template ) {
			$template = $default_path . $template_name;
		}

		// Return what we found.
		return apply_filters( 'above_the_fold_audit_locate_template', $template, $template_name, $template_path );
	}
}
