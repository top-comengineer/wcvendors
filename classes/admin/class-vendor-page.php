<?php 
/**
 *  WC Vendor Page - Vendor WP-Admin Dashboard
 * 
 * @author Jamie Madden <http://wcvendors.com>
 * @package WCVendors
 */

Class WCV_Vendor_page { 

	function __construct(){ 
		// Add Shop Settings page 
		add_action( 'admin_menu', array( $this, 'settings_page_admin_menu') ); 
		// Hook into init for form processing 
		add_action( 'init', array( $this, 'save_shop_settings' ) );
	}

	function settings_page_admin_menu(){
        add_menu_page( __('Shop Settings', 'wcvendors'), __('Shop Settings', 'wcvendors'), 'manage_product', 'wcv-vendor-shopsettings', array( $this, 'settings_page' ) );
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
}