<?php
/**
 * Uninstall
 *
 * Uninstalling plugin code.
 *
 * @package     Above_The_Fold_Audit/Uninstaller
 * @version     1.0.0
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

use Above_The_Fold_Audit\Table;

Table::uninstall();
