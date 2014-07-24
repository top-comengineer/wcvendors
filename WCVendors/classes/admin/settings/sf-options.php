<?php
$options = array();

$options[ ] = array( 'name' => __( 'General', 'wc_product_vendor' ), 'type' => 'heading' );
$options[ ] = array( 'name' => __( 'General options', 'wc_product_vendor' ), 'type' => 'title', 'desc' => __( '', 'wc_product_vendor' ) );

$options[ ] = array(
	'name'     => __( 'Default commission (%)', 'wc_product_vendor' ),
	'desc'     => __( 'The default rate the vendor receives for each product. If a product has a commission rate already set, this value will be ignored for that product.', 'wc_product_vendor' ),
	'id'       => 'default_commission',
	'css'      => 'width:70px;',
	'type'     => 'number',
	'restrict' => array(
		'min' => 0,
		'max' => 100
	)
);

$options[ ] = array(
	'name' => __( 'Registration', 'wc_product_vendor' ),
	'desc' => __( 'Allow users or guests to apply to become a vendor', 'wc_product_vendor' ),
	'tip'  => __( 'This will show a checkbox on the My Account page\'s registration form asking if the user would like to apply to be a vendor. Also, on the Vendor Dashboard, users can apply to become a vendor.', 'wc_product_vendor' ),
	'id'   => 'show_vendor_registration',
	'type' => 'checkbox',
	'std'  => true,
);

$options[ ] = array(
	'desc' => __( 'Approve vendor applications manually', 'wc_product_vendor' ),
	'tip'  => __( 'With this unchecked, all vendor applications are automatically accepted. Otherwise, you must approve each manually.', 'wc_product_vendor' ),
	'id'   => 'manual_vendor_registration',
	'type' => 'checkbox',
	'std'  => true,
);

$options[ ] = array(
	'name' => __( 'Taxes', 'wc_product_vendor' ),
	'desc' => __( 'Give vendors any tax collected per-product', 'wc_product_vendor' ),
	'tip'  => __( 'The tax collected on a vendor\'s product will be given to him in its entirety', 'wc_product_vendor' ),
	'id'   => 'give_tax',
	'type' => 'checkbox',
	'std'  => false,
);

$options[ ] = array( 'name' => __( 'Shop options', 'wc_product_vendor' ), 'type' => 'title', 'desc' => __( '', 'wc_product_vendor' ) );

$options[ ] = array(
	'name' => __( 'Shop HTML', 'wc_product_vendor' ),
	'desc' => __( 'Enable HTML for a vendor\'s shop description by default', 'wc_product_vendor' ),
	'id'   => 'shop_html_enabled',
	'type' => 'checkbox',
	'std'  => true,
);

$options[ ] = array(
	'name' => __( 'Vendor shop page', 'wc_product_vendor' ),
	'desc' => __( 'Eg: <code>yoursite.com/[your_setting_here]/[vendor_name_here]</code>', 'wc_product_vendor' ),
	'id'   => 'vendor_shop_permalink',
	'type' => 'text',
	'std'  => 'vendors/',
);

$options[ ] = array( 'name' => __( 'Products', 'wc_product_vendor' ), 'type' => 'heading' );
$options[ ] = array( 'name' => __( 'Product Add Page', 'wc_product_vendor' ), 'type' => 'title', 'desc' => __( 'Configure what to hide from all vendors when adding a product', 'wc_product_vendor' ) );

$options[ ] = array(
	'name'     => __( 'Left side panel', 'wc_product_vendor' ),
	'desc'     => __( 'Hide these areas of the add product page for vendors', 'wc_product_vendor' ),
	'id'       => 'hide_product_panel',
	'options'  => array(
		'inventory'      => 'Inventory',
		'shipping'       => 'Shipping',
		'linked_product' => 'Linked Products',
		'attributes'     => 'Attributes',
		'advanced'       => 'Advanced',
	),
	'type'     => 'checkbox',
	'multiple' => true,
);

$options[ ] = array(
	'name'     => __( 'Types', 'wc_product_vendor' ),
	'desc'     => __( 'Hide these product types from the vendor', 'wc_product_vendor' ),
	'id'       => 'hide_product_types',
	'options'  => array(
		'simple'   => 'Simple',
		'variable' => 'Variable',
		'grouped'  => 'Grouped',
		'external' => 'External / affiliate',
	),
	'type'     => 'checkbox',
	'multiple' => true,
);

$options[ ] = array(
	'name'     => __( 'Type options', 'wc_product_vendor' ),
	'desc'     => __( 'Hide these product options from the vendor', 'wc_product_vendor' ),
	'id'       => 'hide_product_type_options',
	'options'  => array(
		'virtual'      => 'Virtual',
		'downloadable' => 'Downloadable',
	),
	'type'     => 'checkbox',
	'multiple' => true,
);

