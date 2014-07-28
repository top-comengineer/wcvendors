<?php

/**
 * Plugin Name:         WC Vendors
 * Plugin URI:          http://wcvendors.com
 * Description:         Allow vendors to sell their own products and receive a commission for each sale.
 * Author:              WC Vendors
 * Author URI:          http://wcvendors.com
 *
 * Version:             1.0.0
 * Requires at least:   3.2.1
 * Tested up to:        3.9.1
 *
 * Text Domain:         wcvendors
 * Domain Path:         /WCVendors/languages/
 *
 * @category            Plugin
 * @copyright           Copyright © 2014 WC Vendors
 * @author              WC Vendors
 * @package             WCVendors
 */


/**
 * Required functions
 */
require_once trailingslashit( dirname( __FILE__ ) ) . 'WCVendors/classes/includes/class-functions.php';

/**
 * Check if WooCommerce is active
 */
if ( is_woocommerce_activated() ) {

	/* Define an absolute path to our plugin directory. */
	if ( !defined( 'pv_plugin_dir' ) ) define( 'pv_plugin_dir', trailingslashit( dirname( __FILE__ ) ) . 'WCVendors/' );
	if ( !defined( 'pv_assets_url' ) ) define( 'pv_assets_url', trailingslashit( plugins_url( 'WCVendors/assets', __FILE__ ) ) );
	load_plugin_textdomain( 'wc_product_vendor', false, dirname( plugin_basename( __FILE__ ) ) . '/WCVendors/languages/' );


	/**
	 * Main Product Vendor class
	 *
	 * @package WCVendors
	 */
	class Product_Vendor
	{

		/**
		 * @var
		 */
		public static $pv_options;
		public static $id = 'wc_prd_vendor';

		/**
		 * Constructor.
		 */
		public function __construct()
		{
			$this->title = __( 'Product Vendor', 'wcvendors' );

			// Install & upgrade
			add_action( 'admin_init', array( $this, 'check_install' ) );
			add_action( 'admin_init', array( $this, 'maybe_flush_permalinks' ), 99 );

			add_action( 'plugins_loaded', array( $this, 'load_settings' ) );
			add_action( 'plugins_loaded', array( $this, 'include_gateways' ) );
			add_action( 'plugins_loaded', array( $this, 'include_core' ) );

			add_action( self::$id . '_options_updated', array( $this, 'option_updates' ), 10, 2 );

			// Start a PHP session, if not yet started
			if ( !session_id() ) session_start();
		}


		/**
		 *
		 */
		public function invalid_wc_version()
		{
			echo '<div class="error"><p>' . __( '<b>WC Vendors is disabled</b>. WC Vendors requires WooCommerce v2.0.1.', 'wcvendors' ) . '</p></div>';
		}


		/**
		 * Check whether install has ran before or not
		 *
		 * Run install if it hasn't.
		 *
		 * @return unknown
		 */
		public function check_install()
		{
			global $woocommerce;

			// WC 2.0.1 is required
			if ( version_compare( $woocommerce->version, '2.0.1', '<' ) ) {
				add_action( 'admin_notices', array( $this, 'invalid_wc_version' ) );
				deactivate_plugins( plugin_basename( __FILE__ ) );

				return false;
			}

			require_once pv_plugin_dir . 'classes/class-install.php';

			$this->load_settings();
			$install = new PV_Install;
			$install->init();
		}


		/**
		 * Set static $pv_options to hold options class
		 */
		public function load_settings()
		{
			if ( empty( self::$pv_options ) ) {
				require_once pv_plugin_dir . 'classes/admin/settings/classes/sf-class-settings.php';
				self::$pv_options = new SF_Settings_API( self::$id, $this->title, 'woocommerce', __FILE__ );
				self::$pv_options->load_options( pv_plugin_dir . 'classes/admin/settings/sf-options.php' );
			}
		}


		/**
		 * Include core files
		 */
		public function include_core()
		{
			require_once pv_plugin_dir . 'classes/class-queries.php';
			require_once pv_plugin_dir . 'classes/class-vendors.php';
			require_once pv_plugin_dir . 'classes/class-cron.php';
			require_once pv_plugin_dir . 'classes/class-commission.php';
			require_once pv_plugin_dir . 'classes/class-shipping.php';
			require_once pv_plugin_dir . 'classes/front/class-vendor-cart.php';
			require_once pv_plugin_dir . 'classes/front/dashboard/class-vendor-dashboard.php';
			require_once pv_plugin_dir . 'classes/front/class-vendor-shop.php';
			require_once pv_plugin_dir . 'classes/front/signup/class-vendor-signup.php';
			require_once pv_plugin_dir . 'classes/front/orders/class-orders.php';
			require_once pv_plugin_dir . 'classes/admin/emails/class-emails.php';
			require_once pv_plugin_dir . 'classes/admin/class-product-meta.php';
			require_once pv_plugin_dir . 'classes/admin/class-vendor-applicants.php';
			require_once pv_plugin_dir . 'classes/admin/class-vendor-reports.php';
			require_once pv_plugin_dir . 'classes/admin/class-admin-reports.php';
			require_once pv_plugin_dir . 'classes/admin/class-admin-users.php';
			require_once pv_plugin_dir . 'classes/admin/class-admin-page.php';

			new PV_Vendor_Shop;
			new PV_Vendor_Cart;
			new PV_Commission;
			new PV_Shipping;
			new PV_Cron;
			new PV_Orders;
			new PV_Vendor_Dashboard;
			new PV_Product_Meta;
			new PV_Vendor_Reports;
			new PV_Admin_Setup;
			new PV_Admin_Reports;
			new PV_Vendor_Applicants;
			new PV_Admin_Users;
			new PV_Emails;
			new PV_Vendor_Signup;
		}


		/**
		 * Include payment gateways
		 */
		public function include_gateways()
		{
			require_once pv_plugin_dir . 'classes/gateways/PayPal_AdvPayments/paypal_ap.php';
			require_once pv_plugin_dir . 'classes/gateways/PayPal_Masspay/class-paypal-masspay.php';
		}


		/**
		 * Do an action when options are updated
		 *
		 * @param array   $options
		 * @param unknown $tabname
		 */
		public function option_updates( $options, $tabname )
		{
			// Change the vendor role capabilities
			if ( $tabname == sanitize_title(__( 'Capabilities', 'wcvendors' )) ) {
				$can_add          = $options[ 'can_submit_products' ];
				$can_edit         = $options[ 'can_edit_published_products' ];
				$can_submit_live  = $options[ 'can_submit_live_products' ];
				$can_view_reports = $options[ 'can_view_backend_reports' ];

				$args = array(
					'assign_product_terms'      => $can_add,
					'edit_products'             => $can_add || $can_edit,
					'edit_published_products'   => $can_edit,
					'delete_published_products' => $can_edit,
					'delete_products'           => $can_edit,
					'manage_product'            => $can_add,
					'publish_products'          => $can_submit_live,
					'read'                      => true,
					'read_products'             => $can_edit || $can_add,
					'upload_files'              => true,
					'import'                    => true,
					'view_woocommerce_reports'  => $can_view_reports,
				);

				remove_role( 'vendor' );
				add_role( 'vendor', 'Vendor', $args );
			} // Update permalinks
			else if ( $tabname == sanitize_title(__( 'General', 'wcvendors' ) )) {
				$old_permalink = Product_Vendor::$pv_options->get_option( 'vendor_shop_permalink' );
				$new_permalink = $options[ 'vendor_shop_permalink' ];

				if ( $old_permalink != $new_permalink ) {
					update_option( Product_Vendor::$id . '_flush_rules', true );
				}
			}
		}


		/**
		 *
		 */
		public function maybe_flush_permalinks()
		{
			if ( get_option( Product_Vendor::$id . '_flush_rules' ) ) {
				flush_rewrite_rules();
				update_option( Product_Vendor::$id . '_flush_rules', false );
			}
		}


	}


	new Product_Vendor;

}
