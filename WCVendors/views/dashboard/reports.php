<h2><?php _e( 'Sales Report', 'wc_product_vendor' ); ?></h2>

<?php

if ( $datepicker !== 'false' ) {
	woocommerce_get_template( 'date-picker.php', array(
													  'start_date' => $start_date,
													  'end_date'   => $end_date,
												 ), 'wc-product-vendor/dashboard/', pv_plugin_dir . 'views/dashboard/' );
}

?>

<table class="table table-condensed table-vendor-sales-report">
	<thead>
	<tr>
	<th class="product-header"><?php _e( 'Product', 'wc_product_vendor' ); ?></th>
	<th class="quantity-header"><?php _e( 'Quantity', 'wc_product_vendor' ) ?></th>
	<th class="commission-header"><?php _e( 'Commission', 'wc_product_vendor' ) ?></th>
	<th class="rate-header"><?php _e( 'Rate', 'wc_product_vendor' ) ?></th>
	<th></th>
	</thead>
	<tbody>

	<?php if ( !empty( $vendor_summary ) ) : ?>


		<?php if ( !empty( $vendor_summary[ 'products' ] ) ) : ?>

			<?php foreach ( $vendor_summary[ 'products' ] as $product ) :
				$_product = get_product( $product[ 'id' ] ); ?>

				<tr>

					<td class="product"><strong><a
								href="<?php echo esc_url( get_permalink( $_product->id ) ) ?>"><?php echo $product[ 'title' ] ?></a></strong>
						<?php if ( !empty( $_product->variation_id ) ) {
							echo woocommerce_get_formatted_variation( $_product->variation_data );
						} ?>
					</td>
					<td class="qty"><?php echo $product[ 'qty' ]; ?></td>
					<td class="commission"><?php echo woocommerce_price( $product[ 'cost' ] ); ?></td>
					<td class="rate"><?php echo sprintf( '%.2f%%', $product[ 'commission_rate' ] ); ?></td>

					<?php if ( $can_view_orders ) : ?>
						<td>
							<a href="<?php echo $product[ 'view_orders_url' ]; ?>"><?php _e( 'Show Orders', 'wc_product_vendor' ); ?></a>
						</td>
					<?php endif; ?>

				</tr>

			<?php endforeach; ?>

			<tr>
				<td><strong><?php _e( 'Totals', 'wc_product_vendor' ); ?></strong></td>
				<td><?php echo $vendor_summary[ 'total_qty' ]; ?></td>
				<td><?php echo woocommerce_price( $vendor_summary[ 'total_cost' ] ); ?></td>
				<td></td>

				<?php if ( $can_view_orders ) : ?>
					<td></td>
				<?php endif; ?>

			</tr>

		<?php else : ?>

			<tr>
				<td colspan="4"
					style="text-align:center;"><?php _e( 'You have no sales during this period.', 'wc_product_vendor' ); ?></td>
			</tr>

		<?php endif; ?>



	<?php else : ?>

		<tr>
			<td colspan="4"
				style="text-align:center;"><?php _e( 'You haven\'t made any sales yet.', 'wc_product_vendor' ); ?></td>
		</tr>

	<?php endif; ?>

	</tbody>
</table>
