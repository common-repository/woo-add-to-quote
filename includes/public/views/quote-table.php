<?php
/**
 * Quote Details table for request a quote page.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/wcquote/front/quote-table.php.
 */

defined('ABSPATH') || exit;

if (! isset($wc_quote) ) {
	$wc_quote = new WC_QUOTE_Process();
}
$enable_convert_cart = isset($this->quote_settings['general']['enable_convert_cart']) ? $this->quote_settings['general']['enable_convert_cart'] : '0';
$convert_cart_text = isset($this->quote_settings['customization']['convert_cart_text']) ? $this->quote_settings['customization']['convert_cart_text'] : 'Add To Cart';
$colspan  = 4;

/**
 * Action wc_quote_before_quote_table
 * 
 * @since 1.0
**/
do_action('wc_quote_before_quote_table'); ?>

<table class="shop_table shop_table_responsive cart quote woocommerce-cart-form__contents  wc-quote-form__contents" cellspacing="0">
	<thead>
		<tr>
			<th class="product-remove">&nbsp;</th>
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
	
		foreach ( WC()->session->get('wc_quotes') as $quote_item_key => $quote_item ) {
			 
			if (!isset($quote_item['data']) || !is_object($quote_item['data']) ) {
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
		  $product_id = apply_filters('wc_quote_item_product_id', $quote_item['product_id'], $quote_item, $quote_item_key);
		  $price = empty($quote_item['addons_price']) ? $_product->get_price() : $quote_item['addons_price'];

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
					<tr class="woocommerce-cart-form__quote-item <?php echo esc_attr($quote_item_class); ?>">

						<td class="product-remove">
			 <?php
				echo wp_kses_post(
				/**
				 * Filter wc_quote_item_remove_link
				 * 
				 * @since 1.0
				 **/
					apply_filters( 
						'wc_quote_item_remove_link',
						sprintf(
							'<a href="%s" class="remove remove-cart-item wc-quote-remove-item" aria-label="%s" data-cart_item_key="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
							esc_attr($quote_item_key),
							esc_html__('Remove this item', 'wc-quote-lite'),
							esc_attr($quote_item_key),
							esc_attr($product_id),
							esc_attr($_product->get_sku())
						),
						$quote_item_key
					) 
				);
				?>
					</td>

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

				// Backorder notification.
				if ($_product->backorders_require_notification() && $_product->is_on_backorder($quote_item['quantity']) ) {
					/**
					 * Filter wc_quote_item_backorder_notification
					 * 
					 * @since 1.0
					 **/
					echo wp_kses_post(apply_filters('wc_quote_item_backorder_notification', '<p class="backorder_notification">' . esc_html__('Available on backorder', 'wc-quote-lite') . '</p>', $product_id));
				}

				echo wp_kses_post(sprintf('<p><small>SKU:%s</small></p>', esc_attr($_product->get_sku())));
				/**
				 * Action wc_after_quote_item_name
				 * 
				 * @since 1.0
				**/
				do_action('wc_after_quote_item_name', $quote_item, $quote_item_key);

				// Meta data.
				echo wp_kses_post(wc_get_formatted_cart_item_data($quote_item));
				?>
				</td>

				<td class="product-price" data-title="<?php esc_attr_e('Price', 'wc-quotelite'); ?>">
					<?php
					$args['qty']   = 1;
					$args['price'] = empty($quote_item['addons_price']) ? $price : $quote_item['addons_price'];
						/**
						 * Filter wc_quote_item_price
						 * 
						 * @since 1.0
						**/
						echo wp_kses_post(apply_filters('wc_quote_item_price', $wc_quote->get_product_price($_product, $args), $quote_item, $quote_item_key));
	
					?>
				</td>
		
		
				<td class="product-quantity wc-quote-lite" data-title="<?php esc_attr_e('Quantity', 'wc-quote-lite'); ?>">
			 <?php
				if ($_product->is_sold_individually() ) {
					$product_quantity = sprintf('<input type="hidden" name="quote_qty[%s]" value="1" />', $quote_item_key);
				} else {
					woocommerce_quantity_input(
						array(
							'input_name'   => "quote_qty[{$quote_item_key}]",
							'input_value'  => $quote_item['quantity'],
							'max_value'    => $_product->get_max_purchase_quantity(),
							'min_value'    => '0',
							'product_name' => $_product->get_name(),
						),
						$_product,
						true
					);
				}
				?>
				</td>

			
					<td class="product-subtotal" data-title="<?php esc_attr_e('Subtotal', 'wc-quote-lite'); ?>">
					<?php
					$args['qty']   = $quote_item['quantity'];
					$args['price'] = empty($quote_item['addons_price']) ? $price : $quote_item['addons_price'];
					if ($_product->get_type() === 'subscription' || $_product->get_type() === 'variable-subscription' ) :
						$args['price'] = $price;
					endif;
					/**
					 * Filter wc_quote_item_subtotal
					 * 
					 * @since 1.0
					**/
					echo wp_kses_post(apply_filters('wc_quote_item_subtotal', $wc_quote->get_product_subtotal($_product, $quote_item['quantity'], $args), $quote_item, $quote_item_key));
					?>
					</td>
			 

				</tr>
			 <?php
		 }
		}
		?>
		<tr>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td class="product-subtotal">
				<div>
					<?php
					 esc_html_e('Subtotal' , 'wc-quote-lite');
					?>
				</div>
			</td>
			<td class="product-subtotal">
				<div>
					<?php
					$quote_totals = $wc_quote->get_calculated_totals(wc()->session->get('wc_quotes'));
					$quote_subtotal = isset($quote_totals['_subtotal']) ? $quote_totals['_subtotal'] : 0;
					$vat_total      = isset($quote_totals['_tax_total']) ? $quote_totals['_tax_total'] : 0;
					$quote_total    = isset($quote_totals['_total']) ? $quote_totals['_total'] : 0;
					?>
					<span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol"><?php echo wp_kses_post(wc_price($quote_total)); ?></span>
				</div>
			</td>
		</tr>

		<td colspan="<?php echo esc_attr($colspan + 2); ?>" class="actions">
			<button type="button" type="submit" id="wc_quote_empty_quote_btn" class="wp-element-button button wc_quote_empty_quote_btn" name="empty_quote" value="<?php esc_html_e('Empty Quote', 'wc-quote-lite'); ?>"><?php esc_html_e('Empty Quote', 'wc-quote-lite'); ?></button> 
			<input name="wc_quote_action" type="hidden" value="save_wcquote"/>
					<?php wp_nonce_field('save_wcquote', 'wc_quote_nonce'); ?>
					<?php if ('1' === $enable_convert_cart ) : ?>
						<a href="javascript:void(0)" class="wp-element-button checkout-button button alt wc_quote_add_to_cart wc-forward" data-url="<?php echo esc_url(wc_get_cart_url()); ?>"><?php echo esc_attr($convert_cart_text); ?></a>
					<?php endif; ?>

			</td>
			<?php 
			/**
			 * Action wc_quote_contents
			 * 
			 * @since 1.0
			 **/
			do_action('wc_quote_contents');
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
