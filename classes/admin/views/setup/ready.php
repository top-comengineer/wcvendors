<?php
/**
 * Admin View: Step One
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<h1><?php esc_html_e( 'Your marketplace is ready!', 'wc-vendors' ); ?></h1>

<div class="wcvendors-message wcvendors-newsletter">
<script type='text/javascript' src='//s3.amazonaws.com/downloads.mailchimp.com/js/mc-validate.js'></script><script type='text/javascript'>(function($) {window.fnames = new Array(); window.ftypes = new Array();fnames[0]='EMAIL';ftypes[0]='email';fnames[1]='FNAME';ftypes[1]='text';fnames[2]='LNAME';ftypes[2]='text';fnames[3]='ADDRESS';ftypes[3]='address';fnames[4]='PHONE';ftypes[4]='phone';}(jQuery));var $mcj = jQuery.noConflict(true);</script>
<p><?php esc_html_e( 'Subscribe to our newsletter! Get product updates, marketplace tips, information and more.', 'wc-vendors' ); ?></p>
<iframe id="fluentform" scrolling="no" width="100%" loading="lazy" height="200px" style="min-height: 200px;width: 100%" frameborder="0" src="https://www.wcvendors.com/?ff_landing=3&form=subscribe_wc-vendors-marketplace&embedded=1" onload="this.style.height=(this.contentWindow.document.body.scrollHeight+40)+'px';"></iframe>
</div>
<h4 class="help-title"><?php _e( 'Need Help?', 'wc-vendors' ); ?></h4>
<p class="next-steps-help-text"><?php echo wp_kses_post( $help_text ); ?></p>
