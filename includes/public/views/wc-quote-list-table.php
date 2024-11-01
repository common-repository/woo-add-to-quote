<?php
/**
 * Quote details in my Account.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/wcquote/front/wc-quote-list-table.php.
 */

defined('ABSPATH') || exit;
$return_shop = isset($this->quote_settings['general']['return_shop']) ? $this->quote_settings['general']['return_shop'] : '0';
$return_shop_text = isset($this->quote_settings['customization']['return_shop_text']) ? $this->quote_settings['customization']['return_shop_text'] : 'Return To Shop';
if (! empty($customer_quotes) ) {
	?>
	<table class="shop_table shop_table_responsive cart my_account_orders my_account_quotes">
		<thead>
			<tr>
				<th data-title=""><?php echo esc_html__('Quote', 'wc-quote-lite'); ?></th>
				<th><?php echo esc_html__('Status', 'wc-quote-lite'); ?></th>
				<th><?php echo esc_html__('Date', 'wc-quote-lite'); ?></th>
				<th><?php echo esc_html__('Action', 'wc-quote-lite'); ?></th>
			</tr>
		</thead>
		<tbody>
	<?php
	// phpcs:disable		
	foreach ( $customer_quotes as $quote ) {
		$quote_status = get_post($quote->ID);
	// phpcs:enable	
		?>
				<tr>
					<td data-title="ID">
						<a href="<?php echo esc_url(wc_get_endpoint_url('my-quotes', $quote->ID)); ?>">
		<?php echo esc_html__('#', 'wc-quote-lite') . intval($quote->ID); ?>
						</a>
					</td>
					<td data-title="Status">
		<?php echo isset($quote_status->post_content) ? esc_html($quote_status->post_content) : 'unread'; ?>
					</td>
					<td data-title="Date">
						<time datetime="<?php echo esc_attr(gmdate('Y-m-d', strtotime($quote->post_date))); ?>" title="<?php echo esc_attr(strtotime($quote->post_date)); ?>"><?php echo esc_attr(date_i18n(get_option('date_format'), strtotime($quote->post_date))); ?></time>
					</td>                            
					<td data-title="Action">
						<a href="<?php echo esc_url(wc_get_endpoint_url('my-quotes', $quote->ID)); ?>" class="wp-element-button woocommerce-button button view">
		<?php echo esc_html__('View', 'wc-quote-lite'); ?>
						</a>				
					</td>
				</tr>
	<?php } ?>
		</tbody>
	</table>

<?php } else { ?>

	<div class="woocommerce-MyAccount-content">
		<div class="woocommerce-notices-wrapper"></div>
		<div class="woocommerce-message woocommerce-message--info woocommerce-Message woocommerce-Message--info woocommerce-info">
	<?php if ('1' === $return_shop ) : ?>
				<a class="wp-element-button woocommerce-Button button" href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>"><?php echo esc_html__($return_shop_text, 'wc-quote-lite'); ?></a>
	<?php endif; ?>
	<?php echo esc_html__('No quote has been made yet.', 'wc-quote-lite'); ?>
		</div>
	</div>
	<?php
}
