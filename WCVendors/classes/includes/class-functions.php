<?php 

if ( !class_exists( 'WCV_Dependencies' ) ) require_once 'class-dependencies.php';

/**
 * WC Detection
 * */
if ( !function_exists( 'is_woocommerce_activated' ) ) {
	function is_woocommerce_activated()
	{
		return WCV_Dependencies::woocommerce_active_check();
	}
}

/**
 * JS Detection
 * */
if ( !function_exists( 'is_jigoshop_activated' ) ) {
	function is_jigoshop_activated()
	{
		return WCV_Dependencies::jigoshop_active_check();
	}
}

/**
 * EDD Detection
 * */
if ( !function_exists( 'is_edd_activated' ) ) {
	function is_edd_activated()
	{
		return WCV_Dependencies::edd_active_check();
	}
}

?>