<?php
/**
 * Customer information table for email.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/wcquote/front/quote-totals-table.php.
 */

defined('ABSPATH') || exit;

if (! isset($wc_quote) ) {
	$wc_quote = new WC_QUOTE_Process();
}


$quote_totals = $wc_quote->get_calculated_totals(wc()->session->get('wc_quotes'));
$quote_subtotal = isset($quote_totals['_subtotal']) ? $quote_totals['_subtotal'] : 0;
$vat_total      = isset($quote_totals['_tax_total']) ? $quote_totals['_tax_total'] : 0;
$quote_total    = isset($quote_totals['_total']) ? $quote_totals['_total'] : 0;
$offered_total  = isset($quote_totals['_offered_total']) ? $quote_totals['_offered_total'] : 0;


?>
<?php if ('1' === $price_display || '1' === $of_price_display ) : ?>
	<table cellspacing="0" class="shop_table shop_table_responsive table_quote_totals">
		 
	<?php if ('1' === $price_display ) : ?>
			<tr class="cart-subtotal">
				<th><?php esc_html_e('Subtotal(standard)', 'wc-quote-lite'); ?></th>
				<td data-title="<?php esc_attr_e('Subtotal(standard)', 'wc-quote-lite'); ?>"><?php echo wp_kses_post(wc_price($quote_subtotal)); ?></td>
			</tr>
	<?php endif; ?>

	<?php if ('1' === $of_price_display ) : ?>
			<tr class="cart-subtotal offered">
				<th><?php esc_html_e('Subtotal(offered)', 'wc-quote-lite'); ?></th>
				<td data-title="<?php esc_attr_e('Subtotal(offered)', 'wc-quote-lite'); ?>"><?php echo wp_kses_post(wc_price($offered_total)); ?></td>
			</tr>
	<?php endif; ?>

	<?php
	if (wc_tax_enabled() && WC()->customer ) :
		$taxable_address = WC()->customer->get_taxable_address();
		$estimated_text  = '';

		if (WC()->customer->is_customer_outside_base() && ! WC()->customer->has_calculated_shipping() ) {
			/* translators: %s location. */
			$estimated_text = sprintf(' <small>' . esc_html__('(estimated for %s)', 'woocommerce') . '</small>', WC()->countries->estimated_for_prefix($taxable_address[0]) . WC()->countries->countries[ $taxable_address[0] ]);
		}
		?>
			<tr class="tax-rate">
				<th><?php echo esc_html__('Vat(standard)', 'wc-quote-lite') . wp_kses_post($estimated_text); ?></th>
				<td data-title="<?php echo esc_html__('Vat(standard)', 'wc-quote-lite'); ?>"><?php echo wp_kses_post(wc_price($vat_total)); ?></td>
			</tr>
	<?php endif; ?>

	<?php if ('1' === $price_display ) : ?>
			<tr class="order-total">
				<th><?php esc_html_e('Total(standard)', 'wc-quote-lite'); ?></th>
				<td data-title="<?php esc_attr_e('Total(standard)', 'wc-quote-lite'); ?>"><strong><?php echo wp_kses_post(wc_price($quote_total)); ?></strong></td>
			</tr>
	<?php endif; ?>


	</table>

<?php endif; ?>
