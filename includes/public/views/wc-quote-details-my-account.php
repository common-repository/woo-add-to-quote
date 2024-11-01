<?php
/**
 * Quote details in my Account.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/wcquote/front/wc-quote-details-my-account.php.
 */

defined('ABSPATH') || exit;

$quote_contents = get_post_meta($wcquote_id, 'quote_contents', true);
$quote_contents = !empty($quote_contents) ? $quote_contents : array( 0 => 0 );
$quote_coverter = get_post_meta($wcquote_id, 'converted_by', true);
$quote_status   = $quote->post_status;



if (! isset($wc_quote) ) {
	$wc_quote = new WC_QUOTE_Process();
}

$quote_totals = $wc_quote->get_calculated_totals($quote_contents);


?>
<div class="woocommerce">
	<?php
	/**
	 * Action wc_quote_before_quote_table
	 * 
	 * @since 1.0
	 **/
	do_action('wc_quote_before_quote_table');
	?>

	<table class="shop_table order_details quote_details" cellspacing="0">
		<tr>
			<th class="quote-number"><?php esc_html_e('Quote #', 'wc-quote-lite'); ?></th>
			<td class="quote-number"><?php echo esc_html($wcquote_id); ?> </td>
		</tr>
		<tr>
			<th class="quote-date"><?php esc_html_e('Quote Date', 'wc-quote-lite'); ?></th>
			<td class="quote-date"><?php echo esc_attr(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($quote->post_date))); ?> </td>
		</tr>
		<tr>
			<th class="quote-status"><?php esc_html_e('Current Status', 'wc-quote-lite'); ?></th>
			<td class="quote-status"><?php echo isset($post->post_content) ? esc_html($post->post_content) : 'unread'; ?> </td>
		</tr>

</table>
<h2><?php echo esc_html__('Quote Details', 'wc-quote-lite'); ?></h2>
<table class="shop_table shop_table_responsive cart order_details quote_details" cellspacing="0">
	<thead>
		<tr>
			<th class="product-thumbnail">&nbsp;</th>
			<th class="product-name"><?php esc_html_e('Product', 'wc-quote-lite'); ?></th>
			<th class="product-price"><?php esc_html_e('Price', 'wc-quote-lite'); ?></th>
			<th class="product-quantity"><?php esc_html_e('Quantity', 'wc-quote-lite'); ?></th>
			<th class="product-subtotal"><?php esc_html_e('Subtotal', 'wc-quote-lite'); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php 
		/**
		 * Action wc_quote_before_quote_contents
		 * 
		 * @since 1.0
		 **/
		do_action('wc_quote_before_quote_contents'); 
		?>

			<?php
			foreach ( $quote_contents as $quote_item_key => $quote_item ) {

				if (! isset($quote_item['data']) || ! is_object($quote_item['data']) ) {
					continue;
				}
				/**
				 * Filter wc_quote_item_product
				 * 
				 * @since 1.0
				**/ 
				$_product = apply_filters('wc_quote_item_product', $quote_item['data'], $quote_item, $quote_item_key);
				// for removing attributes from title
				if ($_product->is_type('variation')) :
					$parent_product = wc_get_product($_product->get_parent_id());
					$product_name = $parent_product->get_name();
			 else : 
				 $product_name = $_product->get_name();
			 endif;
			  /**
			   * Filter wc_quote_item_product_id
			   * 
			   * @since 1.0
			  **/ 
			  $product_id    = apply_filters('wc_quote_item_product_id', $quote_item['product_id'], $quote_item, $quote_item_key);
			  $price         = empty($quote_item['addons_price']) ? $_product->get_price() : $quote_item['addons_price'];
			  $offered_Price = isset($quote_item['offered_price']) ? floatval($quote_item['offered_price']) : $price;
			
			  /**
			   * Filter wc_quote_item_visible
			   * 
			   * @since 1.0
			  **/ 
			 if ($_product && $_product->exists() && $quote_item['quantity'] > 0 && apply_filters('wc_quote_item_visible', true, $quote_item, $quote_item_key) ) {
				 /**
				  * Filter wc_quote_item_permalink
				  * 
				  * @since 1.0
				 **/ 
				 $product_permalink = apply_filters('wc_quote_item_permalink', $_product->is_visible() ? $_product->get_permalink($quote_item) : '', $quote_item, $quote_item_key);
				 /**
				  * Filter wc_quote_item_class
				  * 
				  * @since 1.0
				 **/ 
				 $quote_item_class = apply_filters('wc_quote_item_class', 'cart_item', $quote_item, $quote_item_key);
					?>
				  <tr class="wc__quote-item <?php echo esc_attr($quote_item_class); ?>">

					  <td class="product-thumbnail">
				 <?php
					/**
					 * Filter wc_quote_item_thumbnail
					 * 
					 * @since 1.0
					 **/ 
					$thumbnail = apply_filters('wc_quote_item_thumbnail', $_product->get_image(), $quote_item, $quote_item_key);

					if (! $product_permalink ) {
						echo wp_kses_post($thumbnail); 
					} else {
						printf('<a href="%s">%s</a>', esc_url($product_permalink), wp_kses_post($thumbnail));
					}
					?>
				</td>

				<td class="product-name" data-title="<?php esc_attr_e('Product', 'wc-quote-lite'); ?>">
				 <?php
					if (! $product_permalink ) {
						/**
						 * Filter wc_quote_item_name
						 * 
						 * @since 1.0
						**/ 
						echo wp_kses_post(apply_filters('wc_quote_item_name', $product_name, $quote_item, $quote_item_key) . '&nbsp;');
					} else {
						/**
						 * Filter wc_quote_item_name
						 * 
						 * @since 1.0
						**/ 
						echo wp_kses_post(apply_filters('wc_quote_item_name', sprintf('<a href="%s">%s</a>', esc_url($product_permalink), $product_name), $quote_item, $quote_item_key));
					}
					?>
					<br>
				 <?php
					echo '<div class="wc-order-item-sku"><strong>' . esc_html__('SKU:', 'woocommerce') . '</strong> ' . esc_html($_product->get_sku()) . '</div>';
					/**
					 * Action wc_quote_after_quote_item_name
					 * 
					 * @since 1.0
					**/
					do_action('wc_quote_after_quote_item_name', $quote_item, $quote_item_key);

					// Meta data.
					echo wp_kses_post(wc_get_formatted_cart_item_data($quote_item));

					// Backorder notification.
					if ($_product->backorders_require_notification() && $_product->is_on_backorder($quote_item['quantity']) ) {
						/**
						 * Filter wc_quote_item_backorder_notification
						 * 
						 * @since 1.0
						**/ 
						echo wp_kses_post(apply_filters('wc_quote_item_backorder_notification', '<p class="backorder_notification">' . esc_html__('Available on backorder', 'wc-quote-lite') . '</p>', $product_id));
					}
					?>
				</td>

				<td class="product-price" data-title="<?php esc_attr_e('Price', 'wc-quote-lite'); ?>">
					<?php echo wp_kses_post(wc_price($price)); ?>
				</td>
		

				<td class="product-quantity" data-title="<?php esc_attr_e('Quantity', 'wc-quote-lite'); ?>">
				 <?php
					$qty_display = $quote_item['quantity'];
					/**
					 * Filter wc_quote_item_quantity_html
					 * 
					 * @since 1.0
					**/ 
					echo wp_kses_post(apply_filters('wc_quote_item_quantity_html', ' <strong class="product-quantity">' . sprintf('&nbsp;%s', $qty_display) . '</strong>', $quote_item));
					?>
				</td>

			
				<td class="product-subtotal" data-title="<?php esc_attr_e('Subtotal', 'wc-quote-lite'); ?>">
					<?php echo wp_kses_post(wc_price(( $price ) * $qty_display)); ?>
				</td>
				
	
			</tr>
				 <?php
			 }
			}
			?>
