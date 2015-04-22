<?php 
/**
 *  WC Vendor Page - Vendor WP-Admin Dashboard
 * 
 * @author Jamie Madden <http://wcvendors.com>
 * @package WCVendors
 */

Class WCV_Vendor_page { 

	function __construct(){ 
		add_action( 'admin_menu', array( $this, 'settings_page_admin_menu') ); 
	}


	function settings_page_admin_menu(){
        add_menu_page( __('Shop Settings', 'wcvendors'), __('Shop Settings', 'wcvendors'), 'manage_product', 'wcv-vendor-shopsettings', array( $this, 'settings_page' ) );
	}
 
	function settings_page(){ 
		echo '<h1>Shop Settings</h1>'; 
	}

}