<?php
/**
 * Quote Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/wcquote/front/wc-quote-list-widget.php.
 */

defined('ABSPATH') || exit;
if (! isset($wc_quote) ) {
	$wc_quote = new WC_QUOTE_Process();
}

$quotes = isset(WC()->session) ? WC()->session->get('wc_quotes') : '';
$quote_page = isset($this->quote_settings['general']['quote_page']) ? $this->quote_settings['general']['quote_page'] : get_the_ID();
$quote_item_count = 0;
$widget_title = ( ! empty($instance['title']) ) ? $instance['title'] : __('Quote List Widget', 'wc-quote-lite');
$button_label = ( ! empty($instance['button_label']) ) ? $instance['button_label'] : __('View List', 'wc-quote-lite');
$show_thumbnail = isset($instance['show_thumbnail']) ? $instance['show_thumbnail'] : '0';
$show_price = isset($instance['show_price']) ? $instance['show_price'] : '0';
$show_quantity = isset($instance['show_quantity']) ?  $instance['show_quantity'] : '0';
$show_variations = isset($instance['show_variations']) ?  $instance['show_variations'] : '0';
$pageurl          =  get_page_link($quote_page);

$colspan = 2;
$colspan = '1' == $show_price ? $colspan + 1 : $colspan;
$colspan = '1' == $show_thumbnail ? $colspan + 1 : $colspan;
$colspan = '1' == $show_quantity ? $colspan + 1 : $colspan;

if (empty($quotes) ) {
	return;
}

?>
 
  <table class="shop_table shop_table_responsive cart quote wc-quote-widget__contents" cellspacing="0" >

	<tbody>
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
	if (class_exists('Wwp_Wholesale_Pricing') ) {
		$Wholesale_Price_class = new WWP_Easy_Wholesale_Multiuser();
		$check_wwp = $Wholesale_Price_class->is_wholesale($_product->get_id());
		$Wprice = !$check_wwp ? $Wholesale_Price_class->wwp_regular_price_change($price, $_product) : $Wholesale_Price_class->wwp_regular_price_change(get_post_meta($_product->get_id(), '_regular_price', true), $_product);
		$price = empty($quote_item['addons_price']) ? $Wprice : $quote_item['addons_price'];
	}
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

		?>
<tr>

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

		<?php if ('1' == $show_thumbnail ) : ?>

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
		endif; 
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
		//  Back order notification
		if ($_product->backorders_require_notification() && $_product->is_on_backorder($quote_item['quantity']) ) {
			/**
			 * Filter wc_quote_item_backorder_notification
			 * 
			 * @since 1.0
			**/
			echo wp_kses_post(apply_filters('wc_quote_item_backorder_notification', '<p class="backorder_notification">' . esc_html__('Available on backorder', 'wc-quote-lite') . '</p>', $product_id));
		}
		echo wp_kses_post(sprintf('<p><small>SKU:%s</small></p>', esc_attr($_product->get_sku())));
		if ('1' == $show_variations ) : 
			echo wp_kses_post(wc_get_formatted_cart_item_data($quote_item));
		endif;
		/**
		 * Action wc_after_quote_item_name
		 * 
		 * @since 1.0
		**/ 
		do_action('wc_after_quote_item_name', $quote_item, $quote_item_key);

		?>
</td>

		<?php
		if ('1' == $show_price ) : 
			?>
	<td class="product-price" data-title="<?php esc_attr_e('Price', 'wc-quote-lite'); ?>">
			<?php
			$args['qty']   = 1;
			$args['price'] = empty($quote_item['addons_price']) ? $price : $quote_item['addons_price'];
			/**
			 * Action wc_quote_item_price
			 * 
			 * @since 1.0
			**/ 
			echo wp_kses_post(apply_filters('wc_quote_item_price', $wc_quote->get_product_price($_product, $args), $quote_item, $quote_item_key));

			?>
		 
	</td>
			<?php 
		endif; 


		if ('1' == $show_quantity ) : 
			?>
<td class="product-quantity" data-title="<?php esc_attr_e('Quantity', 'wc-quote-lite'); ?>">
			<?php
			if ($_product->is_sold_individually() ) {
				$product_quantity = sprintf('<input type="hidden" name="quote_qty[%s]" value="1" />', $quote_item_key);
			} else {
				echo wp_kses_post($quote_item['quantity']);
			}
			?>
</td>
			<?php

		endif;

	}
	?>
</tr>
	<?php

}

?>

	<tr class="wcquote-view-btn">
<td colspan="<?php echo esc_attr($colspan); ?>">

<a class="wp-element-button button" href="<?php echo esc_url($pageurl); ?>"><?php echo esc_attr($button_label); ?></a>
</td>
</tr>
	</tbody>
</table>
