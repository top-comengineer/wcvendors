<?php

/**
 * Admin orders class
 *
 * All WooCommerce Order related functions for WC Vendors.
 *
 * @since 2.4.0
 * @package WCVendors\Admin
 */

class WCVendors_Admin_Orders {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->init_hooks();
	}

	/**
	 * Initialise all actions and filters here.
	 */
	public function init_hooks() {
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		add_action( 'woocommerce_admin_order_data_after_shipping_address', array( $this, 'add_vendor_shipped_details' ), 10, 2 );
		add_action( 'woocommerce_admin_order_actions', array( $this, 'append_mark_shipped' ), 10, 2 );
		add_action( 'wp_ajax_wcvendors_mark_order_shipped', array( __CLASS__, 'mark_order_shipped' ) );
		add_action( 'wp_ajax_wcvendors_mark_order_vendor_shipped', array( __CLASS__, 'mark_order_vendor_shipped' ) );

		add_filter( 'bulk_actions-edit-shop_order', array( $this, 'add_bulk_order_shipped_action' ) );
		add_filter( 'handle_bulk_actions-edit-shop_order', array( $this, 'handle_bulk_actions' ), 10, 3 );

		add_action( 'woocommerce_order_actions', array( $this, 'add_order_shipped_action' ) );
		add_action( 'woocommerce_order_action_wcvendors_order_shipped', array( $this, 'handle_order_shipped' ), 10, 1 );
	}

	/**
	 * Add the vendor shipped information to the order edit screen.
	 *
	 * @param WC_Order $order the order we are viewing.
	 */
	public function add_vendor_shipped_details( $order ) {
		echo wcv_get_order_vendors_shipped_text( $order, true );
	}


	/**
	 * Append the mark shipped action to the actions column on the orders screen
	 *
	 * @param array    $actions The order actions column
	 * @param WC_Order $order the order row we are currently on.
	 */
	public function append_mark_shipped( $actions, $order ) {

		if ( $order->has_status( wcv_marked_shipped_order_status() ) && ! wcv_all_vendors_shipped( $order ) ) {
			$actions['wcvendors_mark_shipped'] = array(
				'url'    => wp_nonce_url( admin_url( 'admin-ajax.php?action=wcvendors_mark_order_shipped&order_id=' . $order->get_id() ), 'wcvendors-mark-order-shipped' ),
				'name'   => __( 'Mark Shipped', 'wc-vendors' ) . wcv_get_order_vendors_shipped_text( $order ),
				'action' => 'wcvendors_mark_shipped',
			);
		}

		return $actions;
	}

	/**
	 * Add action to bulk actions on order list
	 *
	 * @param   array $actions bulk actions.
	 *
	 * @return  array $actions Modified bulk actions
	 * @since   2.4.0
	 */
	public function add_bulk_order_shipped_action( $actions ) {
		$actions['wcvendors_bulk_order_shipped'] = __( 'Mark shipped', 'wc-vendors' );
		return $actions;
	}

	/**
	 * Add action to calculate commissions on single order edit screen
	 *
	 * @param array $actions The order actions.
	 *
	 * @since 2.4.0
	 */
	public function add_order_shipped_action( $actions ) {
		$actions['wcvendors_order_shipped'] = __( 'Mark shipped', 'wc-vendors' );
		return $actions;
	}


	/**
	 * Bulk action handler
	 */
	public function handle_bulk_actions( $redirect_to, $action, $ids ) {

		$ids = apply_filters( 'wcvendors_bulk_order_action_ids', array_reverse( array_map( 'absint', $ids ) ), $action, 'order' );

		foreach ( $ids as $order_id ) {
			$order = wc_get_order( $order_id );

			if ( ! in_array( $order->get_status(), wcv_marked_shipped_order_status() ) ) {
				continue;
			}

			$vendor_ids = array_keys( WCV_Vendors::get_vendors_from_order( $order ) );

			foreach ( $vendor_ids as $vendor_id ) {
				wcv_mark_vendor_shipped( $order, $vendor_id );
				do_action( 'wcvendors_mark_order_shipped', $order, $vendor_id );
			}

			do_action( 'wcvendors_bulk_order_marked_shipped', $order_id );
		}

		$redirect_to = add_query_arg(
			array(
				'post_type'              => 'shop_order',
				'wcvendors_order_action' => 'orders_marked_shipped',
				'ids'                    => join( ',', $ids ),
			),
			$redirect_to
		);
		return esc_url_raw( $redirect_to );

	}

	/**
	 * Mark the order shipped from the order edit screen
	 *
	 * @param WC_Order $order the order we are viewing.
	 *
	 * @since 2.4.0
	 * @return void
	 */
	public function handle_order_shipped( $order ) {
		if ( ! is_a( $order, 'WC_Order' ) ) {
			$order = wc_get_order( $order );
		}
		wcv_mark_order_shipped( $order );
	}

	/**
	 * Mark an order shipped for all vendors.
	 *
	 * @since 2.4.0
	 */
	public static function mark_order_shipped() {
		if ( current_user_can( 'edit_shop_orders' ) && check_admin_referer( 'wcvendors-mark-order-shipped' ) && $_GET['order_id'] ) {
			$order = wc_get_order( absint( wp_unslash( $_GET['order_id'] ) ) );
			wcv_mark_order_shipped( $order );
		}

		wp_safe_redirect( admin_url( 'edit.php?post_type=shop_order&wcvendors_order_action=order_marked_shipped&order_id=' . $order->get_id() ) );
		exit;
	}

	/**
	 * Mark an order shipped for a particular vendor.
	 *
	 * @since 2.4.0
	 */
	public static function mark_order_vendor_shipped() {
		if ( current_user_can( 'edit_shop_orders' ) && check_admin_referer( 'wcvendors-mark-order-vendor-shipped' ) && $_GET['order_id'] && $_GET['vendor_id'] ) {
			$order     = wc_get_order( absint( wp_unslash( $_GET['order_id'] ) ) );
			$vendor_id = absint( wp_unslash( $_GET['vendor_id'] ) );
			wcv_mark_vendor_shipped( $order, $vendor_id );
		}

		wp_safe_redirect( admin_url( 'post.php?post=' . $order->get_id() . '&action=edit&wcvendors_order_action=order_marked_vendor_shipped&vendor_id=' . absint( wp_unslash( $_GET['vendor_id'] ) ) ) );
		exit;
	}

	/**
	 * Notices
	 */

	/**
	 * Show confirmation message that order has been marked shipped.
	 *
	 * @since 2.4.0
	 */
	public function admin_notices() {
		global $post_type, $pagenow;

		// Bail if not on required page.
		if ( ( 'edit.php' !== $pagenow && 'post.php' !== $pagenow ) || 'shop_order' !== $post_type || ! isset( $_REQUEST['wcvendors_order_action'] ) ) { // WPCS: input var ok, CSRF ok.
			return;
		}

		$action    = wc_clean( wp_unslash( $_REQUEST['wcvendors_order_action'] ) ); // WPCS: input var ok, CSRF ok.
		$order_id  = isset( $_REQUEST['order_id'] ) ? absint( wp_unslash( $_REQUEST['order_id'] ) ) : ''; // WPCS: input var ok, CSRF ok.
		$post_id   = isset( $_REQUEST['post'] ) ? absint( wp_unslash( $_REQUEST['post'] ) ) : ''; // WPCS: input var ok, CSRF ok.
		$ids       = isset( $_REQUEST['ids'] ) ? absint( wp_unslash( $_REQUEST['ids'] ) ) : ''; // WPCS: input var ok, CSRF ok.
		$vendor_id = isset( $_REQUEST['vendor_id'] ) ? absint( wp_unslash( $_REQUEST['vendor_id'] ) ) : ''; // WPCS: input var ok, CSRF ok.

		switch ( $action ) {
			case 'order_marked_shipped':
				if ( $order_id ) {
					$message = sprintf( __( 'Order #%1$d marked shipped for all %2$s.', 'wc-vendors' ), $order_id, wcv_get_vendor_name( false, false ) );
					echo '<div class="updated"><p>' . esc_html( $message ) . '</p></div>';
				}
				break;
			case 'orders_marked_shipped':
				if ( $ids ) {
					$message = sprintf( __( 'Orders marked shipped for all %s.', 'wc-vendors' ), wcv_get_vendor_name( false, false ) );
					echo '<div class="updated"><p>' . esc_html( $message ) . '</p></div>';
				}
				break;
			case 'order_marked_vendor_shipped':
				if ( $post_id ) {
					$vendor_name = WCV_Vendors::get_vendor_shop_name( $vendor_id );
					$message     = sprintf( __( 'Order marked shipped for %s.', 'wc-vendors' ), $vendor_name );
					echo '<div class="updated"><p>' . esc_html( $message ) . '</p></div>';
				}
				break;
			default:
				// code...
				break;
		}

	}

}
