<?php

class PV_Export_CSV
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
		foreach ( $body as $i => $data ) {

			unset( $body[ $i ][ 'comments' ] );

			foreach ( $items[ $i ][ 'items' ] as $item ) {

				$item_meta = new WC_Order_Item_Meta( $item[ 'item_meta' ] );
				$item_meta = $item_meta->display( true, true );

				if ( !empty( $item_meta ) ) {
					$meta          = true;
					$body[ $i ][ ] = $item[ 'qty' ] . 'x: ' . html_entity_decode( $item_meta );
				} else {
					$body[ $i ][ ] = $item[ 'qty' ];
				}

			}
		}

		if ( $meta ) $headers[ 'meta' ] = __( 'Extra data', 'wcvendors' );
		else $headers[ 'quantity' ] = __( 'Quantity', 'wcvendors' );

		$headers = apply_filters( 'wcvendors_csv_headers', $headers, $product_id, $items );
		$body    = apply_filters( 'wcvendors_csv_body', $body, $product_id, $items );

		PV_Export_CSV::download( $headers, $body, $product_id );
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
		ob_end_clean();

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
