<?php
/**
 * Handle front hooks.
 *
 * @class       Front
 * @version     1.0.0
 * @package     Above_The_Fold_Audit/Classes/
 */

namespace Above_The_Fold_Audit\Front;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Front main class
 */
final class Main {

	/**
	 * Initialize hooks
	 *
	 * @return void
	 */
	public static function hooks() {
		Assets::hooks();
	}
}
