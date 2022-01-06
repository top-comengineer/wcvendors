<?php
/**
 * Vendor related functions.
 *
 * @since 2.4.0
 * @version 2.4.0
 */


/**
 * Mark an order shipped for a particular vendor
 *
 * @param WC_Order $order The order to mark.
 * @param int $vendor_id The vendor id to mark shipped.
 *
 * @since 2.4.0
 */

if ( ! function_exists( 'wcv_mark_vendor_shipped' ) ) {
	function wcv_mark_vendor_shipped( $order, $vendor_id ) {

		// Only process orders with the required order status.
		if ( ! $order->has_status( wcv_marked_shipped_order_status() ) ) {
			return;
		}

		$shippers = (array) get_post_meta( $order->get_id(), 'wc_pv_shipped', true );

		// If not in the shippers array mark as shipped otherwise do nothing.
		if ( ! in_array( $vendor_id, $shippers ) ) {

			$shippers[] = $vendor_id;

			if ( ! empty( $mails ) ) {
				WC()->mailer()->emails['WC_Email_Notify_Shipped']->trigger( $order_id, $vendor_id );
			}

			do_action( 'wcvendors_vendor_ship', $order->get_id(), $vendor_id, $order );

			$shop_name = WCV_Vendors::get_vendor_shop_name( $vendor_id );
			$order->add_order_note( apply_filters( 'wcvendors_vendor_shipped_note', sprintf( __( '%s has marked as shipped. ', 'wc-vendors' ), $shop_name ), $vendor_id, $shop_name ) );

		}

		update_post_meta( $order->get_id(), 'wc_pv_shipped', $shippers );

	}
}


/**
 * Mark an order shipped for all vendors
 *
 * @param WC_Order $order The order to mark all vendors shipped for.
 *
 * @since 2.4.0
 */

if ( ! function_exists( 'wcv_mark_order_shipped' ) ) {
	function wcv_mark_order_shipped( $order ) {
		$vendor_ids = array_keys( WCV_Vendors::get_vendors_from_order( $order ) );
		foreach ( $vendor_ids as $vendor_id ) {
			wcv_mark_vendor_shipped( $order, $vendor_id );
		}
	}
}

/**
 * Get the formatted shipped text to output on the WooCommerce order pages.
 *
 * @param WC_Order $order The WooCommerce order being referenced.
 * @param boolean $order_edit Is this the order edit screen.
 *
 * @since 2.4.0
 */
if ( ! function_exists( 'wcv_get_order_vendors_shipped_text' ) ) {
	function wcv_get_order_vendors_shipped_text( $order, $order_edit = false ) {

		$vendors = WCV_Vendors::get_vendors_from_order( $order );
		$vendors = $vendors ? array_keys( $vendors ) : array();

		if ( empty( $vendors ) ) {
			return false;
		}

		$shipped = (array) get_post_meta( $order->get_id(), 'wc_pv_shipped', true );
		$string  = '<div class="wcv-mark-shipped">';
		$string .= '<h4>' . __( 'Vendors shipped', 'wc-vendors' ) . '</h4>';

		foreach ( $vendors as $vendor_id ) {
			$string .= in_array( $vendor_id, $shipped ) ? '&#10004; ' : '&#10005; ';
			$string .= '<span>' . WCV_Vendors::get_vendor_shop_name( $vendor_id ) . '</span>';
			if ( $order_edit && $order->has_status( wcv_marked_shipped_order_status() ) && ! wcv_vendor_shipped( $order, $vendor_id ) ) {
				$mark_vendor_shipped_url = wp_nonce_url( admin_url( 'admin-ajax.php?action=wcvendors_mark_order_vendor_shipped&order_id=' . $order->get_id() . '&vendor_id=' . $vendor_id ), 'wcvendors-mark-order-vendor-shipped' );
				$string                 .= '<a class="" href="' . $mark_vendor_shipped_url . '">' . __( 'Mark shipped', 'wc-vendors' ) . '</a>';
			}
			$string .= '<br />';
		}

		$string .= '</div>';

		return $string;
	}
}

/**
 * Check of all vendors have shipped for the order
 *
 * @param WC_Order $order The order to check
 * @return boolean $all_shipped if all vendors have shipped
 *
 * @since 2.4.0
 */
if ( ! function_exists( 'wcv_all_vendors_shipped' ) ) {
	function wcv_all_vendors_shipped( $order ) {
		$vendor_ids  = array_keys( WCV_Vendors::get_vendors_from_order( $order ) );
		$shipped     = array_filter( (array) get_post_meta( $order->get_id(), 'wc_pv_shipped', true ) );
		$all_shipped = empty( array_diff( $vendor_ids, $shipped ) );

		return $all_shipped;
	}
}

/**
 * Check of all vendors have shipped for the order
 *
 * @param WC_Order $order The order to check
 * @return boolean $all_shipped if all vendors have shipped
 *
 * @since 2.4.0
 */
if ( ! function_exists( 'wcv_vendor_shipped' ) ) {
	function wcv_vendor_shipped( $order, $vendor_id ) {
		$shipped        = array_filter( (array) get_post_meta( $order->get_id(), 'wc_pv_shipped', true ) );
		$vendor_shipped = in_array( $vendor_id, $shipped );
		return $vendor_shipped;
	}
}


/**
 * Define the order status's that can be marked shipped
 *
 * @return array $status's array of order status's
 *
 * @since 2.4.0
 */
function wcv_marked_shipped_order_status() {
	return apply_filters( 'wcvendors_order_mark_shipped_statuses', array( 'completed', 'processing' ) );
}
