<?php
/**
 * Add to Cart for variable product
 *
 * @package WooCommerce Product Table/Templates
 */

?>
<div class="product-cart-chechbox-wrapper variable-product-add-cart <?php echo esc_attr($args['classes']); ?>">
	<?php if ('link_button' === $args['list_variations_type'] && '1' !== $hide_cart_all  ) { ?>
		<div class="product-cart-wrapper">
			<a href="<?php echo esc_url(get_permalink($product->get_id())); ?>" class="wp-element-button button product_type_variable" aria-label="<?php echo esc_html(get_the_title($product->get_id())); ?>" rel="nofollow"><?php esc_html_e('Select options', 'woo-product-table'); ?></a>
		</div>
	<?php } elseif ('separate_row' !== $args['list_variations_type'] && '1' === $show_single && ( '1' === $hide_cart_single || '1' === $hide_cart_all ) ) { ?>
		<div class="product-cart-wrapper">
			<form class="variations_form cart" method="post" enctype='multipart/form-data' data-product_id="<?php echo absint($product->get_id()); ?>" data-product_variations="<?php echo esc_attr(htmlspecialchars(wp_json_encode($args['available_variations']), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401)); ?>">
		<?php if (empty($args['available_variations']) && false !== $args['available_variations'] ) : ?>
					<p class="stock out-of-stock"><?php esc_html_e('Out of Stock', 'woo-product-table'); ?></p>
				<?php else : ?>
					<div class="variations">
					<?php 
					foreach ( $args['attributes'] as $attribute_name => $options ) :
						wc_dropdown_variation_attribute_options(
							array(
								'options'   => $options,
								'attribute' => $attribute_name,
								'product'   => $product,
							)
						);
					endforeach;
					/**
					 * Filter woocommerce_reset_variations_link
					 * 
					 * @since 1.0
					**/
					echo wp_kses_post(apply_filters('woocommerce_reset_variations_link', '<a class="reset_variations" href="#">' . esc_html__('Clear', 'woo-product-table') . '</a>'));
					?>
				</div>
				<div class="single_variation_wrap">
					<?php 
					/**
					 * Action woocommerce_single_variation_add_to_cart_button
					 * 
					 * @since 1.0
					 **/
					do_action('woocommerce_single_variation_add_to_cart_button');
					if (file_exists(get_stylesheet_directory() . '/woocommerce/wcquote/front/quote-variable.php') ) {

						include get_stylesheet_directory() . '/woocommerce/wcquote/front/quote-variable.php';

					} else {

						include WATQ_QUOTE_PATH . 'includes/public/views/quote-variable.php';
					}
					
					?>
				</div>
				<?php endif; ?>
		</form>
		<?php if ('add_button' !== $args['cart_button_type'] && $product->is_in_stock() ) { ?>
			<input type="checkbox" class="product-cart-chechbox" name="product_ids[]" value="<?php echo absint($product->get_id()); ?>">
		<?php } ?>
	</div>
	<?php } ?>
</div>
