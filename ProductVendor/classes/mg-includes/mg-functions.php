<?php
/**
 * Functions used by plugins
 */
if ( !class_exists( 'MGates_Plugin_Updater' ) ) require_once 'class-mgates-plugin-updater.php';
if ( !class_exists( 'MG_Dependencies' ) ) require_once 'class-mg-dependencies.php';

/**
 * WC Detection
 * */
if ( !function_exists( 'is_woocommerce_activated' ) ) {
	function is_woocommerce_activated()
	{
		return MG_Dependencies::woocommerce_active_check();
	}
}

/**
 * JS Detection
 * */
if ( !function_exists( 'is_jigoshop_activated' ) ) {
	function is_jigoshop_activated()
	{
		return MG_Dependencies::jigoshop_active_check();
	}
}

/**
 * EDD Detection
 * */
if ( !function_exists( 'is_edd_activated' ) ) {
	function is_edd_activated()
	{
		return MG_Dependencies::edd_active_check();
	}
}
