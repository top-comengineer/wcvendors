<?php 
/**
 *  WC Vendor Admin Dashboard - Vendor WP-Admin Dashboard Pages
 * 
 * @author Jamie Madden <http://wcvendors.com / https://github.com/digitalchild>
 * @package WCVendors
 */

Class WCV_Vendor_Admin_Dashboard { 

	function __construct(){ 
		// Add Shop Settings page 
		add_action( 'admin_menu', array( $this, 'vendor_dashboard_pages') ); 
		// Hook into init for form processing 
		add_action( 'init', array( $this, 'save_shop_settings' ) );
	}

	function vendor_dashboard_pages(){
        add_menu_page( __('Shop Settings', 'wcvendors'), __('Shop Settings', 'wcvendors'), 'manage_product', 'wcv-vendor-shopsettings', array( $this, 'settings_page' ) );
		$hook = add_menu_page( __( 'Orders', 'wcvendors' ), __( 'Orders', 'wcvendors' ), 'manage_product', 'wcv-vendor-orders', array( 'WCV_Vendor_Admin_Dashboard', 'orders_page' ) );
		add_action( "load-$hook", array( 'WCV_Vendor_Admin_Dashboard', 'add_options' ) );
	}
 
	function settings_page() {  
		$user_id = get_current_user_id(); 
		$paypal_address   = true; 
		$shop_description = true; 
		$description = get_user_meta( $user_id, 'pv_shop_description', true );
		$seller_info = get_user_meta( $user_id, 'pv_seller_info', true );
		$has_html    = get_user_meta( $user_id, 'pv_shop_html_enabled', true );
		$shop_page   = WCV_Vendors::get_vendor_shop_page( wp_get_current_user()->user_login );
		$global_html = WC_Vendors::$pv_options->get_option( 'shop_html_enabled' );
		include('views/html-vendor-settings-page.php'); 
	}

	/** 
	*	Save shop settings 
	*/
	public function save_shop_settings()
	{
		$user_id = get_current_user_id();
		$error = false; 
		$error_msg = '';

		if (isset ( $_POST[ 'wc-vendors-nonce' ] ) ) { 

			if ( !wp_verify_nonce( $_POST[ 'wc-vendors-nonce' ], 'save-shop-settings-admin' ) ) {
				return false;
			}

			if ( !is_email( $_POST[ 'pv_paypal' ] ) ) {
				$error_msg .=  __( 'Your PayPal address is not a valid email address.', 'wcvendors' );
				$error = true; 
			} else {
				update_user_meta( $user_id, 'pv_paypal', $_POST[ 'pv_paypal' ] );
			}
		
			if ( !empty( $_POST[ 'pv_shop_name' ] ) ) {
				$users = get_users( array( 'meta_key' => 'pv_shop_slug', 'meta_value' => sanitize_title( $_POST[ 'pv_shop_name' ] ) ) );
				if ( !empty( $users ) && $users[ 0 ]->ID != $user_id ) {
					$error_msg .= __( 'That shop name is already taken. Your shop name must be unique.', 'wcvendors' ); 
					$error = true; 
				} else {
					update_user_meta( $user_id, 'pv_shop_name', $_POST[ 'pv_shop_name' ] );
					update_user_meta( $user_id, 'pv_shop_slug', sanitize_title( $_POST[ 'pv_shop_name' ] ) );
				}
			}

			if ( isset( $_POST[ 'pv_shop_description' ] ) ) {
				update_user_meta( $user_id, 'pv_shop_description', $_POST[ 'pv_shop_description' ] );
			}

			if ( isset( $_POST[ 'pv_seller_info' ] ) ) {
				update_user_meta( $user_id, 'pv_seller_info', $_POST[ 'pv_seller_info' ] );
			}

			do_action( 'wcvendors_shop_settings_admin_saved', $user_id );

			if ( ! $error ) {
				echo '<div class="updated"><p>';
				echo __( 'Settings saved.', 'wcvendors' );
				echo '</p></div>';
			} else { 
				echo '<div class="error"><p>';
				echo $error_msg;
				echo '</p></div>';
			}
		}
	}

	/**
	 *
	 *
	 * @param unknown $status
	 * @param unknown $option
	 * @param unknown $value
	 *
	 * @return unknown
	 */
	public static function set_table_option( $status, $option, $value )
	{
		if ( $option == 'orders_per_page' ) {
			return $value;
		}
	}


	/**
	 *
	 */
	public static function add_options()
	{
		global $WCV_Vendor_Order_Page;

		$args = array(
			'label'   => 'Rows',
			'default' => 10,
			'option'  => 'orders_per_page'
		);
		add_screen_option( 'per_page', $args );

		$WCV_Vendor_Order_Page = new WCV_Vendor_Order_Page();

	}


	/**
	 * HTML setup for the Orders Page 
	 */
	public static function orders_page()
	{
		global $woocommerce, $WCV_Vendor_Order_Page;

		$WCV_Vendor_Order_Page->prepare_items();

		?>
		<div class="wrap">

			<div id="icon-woocommerce" class="icon32 icon32-woocommerce-reports"><br/></div>
			<h2><?php _e( 'Orders', 'wcvendors' ); ?></h2>

			<form id="posts-filter" method="get">

				<input type="hidden" name="page" value="wcv-vendor-orders"/>
				<?php $WCV_Vendor_Order_Page->display() ?>

			</form>
			<div id="ajax-response"></div>
			<br class="clear"/>
		</div>

<?php }

} // End WCV_Vendor_Admin_Dashboard

