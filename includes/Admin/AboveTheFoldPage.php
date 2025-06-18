<?php
/**
 * Register plugin admin page with options.
 *
 * @class       AdminAboveTheFoldPage
 * @version     1.0.0
 * @package     Above_The_Fold_Audit/Classes/
 */

namespace Above_The_Fold_Audit\Admin;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Above_The_Fold_Audit\AdminPage as AdminPageMain;

class AboveTheFoldPage extends AdminPageMain {

	/**
	 * Hook in methods.
	 */
	public static function hooks() {
		$instance = new AboveTheFoldPage();

		// Register our options_page to the admin_menu action hook.
		add_action( 'admin_menu', array( $instance, 'add_admin_page' ) );
	}

	/**
	 * Adds the admin page to the menu.
	 */
	public function add_admin_page() {
		add_menu_page(
			$this->get_page_title(),
			$this->get_page_title(),
			'manage_options',
			$this->get_menu_slug(),
			array( $this, 'above_the_fold_page_html' ),
			'dashicons-chart-pie'
		);
	}

	/**
	 * Render the options page.
	 */
	public function above_the_fold_page_html() {
		// Check user capabilities.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		?>
		<div class="wrap" id="above-the-fold-audit-options">
			<h1><?php echo esc_html( $this->get_page_title() ); ?></h1>
			<p><?php esc_html_e( 'All Above The Fold Links on your home page for different screen sizes.', 'above-the-fold-audit' ); ?></p>
			<?php
			// TODO: template to render data!
			?>
		</div>
		<?php
	}

	/**
	 * Get the options page menu slug and options name.
	 *
	 * @return string
	 */
	protected function get_menu_slug() {
		return 'above-the-fold-page';
	}

	/**
	 * Get the options page title.
	 *
	 * @return string
	 */
	protected function get_page_title() {
		return __( 'Above The Fold', 'above-the-fold-audit' );
	}
}
