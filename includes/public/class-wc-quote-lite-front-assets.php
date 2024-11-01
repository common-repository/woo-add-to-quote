<?php
/**
 * Handle frontend scripts
 */


if (! defined('ABSPATH') ) {
	exit;
}

if (! class_exists('WC_QUOTE_Front_Assets', false) ) :
	/**
	 * Frontend scripts class.
	 */
	class WAQT_Front_Assets {
	

		/**
		 * Hook in tabs.
		 */
		private $quote_settings;

		public function __construct() {
			$this->quote_settings['general'] = get_option('wc_quote_general', false);
			$this->quote_settings['notifications'] = get_option('wc_quote_notifications', false);
			add_action('wp_enqueue_scripts', array( $this, 'front_styles' ), 99);
			add_action('wp_enqueue_scripts', array( $this, 'front_scripts' ), 99);
		}


		public function front_styles() {
			 
			$quote_page = isset($this->quote_settings['general']['quote_page']) ? $this->quote_settings['general']['quote_page'] : '';
			if (( get_queried_object_id() ===  (int) $quote_page ) || ( is_shop() && get_option('woocommerce_shop_page_id', true) == (int) $quote_page )) {

				wp_enqueue_style('wc_quote_bootstrap');
			
				/**
				 * Action wc_quote_page_css
				 * 
				 * @since 1.0
				**/
				do_action('wc_quote_page_css');

			}
			wp_enqueue_style('wc_quote_front_styles', WATQ_PLUGIN_URL . 'assets/public/css/wc-quote-front.css', false, WATQ_VERSION);
		}
		public function front_scripts() {
	
			$suffix = ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) ? '' : '';
			$post_id = get_the_ID();
			wp_register_script('wc-quote-front', WATQ_PLUGIN_URL . 'assets/public/js/wc-quote-front' . $suffix . '.js', array( 'jquery' ), WATQ_VERSION);
			wp_register_script('wc-quote-front-fields', WATQ_PLUGIN_URL . '/assets/public/js/wc-quote-front-fields' . $suffix . '.js', array( 'jquery' ), WATQ_VERSION, true);
			wp_register_script(
				'wc-quote-lite-block', // Handle
				WATQ_PLUGIN_URL . '/build/index.js', // Script URL
				array( 'jquery', 'wp-blocks', 'wp-element', 'wp-i18n', 'wp-editor', 'wc-settings', 'wc-blocks-checkout' ), // Dependencies
				WATQ_VERSION, // Version
				true // In footer
			);
			$add_quote_redirect = isset($this->quote_settings['notifications']['add_quote_redirect']) ? $this->quote_settings['notifications']['add_quote_redirect'] : '0';
			$add_quote_redirect_url = isset($this->quote_settings['notifications']['add_quote_redirect_url']) ? $this->quote_settings['notifications']['add_quote_redirect_url'] : '';
			$quote_page = isset($this->quote_settings['general']['quote_page']) ? $this->quote_settings['general']['quote_page'] : '';
			$global_hide_add_to_cart = isset($this->quote_settings['general']['add_to_cart_hide_global']) ? $this->quote_settings['general']['add_to_cart_hide_global'] : 0;

			$wc_quote_vars = array(
				'ajax_url' => admin_url('admin-ajax.php'),
				'nonce'     => wp_create_nonce('wc-quote-ajax-nonce'),
				'redirect'  => $add_quote_redirect,
				'redirect_url'   => $add_quote_redirect_url,
				'post_id' => $post_id,
				'quote_page_url'=> esc_url(get_page_link($quote_page)),
				'global_hide_add_to_cart' => $global_hide_add_to_cart
			);
			wp_localize_script('wc-quote-front', 'wc_quote_vars', $wc_quote_vars);
			wp_localize_script('wc-quote-lite-block', 'wc_quote_vars', $wc_quote_vars);
			wp_enqueue_script('wc-quote-front');
			wp_enqueue_script('wc-quote-lite-block');
			$post_content = isset(get_post($post_id)->post_content) ? get_post($post_id)->post_content : '';
			$quote_page = isset($this->quote_settings['general']['quote_page']) ? $this->quote_settings['general']['quote_page'] : '';
			 
			if (( get_queried_object_id() ===  (int) $quote_page ) || ( is_shop() && get_option('woocommerce_shop_page_id', true) == (int) $quote_page ) || has_shortcode($post_content, 'wc-quote-request-page')) {

				$email_valid_msg = isset($this->quote_settings['notifications']['email_valid_msg']) && !empty($this->quote_settings['notifications']['email_valid_msg']) ? $this->quote_settings['notifications']['email_valid_msg'] : '{{field}} must be a valid email.';
				$current_user = wp_get_current_user();
				$wc_quote_fields_vars = array(

					'user_email' => $current_user->user_email,
					'invalid_email_msg' => $email_valid_msg,
				);
				wp_localize_script('wc-quote-front-fields', 'wc_quote_fields_vars', $wc_quote_fields_vars);
				wp_enqueue_script('wc-quote-front-fields');
	
				/**
				 * Action wc_quote_page_js
				 * 
				 * @since 1.0
				**/
				do_action('wc_quote_page_js');
			}
		}
	}
	new WAQT_Front_Assets();
endif;
