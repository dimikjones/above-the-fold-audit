<?php
/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to output user specific weather data information.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Above_The_Fold_Audit
 * @subpackage Above_The_Fold_Audit/templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Above_The_Fold_Audit\Table;

// Number of items to display per page.
$per_page = 20;

// Get current page number for pagination.
$current_page = isset( $_GET['paged'] ) ? max( 1, intval( $_GET['paged'] ) ) : 1;
$offset       = ( $current_page - 1 ) * $per_page;

$total_entries = count( Table::get_data( false, false ) );

$data = Table::get_data( $current_page, $offset );
?>

<?php if ( empty( $data ) && 1 === $current_page ) : ?>
	<p><?php esc_html_e( 'No audit data found yet.', 'above-the-fold-audit' ); ?></p>
<?php else : ?>
	<table class="above-the-fold-audit-table widefat striped">
		<thead>
		<tr>
			<th><?php esc_html_e( 'Timestamp', 'above-the-fold-audit' ); ?></th>
			<th><?php esc_html_e( 'Page URL', 'above-the-fold-audit' ); ?></th>
			<th><?php esc_html_e( 'Viewport Size', 'above-the-fold-audit' ); ?></th>
			<th><?php esc_html_e( 'Visible Links (Above Fold)', 'above-the-fold-audit' ); ?></th>
			<th><?php esc_html_e( 'User IP', 'above-the-fold-audit' ); ?></th>
			<th><?php esc_html_e( 'User Agent', 'above-the-fold-audit' ); ?></th>
		</tr>
		</thead>
		<tbody>
		<?php foreach ( $data as $row ) : ?>
			<tr>
				<td><?php echo esc_html( $row['timestamp'] ); ?></td>
				<td class="url-cell"><a href="<?php echo esc_url( $row['page_url'] ); ?>" target="_blank"><?php echo esc_html( $row['page_url'] ); ?></a></td>
				<td><?php echo esc_html( $row['viewport_width'] . 'x' . $row['viewport_height'] ); ?></td>
				<td>
					<?php
					$visible_links = json_decode( $row['visible_links'], true );
					if ( ! empty( $visible_links ) && is_array( $visible_links ) ) {
						echo '<ul>';
						foreach ( $visible_links as $link ) {
							printf(
								'<li><a href="%1$s" target="_blank">%2$s</a> (W:%3$s H:%4$s T:%5$s L:%6$s)</li>',
								esc_url( $link['href'] ?? '#' ),
								esc_html( $link['text'] ?? 'N/A' ),
								esc_html( $link['position']['width'] ?? 'N/A' ),
								esc_html( $link['position']['height'] ?? 'N/A' ),
								esc_html( $link['position']['top'] ?? 'N/A' ),
								esc_html( $link['position']['left'] ?? 'N/A' )
							);
						}
						echo '</ul>';
					} else {
						echo esc_html__( 'No links recorded', 'above-the-fold-audit' );
					}
					?>
				</td>
				<td><?php echo esc_html( $row['user_ip'] ); ?></td>
				<td>
					<pre><?php echo esc_html( $row['user_agent'] ); ?></pre>
				</td>
			</tr>
		<?php endforeach; ?>
		<?php if ( empty( $results ) && $current_page > 1 ) : ?>
			<tr>
				<td colspan="6"><?php esc_html_e( 'No more entries.', 'above-the-fold-audit' ); ?></td>
			</tr>
		<?php endif; ?>
		</tbody>
	</table>

	<div class="tablenav bottom">
		<div class="tablenav-pages">
			<?php
			$pages_args = array(
				'base'      => add_query_arg( 'paged', '%#%' ),
				'format'    => '',
				'total'     => ceil( $total_entries / $per_page ),
				'current'   => $current_page,
				'show_all'  => false,
				'end_size'  => 1,
				'mid_size'  => 2,
				'prev_next' => true,
				'prev_text' => __( '&laquo; Previous', 'above-the-fold-audit' ),
				'next_text' => __( 'Next &raquo;', 'above-the-fold-audit' ),
				// 'plain' for simple links, 'array' for custom HTML, 'list' for <ul>.
				'type'      => 'plain',
				// Don't add current URL args, will be handled by 'base'.
				'add_args'  => false,
			);
			echo paginate_links( $pages_args );
			?>
		</div>
	</div>
<?php endif; ?>
