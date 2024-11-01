<?php
/**
 * Mini-quote
 *
 * Contains the drop down items of mini quote basket.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/wcquote/quote/mini-quote-dropdown.php.
 */

defined('ABSPATH') || exit;

$wc_quote     = new WC_QUOTE_Process();
$quote_totals = $wc_quote->get_calculated_totals(WC()->session->get('wc_quotes'));

$price_display    = isset($this->quote_settings['general']['enable_standard']) ? $this->quote_settings['general']['enable_standard'] : '0';

$subtotal      = $quote_totals['_subtotal'];
$subtotal_html = wc_price($subtotal);

if ('incl' == get_option('woocommerce_tax_display_cart') ) {
	$subtotal      += $quote_totals['_tax_total'];
	$subtotal_html  = wc_price($subtotal);
	$price_suffix   = 'incl' === get_option('woocommerce_tax_display_cart') ? wc()->countries->inc_tax_or_vat() : '';
	$price_suffix   = '<small>' . $price_suffix . '</small>';
	$subtotal_html .= ' ' . $price_suffix;
}
/**
 * Action wc_quote_before_mini_quote
 * 
 * @since 1.0
**/
do_action('wc_quote_before_mini_quote'); ?>

<div class="mini-quote-dropdown">
<?php if (! empty(WC()->session->get('wc_quotes')) ) : ?>

	<ul class="wc-quote-mini-cart quote_list product_list_widget">
	<?php
	/**
	 * Action wc_quote_before_mini_quote_contents
	 * 
	 * @since 1.0
	 **/
	do_action('wc_quote_before_mini_quote_contents');

	foreach ( WC()->session->get('wc_quotes') as $quote_item_key => $quote_item ) {
		/**
		 * Filter wc_quote_cart_item_product
		 * 
		 * @since 1.0
		**/
		$_product   = apply_filters('wc_quote_cart_item_product', $quote_item['data'], $quote_item, $quote_item_key);
			 
		// for removing attributes from title
		if ($_product->is_type('variation')) :
			$parent_product = wc_get_product($_product->get_parent_id());
			$product_name = $parent_product->get_name();
	 else : 
		 $product_name = $_product->get_name();
	 endif;
	 /**
	  * Filter wc_quote_cart_item_product_id
	  * 
	  * @since 1.0
	 **/
	 $product_id = apply_filters('wc_quote_cart_item_product_id', $quote_item['product_id'], $quote_item, $quote_item_key);
	 /**
	  * Filter wc_quote_widget_cart_item_visible
	  * 
	  * @since 1.0
	 **/
	 if ($_product && $_product->exists() && $quote_item['quantity'] > 0 && apply_filters('wc_quote_widget_cart_item_visible', true, $quote_item, $quote_item_key) ) {
		 /**
		  * Filter wc_quote_cart_item_name
		  * 
		  * @since 1.0
		 **/
		 $product_name  = apply_filters('wc_quote_cart_item_name', $product_name, $quote_item, $quote_item_key);
		 /**
		  * Filter wc_quote_cart_item_thumbnail
		  * 
		  * @since 1.0
		 **/
		 $thumbnail     = apply_filters('wc_quote_cart_item_thumbnail', $_product->get_image(), $quote_item, $quote_item_key);
		 $product_price = empty($quote_item['addons_price']) ? $_product->get_price() : $quote_item['addons_price'];
		 if (class_exists('Wwp_Wholesale_Pricing') ) {
				 
			 $Wholesale_Price_class = new WWP_Easy_Wholesale_Multiuser();
			 remove_filter('woocommerce_available_variation', array( $Wholesale_Price_class, 'filter_woocommerce_available_variation' ), 200);
			 remove_action('woocommerce_single_variation', array( $Wholesale_Price_class, 'variation_load_tire_priceing_table' ));
			 remove_action('woocommerce_before_add_to_cart_form', array( $Wholesale_Price_class, 'simple_load_tire_priceing_table' ));
			 $check_wwp = $Wholesale_Price_class->is_wholesale($_product->get_id());
			 $Wprice = !$check_wwp ? $Wholesale_Price_class->wwp_regular_price_change($product_price, $_product) : $Wholesale_Price_class->wwp_regular_price_change(get_post_meta($_product->get_id(), '_regular_price', true), $_product);
			 $product_price         = empty($quote_item['addons_price']) ? $Wprice : $quote_item['addons_price'];
		 }
		 $args          = array();
		 $args['qty']   = 1;
		 $args['price'] = $product_price;
		 /**
		  * Filter wc_quote_item_price
		  * 
		  * @since 1.0
		 **/
		 $price_html        = apply_filters('wc_quote_item_price', $wc_quote->get_product_price($_product, $args), $quote_item, $quote_item_key);
		 /**
		  * Filter wc_quote_cart_item_permalink
		  * 
		  * @since 1.0
		 **/
		 $product_permalink = apply_filters('wc_quote_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($quote_item) : '', $quote_item, $quote_item_key);
		 /**
		  * Filter wc_quote_mini_quote_item_class
		  * 
		  * @since 1.0
		 **/
		 $quote_item_class = apply_filters('wc_quote_mini_quote_item_class', 'mini_quote_item', $quote_item, $quote_item_key);
			?>
				<li class="wc-quote-mini-cart-item <?php echo esc_attr($quote_item_class); ?>">
		 <?php
			echo wp_kses_post(
			/**
			 * Filter wc_quote_cart_item_remove_link
			 * 
			 * @since 1.0
			 **/
				apply_filters( 
					'wc_quote_cart_item_remove_link',
					sprintf(
						'<a href="%s" class="wc-quote-remove remove_from_quote_button" aria-label="%s" data-product_id="%s" data-cart_item_key="%s" data-product_sku="%s">&times;</a>',
						esc_url(''),
						esc_attr__('Remove this item', 'wc-quote-lite'),
						esc_attr($product_id),
						esc_attr($quote_item_key),
						esc_attr($_product->get_sku())
					),
					$quote_item_key
				) 
			);
			?>
			 
		 <?php if (empty($product_permalink) ) : ?>
				<?php echo wp_kses_post($thumbnail . $product_name); ?>
					<?php else : ?>
						<a href="<?php echo esc_url($product_permalink); ?>">
						<?php echo wp_kses_post($thumbnail . $product_name); ?>
						</a>
					<?php endif; ?>
		 <?php echo wp_kses_post(wc_get_formatted_cart_item_data($quote_item)); ?>
		 <?php
			if ('1' === $price_display ) :
				/**
				 * Filter wc_quote_widget_cart_item_quantity
				 * 
				 * @since 1.0
				**/
				echo wp_kses_post(apply_filters('wc_quote_widget_cart_item_quantity', '<span class="quantity">' . sprintf('%s &times; %s', $quote_item['quantity'], $price_html) . '</span>', $quote_item, $quote_item_key));
			endif;
			?>
				</li>
		 <?php
	 }
	}
	/**
	 * Action wc_quote_mini_quote_contents
	 * 
	 * @since 1.0
	**/
	do_action('wc_quote_mini_quote_contents');
	?>
	</ul>
	<?php if ('1' === $price_display ) : ?>
		<p class="wc-quote-mini-cart__total total">
			<strong><?php echo esc_html__('Subtotal', 'wc-quote-lite'); ?>:</strong>
		<?php echo wp_kses_post($subtotal_html); ?>
		</p>
	<?php endif; ?>
	<?php
	/**
	 * Action wc_quote_widget_shopping_quote_before_buttons
	 * 
	 * @since 1.0
	 **/
	do_action('wc_quote_widget_shopping_quote_before_buttons');
	?>

	<p class="wc-quote-mini-cart__buttons buttons">
	<?php
	/**
	 * Action wc_quote_widget_shopping_quote_buttons
	 * 
	 * @since 1.0
	 **/
	do_action('wc_quote_widget_shopping_quote_buttons');
	?>
		<a href="<?php echo esc_url($pageurl); ?>" class="wp-element-button btn wc-forward" id="view-quote">
	<?php echo esc_html__(' View Quote', 'wc-quote-lite'); ?>
		</a>                                           
	</p>
	<?php
	/**
	 * Action wc_quote_widget_shopping_quote_after_buttons
	 * 
	 * @since 1.0
	 **/
	do_action('wc_quote_widget_shopping_quote_after_buttons');
	?>

<?php else : ?>

	<p class="wc-quote-mini-cart__empty-message"><?php esc_html_e('No products in the Quote Basket.', 'wc-quote-lite'); ?></p>

<?php endif; ?>
</div>

<?php
/**
 * Action wc_quote_after_mini_quote
 * 
 * @since 1.0
 **/
do_action('wc_quote_after_mini_quote');
?>
