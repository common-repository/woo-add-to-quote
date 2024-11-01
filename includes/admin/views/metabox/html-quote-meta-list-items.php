<?php
/**
 * Quote list items
 *
 * It shows the details of quotes items in meta box.
 */

defined('ABSPATH') || exit;
?>
<div id="wc_quote_items_container">
	<table cellpadding="0" cellspacing="0" id="wc_quote_items_table" class="woocommerce_order_items wc_quote_items">

		<thead>
			<tr>
				<th class="thumb sortable" data-sort="string-ins"><?php esc_html_e('Thumbnail', 'wc-quote-lite'); ?></th>
				<th class="item sortable" data-sort="string-ins"><?php esc_html_e('Item', 'wc-quote-lite'); ?></th>
				<th></th>
				<th class="line_actions" ></th>
				<th class="item_cost sortable" data-sort="float"><?php esc_html_e('Cost', 'wc-quote-lite'); ?></th>
				<th class="quantity sortable" data-sort="int"><?php esc_html_e('Qty', 'wc-quote-lite'); ?></th>
				<th class="line_cost sortable" data-sort="float"><?php esc_html_e('Subtotal', 'wc-quote-lite'); ?></th>
			</tr>
		</thead>

		<tbody id="order_line_items">
			<?php
			/**
			 * Action wc_quote_details_before_quote_table_items
			 * 
			 * @since 1.0
			 **/
			do_action('wc_quote_details_before_quote_table_items', $post);

			$offered_price_subtotal = 0;
			foreach ( (array) $quote_contents as $item_id => $item ) {

				if (isset($item['data']) ) {

					$product = $item['data'];

				} else {

					continue;
				}

				if (! is_object($product) ) {
					continue;
				}

				$price = empty($item['addons_price']) ? $product->get_price() : $item['addons_price'];
				$offered_signup = 0;
				$signup_fee = 0;
				if (class_exists('Wwp_Wholesale_Pricing') ) {
					$Wholesale_Price_class = new WWP_Easy_Wholesale_Multiuser();
					$check_wwp = $Wholesale_Price_class->is_wholesale($product->get_id());
					$Wprice = !$check_wwp ? $Wholesale_Price_class->wwp_regular_price_change($price, $product) : $Wholesale_Price_class->wwp_regular_price_change(get_post_meta($product->get_id(), '_regular_price', true), $product);
					$price = empty($item['addons_price']) ? $Wprice : $item['addons_price'];
				}
				if ($product->get_type() === 'subscription' || $product->get_type() === 'variable-subscription' ) :
					$signup_fee = WC_Subscriptions_Product::get_sign_up_fee($product);
					$offered_signup = empty($item['offered_signup']) ? $signup_fee : $item['offered_signup'];
				endif;
				$qty_display   = $item['quantity'];
				$offered_price = isset($item['offered_price']) ? floatval($item['offered_price']) : $price;
				$product_link  = $product ? admin_url('post.php?post=' . $product->get_id() . '&action=edit') : '';
				/**
				* Filter wc_quote_item_thumbnail
				* 
				* @since 1.0
				**/
				$thumbnail     = $product ? apply_filters('wc_quote_item_thumbnail', $product->get_image('thumbnail', array( 'title' => '' ), false), $item_id, $item) : '';

				$offered_price_subtotal += floatval(esc_attr($offered_price) + esc_attr($offered_signup)) * intval(esc_attr($qty_display));

				?>
				<tr class="item" data-order_item_id="<?php echo esc_attr($item_id); ?>">
					<td class="thumb">
				<?php echo '<div class="wc-order-item-thumbnail">' . wp_kses_post($thumbnail) . '</div>'; ?>
					</td>

					<td class="name" data-sort-value="<?php echo esc_attr($product->get_name()); ?>">
				<?php
				$is_visible        = $product && $product->is_visible();
				/**
				 * Filter wc_quote_item_permalink
				 * 
				 * @since 1.0
				**/
				$product_permalink = apply_filters('wc_quote_item_permalink', $is_visible ? $product->get_permalink($item) : '', $item, $post);
				/**
				 * Filter wc_quote_item_name
				 * 
				 * @since 1.0
				**/
				echo wp_kses_post(apply_filters('wc_quote_item_name', $product_permalink ? sprintf('<a href="%s">%s</a>', $product_permalink, $product->get_name()) : $product->get_name(), $item, $is_visible));
				?>
						 
				<?php
				echo wp_kses_post('<div class="wc-quote-item-sku"><strong>' . esc_html__('SKU:', 'wc-quote-lite') . '</strong> ' . esc_html($product->get_sku()) . '</div>');
				/**
				* Action wc_quote_item_meta_start
				* 
				* @since 1.0
				**/
				do_action('wc_quote_item_meta_start', $item_id, $item, $post, false);

				// Meta data.
				echo wp_kses_post(wc_get_formatted_cart_item_data($item));
				/**
				* Action wc_quote_item_meta_end
				* 
				* @since 1.0
				**/
				do_action('wc_quote_item_meta_end', $item_id, $item, $post, false);
				?>
					</td>
					<td></td>
					<td></td>

					<td class="item_cost" data-sort-value="<?php echo esc_attr($price); ?>">
						<div class="edit">
				<?php 
				if ( $product->get_type() === 'subscription' || ( $product->get_type() === 'variable-subscription' && $signup_fee > 0 ) ) : 
					echo wp_kses_post(WC_Subscriptions_Product::get_price_string($product, array(
						'price' => wc_price($price),
						'sign_up_fee' => $signup_fee
					)));
			 else :
					echo wp_kses_post(wc_price($price));
			 endif; 
				?>
						</div>
					</td>
					<td class="quantity" width="1%">
						<div class="edit">
							<?php echo esc_attr($item['quantity']); ?>
						</div>
					</td>

					<td class="line_cost" data-sort-value="<?php echo esc_attr($price * $qty_display); ?>">
						<div class="view">
							<?php echo wp_kses_post(wc_price(( $price + $signup_fee ) * $qty_display)); ?>
						</div>
					</td>
				</tr>
				<?php
			}
			/**
			* Action wc_quote_details_after_quote_table_items
			* 
			* @since 1.0
			**/
			do_action('wc_quote_details_after_quote_table_items', $post);
			?>
		</tbody>
	</table>
	<div class="wc-order-data-row wc-order-totals-items wc-order-items-editable">
		<table cellpadding="0" cellspacing="0" id="wc_quote_total_table" class="wc-order-totals wc_quote_items_total">
				<?php
				foreach ( $quote_totals as $key => $total ) {

					$label = '';
					switch ( $key ) {
						case '_subtotal':
							$label = esc_html__('Subtotal(standard)', 'wc-quote-lite');
							break;
						case '_tax_total':
							$label = esc_html__('Vat(standard)', 'wc-quote-lite');
							break;
						case '_total':
							$label = esc_html__('Total(standard)', 'wc-quote-lite');
							break;
						default:
							$label = '';
							break;
					}

					if (empty($label) ) {
						continue;
					}

					// if ( '_tax_total' == $key ) {
					//     continue;
					// }

					?>
						<tr>
							<td scope="row"><?php echo esc_html($label); ?></td>
							<th colspan="2"><?php echo wp_kses_post(wc_price($total)); ?></th>
						</tr>
					<?php
				}
				?>
				<tr>
					<td colspan="3"><?php echo esc_html__('Note: Tax/Vat will be calculated on quote conversion to cart but it is visible to customers.', 'wc-quote-lite'); ?></td>
				</tr>
				 
		</table>
	</div>
</div>
