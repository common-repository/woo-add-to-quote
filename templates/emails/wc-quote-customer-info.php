<?php
/**
 * Customer information table for email.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/wcquote/emails/wc-quote-customer-info.php.
 */

defined('ABSPATH') || exit;
/**
 * Action wc_quote_before_email_customer_details
 * 
 * @since 1.0
**/
do_action('wc_quote_before_email_customer_details');

?>

<table width="100%" cellspacing="0" cellpadding="10" border="0">
	<tbody>
		<?php 
		foreach ( $customer_info as $key => $info ) : 
			$info['label'] = __($info['label'], 'wc-quote-lite');
			?>
			<tr>
				<th width="30%" style="padding: 12px 0px 12px 0px; text-align: left;">
			<?php /* translators: %s: Label */ ?>
			<?php printf(esc_html__('%s:', 'wc-quote-lite'), esc_html($info['label'])); ?>
				</th>
				<td style="padding: 12px 0px 12px 0px; color: #96588a;font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; font-size: 18px; font-weight: bold;line-height: 130%; margin: 0 0 18px; text-align: left;">
			<?php /* translators: %s: Value */ ?>
			<?php echo wp_kses_post($info['value']); ?>
				</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>


