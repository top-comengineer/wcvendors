<h2><?php _e( 'Control Center', 'wcvendors' ); ?></h2>
<p>
	<b><?php _e( 'My shop', 'wcvendors' ); ?></b><br/>
	<a target="_TOP" href="<?php echo $shop_page; ?>"><?php echo $shop_page; ?></a>
</p>
<p>
	<b><?php _e( 'My settings', 'wcvendors' ); ?></b><br/>
	<a target="_TOP" href="<?php echo $settings_page; ?>"><?php echo $settings_page; ?></a>
</p>

<?php if ( $can_submit ) { ?>
	<p>
		<b><?php _e( 'Submit a product', 'wcvendors' ); ?></b><br/>
		<a target="_TOP" href="<?php echo $submit_link; ?>"><?php echo $submit_link; ?></a>
	</p>
<?php } ?>

<hr>