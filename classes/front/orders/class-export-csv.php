<?php

class WCV_Export_CSV
{

	/**
	 * Sort the data for CSV output first
	 *
	 * @param int   $product_id
	 * @param array $headers
	 * @param array $body
	 * @param array $items
	 */


	public static function output_csv( $product_id, $headers, $body, $items )
	{

		$headers[ 'quantity' ] = __( 'Quantity', 'wcvendors' );
		$new_body = array(); 

		foreach ( $body as $i => $order ) {

			// Remove comments
			unset( $body[ $i ][ 'comments' ] );

			// Remove all numeric keys in each order (these are the meta values we are redoing into new lines)
			foreach ( $order as $key => $col ) {
	            if ( is_int( $key ) ) unset( $order[ $key ] );
	        }

	        // New order row 
	        $new_row = $body[ $i ]; 
	        // Remove order to redo
	        unset( $body[ $i ] );  

	        $order = new WC_Order( $i );

			foreach ( $items[ $i ][ 'items' ] as $item ) {

				$product_id = !empty( $item['variation_id'] ) ? $item['variation_id'] : $item['product_id']; 

				$new_row_with_meta = $new_row; 

				// Add the qty row 
				$new_row_with_meta[] = $item[ 'qty' ];
				
				$item_meta = $item[ 'name' ]; 

				if ( $metadata = $order->has_meta( $item['product_id'] ) ) {
					foreach ( $metadata as $meta ) {

						// Skip hidden core fields
						if ( in_array( $meta['meta_key'], apply_filters( 'woocommerce_hidden_order_itemmeta', array(
							'_qty',
							'_tax_class',
							'_product_id',
							'_variation_id',
							'_line_subtotal',
							'_line_subtotal_tax',
							'_line_total',
							'_line_tax',
							WC_Vendors::$pv_options->get_option( 'sold_by_label' ), 
						) ) ) ) {
							continue;
						}

						// Skip serialised meta
						if ( is_serialized( $meta['meta_value'] ) ) {
							continue;
						}

						// Get attribute data
						if ( taxonomy_exists( wc_sanitize_taxonomy_name( $meta['meta_key'] ) ) ) {
							$term               = get_term_by( 'slug', $meta['meta_value'], wc_sanitize_taxonomy_name( $meta['meta_key'] ) );
							$meta['meta_key']   = wc_attribute_label( wc_sanitize_taxonomy_name( $meta['meta_key'] ) );
							$meta['meta_value'] = isset( $term->name ) ? $term->name : $meta['meta_value'];
						} else {
							$meta['meta_key']   = apply_filters( 'woocommerce_attribute_label', wc_attribute_label( $meta['meta_key'], $_product ), $meta['meta_key'] );
						}

						$item_meta .= wp_kses_post( rawurldecode( $meta['meta_key'] ) ) . ':' . wp_kses_post( wpautop( make_clickable( rawurldecode( $meta['meta_value'] ) ) ) );
					}
				} 

				$new_row_with_meta['product'] = $item_meta;

				$new_body[] = $new_row_with_meta; 
			}
		}		

		$headers = apply_filters( 'wcvendors_csv_headers', $headers, $product_id, $items );
		$body    = apply_filters( 'wcvendors_csv_body', $new_body, $product_id, $items );

		WCV_Export_CSV::download( $headers, $body, $product_id );
	}


	/**
	 * Send the CSV to the browser for download
	 *
	 * @param array  $headers
	 * @param array  $body
	 * @param string $filename
	 */
	public static function download( $headers, $body, $filename )
	{
		// Clear browser output before this point
		if (ob_get_contents()) ob_end_clean(); 

		// Output headers so that the file is downloaded rather than displayed
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=orders_for_' . $filename . '.csv' );

		// Create a file pointer connected to the output stream
		$output = fopen( 'php://output', 'w' );

		// Output the column headings
		fputcsv( $output, $headers );

		// Body
		foreach ( $body as $data )
			fputcsv( $output, $data );

		die();
	}


}
