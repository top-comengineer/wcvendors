<?php

/**
 * Vendor Signup
 *
 * This file is used to output the vendor signup options on the wordpress login form.
 *
 * @link       http://www.wcvendors.com
 * @since      1.9.0
 *
 * @package    WCVendors
 * @subpackage WCVendors/classes/front/signup/views
 */

?>

<?php do_action( 'wcvendors_login_apply_for_vendor_before' ); ?>

<p>
	<label for="apply_for_vendor">
		<input class="input-checkbox" id="apply_for_vendor" <?php checked( isset( $_POST[ 'apply_for_vendor' ] ), true ) ?> type="checkbox" name="apply_for_vendor" value="1"/>
		<?php echo apply_filters('wcvendors_vendor_registration_checkbox', __( 'Apply to become a vendor? ', 'wc-vendors' )); ?>
	</label>
	<br />
</p>

<?php do_action( 'wcvendors_login_apply_for_vendor_after' ); ?>

<?php if ( $this->terms_page ) : ?>

	<?php do_action( 'wcvendors_login_agree_to_terms_before' ); ?>

	<p class="agree-to-terms-container" style="display:none;">
		<label for="agree_to_terms">
			<input class="input-checkbox" id="agree_to_terms" <?php checked( isset( $_POST[ 'agree_to_terms' ] ), true ); ?> type="checkbox" name="agree_to_terms" value="1"/>
			<?php apply_filters( 'wcvendors_vendor_registration_terms', printf(  __( 'I have read and accepted the <a target="top" href="%s">terms and conditions</a>.', 'wc-vendors' ), get_permalink( $this->terms_page ) ) ); ?>
		</label>
	</p>

	<?php do_action( 'wcvendors_login_agree_to_terms_after' ); ?>


	<script type="text/javascript">

		var error_message = "<?php _e( 'Please agree to the terms and conditions', 'wc-vendors'); ?>";

		jQuery( function( $ ){

			jQuery( "#apply_for_vendor" ).change( function() {
			    if ( this.checked ) {
			        jQuery('.agree-to-terms-container').show();
			    } else {
			    	jQuery('.agree-to-terms-container').hide();
			    }
			});

			$( 'form.register').on( 'submit', function ( e ){
				if (  jQuery('#agree_to_terms').is(':visible') && ! jQuery('#agree_to_terms').is(':checked') ) {
					e.preventDefault();
				}
			} );

		});
	</script>

<?php endif; ?>

<div class="clear"></div>
