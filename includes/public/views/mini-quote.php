<?php
/**
 * Mini-quote
 *
 * Contains the drop down items of mini quote basket.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/wcquote/quote/mini-quote.php.
 */

defined('ABSPATH') || exit;
$quotes = (array) WC()->session->get('wc_quotes');
$quote_page = isset($this->quote_settings['general']['quote_page']) ? $this->quote_settings['general']['quote_page'] : get_the_ID();
$mini_cart = isset($this->quote_settings['general']['mini_cart']) ? $this->quote_settings['general']['mini_cart'] : '0';
$quote_basket_style = isset($this->quote_settings['general']['quote_basket_style']) ? $this->quote_settings['general']['quote_basket_style'] : 'dropdown';
$pageurl          = get_page_link($quote_page);
$quote_item_count = 0;
foreach ( $quotes as $qoute_item ) {

	$quote_item_count += isset($qoute_item['quantity']) ? $qoute_item['quantity'] : 0;
}
if ('1' === $mini_cart ) : ?>

	<li id="wc-quote-li" class="wc-quote-li">
		<a href="<?php echo esc_url($pageurl); ?>" title="<?php echo esc_html__('View Quote', 'wc-quote-lite'); ?>">
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="dashicons dashiconsc"><path d="M15.812 4.819c-.33-.341-.312-.877.028-1.207l3.469-3.365c.17-.164.387-.247.603-.247.219 0 .438.085.604.257l-4.704 4.562zm-5.705 8.572c-.07.069-.107.162-.107.255 0 .194.158.354.354.354.089 0 .178-.033.247-.1l.583-.567-.493-.509-.584.567zm4.924-6.552l-1.994 1.933c-1.072 1.039-1.619 2.046-2.124 3.451l.881.909c1.419-.461 2.442-.976 3.514-2.016l1.994-1.934-2.271-2.343zm5.816-5.958l-5.137 4.982 2.586 2.671 5.138-4.98c.377-.366.566-.851.566-1.337 0-1.624-1.968-2.486-3.153-1.336zm-11.847 12.119h-4v1h4v-1zm9-1.35v1.893c0 4.107-6 2.457-6 2.457s1.518 6-2.638 6h-7.362v-20h12.629l2.062-2h-16.691v24h10.189c3.163 0 9.811-7.223 9.811-9.614v-4.687l-2 1.951z"></path></svg>
			<span id="total-items" class="totalitems">
				<?php echo esc_attr($quote_item_count) . esc_html__(' items in quote', 'wc-quote-lite'); ?>
			</span>
		</a>
	<?php
	if ('dropdown' === $quote_basket_style ) :
		if (file_exists(get_stylesheet_directory() . '/woocommerce/wcquote/quote/mini-quote-dropdown.php') ) {

			include get_stylesheet_directory() . '/woocommerce/wcquote/quote/mini-quote-dropdown.php';

		} else {

			include WATQ_QUOTE_PATH . 'includes/public/views/mini-quote-dropdown.php';
		}
	endif;
	?>
	<li>
<?php endif; ?>
