<?php
/**
 * Quote details in Meta box.
 *
 * It shows the details of quotes items in meta box.
 */

defined('ABSPATH') || exit;

global $post;

$quote_contents = get_post_meta($post->ID, 'quote_contents', true);
$quote_status   = get_post_status($post);
$user_id        = $post->post_author;
$wc_quote       = new WC_QUOTE_Process($quote_contents);

$quote_totals = $wc_quote->get_calculated_totals((array) $quote_contents);
$quote_id     = $post->ID;

?>
<div class="woocommerce_order_items_wrapper wc-order-items-editable">
	<?php
	/**
	 * Action wc_quote_details_before_quote_table
	 * 
	 * @since 1.0
	 **/
	do_action('wc_quote_details_before_quote_table', $post);
	if (file_exists(get_stylesheet_directory() . '/woocommerce/wcquote/admin/html-quote-meta-list-items.php') ) {

		include_once get_stylesheet_directory() . '/woocommerce/wcquote/admin/html-quote-meta-list-items.php';

	} else {

		include_once WATQ_QUOTE_PATH . '/includes/admin/views/metabox/html-quote-meta-list-items.php';
	}
	/**
	* Action wc_quote_details_after_quote_table
	* 
	* @since 1.0
	**/
	do_action('wc_quote_details_after_quote_table', $post);
	?>
</div>
<?php
