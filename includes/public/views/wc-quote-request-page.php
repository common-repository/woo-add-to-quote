<?php
/**
 * Quote Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/wcquote/front/wc-quote-request-page.php.
 */

defined('ABSPATH') || exit;


$quotes = isset(WC()->session) ? WC()->session->get('wc_quotes') : '';
$wc_quote = new WC_QUOTE_Process($quotes);
$return_shop_text = isset($this->quote_settings['customization']['return_shop_text']) ? $this->quote_settings['customization']['return_shop_text'] : 'Return To Shop';
$enable_convert_cart = isset($this->quote_settings['general']['enable_convert_cart']) ? $this->quote_settings['general']['enable_convert_cart'] : '0';
$allow_guest = isset($this->quote_settings['general']['allow_guest']) ? $this->quote_settings['general']['allow_guest'] : '0';

// phpcs:disable
$current_user = wp_get_current_user();
$user_email = !empty($current_user->user_email) ? $current_user->user_email : ''; 
$current_user_id = get_current_user_id(); 
// phpcs:enable

if (!empty($quotes) ) {
	 
	foreach ( WC()->session->get('wc_quotes') as $quote_item_key => $quote_item ) {
		if (isset($quote_item['quantity']) && empty($quote_item['quantity']) ) {
			unset($quotes[ $quote_item_key ]);
		}

		if (!isset($quote_item['data']) ) {
			unset($quotes[ $quote_item_key ]);
		}
	}

	WC()->session->set('wc_quotes', $quotes);
}

if (isset(WC()->session) && ! empty(WC()->session->get('wc_quotes')) ) {

	$quotes         = WC()->session->get('wc_quotes');
	$total          = 0;
	$user           = null;
	$user_name      = '';
	$user_email_add = '';

	if (is_user_logged_in() ) {
		$user = wp_get_current_user(); // object
		if ('' == $user->user_firstname && '' == $user->user_lastname ) {
			$user_name = $user->nickname; // probably admin user
		} elseif ('' == $user->user_firstname || '' == $user->user_lastname ) {
			$user_name = trim($user->user_firstname . ' ' . $user->user_lastname);
		} else {
			$user_name = trim($user->user_firstname . ' ' . $user->user_lastname);
		}

		$user_email_add = $user->user_email;
	}
	/**
	 * Action wc_quote_before_quote
	 * 
	 * @since 1.0
	**/ 
	do_action('wc_quote_before_quote');

	?>
	<div class="woocommerce">
		<div class="woocommerce-notices-wrapper">
	</div>
		<form class="woocommerce-cart-form wc-quote-form" method="post" enctype="multipart/form-data">
			 
	<?php
	if (file_exists(get_stylesheet_directory() . '/woocommerce/wcquote/front/quote-table.php') ) {

		include get_stylesheet_directory() . '/woocommerce/wcquote/front/quote-table.php';

	} else {
		include WATQ_QUOTE_PATH . 'includes/public/views/quote-table.php';
		
	}

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
			 * Quote collaterals hook.
			 *
			 * @hooked wc_quote_cross_sell_display
			 * @hooked wc_quote_totals - 10
			 * @since  1.0
			 */
			do_action('wc_quote_collaterals');
			?>
			<div id="order_review" class="cart_totals">

				<?php 
				/**
				 * Action wc_quote_before_quote_totals
				 * 
				 * @since 1.0
				 **/ 
				do_action('wc_quote_before_quote_totals'); 
				?>
				
		<div class="wc-proceed-to-checkout form_row">
			<input name="wc_quote_action" type="hidden" value="save_wcquote"/>
			<?php wp_nonce_field('save_wcquote', 'wc_quote_nonce'); ?>
		</div>
			</div>
		</div>

	<?php if ( 1 == $allow_guest || is_user_logged_in() ) { ?>
			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label>
				<?php echo esc_html( apply_filters( 'watq_email_field_popup_guest', esc_html__( 'Write comma separated email addresses.', 'wc-quote-lite' ) ) ); ?>
				</label>
				<input type="hidden" name="quote_user_id" value="<?php echo esc_attr($current_user_id); ?>">
				<input type="text" class="woocommerce-Input woocommerce-Input--text wc-quote-send-email" name="_to_send_email" id="_to_send_email" value="<?php echo esc_attr( $user_email ); ?>">			
			</p>
			<a href="javascript:void(0)" id="wc_send_quote_email" class="wp-element-button button wc_quote_checkout_place_quote send_quote_button"><?php esc_html_e('Send Quote', 'wc-quote-lite'); ?></a>	
	<?php } ?>

	<?php 
	/**
	 * Action wc_quote_after_quote
	 * 
	 * @since 1.0
	 **/ 
	do_action('wc_quote_after_quote'); 
	?>

		</form>
	</div>

<?php } else { ?>

	<div class="wcquote woocommerce">
		<p class="cart-empty woocommerce-info"><?php echo esc_html__('Your quote is currently empty.', 'wc-quote-lite'); ?></p>
			<p class="return-to-shop"><a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="wp-element-button button wc-backward"><?php echo esc_html__($return_shop_text, 'wc-quote-lite'); ?></a>
		</p>
	</div>

<?php
}



