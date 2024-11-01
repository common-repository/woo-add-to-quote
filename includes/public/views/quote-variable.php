<?php
/**
 * Single variation cart button
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.4.0
 */

defined('ABSPATH') || exit;

global $product;
?>
<div class="woocommerce-variation-add-to-cart variations_button">
	<?php 
	/**
	 * Action woocommerce_before_add_to_cart_button
	 * 
	 * @since 1.0
	 **/
	do_action('woocommerce_before_add_to_cart_button');
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

	<input type="hidden" name="add-to-cart" value="<?php echo absint($product->get_id()); ?>" />
	<input type="hidden" name="product_id" value="<?php echo absint($product->get_id()); ?>" />
	<input type="hidden" name="variation_id" class="variation_id" value="0" />
</div>