$options[ ] = array(
	'name'     => __( 'Miscellaneous', 'wc_product_vendor' ),
	'id'       => 'hide_product_misc',
	'options'  => array(
		'taxes' => 'Taxes',
		'sku'   => 'SKU',
	),
	'type'     => 'checkbox',
	'multiple' => true,
);

$options[ ] = array(
	'name' => __( 'Stylesheet', 'wc_product_vendor' ),
	'desc' => __( 'You can add CSS in this textarea, which will be loaded on the product add/edit page for vendors.', 'wc_product_vendor' ),
	'id'   => 'product_page_css',
	'type' => 'textarea',
);


$options[ ] = array( 'name' => __( 'Capabilities', 'wc_product_vendor' ), 'type' => 'heading', 'id' => 'capabilities' );
$options[ ] = array( 'name' => __( 'Permissions', 'wc_product_vendor' ), 'id' => 'permissions', 'type' => 'title', 'desc' => __( 'General permissions used around the shop', 'wc_product_vendor' ) );

$options[ ] = array(
	'name' => __( 'Orders', 'wc_product_vendor' ),
	'desc' => __( 'View orders', 'wc_product_vendor' ),
	'tip'  => __( 'Show customer details such as email, address, name, etc, for each order', 'wc_product_vendor' ),
	'id'   => 'can_show_orders',
	'type' => 'checkbox',
	'std'  => true,
);

$options[ ] = array(
	'desc' => __( 'View comments', 'wc_product_vendor' ),
	'tip'  => __( 'View all vendor comments for an order on the frontend', 'wc_product_vendor' ),
	'id'   => 'can_view_order_comments',
	'type' => 'checkbox',
	'std'  => true,
);

$options[ ] = array(
	'desc' => __( 'Submit comments', 'wc_product_vendor' ),
	'tip'  => __( 'Submit comments for an order on the frontend. Eg, tracking ID for a product', 'wc_product_vendor' ),
	'id'   => 'can_submit_order_comments',
	'type' => 'checkbox',
	'std'  => true,
);

$options[ ] = array(
	'desc' => __( 'View email addresses', 'wc_product_vendor' ),
	'tip'  => __( 'While viewing order details on the frontend, you can disable or enable email addresses', 'wc_product_vendor' ),
	'id'   => 'can_view_order_emails',
	'type' => 'checkbox',
	'std'  => true,
);

$options[ ] = array(
	'desc' => __( 'Export a CSV file of orders for a product', 'wc_product_vendor' ),
	'tip'  => __( 'Vendors could export orders for a product on the frontend', 'wc_product_vendor' ),
	'id'   => 'can_export_csv',
	'type' => 'checkbox',
	'std'  => true,
);

$options[ ] = array(
	'name' => __( 'Reports', 'wc_product_vendor' ),
	'desc' => __( 'View backend sales reports', 'wc_product_vendor' ),
	'tip'  => __( 'Graphs and tables via the Reports page in backend. The reports will only display sales data that pertain to their products', 'wc_product_vendor' ),
	'id'   => 'can_view_backend_reports',
	'type' => 'checkbox',
	'std'  => true,
);

$options[ ] = array(
	'desc' => __( 'View Frontend sales reports', 'wc_product_vendor' ),
	'tip'  => __( 'Sales table on the frontend on the My Account page. The table will only display sales data that pertain to their products', 'wc_product_vendor' ),
	'id'   => 'can_view_frontend_reports',
	'type' => 'checkbox',
	'std'  => true,
);

$options[ ] = array(
	'name' => __( 'Products', 'wc_product_vendor' ),
	'desc' => __( 'Submit products', 'wc_product_vendor' ),
	'tip'  => __( 'Vendors could submit a product through the backend, and an admin would approve or deny it', 'wc_product_vendor' ),
	'id'   => 'can_submit_products',
	'type' => 'checkbox',
	'std'  => true,
);

$options[ ] = array(
	'desc' => __( 'Edit live products', 'wc_product_vendor' ),
	'tip'  => __( 'Vendors could edit an approved product after it has already gone live. There is no approval or review after editing a live product. This could be dangerous with malicious vendors, so take caution.', 'wc_product_vendor' ),
	'id'   => 'can_edit_published_products',
	'type' => 'checkbox',
	'std'  => false,
);

$options[ ] = array(
	'desc' => __( 'Submit products live without requiring approval', 'wc_product_vendor' ),
	'tip'  => __( 'Vendors can submit products without review or approval from a shop admin. This could be dangerous with malicious vendors, so take caution.', 'wc_product_vendor' ),
	'id'   => 'can_submit_live_products',
	'type' => 'checkbox',
	'std'  => false,
);

