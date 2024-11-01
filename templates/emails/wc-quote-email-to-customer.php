<?php
/**
 * Template for email to customer.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/wcquote/emails/wc-quote-email-to-customer.php.
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
 * @hooked WC_QUOTE_Email_Controller::quote_details() Shows the quote details table.
 *
 * @since 1.0
 **/
do_action('wc_quote_email_quote_details', $quote_id, $email);

/**
 * Action wc_quote_email_quote_meta
 * 
 * @hooked WC_QUOTE_Email_Controller::quote_meta() Shows quote meta data.
 * 
 * @since 1.0
 */
do_action('wc_quote_email_quote_meta', $quote_id, $email);


$user_show_link  = isset($this->quote_settings['email'][ $quote_status . '_user_show_link' ]) ? $this->quote_settings['email'][ $quote_status . '_user_show_link' ] : '0';

$nonce = wp_create_nonce('wc_quote_status_nonce');

if ('1' === $user_show_link ) :

	$accept_link = wc_get_cart_url() . '?status=quote_accepted&accept_nonce=' . $nonce . '&quote_id=' . base64_encode($quote_id);
	$reject_link = wc_get_cart_url() . '?status=quote_rejected&reject_nonce=' . $nonce . '&quote_id=' . base64_encode($quote_id);
	?>

<table width="100%" cellspacing="0" cellpadding="10" border="0" id="template_footer">
	<tbody>
		<tr>
			<th width="30%" style="padding: 12px 0px 12px 0px; text-align: left;">
				<a style="padding: 12px 0px 12px 0px; color: #96588a;font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; font-size: 18px; font-weight: bold;line-height: 130%; margin: 0 0 18px; text-align: left;" href="<?php echo esc_url($accept_link); ?>"><?php echo esc_html__('Accept & Checkout', 'wc-quote-lite'); ?></a>
			</th>
			<td style="padding: 12px 0px 12px 0px; color: #96588a;font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; font-size: 18px; font-weight: bold;line-height: 130%; margin: 0 0 18px; text-align: left;">
				<a style="padding: 12px 0px 12px 0px; color: #96588a;font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; font-size: 18px; font-weight: bold;line-height: 130%; margin: 0 0 18px; text-align: left;" href="<?php echo esc_url($reject_link); ?>"><?php echo esc_html__('Reject', 'wc-quote-lite'); ?></a>
			</td>
		</tr>
	</tbody>
</table>
	<?php 
endif; 

/**
 * Action wc_quote_email_footer
 * 
 * @hooked WC_QUOTE_Email_Controller::email_footer() Output the email footer
 * 
 * @since 1.0
 */
do_action('wc_quote_email_footer', $email);
