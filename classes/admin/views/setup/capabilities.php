<?php
/**
 * Admin View: Step Two
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<form method="post">
	<?php wp_nonce_field( 'wcv-setup' ); ?>
	<p class="store-setup"><?php printf( __( 'Enable and disable capabilites of the %s', 'wcvendors' ), lcfirst( wcv_get_vendor_name( false ) ) ); ?></p>
		
	<table class="wcv-setup-table">
		<thead>
			<tr>
				<td class="table-desc"><strong><?php _e( 'Products', 'wcvendors' ); ?></strong></td>
				<td class="table-check"></td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="table-desc"><?php printf( __( 'Allow %s to add/edit products', 'wcvendors' ), lcfirst( wcv_get_vendor_name( false ) ) ); ?></td>
				<td class="table-check">
					<input
						type="checkbox"
						style="float: right; font-size: 4em;"
						id="wcvendors_capability_products_enabled"
						name="wcvendors_capability_products_enabled"
						value="yes"
						<?php checked( $products_enabled, 'yes' ); ?>
					/>
				</td>
			</tr>
			<tr>
				<td class="table-desc"><?php printf( __( 'Allow %s to edit published (live) products', 'wcvendors' ), lcfirst( wcv_get_vendor_name( false ) ) ); ?></td>
				<td class="table-check">
					<input
						type="checkbox"
						style="float: right; font-size: 4em;"
						id="wcvendors_capability_products_edit"
						name="wcvendors_capability_products_edit"
						value="yes"
						<?php checked( $live_products, 'yes' ); ?>
					/>
				</td>
			</tr>
			<tr>
				<td class="table-desc"><?php printf( __( 'Allow %s to publish products without requiring approval.', 'wcvendors' ), lcfirst( wcv_get_vendor_name( false ) ) )?></td>
				<td class="table-check">
					<input
						type="checkbox"
						style="float: right; font-size: 4em;"
						id="wcvendors_capability_products_live"
						name="wcvendors_capability_products_live"
						value="yes"
						<?php checked( $products_approval, 'yes' ); ?>
					/>
				</td>
			</tr>
			
		</tbody>
	</table>

	<table class="wcv-setup-table">
		<thead>
			<tr>
				<td class="table-desc"><strong><?php _e( 'Orders', 'wcvendors' ); ?></strong></td>
				<td class="table-check"></td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="table-desc"><?php printf( __( 'Allow %s to manage orders', 'wcvendors' ), lcfirst( wcv_get_vendor_name( false ) ) ); ?></td>
				<td class="table-check">
					<input
						type="checkbox"
						style="float: right; font-size: 4em;"
						id="wcvendors_capability_orders_enabled"
						name="wcvendors_capability_orders_enabled"
						value="yes"
						<?php checked( $orders_enabled, 'yes' ); ?>
					/>
				</td>
			</tr>
			<tr>
				<td class="table-desc"><?php printf( __( 'Allow %s to export their orders to a CSV file', 'wcvendors' ), lcfirst( wcv_get_vendor_name( false ) ) ); ?></td>
				<td class="table-check">
					<input
						type="checkbox"
						style="float: right; font-size: 4em;"
						id="wcvendors_capability_orders_export"
						name="wcvendors_capability_orders_export"
						value="yes"
						<?php checked( $export_orders, 'yes' ); ?>
					/>
				</td>
			</tr>
			<tr>
				<td class="table-desc"><?php printf( __( 'Allow %s to view order notes', 'wcvendors' ), lcfirst( wcv_get_vendor_name( false ) ) ); ?></td>
				<td class="table-check">
					<input
						type="checkbox"
						style="float: right; font-size: 4em;"
						id="wcvendors_capability_order_read_notes"
						name="wcvendors_capability_order_read_notes"
						value="yes"
						<?php checked( $view_order_notes, 'yes' ); ?>
					/>
				</td>
			</tr>
			<tr>
				<td class="table-desc"><?php printf( __( 'Allow %s to add order notes.', 'wcvendors' ), lcfirst( wcv_get_vendor_name( false ) ) ); ?></td>
				<td class="table-check">
					<input
						type="checkbox"
						style="float: right; font-size: 4em;"
						id="wcvendors_capability_order_update_notes"
						name="wcvendors_capability_order_update_notes"
						value="yes"
						<?php checked( $view_order_notes, 'yes' ); ?>
					/>
				</td>
			</tr>
		</tbody>
	</table>


	<p class="wcv-setup-actions step">
		<button type="submit" class="button button-next" value="<?php esc_attr_e( "Next", 'wcvendors' ); ?>" name="save_step"><?php esc_html_e( "Next", 'wcvendors' ); ?></button>
	</p>
</form>