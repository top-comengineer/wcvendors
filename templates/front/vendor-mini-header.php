<?php
/**
 *  Vendor Mini Header - Hooked into archive-product page 
 *
 * @author WCVendors
 * @package WCVendors
 * @version 1.3.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

/*
*	Template Variables available 
*   $vendor : 			For pulling additional user meta from vendor account
*   $shop_name : 		Store/Shop Name from Vendor Shop Settings
*   $shop_description : pv_shop_description (completely sanitized)
*   $vendor_id  : 		current vendor id for customization 
*   $seller_info : 		Seller Info from Vendor Shop Settings
*/ 

?>

<h1><?php echo $shop_name; ?></h1>
<div class="wcv_shop_description">
<?php echo $shop_description; ?>
</div>
