<?php

/**
 * Plugin Name:          WC Vendors
 * Plugin URI:           https://www.wcvendors.com
 * Description:          Allow vendors to sell their own products and receive a commission for each sale.
 * Author:               WC Vendors
 * Author URI:           https://www.wcvendors.com
 * GitHub Plugin URI:    https://github.com/wcvendors/wcvendors
 *
 * Version:              2.0.0
 * Requires at least:    4.4.0
 * Tested up to:         4.9.4
 * WC requires at least: 3.0.0
 * WC tested up to: 	 3.3.1
 *
 * Text Domain:         wc-vendors
 * Domain Path:         /languages/
 *
 * @category            Plugin
 * @copyright           Copyright © 2012 Matt Gates
 * @copyright           Copyright © 2018 WC Vendors
 * @author              Matt Gates, WC Vendors
 * @package             WCVendors
 * @license     		GPL2

WC Vendors is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

WC Vendors is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with WC Vendors. If not, see http://www.gnu.org/licenses/gpl-2.0.txt.

*/


/**
 *   Plugin activation hook
 */
function wcvendors_activate() {

	/**
	 *  Requires woocommerce to be installed and active
	 */
	if ( !class_exists( 'WooCommerce' ) ) {
		deactivate_plugins( plugin_basename( __FILE__ ) );
		wp_die( __( 'WC Vendors requires WooCommerce to run. Please install WooCommerce and activate before attempting to activate again.', 'wc-vendors' ) );
	}
} // wcvendors_activate()

register_activation_hook( __FILE__, 'wcvendors_activate' );


/**
 * Required functions
 */
require_once trailingslashit( dirname( __FILE__ ) ) . 'classes/includes/class-functions.php';

/**
 * Check if WooCommerce is active
 */
