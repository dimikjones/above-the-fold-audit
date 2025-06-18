<?php
/**
 * Handle plugin's custom database table for analytics data.
 *
 * @class       Table
 * @version     1.0.0
 * @package     Above_The_Fold_Audit/Classes/
 */

namespace Above_The_Fold_Audit;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Define the table name for analytics data.
 * WordPress recommends prefixing table names with $wpdb->prefix to avoid conflicts.
 */
global $wpdb;
define( 'ABOVE_THE_FOLD_AUDIT_TABLE', $wpdb->prefix . 'above_the_fold_audit_data' );

/**
 * Define the current database version.
 * Increment this number whenever you make changes to the table schema.
 */
define( 'ABOVE_THE_FOLD_AUDIT_DB_VERSION', '1.0' );

/**
 * Table class for creating and managing the custom analytics database table.
 */
final class Table {
	/**
	 * The database version option key in wp_options.
	 *
	 * @var string
	 */
	const DB_VERSION_OPTION_KEY = 'above_the_fold_audit_db_version';

	/**
	 * Initialize hooks
	 *
	 * @return void
	 */
	public static function hooks() {
		add_action( 'above_the_fold_audit_loaded', array( __CLASS__, 'install' ) );
	}

	/**
	 * Installs the custom database table.
	 * This method should be called on plugin activation.
	 *
	 * It uses dbDelta for safe table creation/alteration and handles versioning.
	 *
	 * @return void
	 */
	public static function install() {
		global $wpdb;
		$table_name = ABOVE_THE_FOLD_AUDIT_TABLE;

		// Load dbDelta for database operations.
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$charset_collate = $wpdb->get_charset_collate();

		// SQL to create the table.
		// Make sure column definitions are on separate lines for dbDelta to work correctly.
		$sql = "CREATE TABLE {$table_name} (
         id bigint(20) NOT NULL AUTO_INCREMENT,
         timestamp datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
         page_url varchar(2000) NOT NULL,
         viewport_width int(11) NOT NULL,
         viewport_height int(11) NOT NULL,
         visible_links longtext NOT NULL,
         user_ip varchar(45) DEFAULT '' NOT NULL,
         user_agent text NOT NULL,
         PRIMARY KEY  (id)
      ) {$charset_collate};";

		// Execute the SQL. dbDelta will create the table if it doesn't exist
		// or alter it if the schema has changed (based on the current version).
		dbDelta( $sql );

		// Update the database version in options.
		update_option( self::DB_VERSION_OPTION_KEY, ABOVE_THE_FOLD_AUDIT_DB_VERSION );
	}

	/**
	 * Updates the custom database table.
	 * This method can be called if you need to perform specific schema migrations
	 * beyond what dbDelta automatically handles, or just to ensure dbDelta runs
	 * if the version number is different.
	 *
	 * @return void
	 */
	public static function update() {
		$installed_version = get_option( self::DB_VERSION_OPTION_KEY );

		// If the installed version is different from the current plugin version, run install() again.
		// dbDelta is smart enough to only apply necessary changes.
		if ( version_compare( $installed_version, ABOVE_THE_FOLD_AUDIT_DB_VERSION, '<' ) ) {
			self::install();
		}
	}

	public static function get_data() {
		global $wpdb;
		$table_name = ABOVE_THE_FOLD_AUDIT_TABLE;

		$results = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM %i ORDER BY timestamp DESC LIMIT 50', $table_name ), ARRAY_A ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery

		return $results;
	}

	/**
	 * Drops the custom database table.
	 * This method should be called on plugin deactivation or uninstall.
	 * Use with caution, as this will permanently delete all stored data.
	 *
	 * @return void
	 */
	public static function uninstall() {
		global $wpdb;
		$table_name = ABOVE_THE_FOLD_AUDIT_TABLE;
		$sql        = "DROP TABLE IF EXISTS {$table_name};";
		$wpdb->query( $sql );

		// Also delete the database version option.
		delete_option( self::DB_VERSION_OPTION_KEY );
	}
}
