<?php
/**
 * Handle plugin's custom database table for analytics data.
 *
 * @class       RestApi
 * @version     1.0.0
 * @package     Above_The_Fold_Audit/Classes/
 */

namespace Above_The_Fold_Audit;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * RestApi class for creating and managing the custom analytics database table.
 */
final class RestApi {

	public static function hooks() {
		// Hook into REST API initialization to register our endpoint.
		add_action( 'rest_api_init', array( __CLASS__, 'register_rest_endpoint' ) );

		// Localize nonce to frontend.
		add_filter( 'above_the_fold_audit_general_params', array( __CLASS__, 'enqueue_frontend_script' ) );
	}

	/**
	 * Enqueues the frontend script and localizes the WordPress REST API nonce.
	 * This makes `wpApiSettings.nonce` available in your JavaScript.
	 *
	 * @return array
	 */
	public static function enqueue_frontend_script( $data ) {
		// Localize script data with nonce for REST API requests.
		// The `wp_localize_script` function should be called after `wp_enqueue_script`.
		$new_data = array(
			'rest_nonce' => wp_create_nonce( 'wp_rest' ) // Create the nonce for general REST API access.
		);

		return array_merge( $data, $new_data );
	}

	/**
	 * Registers a custom REST API endpoint to receive data.
	 * The endpoint will be: YOUR_SITE_URL/wp-json/your-plugin/v1/analytics
	 */
	public static function register_rest_endpoint() {
		register_rest_route(
			'above-the-fold/v1',
			'/audit',
			array(
				'methods'             => 'POST',
				'callback'            => array( __CLASS__, 'handle_analytics_data' ),
				'permission_callback' => array( __CLASS__, 'permission_check' ),
				'args'                => array(
					'timestamp' => array(
						'type'              => 'string',
						'required'          => true,
						'validate_callback' => function( $param ) {
							return (bool) strtotime( $param ); // Basic validation for timestamp format
						},
					),
					'pageUrl' => array(
						'type'              => 'string',
						'required'          => true,
						'sanitize_callback' => 'esc_url_raw',
					),
					'viewportSize' => array(
						'type'       => 'object',
						'required'   => true,
						'properties' => array(
							'width'  => array( 'type' => 'integer' ),
							'height' => array( 'type' => 'integer' ),
						),
					),
					'visibleLinks' => array(
						'type'     => 'array',
						'required' => true,
						'items'    => array(
							'type'       => 'object',
							'properties' => array(
								'text' => array( 'type' => 'string' ),
								'href' => array( 'type' => 'string', 'format' => 'uri' ),
								'position' => array(
									'type'       => 'object',
									'properties' => array(
										'top'    => array( 'type' => 'number' ),
										'left'   => array( 'type' => 'number' ),
										'width'  => array( 'type' => 'number' ),
										'height' => array( 'type' => 'number' ),
									),
								),
							),
						),
					),
				),
			)
		);
	}

	/**
	 * Permission callback for the REST endpoint.
	 * For basic tracking, allowing public access (without nonce) might be acceptable,
	 * but be aware of potential abuse. For production, consider implementing a nonce.
	 *
	 * @param \WP_REST_Request $request The request object.
	 * @return bool|WP_Error True if permission is granted, WP_Error otherwise.
	 */
	public static function permission_check( \WP_REST_Request $request ) {
		// Get the nonce from the request header.
		$nonce = $request->get_header( 'X-WP-Nonce' );

		// Verify the nonce. 'wp_rest' is the default action for REST API nonces.
		if ( ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
			// If nonce is invalid, return a WP_Error object.
			return new \WP_Error(
				'rest_forbidden_nonce',
				__( 'Invalid or missing nonce.', 'above-the-fold-audit' ),
				array( 'status' => 401 )
			);
		}

		// If nonce is valid, grant permission.
		return true;
	}

	/**
	 * Retrieves the client's IP address, handling proxies.
	 *
	 * @return string The client IP address.
	 */
	private static function get_client_ip() {
		$ip_address = '';
		if ( isset( $_SERVER['HTTP_CLIENT_IP'] ) && ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			$ip_address = sanitize_text_field( wp_unslash( $_SERVER['HTTP_CLIENT_IP'] ) );
		} elseif ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) && ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			// Can contain multiple IPs, get the first one.
			$ip_address = explode( ',', sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) )[0];
		} elseif ( isset( $_SERVER['REMOTE_ADDR'] ) && ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
			$ip_address = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
		}
		return $ip_address;
	}

	/**
	 * Handles the incoming analytics data from the frontend and inserts it into the custom table.
	 *
	 * @param \WP_REST_Request $request The request object containing the JSON data.
	 * @return \WP_REST_Response The response object.
	 */
	public static function handle_analytics_data( \WP_REST_Request $request ) {
		global $wpdb;
		$table_name = ABOVE_THE_FOLD_AUDIT_TABLE;

		$data = $request->get_json_params(); // Get JSON data from the request body.

		if ( empty( $data ) ) {
			return new \WP_REST_Response( array( 'message' => 'No data received.' ), 400 );
		}

		// Extract and sanitize data from the request.
		$timestamp       = sanitize_text_field( $data['timestamp'] );
		$page_url        = esc_url_raw( $data['pageUrl'] );
		$viewport_width  = isset( $data['viewportSize']['width'] ) ? intval( $data['viewportSize']['width'] ) : 0;
		$viewport_height = isset( $data['viewportSize']['height'] ) ? intval( $data['viewportSize']['height'] ) : 0;
		// Store as JSON string.
		$visible_links   = isset( $data['visibleLinks'] ) ? wp_json_encode( $data['visibleLinks'] ) : '[]';

		// Get user IP address using helper method.
		$user_ip    = self::get_client_ip();
		$user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';

		// Prepare data for insertion.
		$insert_data = array(
			'timestamp'       => $timestamp,
			'page_url'        => $page_url,
			'viewport_width'  => $viewport_width,
			'viewport_height' => $viewport_height,
			'visible_links'   => $visible_links,
			'user_ip'         => $user_ip,
			'user_agent'      => $user_agent,
		);

		// Define data formats for wpdb->insert (important for security and correct data types).
		$insert_format = array(
			'%s', // timestamp (string).
			'%s', // page_url (string).
			'%d', // viewport_width (integer).
			'%d', // viewport_height (integer).
			'%s', // visible_links (string - JSON).
			'%s', // user_ip (string).
			'%s', // user_agent (string).
		);

		// Insert data into the custom table.
		$inserted = $wpdb->insert( $table_name, $insert_data, $insert_format ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery

		if ( false === $inserted ) {
			// Log the last error for debugging purposes (check WordPress debug.log).
			error_log( 'WP Analytics Tracker: Failed to insert data into database. Error: ' . $wpdb->last_error );
			return new \WP_REST_Response( array( 'message' => 'Failed to store analytics data in database.', 'error' => $wpdb->last_error ), 500 );
		}

		// Return a success response.
		return new \WP_REST_Response( array( 'message' => 'Analytics data received and stored successfully.', 'entry_id' => $wpdb->insert_id ), 200 );
	}
}
