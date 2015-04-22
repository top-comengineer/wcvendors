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

			if ( isset( $_POST[ 'pv_paypal' ] ) ) {
				if ( !is_email( $_POST[ 'pv_paypal' ] ) ) {
					$error_msg .=  __( 'Your PayPal address is not a valid email address.', 'wcvendors' );
					$error = true; 
				} else {
					update_user_meta( $user_id, 'pv_paypal', $_POST[ 'pv_paypal' ] );
				}
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
			case 'id' :
				return $item->id;
			case 'vendor_id' :
				$user = get_userdata( $item->vendor_id );

				return '<a href="' . admin_url( 'user-edit.php?user_id=' . $item->vendor_id ) . '">' . WCV_Vendors::get_vendor_shop_name( $item->vendor_id ) . '</a>';
			case 'total_due' :
				return woocommerce_price( $item->total_due + $item->total_shipping + $item->tax );
			case 'product_id' :
				$parent = get_post_ancestors( $item->product_id );
				$product_id = $parent ? $parent[ 0 ] : $item->product_id;
				return '<a href="' . admin_url( 'post.php?post=' . $product_id . '&action=edit' ) . '">' . get_the_title( $item->product_id ) . '</a>';
			case 'order_id' :
				return '<a href="' . admin_url( 'post.php?post=' . $item->order_id . '&action=edit' ) . '">' . $item->order_id . '</a>';
			case 'status' :
				return $item->status;
			case 'time' :
				return date_i18n( get_option( 'date_format' ), strtotime( $item->time ) );
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
			'id',
			/*$2%s*/
			$item->id
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
			'total_due' => __( 'Total', 'wcvendors' ),
			'time'      => __( 'Date', 'wcvendors' ),
			'status'    => __( 'Status', 'wcvendors' ),
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
			'order_id'   => array( 'order_id', false ),
			'total_due'  => array( 'total_due', false ),
			'status'     => array( 'status', false ),
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
			'mark_shipped'     => __( 'Mark shipped', 'wcvendors' ),
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
		if ( !isset( $_GET[ 'id' ] ) ) return;

		$items = array_map( 'intval', $_GET[ 'id' ] );
		$ids   = implode( ',', $items );

		switch ( $this->current_action() ) {
			case 'mark_shipped':

				$result = $this->mark_shipped( $ids );

				if ( $result )
					echo '<div class="updated"><p>' . __( 'Orders marked shipped.', 'wcvendors' ) . '</p></div>';
				break;
				
			default:
				// code...
				break;
		}

	}


	/**
	 *
	 *
	 * @param unknown $ids (optional)
	 *
	 * @return unknown
	 */
	public function mark_paid( $ids = array() )
	{
		global $wpdb;

		$table_name = $wpdb->prefix . "pv_commission";

		$query  = "UPDATE `{$table_name}` SET `status` = 'paid' WHERE id IN ($ids) AND `status` = 'due'";
		$result = $wpdb->query( $query );

		return $result;
	}


	/**
	 *
	 *
	 * @param unknown $ids (optional)
	 *
	 * @return unknown
	 */
	public function mark_reversed( $ids = array() )
	{
		global $wpdb;

		$table_name = $wpdb->prefix . "pv_commission";

		$query  = "UPDATE `{$table_name}` SET `status` = 'reversed' WHERE id IN ($ids) AND `status` = 'due'";
		$result = $wpdb->query( $query );

		return $result;
	}


	/**
	 *
	 *
	 * @param unknown $ids (optional)
	 *
	 * @return unknown
	 */
	public function mark_due( $ids = array() )
	{
		global $wpdb;

		$table_name = $wpdb->prefix . "pv_commission";

		$query  = "UPDATE `{$table_name}` SET `status` = 'due' WHERE id IN ($ids)";
		$result = $wpdb->query( $query );

		return $result;
	}


	/**
	 * prepare_items function.
	 *
	 * @access public
	 */
	function prepare_items()
	{
		global $wpdb;

		$per_page     = $this->get_items_per_page( 'commission_per_page', 10 );
		$current_page = $this->get_pagenum();

		$orderby = !empty( $_REQUEST[ 'orderby' ] ) ? esc_attr( $_REQUEST[ 'orderby' ] ) : 'time';
		$order   = ( !empty( $_REQUEST[ 'order' ] ) && $_REQUEST[ 'order' ] == 'asc' ) ? 'ASC' : 'DESC';
		$com_status = !empty( $_REQUEST[ 'com_status' ] ) ? esc_attr( $_REQUEST[ 'com_status' ] ) : '';
		$status_sql = '';
		$time_sql = ''; 

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
		$sql = "SELECT COUNT(id) FROM {$wpdb->prefix}pv_commission";

		if ( !empty( $_GET[ 'm' ] ) ) {

			$year  = substr( $_GET[ 'm' ], 0, 4 );
			$month = substr( $_GET[ 'm' ], 4, 2 );

			$time_sql = "
				WHERE MONTH(`time`) = '$month'
				AND YEAR(`time`) = '$year'
			";

			$sql .= $time_sql;
		}

		if ( !empty( $_GET[ 'com_status' ] ) ) { 

			if ( $time_sql == '' ) { 
				$status_sql = " 
				WHERE status = '$com_status'
				"; 
			} else { 
				$status_sql = " 
				AND status = '$com_status'
				";
			}
			
			$sql .= $status_sql; 
		}

		$max = $wpdb->get_var( $sql );

		$sql = "
			SELECT * FROM {$wpdb->prefix}pv_commission
		";

		if ( !empty( $_GET[ 'm' ] ) ) {
			$sql .= $time_sql;
		}

		if ( !empty( $_GET['com_status'] ) ) { 
			$sql .= $status_sql;
		}

		$offset = ( $current_page - 1 ) * $per_page; 


		$sql .= "
			ORDER BY `{$orderby}` {$order}
			LIMIT {$offset}, {$per_page}
		";

		// $this->items = $wpdb->get_results( $wpdb->prepare( $sql, ( $current_page - 1 ) * $per_page, $per_page ) );
		$this->items = $wpdb->get_results( $sql );

		/**
		 * Pagination
		 */
		$this->set_pagination_args( array(
										 'total_items' => $max,
										 'per_page'    => $per_page,
										 'total_pages' => ceil( $max / $per_page )
									) );
	}


}