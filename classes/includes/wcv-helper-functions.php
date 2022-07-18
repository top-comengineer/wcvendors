<?php 

/** 
 * WC Vendors Helper Functions 
 * 
 */


 /**
  * PayPal Supported Currencies 
  * 
  * Reference: https://developer.paypal.com/reference/currency-codes/
  * 
  *
  * @version 2.4.3
  * @return void
  */
function wcv_paypal_currencies(){ 
   
    $paypal_currencies = apply_filters( 'wcvendors_paypal_currencies', 
        array(   
            'AUD' => __('Australian Dollar', 'wc-vendors'), 
            'BRL' => __('Brazilian Real', 'wc-vendors'), 
            'CAD' => __('Canadian Dollar', 'wc-vendors'), 
            'CNY' => __('Chinese Renmenbi', 'wc-vendors'), 
            'CZK' => __('Czech Koruna', 'wc-vendors'), 
            'DKK' => __('Danish Krone', 'wc-vendors'), 
            'EUR' => __('Euro', 'wc-vendors'), 
            'HKD' => __('Hong Kong Dollar', 'wc-vendors'), 
            'HUF' => __('Hungarian Forint', 'wc-vendors'), 
            'ILS' => __('Israeli New Shekel', 'wc-vendors'), 
            'JPY' => __('Japanese Yen', 'wc-vendors'), 
            'MYR' => __('Malaysian Ringgit', 'wc-vendors'), 
            'MXN' => __('Mexican Peso', 'wc-vendors'), 
            'TWD' => __('New Taiwan Ddollar', 'wc-vendors'), 
            'NZD' => __('New Zealand Dollar	', 'wc-vendors'), 
            'NOK' => __('Norwegian krone	', 'wc-vendors'), 
            'PHP' => __('Philippine Peso', 'wc-vendors'),  
            'PLN' => __('Polish Złoty', 'wc-vendors'), 
            'GBP' => __('Pound Sterling', 'wc-vendors'), 
            'RUB' => __('Russian Ruble', 'wc-vendors'), 
            'SGD' => __('Singapore Dollar', 'wc-vendors'), 
            'SEK' => __('Swedish Krona', 'wc-vendors'), 
            'CHF' => __('Swiss Franc', 'wc-vendors'), 
            'THB' => __('Thai Baht', 'wc-vendors'), 
            'USD' => __('United States Dollar', 'wc-vendors')
        )
    ); 

    return $paypal_currencies; 
 }
 
/**
 * PayPal wallet
 *
 * @version 2.4.3
 * @return void
 */
function wcv_paypal_wallet(){

    $paypal_wallet = apply_filters( 'wcvendors_paypal_wallet', 
        array(
            'paypal' => __('PayPal', 'wc-vendors' ), 
            'venmo' => __('Venmo', 'wc-vendors' ), 
        )
    );

    return $paypal_wallet;
 }