if ( wcv_is_woocommerce_activated() ) {

	/* Define an absolute path to our plugin directory. */
	if ( !defined( 'wcv_plugin_dir' ) ) 		define( 'wcv_plugin_dir', trailingslashit( dirname( __FILE__ ) ) );
	if ( !defined( 'wcv_assets_url' ) ) 		define( 'wcv_assets_url', trailingslashit( plugins_url( 'assets', __FILE__ ) ) );
	if ( !defined( 'wcv_plugin_base' ) ) 		define( 'wcv_plugin_base', plugin_basename( __FILE__ ) );
	if ( !defined( 'wcv_plugin_dir_path' ) )	define( 'wcv_plugin_dir_path', untrailingslashit( plugin_dir_path( __FILE__ ) ) );


	define(	'WCV_VERSION', '2.0.0' );
	define( 'WCV_ABSPATH_ADMIN', dirname( __FILE__ ) . '/classes/admin/' );

	/**
	 * Main Product Vendor class
	 *
	 * @package WCVendors
	 */
	class WC_Vendors
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

			// Load text domain
			add_action( 'plugins_loaded', array( $this, 'load_il8n' ) );

			$this->title = __( 'WC Vendors', 'wc-vendors' );

			// Install & upgrade
			add_action( 'admin_init', array( $this, 'check_install' ) );
			add_action( 'admin_init', array( $this, 'maybe_flush_permalinks' ), 99 );
			add_action( 'admin_init', array( $this, 'wcv_required_ignore_notices' ) );

			add_action( 'plugins_loaded', array( $this, 'load_settings' ) );
			add_action( 'plugins_loaded', array( $this, 'include_gateways' ) );
			add_action( 'plugins_loaded', array( $this, 'include_core' ) );
			add_action( 'init', 		  array( $this, 'include_init' ) );
			add_action( 'current_screen', array( $this, 'include_assets' ) );

			// add_filter( 'plugin_row_meta', array($this, 'plugin_row_meta'), 10, 2 );
			add_action( self::$id . '_options_updated', array( $this, 'option_updates' ), 10, 2 );

			// Start a PHP session, if not yet started then destroy if logged in or out
			add_action( 'init', 		array( $this, 'init_session'), 1 );
			add_action( 'wp_logout', 	array( $this, 'destroy_session') );
			add_action( 'wp_login', 	array( $this, 'destroy_session') );

			// add_filter( 'wc_prd_vendor_options', array( $this, 'upgrade_pro_notice' ) );
		}


		/**
		 *
		 */
		public function invalid_wc_version() {
			echo '<div class="error"><p>' . __( '<b>WC Vendors is inactive</b>. WC Vendors requires a minimum of WooCommerce 3.0.0 to operate.', 'wc-vendors' ) . '</p></div>';
		}

		/**
		 *  Start the session
		 */
		public function init_session(){

			 if ( !session_id() && is_user_logged_in() ) {
        		session_start();
    		 }

		} //init_session()

		public function destroy_session(){

			 if ( session_id() ) {
        		session_destroy();
    		 }

		} // destroy_session()


		/**
		 * Check whether install has ran before or not
		 *
		 * Run install if it hasn't.
		 *
		 * @return unknown
		 */
		public function check_install()	{

			if ( version_compare( WC_VERSION, '2.6', '<' ) ) {
				add_action( 'admin_notices', array( $this, 'invalid_wc_version' ) );
				deactivate_plugins( plugin_basename( __FILE__ ) );
				return false;
			}

			// require_once wcv_plugin_dir . 'classes/class-install.php';
			// $this->load_settings();
		}


		/**
		 * Set static $pv_options to hold options class
		 */
		public function load_settings()
		{
			if ( empty( self::$pv_options ) ) {
				require_once wcv_plugin_dir . 'classes/admin/settings/classes/sf-class-settings.php';
				self::$pv_options = new SF_Settings_API( self::$id, $this->title, 'woocommerce', __FILE__ );
				self::$pv_options->load_options( wcv_plugin_dir . 'classes/admin/settings/sf-options.php' );
			}
		}

		public function load_il8n() {
		    $locale = apply_filters( 'plugin_locale', get_locale(), 'wc-vendors' );
		    load_textdomain( 'wc-vendors', WP_LANG_DIR.'/wc-vendors/wc-vendors-'.$locale.'.mo');
			load_plugin_textdomain( 'wc-vendors', false, plugin_basename( dirname( __FILE__ ) ) . '/languages/' );

		}


		/**
		 * Include core files
		 */
		public function include_core() {

			include_once( wcv_plugin_dir . 'classes/class-install.php' );
			include_once( wcv_plugin_dir . 'classes/class-queries.php');
			include_once( wcv_plugin_dir . 'classes/class-vendors.php');
			include_once( wcv_plugin_dir . 'classes/class-cron.php');
			include_once( wcv_plugin_dir . 'classes/class-commission.php');
			include_once( wcv_plugin_dir . 'classes/class-shipping.php');
			include_once( wcv_plugin_dir . 'classes/class-vendor-order.php');
			include_once( wcv_plugin_dir . 'classes/class-vendor-post-types.php');
			include_once( wcv_plugin_dir . 'classes/front/class-vendor-cart.php');
			include_once( wcv_plugin_dir . 'classes/front/dashboard/class-vendor-dashboard.php');
			include_once( wcv_plugin_dir . 'classes/front/class-vendor-shop.php');
			include_once( wcv_plugin_dir . 'classes/front/signup/class-vendor-signup.php');
			include_once( wcv_plugin_dir . 'classes/front/orders/class-orders.php');
			include_once( wcv_plugin_dir . 'classes/admin/emails/class-emails.php');
			include_once( wcv_plugin_dir . 'classes/admin/class-vendor-applicants.php');
			include_once( wcv_plugin_dir . 'classes/admin/class-admin-reports.php');
			include_once( wcv_plugin_dir . 'classes/admin/class-wcv-commissions-page.php');
			include_once( wcv_plugin_dir . 'classes/admin/class-wcv-admin-setup.php');
			include_once( wcv_plugin_dir . 'classes/admin/class-wcv-admin-notices.php');
			include_once( wcv_plugin_dir . 'classes/admin/class-wcv-admin-settings.php');
			include_once( wcv_plugin_dir . 'classes/admin/class-admin-menus.php');
			include_once( wcv_plugin_dir . 'classes/admin/class-setup-wizard.php');
			include_once( wcv_plugin_dir . 'classes/admin/class-vendor-admin-dashboard.php');
			include_once( wcv_plugin_dir . 'classes/includes/class-wcv-shortcodes.php');
			include_once( wcv_plugin_dir . 'classes/includes/wcv-update-functions.php');


			if ( !function_exists( 'woocommerce_wp_text_input' ) && !is_admin() ) {
				include_once(WC()->plugin_path() . '/includes/admin/wc-meta-box-functions.php');
			}

			new WCV_Vendors;
			new WCV_Vendor_Shop;
			new WCV_Vendor_Cart;
			new WCV_Commission;
			new WCV_Shipping;
			new WCV_Cron;
			new WCV_Orders;
			new WCV_Vendor_Dashboard;
			new WCV_Admin_Setup;
			new WCV_Vendor_Admin_Dashboard;
			new WCV_Admin_Reports;
			new WCV_Vendor_Applicants;
			new WCV_Emails;
			new WCV_Vendor_Signup;
			new WCV_Shortcodes;
		}


		/**
		 * These need to be initlized later in loading to fix interaction with other plugins that call current_user_can at the right time.
		 *
		 * @since 1.9.4
		 * @access public
		 */
		public function include_init(){

			require_once wcv_plugin_dir . 'classes/admin/class-vendor-reports.php';
			require_once wcv_plugin_dir . 'classes/admin/class-product-meta.php';
			require_once wcv_plugin_dir . 'classes/admin/class-admin-users.php';


			new WCV_Vendor_Reports;
			new WCV_Product_Meta;
			new WCV_Admin_Users;

		} // include_init()

		/**
		*	Load plugin assets
		*/
		public function include_assets(){

			$screen = get_current_screen();

			if ( in_array( $screen->id, array( 'edit-product' ) ) ) {
				wp_enqueue_script( 'wcv_quick-edit', wcv_assets_url. 'js/wcv-admin-quick-edit.js', array('jquery') );
			}

		}


		/**
		 * Include payment gateways
		 */
		public function include_gateways()
		{
			require_once wcv_plugin_dir . 'classes/gateways/PayPal_AdvPayments/paypal_ap.php';
			require_once wcv_plugin_dir . 'classes/gateways/PayPal_Masspay/class-paypal-masspay.php';
			require_once wcv_plugin_dir . 'classes/gateways/WCV_Gateway_Test/class-wcv-gateway-test.php';
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
			if ( $tabname == sanitize_title(__( 'Capabilities', 'wc-vendors' )) ) {
				$can_add          = $options[ 'can_submit_products' ];
				$can_edit         = $options[ 'can_edit_published_products' ];
				$can_submit_live  = $options[ 'can_submit_live_products' ];

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
					'view_woocommerce_reports'  => false,
				);

				remove_role( 'vendor' );

				add_role( 'vendor', __('Vendor', 'wc-vendors'), $args );
			} // Update permalinks
			else if ( $tabname == sanitize_title(__( 'General', 'wc-vendors' ) )) {
				$old_permalink = WC_Vendors::$pv_options->get_option( 'vendor_shop_permalink' );
				$new_permalink = $options[ 'vendor_shop_permalink' ];

				if ( $old_permalink != $new_permalink ) {
					update_option( WC_Vendors::$id . '_flush_rules', true );
				}
			}

			do_action( 'wcvendors_option_updates', $options, $tabname );

		}


		/**
		 *  If the settings are updated and the vendor page link has changed update permalinks
		 *	@access public
		 *
		*/
		public function maybe_flush_permalinks()
		{
			if ( get_option( WC_Vendors::$id . '_flush_rules' ) ) {
				flush_rewrite_rules();
				update_option( WC_Vendors::$id . '_flush_rules', false );
			}
		}

		/**
		 *  Add links to plugin page to our external help site.
		 *	@param $links - links array from action
		 *	@param $file - file reference for this plugin
		 *	@access public
		 *
		 */
		public static function plugin_row_meta( $links, $file ) {

			if ( $file == wcv_plugin_base ) {

				$row_meta = array(
	                            'docs' 		=> '<a href="https://docs.wcvendors.com/?utm_source=plugin" target="_blank">'.__( 'Documentation', 'wc-vendors' ).'</a>',
	                            'pro' 		=> '<strong><a href="https://www.wcvendors.com/product/wc-vendors-pro/?utm_source=plugin&utm_campaign=upgrade_promo" target="_blank">'.__( 'Upgrade to Pro', 'wc-vendors').'</a></strong>',
	                            'support' 	=> '<a href="https://www.wcvendors.com/contact-us/?utm_source=plugin" target="_blank">'.__( 'Contact Us', 'wc-vendors' ).'</a>'
	                        );

				if ( class_exists( 'WCVendors_Pro' ) ) unset( $row_meta[ 'pro' ] );

				return array_merge( $links, $row_meta );
			}

			return (array) $links;
		}

		/**
		 * Add user meta to remember ignore notices
		 * @access public
         *
		 */
		public function wcv_required_ignore_notices(){
			global $current_user;
    		$current_user_id = $current_user->ID;

	        /* If user clicks to ignore the notice, add that to their user meta */
	        if ( isset( $_GET[ 'wcv_shop_ignore_notice' ] ) && '0' == $_GET[ 'wcv_shop_ignore_notice' ] ) {
	            add_user_meta( $current_user_id, 'wcv_shop_ignore_notice', 'true', true);
	    	}
			if ( isset($_GET['wcv_pl_ignore_notice']) && '0' == $_GET['wcv_pl_ignore_notice'] ) {
			 	add_user_meta( $current_user_id, 'wcv_pl_ignore_notice', 'true' , true);
			}

		}

		/**
		 * Class logger so that we can keep our debug and logging information cleaner
		 *
		 * @since 2.0.0
		 * @version 2.0.0
		 * @access public
		 *
		 * @param mixed - $data the data to go to the error log could be string, array or object
		 */
		public static function log( $data = '', $prefix = '' ){

			$trace 		= debug_backtrace( false, 2 );
			$caller 	= ( isset( $trace[ 1 ]['class'] ) ) ? $trace[ 1 ]['class'] : basename( $trace[ 1 ][ 'file' ] );

			if ( is_array( $data ) || is_object( $data ) ) {
				if ( $prefix ){
					error_log( '===========================' );
					error_log( $prefix );
					error_log( '===========================' );
				}
				error_log( $caller . ' : ' . print_r( $data, true ) );
			} else {
				if ( $prefix ){
					error_log( '===========================' );
					error_log( $prefix );
					error_log( '===========================' );
				}
				error_log( $caller  . ' : ' . $data );
			}

		} // log()


		public function upgrade_pro_notice( $options ){

			if ( ! class_exists( 'WCVendors_Pro' ) ){


				$options[ ] = array( 'name' => __( 'Upgrade to Pro', 'wc-vendors' ), 'type' => 'heading' );

				$options[ ] = array(
						'name' => __( 'Upgrade to WC Vendors Pro!', 'wc-vendors' ), 'type' => 'title', 'desc' =>

					sprintf( __( 'WC Vendors Pro extends your marketplace to include some of the following enhanced features
							<ul>
							 	<li>Complete front end dashboard for your vendors</li>
							 	<li>Vendor Coupons</li>
							 	<li>Vendor product management</li>
							 	<li>Vendor Order management</li>
							 	<li>Vendor ratings</li>
							 	<li>Vendors can upload their own store banners</li>
							 	<li>A comprehensive shipping system for your vendors including two systems. Flat rate and Table rate shipping.</li>
							 	<li>All default product types supported on the front end</li>
							 	<li>Custom product-edit templates</li>
							 	<li>Complete vendor signup form</li>
							</ul>
							<a href="http://www.wcvendors.com/home/comparison/?utm_source=plugin">See the complete comparison list here.</a>
							<h1><a href="https://www.wcvendors.com/product/wc-vendors-pro/?utm_source=plugin&utm_campaign=upgrade_promo">Buy Pro Today</a></h1>', 'wc-vendors' )
					),
				);
			}


			return $options;
		}

	}

	$wc_vendors = new WC_Vendors;

}