if ( !class_exists( 'WP_List_Table' ) ) require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';

/**
 * WCV Vendor Order Page 
 * 
 * @author Jamie Madden <http://wcvendors.com / https://github.com/digitalchild>
 * @package WCVendors 
 * @extends WP_List_Table
 */
class WCV_Vendor_Order_Page extends WP_List_Table
{

	public $index;


	/**
	 * __construct function.
	 *
	 * @access public
	 */
	function __construct()
	{
		global $status, $page;

		$this->index = 0;

		//Set parent defaults
		parent::__construct( array(
								  'singular' => 'order',
								  'plural'   => 'orders',
								  'ajax'     => false
							 ) );
	}


	/**
	 * column_default function.
	 *
	 * @access public
	 *
	 * @param unknown $item
	 * @param mixed   $column_name
	 *
	 * @return unknown
	 */
	function column_default( $item, $column_name )
	{
		global $wpdb;

		switch ( $column_name ) {
			case 'order_id' :
				return $item->order_id;
			case 'customer' : 
				return $item->customer; 
			case 'products' :
				return $item->products; 
			case 'total' : 
				return $item->total; 
			case 'date' : 
				return $item->date; 
			case 'status' : 
				return $item->status;
		}
	}


	/**
	 * column_cb function.
	 *
	 * @access public
	 *
	 * @param mixed $item
	 *
	 * @return unknown
	 */
	function column_cb( $item )
	{
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			/*$1%s*/
			'order_id',
			/*$2%s*/
			$item->order_id
		);
	}


	/**
	 * get_columns function.
	 *
	 * @access public
	 * @return unknown
	 */
	function get_columns()
	{
		$columns = array(
			'cb'        => '<input type="checkbox" />',
			'order_id'  => __( 'Order ID', 'wcvendors' ),
			'customer'  => __( 'Customer', 'wcvendors' ),
			'products'  => __( 'Products', 'wcvendors' ),
			'total' 	=> __( 'Total', 'wcvendors' ),
			'date'      => __( 'Date', 'wcvendors' ),
			'status'    => __( 'Shipped', 'wcvendors' ),
		);

		return $columns;
	}


	/**
	 * get_sortable_columns function.
	 *
	 * @access public
	 * @return unknown
	 */
	function get_sortable_columns()
	{
		$sortable_columns = array(
			'order_id'  	=> array( 'order_id', false ),
			'total'  		=> array( 'total', false ),
			'status'     	=> array( 'status', false ),
		);

		return $sortable_columns;
	}


	/**
	 * Get bulk actions
	 *
	 * @return unknown
	 */
	function get_bulk_actions()
	{
		$actions = array(
			'mark_shipped'     =>  apply_filters( 'wcvendors_mark_shipped_label', __( 'Mark shipped', 'wcvendors' ) ),
		);

		return $actions;
	}


	/**
	 * Process bulk actions
	 *
	 * @return unknown
	 */
	function process_bulk_action()
	{
		if ( !isset( $_GET[ 'order_id' ] ) ) return;

		$items = array_map( 'intval', $_GET[ 'order_id' ] );

		switch ( $this->current_action() ) {
			case 'mark_shipped':

				$result = $this->mark_shipped( $items );

				if ( $result )
					echo '<div class="updated"><p>' . __( 'Orders marked shipped.', 'wcvendors' ) . '</p></div>';
				break;

			default:
				// code...
				break;
		}

	}


	/**
	 *  Mark orders as shipped 
	 *
	 * @param unknown $ids (optional)
	 *
	 * @return unknown
	 */
	public function mark_shipped( $ids = array() )
	{
		global $woocommerce;

		$user_id = get_current_user_id();

		if ( !empty( $ids ) ) {
			foreach ($ids as $order_id ) {
				$shippers = (array) get_post_meta( $order_id, 'wc_pv_shipped', true );
				if( !in_array($user_id, $shippers)) {
					$shippers[] = $user_id;
					$mails = $woocommerce->mailer()->get_emails();
					if ( !empty( $mails ) ) {
						$mails[ 'WC_Email_Notify_Shipped' ]->trigger( $order_id, $user_id );
					}
					do_action('wcvendors_vendor_ship', $order_id, $user_id);
				}
				update_post_meta( $order_id, 'wc_pv_shipped', $shippers );
			}
			return true; 
		}
		return false; 
	}


	function get_orders() { 

		$user_id = get_current_user_id(); 

		$orders = array(); 


		$vendor_products = WCV_Queries::get_commission_products( $user_id );
		$products = array();

		foreach ($vendor_products as $_product) {
			$products[] = $_product->ID;
		}

		$_orders   = WCV_Queries::get_orders_for_products( $products );
		
		if (!empty( $_orders ) ) { 
			foreach ( $_orders as $order ) {

				$order = new WC_Order( $order->order_id );
				$valid_items = WCV_Queries::get_products_for_order( $order->id );
				$valid = array();

				$items = $order->get_items();

				foreach ($items as $key => $value) {
					if ( in_array($value['variation_id'], $valid_items) || in_array($value['product_id'], $valid_items)) {
						$valid[] = $value;
					}
				}

				$products = ''; 

				foreach ($valid as $key => $item) { 
							$item_meta = new WC_Order_Item_Meta( $item[ 'item_meta' ] );
							// $item_meta = $item_meta->display( false, true ); 
							$item_meta = $item_meta->get_formatted( ); 
							$products .= '<strong>'. $item['qty'] . ' x ' . $item['name'] . '</strong><br />'; 
							foreach ($item_meta as $key => $meta) {
								// Remove the sold by meta key for display 
								if (strtolower($key) != 'sold by' ) $products .= $meta[ 'label' ] .' : ' . $meta[ 'value' ]. '<br />'; 
							}
				}

				$shippers = (array) get_post_meta( $order->id, 'wc_pv_shipped', true );
				$shipped = in_array($user_id, $shippers) ? 'Yes' : 'No' ; 

				$sum = WCV_Queries::sum_for_orders( array( $order->id ), array('vendor_id' =>get_current_user_id() ) ); 
				$total = $sum[0]->line_total; 

				$order_items = array(); 
				$order_items[ 'order_id' ] 	= $order->id;
				$order_items[ 'customer' ] 	= $order->get_formatted_shipping_address();
				$order_items[ 'products' ] 	= $products; 
				$order_items[ 'total' ] 	= woocommerce_price( $total );
				$order_items[ 'date' ] 		= date_i18n( wc_date_format(), strtotime( $order->order_date ) ); 
				$order_items[ 'status' ] 	= $shipped;

				$orders[] = (object) $order_items; 
			}
		}
		return $orders; 

	}



	/**
	 * prepare_items function.
	 *
	 * @access public
	 */
	function prepare_items()
	{

		
		/**
		 * Init column headers
		 */
		$this->_column_headers = $this->get_column_info();


		/**
		 * Process bulk actions
		 */
		$this->process_bulk_action();

		/**
		 * Get items
		 */
		
		$this->items = $this->get_orders();

		/**
		 * Pagination
		 */
	}


}