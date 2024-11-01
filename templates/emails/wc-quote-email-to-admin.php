<?php
/**
 * Template for email to admin.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/wcquote/emails/wc-quote-email-to-admin.php.
 */

defined('ABSPATH') || exit;

/**
 * Action wc_quote_email_header
 * 
 * @hooked WC_QUOTE_Email_Controller::email_header() Output the email header
 * 
 * @since 1.0
**/
do_action('wc_quote_email_header', $email_heading, $email); ?>


<p>
	<?php
	echo wp_kses_post(
		str_replace(
			array( '{user_name}', '{quote_id}' ),
			array( $user_name, $quote_id ),
			/**
			* Filter wc_quote_email_text
			* 
			* @since 1.0
			**/
				wpautop(wptexturize(apply_filters('wc_quote_email_text', $email_message)))
		)
	);
	?>
</p>
<?php

/**
 * Action wc_quote_email_customer_details
 * 
 * @hooked WC_QUOTE_Email_Controller::customer_details() Shows customer details
 * 
 * @hooked WC_QUOTE_Email_Controller::email_address() Shows email address 
 * 
 * @since 1.0
 **/
do_action('wc_quote_email_customer_details', $quote_id, $email);

/**
 * Action wc_quote_email_after_customer_details
 * 
 * @since 1.0
**/
do_action('wc_quote_email_after_customer_details', $quote_id, $email);

/**
 * Action wc_quote_email_quote_details
 * 
 * @hooked WC_QUOTE_Email_Controller::order_details() Shows the order details table.
 * 
 * @since 1.0
**/
do_action('wc_quote_email_quote_details', $quote_id, $email);

/**
 * Action wc_quote_email_quote_meta
 * 
 * @hooked WC_QUOTE_Email_Controller::order_meta() Shows order meta data.
 * 
 * @since 1.0
**/
do_action('wc_quote_email_quote_meta', $quote_id, $email);

/**
 * Action wc_quote_email_footer
 * 
 * @hooked WC_QUOTE_Email_Controller::email_footer() Output the email footer
 * 
 * @since 1.0
**/
do_action('wc_quote_email_footer', $email);
