<?php
/**
 * Email Quote Contents.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/wcquote/emails/wc-quote-content-table.php.
 */

defined('ABSPATH') || exit;
/**
 * Filter wc_quote_before_email_quote_contents
 * 
 * @since 1.0
**/
do_action('wc_quote_before_email_quote_contents');

?>
<div style="margin-top: 10px; margin-bottom: 10px;">
	<table style="color:#636363;border:1px solid #e5e5e5;vertical-align:middle;width:100%;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif" cellspacing="0" cellpadding="6" border="1">
		<thead>
			<tr>
				<th scope="col" style="color:#636363;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:center" colspan="2">
					<?php echo esc_html__('Product', 'wc-quote-lite'); ?>
				</th>
				<th scope="col" style="color:#636363;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left">
					<?php echo esc_html__('Quantity', 'wc-quote-lite'); ?>
				</th>
				
				<th scope="col" style="color:#636363;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left">
					<?php echo esc_html__('Price', 'wc-quote-lite'); ?>
				</th>
				
				<?php if (in_array(get_post($quote_id)->post_status, array( 'quote_quoted', 'quote_accepted' )) ) : ?>
					<th scope="col" style="color:#636363;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left">
					<?php echo esc_html__('Offered Price', 'wc-quote-lite'); ?>
					</th>
				<?php endif; ?>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach ( (array) $quote_contents as $key => $item ) :

				$product = isset($item['data']) ? $item['data'] : '';

				if (! is_object($product) ) {
					continue;
				}
				// for removing attributes from title
				if ($product->is_type('variation')) :
					$parent_product = wc_get_product($product->get_parent_id());
					$product_name = $parent_product->get_name();
			  else : 
				  $product_name = $product->get_name();
			  endif;

			  $price = empty($item['addons_price']) ? $product->get_price() : $item['addons_price'];
			  if (class_exists('Wwp_Wholesale_Pricing') ) {
				  $Wholesale_Price_class = new WWP_Easy_Wholesale_Multiuser();
				  $check_wwp = $Wholesale_Price_class->is_wholesale($product->get_id());
				  $Wprice = !$check_wwp ? $Wholesale_Price_class->wwp_regular_price_change($price, $product) : $Wholesale_Price_class->wwp_regular_price_change(get_post_meta($product->get_id(), '_regular_price', true), $product);
				  $price = empty($item['addons_price']) ? $Wprice : $item['addons_price'];
			  }
			  $offered_price = isset($item['offered_price']) ? floatval($item['offered_price']) : $price;
			  $offered_signup = 0;
			  $signup_fee     = 0;
			  if ($product->get_type() === 'subscription' || $product->get_type() === 'variable-subscription' ) :
				  $signup_fee = WC_Subscriptions_Product::get_sign_up_fee($product);
				  $offered_signup = isset($item['offered_signup']) ? floatval($item['offered_signup']) : $signup_fee;
			  endif;
						/**
						 * Filter wc_quote_item_thumbnail
						 * 
						 * @since 1.0
						**/
			  $thumbnail = $product ? apply_filters('wc_quote_item_thumbnail', $product->get_image(array( 50, 50 ), array( 'style' => 'border: 2px solid #e8e8e8 !important' ), false), $product->get_id(), $item) : '';
				?>
				<tr>
					<td style="color:#636363;border:1px solid #e5e5e5;border-right: none;padding:12px;text-align:left;vertical-align:middle;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif">
				<?php echo '<a href="' . esc_url($product->get_permalink()) . '">' . wp_kses_post($thumbnail) . '</a>'; ?>
					</td>
					<td style="color:#636363;border:1px solid #e5e5e5;border-left: none;padding:12px;text-align:left;vertical-align:middle;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif;word-wrap:break-word">
						<a href="<?php echo esc_url($product->get_permalink()); ?>"><?php echo wp_kses_post(html_entity_decode($product_name, ENT_COMPAT)); ?></a>
				<?php echo wp_kses_post(wc_get_formatted_cart_item_data($item)); ?>
						<br>
						<small>
							<b>SKU:</b><?php echo esc_html($product->get_sku()); ?>
						</small>
					</td>

					<td style="color:#636363;border:1px solid #e5e5e5;padding:12px;text-align:center;vertical-align:middle;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif">
				<?php echo esc_attr($item['quantity']); ?>
					</td>

					<td style="color:#636363;border:1px solid #e5e5e5;padding:12px;text-align:left;vertical-align:middle;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif">
					<?php 
					if ($product->get_type() === 'subscription' || $product->get_type() === 'variable-subscription' ) :
							echo wp_kses_post(WC_Subscriptions_Product::get_price_string($product, array(
								'price' => wc_price($price),
								'sign_up_fee' => $signup_fee
							)));
							else :
								echo wp_kses_post(wc_price($price));
						endif; 
							?>
					</td>

				<?php if (in_array(get_post($quote_id)->post_status, array( 'quote_quoted', 'quote_accepted' )) ) : ?>
						<td style="color:#636363;border:1px solid #e5e5e5;padding:12px;text-align:left;vertical-align:middle;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif">
					<?php 
					if ($product->get_type() === 'subscription' || $product->get_type() === 'variable-subscription' ) :
						echo wp_kses_post(WC_Subscriptions_Product::get_price_string($product, array(
							'price' => wc_price($offered_price),
							'sign_up_fee' => $offered_signup
						)));
							else :
								echo wp_kses_post(wc_price($offered_price));
							endif; 
							?>
						</td>
				<?php endif; ?>
				</tr>
			<?php endforeach; ?>
		</tbody>
		<tfoot>
			<tr>
				<th scope="row" colspan="<?php echo intval($colspan); ?>" style="color:#636363;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left;">
				<?php echo esc_html__('Subtotal(standard)', 'wc-quote-lite'); ?>:</th>
				<td style="color:#636363;border:0.01px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left;"></td>
				<td style="color:#636363;border:0.01px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left;"></td>
				<td style="color:#636363;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left;">
				<?php echo wp_kses_post(wc_price($quote_subtotal)); ?>
				</td>
			</tr>

			<?php if (in_array(get_post($quote_id)->post_status, array( 'quote_quoted', 'quote_accepted' )) ) : ?>
			<tr>
				<th scope="row" colspan="<?php echo intval($colspan); ?>" style="color:#636363;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left;">
				<?php echo esc_html__('Subtotal(offered)', 'wc-quote-lite'); ?>:</th>
				<td style="color:#636363;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left">
				<?php echo wp_kses_post(wc_price($offered_total)); ?>
				</td>
			</tr>
			<?php endif; ?>
			<?php if (wc_tax_enabled()) : ?>
			<tr>
				<th scope="row" colspan="<?php echo intval($colspan); ?>" style="color:#636363;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left;border-top-width:4px">
				<?php echo esc_html__('Vat(Standard)', 'wc-quote-lite'); ?>:</th>
				<td style="color:#636363;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left;border-top-width:4px">
				<?php echo wp_kses_post(wc_price($vat_total)); ?>
				</td>
			</tr>
			<?php endif; ?>
	
			<tr>
				<th scope="row" colspan="<?php echo intval($colspan); ?>" style="color:#636363;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left;">
				<?php echo esc_html__('Total(standard)', 'wc-quote-lite'); ?>:</th>
				<td style="color:#636363;border:0.01px solid #e5e5e5;"></td>
				<td style="color:#636363;border:0.01px solid #e5e5e5;"></td>
				<td style="color:#636363;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left;">
				<?php echo wp_kses_post(wc_price($quote_total)); ?>
				</td>
			</tr>
			
		</tfoot>
	</table>
</div>