</tbody>
</table>
<?php
	/**
	 * Action wc_quote_after_quote_contents
	 * 
	 * @since 1.0
	 **/ 
	do_action('wc_quote_after_quote_contents'); 
?>

	<?php
	/**
	 * Action wc_quote_after_quote_table
	 * 
	 * @since 1.0
	 **/  
	do_action('wc_quote_after_quote_table'); 
	?>


	<?php 
	/**
	 * Action wc_quote_before_quote_collaterals
	 * 
	 * @since 1.0
	 **/ 
	do_action('wc_quote_before_quote_collaterals'); 
	?>

	<div class="cart-collaterals">

		<?php
		/**
		 * Cart collaterals hook.
		 *
		 * @hooked wc_quote_cross_sell_display
		 * @since  1.0
		 * @hooked wc_quote_totals - 10
		 */
		do_action('wc_quote_collaterals');
		?>
		<div class="cart_totals">
			<?php 
			/**
			 * Action wc_quote_before_quote_totals
			 * 
			 * @since 1.0
			 **/ 
			do_action('wc_quote_before_quote_totals'); 
			?>

			<h2><?php esc_html_e('Quote totals', 'wc-quote-lite'); ?></h2>

			<table cellspacing="0" class="shop_table shop_table_responsive">
			
			<tr class="cart-subtotal">
				<th><?php esc_html_e('Subtotal(standard)', 'wc-quote-lite'); ?></th>
				<td data-title="<?php esc_attr_e('Subtotal(standard)', 'wc-quote-lite'); ?>"><?php echo wp_kses_post(wc_price($quote_totals['_subtotal'])); ?></td>
			</tr>

			<?php if (wc_tax_enabled()) : ?>
				<tr class="tax-rate">
					<th><?php echo esc_html__('Vat(standard)', 'wc-quote-lite'); ?></th>
					<td data-title="<?php echo esc_html__('Vat(standard)', 'wc-quote-lite'); ?>"><?php echo wp_kses_post(wc_price($quote_totals['_tax_total'])); ?></td>
				</tr>
			<?php endif; ?>
	
			<tr class="order-total">
				<th><?php esc_html_e('Total(standard)', 'wc-quote-lite'); ?></th>
				<td data-title="<?php esc_attr_e('Total(standard)', 'wc-quote-lite'); ?>"><?php echo wp_kses_post(wc_price($quote_totals['_total'])); ?></td>
			</tr>

		</table>                
	</div>
</div>
</div>
