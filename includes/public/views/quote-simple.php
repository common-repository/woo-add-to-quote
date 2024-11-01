<?php
/**
 * Simple product add to cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/simple.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.4.0
 */

defined('ABSPATH') || exit;

global $product;

if (! $product->is_purchasable() ) {
	return;
}
$show_on_stock = isset($this->quote_settings['general']['show_on_stock']) ? $this->quote_settings['general']['show_on_stock'] : 'hide';
if (!$product->is_in_stock() && 'show' !== $show_on_stock ) {

	echo wp_kses_post(wc_get_stock_html($product));

} else {
	/**
	 * Action woocommerce_before_add_to_cart_form
	 * 
	 * @since 1.0
	**/
	do_action('woocommerce_before_add_to_cart_form');
	/**
	 * Filter woocommerce_add_to_cart_form_action
	 * 
	 * @since 1.0
	**/
	$form_action = apply_filters('woocommerce_add_to_cart_form_action', $product->get_permalink()); ?>
	<form class="cart" action="<?php echo esc_url($form_action); ?>" method="post" enctype='multipart/form-data'>
	<?php
	/**
	 * Action woocommerce_before_add_to_cart_button
	 * 
	 * @since 1.0
	 **/
	do_action('woocommerce_before_add_to_cart_button');
	?>

	<?php
	/**
	 * Action woocommerce_before_add_to_cart_quantity
	 * 
	 * @since 1.0
	 **/
	do_action('woocommerce_before_add_to_cart_quantity');

	woocommerce_quantity_input(
		array(
			/**
			 * Filter woocommerce_quantity_input_min
			 * 
			 * @since 1.0
			**/
			'min_value'   => apply_filters('woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product),
			/**
			 * Filter woocommerce_quantity_input_max
			 * 
			 * @since 1.0
			**/
			'max_value'   => apply_filters('woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product),
			'input_value' => $product->get_min_purchase_quantity(), // WPCS: CSRF ok, input var ok.
		)
	);
		/**
		 * Action woocommerce_after_add_to_cart_quantity
		 * 
		 * @since 1.0
		**/
		do_action('woocommerce_after_add_to_cart_quantity');

		echo '<a href="javascript:void(0)" rel="nofollow" data-product_id="' . intval($product->get_ID()) . '" data-product_sku="' . esc_attr($product->get_sku()) . '" class="wp-element-button wc_quote_single_page button single_add_to_cart_button alt product_type_' . esc_attr($product->get_type()) . '" style="background-color:' . esc_attr($quote_btn_color) . ';color:' . esc_attr($quote_text_color) . ';">' . esc_attr($add_quote_text) . '</a>';
		/**
		 * Action wc_quote_after_add_to_quote_button
		 * 
		 * @since 1.0
		**/
		do_action('wc_quote_after_add_to_quote_button');
		/**
		 * Action woocommerce_after_add_to_cart_button
		 * 
		 * @since 1.0
		**/
		do_action('woocommerce_after_add_to_cart_button');
		 
	?>
		 
	</form>

	<?php 
	/**
	 * Action woocommerce_after_add_to_cart_form
	 * 
	 * @since 1.0
	 **/
	do_action('woocommerce_after_add_to_cart_form');
}
