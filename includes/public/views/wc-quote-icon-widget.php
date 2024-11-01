<?php
/**
 * WCQUOTE icon widget
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/wcquote/front/wc-quote-icon-widget.php.
 */

defined('ABSPATH') || exit;
if (! isset($wc_quote) ) {
	$wc_quote = new WC_QUOTE_Process();
}
$quotes = isset(WC()->session) ? WC()->session->get('wc_quotes') : array();
$quote_page = isset($this->quote_settings['general']['quote_page']) ? $this->quote_settings['general']['quote_page'] : get_the_ID();
$pageurl          = get_attachment_link($quote_page);
$quote_item_count = 0;
$widget_title         = ( ! empty($instance['title']) ) ? $instance['title'] : __('Quote Button Widget', 'wc-quote-lite');
$button_label = ( ! empty($instance['button_label']) ) ? $instance['button_label'] : __('View List', 'wc-quote-lite');
$show_thumbnail = isset($instance['show_thumbnail']) ? $instance['show_thumbnail'] : '0';
$show_price = isset($instance['show_price']) ? $instance['show_price'] : '0';
$show_quantity = isset($instance['show_quantity']) ?  $instance['show_quantity'] : '0';
$show_variations = isset($instance['show_variations']) ?  $instance['show_variations'] : '0';
$pageurl          =  get_attachment_link($quote_page);

$colspan = 2;
$colspan = '1' == $show_price ? $colspan + 1 : $colspan;
$colspan = '1' == $show_thumbnail ? $colspan + 1 : $colspan;
$colspan = '1' == $show_quantity ? $colspan + 1 : $colspan;

if (empty($quotes) ) {
	return;
}

foreach ( $quotes as $qoute_item ) {
	$quote_item_count += isset($qoute_item['quantity']) ? $qoute_item['quantity'] : 0;
}

?>

	<div class="wc-quote-icon">

		<a href="<?php echo esc_url($pageurl); ?>" title="<?php echo esc_html__('View Quote', 'wc-quote-lite'); ?>">
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="dashicons dashiconsc"><path d="M15.812 4.819c-.33-.341-.312-.877.028-1.207l3.469-3.365c.17-.164.387-.247.603-.247.219 0 .438.085.604.257l-4.704 4.562zm-5.705 8.572c-.07.069-.107.162-.107.255 0 .194.158.354.354.354.089 0 .178-.033.247-.1l.583-.567-.493-.509-.584.567zm4.924-6.552l-1.994 1.933c-1.072 1.039-1.619 2.046-2.124 3.451l.881.909c1.419-.461 2.442-.976 3.514-2.016l1.994-1.934-2.271-2.343zm5.816-5.958l-5.137 4.982 2.586 2.671 5.138-4.98c.377-.366.566-.851.566-1.337 0-1.624-1.968-2.486-3.153-1.336zm-11.847 12.119h-4v1h4v-1zm9-1.35v1.893c0 4.107-6 2.457-6 2.457s1.518 6-2.638 6h-7.362v-20h12.629l2.062-2h-16.691v24h10.189c3.163 0 9.811-7.223 9.811-9.614v-4.687l-2 1.951z"></path></svg>
			<span id="total-items" class="totalitems">
				<?php echo esc_attr($quote_item_count); ?>
			</span>
		</a>
	 
  <table class="shop_table shop_table_responsive cart quote wc-quote-widget__contents wc-quote-icon-widget__contents" cellspacing="0">
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

<?php if ( '1' == $show_thumbnail ) : ?>
	<td class="product-thumbnail">
		<?php
			/**
			 * Filter wc_quote_item_thumbnail
			 * 
			 * @since 1.0
			 **/
			$thumbnail = apply_filters('wc_quote_item_thumbnail', $_product->get_image(), $quote_item, $quote_item_key);
			// phpcs:disable
			if ( ! $product_permalink ) {
				echo wp_kses_post($thumbnail);
			} else {
				printf('<a href="%s">%s</a>', esc_url($product_permalink), wp_kses_post($thumbnail));
			}
			?>
	</td>
	<?php endif; ?>

<td class="product-name" data-title="<?php esc_attr_e('Product', 'wc-quote-lite'); ?>">
	<?php 
	// phpcs:enable	
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

		<?php
		if ('1' == $show_price ) : 
			?>

	<td class="product-price" data-title="<?php esc_attr_e('Price', 'wc-quote-lite'); ?>">
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

</div> 