$options[ ] = array( 'name' => __( 'Pages', 'wc_product_vendor' ), 'type' => 'heading' );
$options[ ] = array( 'name' => __( 'Page configuration', 'wc_product_vendor' ), 'type' => 'title', 'desc' => __( '', 'wc_product_vendor' ) );

$options[ ] = array(
	'name'    => __( 'Vendor dashboard', 'wc_product_vendor' ),
	'desc'    => __( 'Choose the page that has the shortcode <code>[pv_vendor_dashboard]</code><br/>By default, My Account > Vendor Dashboard should have the shortcode.', 'wc_product_vendor' ),
	'id'      => 'vendor_dashboard_page',
	'type'    => 'single_select_page',
	'select2' => true,
);

$options[ ] = array(
	'name'    => __( 'Shop settings', 'wc_product_vendor' ),
	'desc'    => __( 'Choose the page that has the shortcode <code>[pv_shop_settings]</code><br/>These are the shop settings a vendor can configure.', 'wc_product_vendor' ),
	'id'      => 'shop_settings_page',
	'type'    => 'single_select_page',
	'select2' => true,
);

$options[ ] = array(
	'name'    => __( 'Orders page', 'wc_product_vendor' ),
	'desc'    => __( 'Choose the page that has the shortcode <code>[pv_orders]</code><br/>By default, My Account > Orders should have the shortcode.', 'wc_product_vendor' ),
	'id'      => 'orders_page',
	'type'    => 'single_select_page',
	'select2' => true,
);

$options[ ] = array(
	'name'    => __( 'Vendor terms', 'wc_product_vendor' ),
	'desc'    => __( 'These terms are shown to a user when submitting an application to become a vendor.<br/>If left blank, no terms will be shown to the applicant.', 'wc_product_vendor' ),
	'id'      => 'terms_to_apply_page',
	'type'    => 'single_select_page',
	'select2' => true,
);

$total_due = 0;
if ( !empty( $_GET[ 'tab' ] ) && $_GET[ 'tab' ] == 'payments' ) {
	global $wpdb;

	$table_name = $wpdb->prefix . "pv_commission";
	$query      = "SELECT sum(total_due + total_shipping + tax) as total
				FROM `{$table_name}`
				WHERE status = %s";
	$results    = $wpdb->get_results( $wpdb->prepare( $query, 'due' ) );

	$total_due = array_shift( $results )->total;
}
$options[ ] = array( 'name' => __( 'Payments', 'wc_product_vendor' ), 'type' => 'heading' );
$options[ ] = array(
	'name' => __( 'User payments', 'wc_product_vendor' ), 'type' => 'title', 'desc' =>
		sprintf( __( 'Total commission currently due: %s. <a href="%s">View details</a>.', 'wc_product_vendor' ), !function_exists( 'woocommerce_price' ) ? $total_due : woocommerce_price( $total_due ), '?page=pv_admin_commissions' ) .
		'<br/><br/>' . sprintf( __( 'Make sure you update your PayPal Adaptive Payments settings <a href="%s">here</a>.', 'wc_product_vendor' ), 'admin.php?page=woocommerce_settings&tab=payment_gateways&section=WC_PaypalAP' )
);

$options[ ] = array(
	'name' => __( 'Instant pay', 'wc_product_vendor' ),
	'desc' => __( 'Instantly pay vendors their commission when an order is made', 'wc_product_vendor' ),
	'tip'  => __( 'For this to work, customers must checkout with the PayPal Adaptive Payments gateway. Using other gateways will not pay vendors instantly', 'wc_product_vendor' ),
	'id'   => 'instapay',
	'type' => 'checkbox',
	'std'  => true,
);

$options[ ] = array(
	'name'    => __( 'Payment schedule', 'wc_product_vendor' ),
	'desc'    => __( 'Note: Schedule will only work if instant pay is unchecked', 'wc_product_vendor' ),
	'id'      => 'schedule',
	'type'    => 'radio',
	'std'     => 'manual',
	'options' => array(
		'weekly'   => __( 'Weekly', 'wc_product_vendor' ),
		'biweekly' => __( 'Biweekly', 'wc_product_vendor' ),
		'monthly'  => __( 'Monthly', 'wc_product_vendor' ),
		'manual'   => __( 'Manual', 'wc_product_vendor' ),
		'now'      => '<span style="color:green;"><strong>' . __( 'Now', 'wc_product_vendor' ) . '</strong></span>',
	)
);

$options[ ] = array(
	'name' => __( 'Email notification', 'wc_product_vendor' ),
	'desc' => __( 'Send the WooCommerce admin an email each time a payment has been made via the payment schedule options above', 'wc_product_vendor' ),
	'id'   => 'mail_mass_pay_results',
	'type' => 'checkbox',
	'std'  => true,
);
