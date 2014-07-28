<?php

/**
 * Shop functions for each vendor.
 *
 * @author  WC Vendors <http://wcvendors.com>
 * @package ProductVendor
 */


class WCV_Vendor_Shop
{

	public static $seller_info;


	/**
	 * init
	 */
	function __construct()
	{
		add_filter( 'product_enquiry_send_to', array( 'WCV_Vendor_Shop', 'product_enquiry_compatibility' ), 10, 2 );

		add_action( 'woocommerce_product_query', array( $this, 'vendor_shop_query' ), 10, 2 );
		add_filter( 'init', array( $this, 'add_rewrite_rules' ), 0 );

		add_action( 'woocommerce_before_main_content', array( $this, 'shop_description' ), 20 );
		add_filter( 'woocommerce_product_tabs', array( 'WCV_Vendor_Shop', 'seller_info_tab' ) );
		add_filter( 'post_type_archive_link', array( 'WCV_Vendor_Shop', 'change_archive_link' ) );
	}

	public function change_archive_link( $link )
	{
		$vendor_shop = urldecode( get_query_var( 'vendor_shop' ) );
		$vendor_id   = PV_Vendors::get_vendor_id( $vendor_shop );

		return !$vendor_id ? $link : PV_Vendors::get_vendor_shop_page( $vendor_id );
	}

	public function vendor_shop_query( $q, $that )
	{
		$vendor_shop = urldecode( get_query_var( 'vendor_shop' ) );
		$vendor_id   = PV_Vendors::get_vendor_id( $vendor_shop );

		if ( !$vendor_id ) return;
		add_filter( 'woocommerce_page_title', array( 'WCV_Vendor_Shop', 'page_title' ) );

		$q->set( 'author', $vendor_id );
	}

	public function product_enquiry_compatibility( $send_to, $product_id )
	{
		$author_id = get_post( $product_id )->post_author;
		if ( PV_Vendors::is_vendor( $author_id ) ) {
			$send_to = get_userdata( $author_id )->user_email;
		}

		return $send_to;
	}


	/**
	 *
	 *
	 * @param unknown $tabs
	 *
	 * @return unknown
	 */
	public function seller_info_tab( $tabs )
	{
		global $post;

		if ( PV_Vendors::is_vendor( $post->post_author ) ) {

			$seller_info = get_user_meta( $post->post_author, 'pv_seller_info', true );
			$has_html    = get_user_meta( $post->post_author, 'pv_shop_html_enabled', true );
			$global_html = WC_Vendors::$pv_options->get_option( 'shop_html_enabled' );

			if ( !empty( $seller_info ) ) {

				$seller_info = do_shortcode( $seller_info );
				self::$seller_info = '<div class="pv_seller_info">';
				self::$seller_info .= ( $global_html || $has_html ) ? wpautop( wptexturize( wp_kses_post( $seller_info ) ) ) : sanitize_text_field( $seller_info );
				self::$seller_info .= '</div>';

				$tabs[ 'seller_info' ] = array(
					'title'    => apply_filters( 'wcvendors_seller_info_label', __( 'Seller info', 'wcvendors' ) ),
					'priority' => 50,
					'callback' => array( 'WCV_Vendor_Shop', 'seller_info_tab_panel' ),
				);
			}
		}

		return $tabs;
	}


	/**
	 *
	 */
	public function seller_info_tab_panel()
	{
		echo self::$seller_info;
	}


	/**
	 * Show the description a vendor sets when viewing products by that vendor
	 */
	public function shop_description()
	{
		$vendor_shop = urldecode( get_query_var( 'vendor_shop' ) );
		$vendor_id   = PV_Vendors::get_vendor_id( $vendor_shop );

		if ( $vendor_id ) {
			$has_html    = get_user_meta( $vendor_id, 'pv_shop_html_enabled', true );
			$global_html = WC_Vendors::$pv_options->get_option( 'shop_html_enabled' );
			$description = do_shortcode( get_user_meta( $vendor_id, 'pv_shop_description', true ) );

			echo '<div class="pv_shop_description">';
			echo ( $global_html || $has_html ) ? wpautop( wptexturize( wp_kses_post( $description ) ) ) : sanitize_text_field( $description );
			echo '</div>';
		}
	}

	/**
	 *
	 */
	public function add_rewrite_rules()
	{
		$permalink = untrailingslashit( WC_Vendors::$pv_options->get_option( 'vendor_shop_permalink' ) );

		// Remove beginning slash
		if ( substr( $permalink, 0, 1 ) == '/' ) {
			$permalink = substr( $permalink, 1, strlen( $permalink ) );
		}

		add_rewrite_tag( '%vendor_shop%', '([^&]+)' );

		add_rewrite_rule( $permalink . '/([^/]*)/page/([0-9]+)', 'index.php?post_type=product&vendor_shop=$matches[1]&paged=$matches[2]', 'top' );
		add_rewrite_rule( $permalink . '/([^/]*)', 'index.php?post_type=product&vendor_shop=$matches[1]', 'top' );
	}


	public function page_title( $page_title = "" )
	{
		$vendor_shop = urldecode( get_query_var( 'vendor_shop' ) );
		$vendor_id   = PV_Vendors::get_vendor_id( $vendor_shop );

		return $vendor_id ? PV_Vendors::get_vendor_shop_name( $vendor_id ) : $page_title;
	}


}
