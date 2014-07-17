<?php

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * New Order Email
 *
 * An email sent to the admin when a new order is received/paid for.
 *
 * @class    WC_Email_Approve_Vendor
 * @version  2.0.0
 * @extends  WC_Email
 * @author   WooThemes
 * @package  WooCommerce/Classes/Emails
 */


class WC_Email_Approve_Vendor extends WC_Email
{


	/**
	 * Constructor
	 */
	function __construct()
	{
		$this->id          = 'vendor_application';
		$this->title       = __( 'Vendor Application', 'wc_product_vendor' );
		$this->description = __( 'Vendor application will either be approved, denied, or pending.', 'wc_product_vendor' );

		$this->heading = __( 'Application {status}', 'wc_product_vendor' );
		$this->subject = __( '[{blogname}] Your vendor application has been {status}', 'wc_product_vendor' );

		$this->template_base  = dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/views/emails/';
		$this->template_html  = 'application-status.php';
		$this->template_plain = 'application-status.php';

		// Call parent constuctor
		parent::__construct();

		// Other settings
		$this->recipient = $this->get_option( 'recipient' );

		if ( !$this->recipient )
			$this->recipient = get_option( 'admin_email' );
	}

	/**
	 * trigger function.
	 *
	 * @access public
	 * @return void
	 *
	 * @param unknown $order_id
	 */
	function trigger( $user_id, $status )
	{
		if ( !$this->is_enabled() ) return;

		$this->find[ ]    = '{status}';
		$this->replace[ ] = $status;

		$this->status = $status;

		$this->user = get_userdata( $user_id );
		$user_email = $this->user->user_email;

		$this->send( $user_email, $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );

		if ( $status == __( 'pending', 'wc_product_vendor' ) ) {
			$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
		}
	}

	/**
	 * get_content_html function.
	 *
	 * @access public
	 * @return string
	 */
	function get_content_html()
	{
		ob_start();
		woocommerce_get_template( $this->template_html, array(
															 'status'        => $this->status,
															 'user'          => $this->user,
															 'email_heading' => $this->get_heading()
														), 'woocommerce/', $this->template_base );

		return ob_get_clean();
	}


	/**
	 * get_content_plain function.
	 *
	 * @access public
	 * @return string
	 */
	function get_content_plain()
	{
		ob_start();
		woocommerce_get_template( $this->template_plain, array(
															  'status'        => $this->status,
															  'user'          => $this->user,
															  'email_heading' => $this->get_heading()
														 ), 'woocommerce/', $this->template_base );

		return ob_get_clean();
	}


	/**
	 * Initialise Settings Form Fields
	 *
	 * @access public
	 * @return void
	 */
	function init_form_fields()
	{
		$this->form_fields = array(
			'enabled'    => array(
				'title'   => __( 'Enable/Disable', 'wc_product_vendor' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable this email notification', 'wc_product_vendor' ),
				'default' => 'yes'
			),
			'recipient'  => array(
				'title'       => __( 'Recipient(s)', 'woocommerce' ),
				'type'        => 'text',
				'description' => sprintf( __( 'Enter recipients (comma separated) for this email. Defaults to <code>%s</code>.', 'wc_product_vendor' ), esc_attr( get_option( 'admin_email' ) ) ),
				'placeholder' => '',
				'default'     => ''
			),
			'subject'    => array(
				'title'       => __( 'Subject', 'wc_product_vendor' ),
				'type'        => 'text',
				'description' => sprintf( __( 'This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', 'wc_product_vendor' ), $this->subject ),
				'placeholder' => '',
				'default'     => ''
			),
			'heading'    => array(
				'title'       => __( 'Email Heading', 'wc_product_vendor' ),
				'type'        => 'text',
				'description' => sprintf( __( 'This controls the main heading contained within the email notification. Leave blank to use the default heading: <code>%s</code>.', 'wc_product_vendor' ), $this->heading ),
				'placeholder' => '',
				'default'     => ''
			),
			'email_type' => array(
				'title'       => __( 'Email type', 'wc_product_vendor' ),
				'type'        => 'select',
				'description' => __( 'Choose which format of email to send.', 'wc_product_vendor' ),
				'default'     => 'html',
				'class'       => 'email_type',
				'options'     => array(
					'plain'     => __( 'Plain text', 'wc_product_vendor' ),
					'html'      => __( 'HTML', 'wc_product_vendor' ),
					'multipart' => __( 'Multipart', 'wc_product_vendor' ),
				)
			)
		);
	}


}
