<?php

global $woocommerce; ?>

<?php if ( function_exists( 'wc_print_notices' ) ) { wc_print_notices(); } ?>

<h2><?php printf( 'Orders for %s', get_product( $product_id )->get_title() ); ?></h2>

<table class="table table-striped table-bordered">
	<thead>
	<tr>
		<?php foreach ( $headers as $header ) : ?>
			<th class="<?php echo sanitize_title( $header ); ?>"><?php echo $header; ?></th>
		<?php endforeach; ?>
	</tr>
	</thead>
	<tbody>

	<?php foreach ( $body as $order_id => $order ) :

		$order_items = !empty( $items[ $order_id ][ 'items' ] ) ? $items[ $order_id ][ 'items' ] : array();
		$count       = count( $order_items ); ?>

		<tr>

			<?php
			$order_keys = array_keys( $order );
			$first_index = array_shift( $order_keys );
			$last_index = end( $order_keys );
			foreach ( $order as $detail_key => $detail ) : if ( $detail_key == $last_index ) continue; ?>
				<?php if ( $detail_key == $first_index ) : ?>

					<td class="<?php echo $detail_key; ?>"
						rowspan="<?php echo $count == 1 ? 3 : ( $count + 3 ); ?>"><?php echo $detail; ?></td>

				<?php else : ?>

					<td class="<?php echo $detail_key; ?>"><?php echo $detail; ?></td>

				<?php endif; ?>
			<?php endforeach; ?>

		</tr>

		<tr>

			<?php foreach ( $order_items as $item ) {

				wc_get_template( 'table-body.php', array(
																 'item'     => $item,
																 'count'    => $count,
																 'order_id' => $order_id,
															), 'wc-vendors/orders/', wcv_plugin_dir . 'templates/orders/' );

			}

			if ( !empty( $order[ 'comments' ] ) ) {
				$customer_note = $order[ 'comments' ];
				wc_get_template( 'customer-note.php', array(
																	'customer_note' => $customer_note,
															   ), 'wc-vendors/orders/customer-note/', wcv_plugin_dir . 'templates/orders/customer-note/' );
			}

			?>

		<tr>
			<td colspan="100%">

				<?php
				$can_view_comments = WC_Vendors::$pv_options->get_option( 'can_view_order_comments' );
				$can_add_comments = WC_Vendors::$pv_options->get_option( 'can_submit_order_comments' );

				if ($can_view_comments || $can_add_comments) :

				$comments = array();

				if ( $can_view_comments ) {
					$order    = new WC_Order( $order_id );
					$comments = $order->get_customer_order_notes();
				}
				?>
				<a href="#" class="order-comments-link">
					<p>
						<?php printf( __( 'Comments (%s)', 'wcvendors' ), count( $comments ) ); ?>
					</p>
				</a>

				<div class="order-comments">
					<?php

					endif;

					if ( $can_view_comments && !empty( $comments ) ) {
						wc_get_template( 'existing-comments.php', array(
																				'comments' => $comments,
																		   ), 'wc-vendors/orders/comments/', wcv_plugin_dir . 'templates/orders/comments/' );
					}

					if ( $can_add_comments ) {
						wc_get_template( 'add-new-comment.php', array(
																			  'order_id'   => $order_id,
																			  'product_id' => $product_id,
																		 ), 'wc-vendors/orders/comments/', wcv_plugin_dir . 'templates/orders/comments/' );
					}

					?>
				</div>

				<?php if ( is_array( $providers ) ) : ?>

					<a href="#" class="order-tracking-link">
						<p>
							<?php _e( 'Shipping', 'wcvendors' ); ?>
						</p>
					</a>

					<div class="order-tracking">
						<?php 
						$js = "
							jQuery(function() {

								var providers = jQuery.parseJSON( '" . json_encode( $provider_array ) . "' );

								jQuery('#tracking_number').prop('readonly',true);
								jQuery('#date_shipped').prop('readonly',true);

								function updatelink( tracking, provider ) { 

									var postcode = '32';
									postcode = encodeURIComponent(postcode);

									link = providers[provider];
									link = link.replace('%251%24s', tracking);
									link = link.replace('%252%24s', postcode);
									link = decodeURIComponent(link);
									return link; 
								}

								jQuery('.tracking_provider, #tracking_number').unbind().change(function(){
									
									var form = jQuery(this).parent().parent().attr('id');

									var tracking = jQuery('#' + form + ' input#tracking_number').val();
									var provider = jQuery('#' + form + ' #tracking_provider').val();
									
									if ( providers[ provider ]) {
										link = updatelink(tracking, provider); 
										jQuery('#' + form + ' #tracking_number').prop('readonly',false);
										jQuery('#' + form + ' #date_shipped').prop('readonly',false);
										jQuery('#' + form + ' .custom_tracking_url_field, #' + form + ' .custom_tracking_provider_name_field').hide();
									} else {
										jQuery('#' + form + ' .custom_tracking_url_field, #' + form + ' .custom_tracking_provider_name_field').show();
										link = jQuery('#' + form + ' input#custom_tracking_link').val();
									}

									if (link) {
										jQuery('#' + form + ' p.preview_tracking_link a').attr('href', link);
										jQuery('#' + form + ' p.preview_tracking_link').show();
									} else {
										jQuery('#' + form + ' p.preview_tracking_link').hide();
									}

								});

								jQuery('#custom_tracking_provider_name').unbind().click(function(){
									
									var form = jQuery(this).parent().parent().attr('id');

									jQuery('#' + form + ' #tracking_number').prop('readonly',false);
									jQuery('#' + form + ' #date_shipped').prop('readonly',false);
								
								});
							
							});
						"; 

							if ( function_exists( 'wc_enqueue_js' ) ) {
								wc_enqueue_js( $js );
							} else {
								$woocommerce->add_inline_js( $js );
							}

						wc_get_template( 'shipping-form.php', array(
																			'order_id'       => $order_id,
																			'product_id'     => $product_id,
																			'providers'      => $providers,
																			'provider_array' => $provider_array, 
																	   ), 'wc-vendors/orders/shipping/', wcv_plugin_dir . 'templates/orders/shipping/' );
						?>
					</div>

				<?php endif; ?>

			</td>
		</tr>

		</tr>

	<?php endforeach; ?>

	</tbody>
</table>