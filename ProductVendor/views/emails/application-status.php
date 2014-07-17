<?php if ( !defined( 'ABSPATH' ) ) exit; ?>

<?php do_action( 'woocommerce_email_header', $email_heading ); ?>

	<p><?php printf( __( "Hi there. This is a notification about a vendor application on %s.", 'wc_product_vendor' ), get_option( 'blogname' ) ); ?></p>

	<p>
		<?php printf( __( "Application status: %s", 'wc_product_vendor' ), $status ); ?><br/>
		<?php printf( __( "Applicant username: %s", 'wc_product_vendor' ), $user->user_login ); ?>
	</p>

<?php do_action( 'woocommerce_email_footer' ); ?>