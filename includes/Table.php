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

		// Schedule daily cleanup of old data (if not already scheduled).
		add_action( 'wp', array( __CLASS__, 'schedule_daily_cleanup' ) );
		add_action( 'above_the_fold_audit_daily_cleanup', array( __CLASS__, 'clear_old_data' ) );
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

	/**
	 * Fetch data from the database optionally with LIMIT and OFFSET for pagination.
	 *
	 * @param int $per_page
	 * @param int $offset
	 *
	 * @return array
	 */
	public static function get_data( $per_page, $offset ) {
		global $wpdb;
		$table_name = ABOVE_THE_FOLD_AUDIT_TABLE;

		if ( $per_page && $offset ) {
			$results = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM %i ORDER BY timestamp DESC LIMIT %d OFFSET %d', $table_name, $per_page, $offset ), ARRAY_A ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		} else {
			$results = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM %i ORDER BY timestamp DESC', $table_name ), ARRAY_A ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		}

		return $results;
	}

	/**
	 * Get total entries from the database table by counting ID only - faster than fetching everything.
	 *
	 * @return int
	 */
	public static function get_total_entries() {
		global $wpdb;
		$table_name = ABOVE_THE_FOLD_AUDIT_TABLE;

		$total_entries = $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(id) FROM %i', $table_name ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery

		return intval( $total_entries );
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

		$wpdb->query( $wpdb->prepare( 'DROP TABLE IF EXISTS %i', $table_name ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery

		// Also delete the database version option.
		delete_option( self::DB_VERSION_OPTION_KEY );
	}

	/**
	 * Schedules a daily cleanup event for old analytics data.
	 * This cron event runs once daily.
	 *
	 * @return void
	 */
	public static function schedule_daily_cleanup() {
		if ( ! wp_next_scheduled( 'above_the_fold_audit_daily_cleanup' ) ) {
			wp_schedule_event( time(), 'daily', 'above_the_fold_audit_daily_cleanup' );
		}
	}

	/**
	 * Clears analytics data older than 7 days from the custom database table.
	 * This method is triggered by the scheduled cron event.
	 *
	 * @return void
	 */
	public static function clear_old_data() {
		global $wpdb;
		$table_name     = ABOVE_THE_FOLD_AUDIT_TABLE;
		$seven_days_ago = gmdate( 'Y-m-d H:i:s', strtotime( '-7 days' ) );

		$deleted = $wpdb->query( $wpdb->prepare( "DELETE FROM %i WHERE timestamp < %s", $table_name, $seven_days_ago ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery

		if ( false === $deleted ) {
			error_log( 'Above The Fold Audit: Failed to clear old data. Error: ' . $wpdb->last_error );
		} else {
			error_log( 'Above The Fold Audit: Successfully cleared ' . $deleted . ' old data entries.' );
		}
	}
}
