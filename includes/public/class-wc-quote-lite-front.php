<?php

/**
 * WC_QUOTE_Front
 *
 * The WooCommerce quote front ajax class stores quote data and maintain session of quotes.
 * The quote class also has a price calculation function which calls upon other classes to calculate totals.
 */

defined('ABSPATH') || exit;

if (! class_exists('WAQT_Front', false) ) :
	/**
	 * WC_QUOTE_Front class.
	 */
	class WAQT_Front {
	
		private $quote_settings;


		public function __construct() {
			
			$this->quote_settings['general'] = get_option('wc_quote_general', false);
			$this->quote_settings['customization'] = get_option('wc_quote_customization', false);

			// Process form submit actions.
			add_action('wp_loaded', array( $this, 'wc_quote_convert_to_cart' ));
			add_action('wp_loaded', array( $this, 'wc_quote_insert_customer_quote' ));

			add_action('woocommerce_after_cart_totals', array( $this, 'wc_quote_build_quote_button' ));

			// Request a Quote page short code. 
			add_shortcode('wc-quote-request-page', array( $this, 'wc_quote_request_page_shortcode_function' ));

			// Add endpoint of quote and process its content.
			add_action('init', array( $this, 'wc_quote_add_endpoints' ));
			add_filter('woocommerce_account_menu_items', array( $this, 'wc_quote_new_menu_items' ));
			add_action('woocommerce_account_my-quotes_endpoint', array( $this, 'wc_quote_endpoint_content' ));
			add_filter('query_vars', array( $this, 'wc_quote_add_query_vars' ), 0);
			add_filter('the_title', array( $this, 'wc_quote_endpoint_title' ));
			
			// Start customer session for guest users.
			add_action('woocommerce_init', array( $this, 'wc_quote_start_customer_session' ));
			add_action('woocommerce_after_main_content', array( $this, 'wc_quote_add_shop_shortcode_content' ));

			// Load and update saved quotes of registered users.
			add_action('wp_login', array( $this, 'wc_quote_update_quote_data_after_login' ), 100, 2);
			add_action('woocommerce_before_calculate_totals', array( $this, 'wc_quote_cart_price_override' ));
			// Load and show quote page.
			add_filter('the_content', array( $this, 'wc_quote_add_shortcode_content' ));
			
			add_filter('wwp_quantity_min_check', array( $this, 'wc_quote_override_wholesale_qty' ), 400, 4);
			add_filter('wwp_cart_quantity', array( $this, 'wc_quote_override_cart_qty' ), 400, 2);
			add_filter('wwp_is_cart_or_checkout_page', array( $this, 'wc_quote_is_quote_page' ), 400);
			add_filter('wwp_is_admin_product_price', array( $this, 'wc_quote_tier_pricing_bypass' ), 400);

			add_filter('woocommerce_is_attribute_in_product_name', array( $this, 'wc_quote_attr' ));
			add_filter('body_class', array( $this, 'wc_quote_add_class' ));
			add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'action_woocommerce_after_add_to_cart_button' ));

			add_action('woocommerce_single_product_summary', array( $this, 'hide_add_to_cart_btn_single_product_page' ));
			add_action('woocommerce_init', array( $this, 'hide_add_to_cart_btn_shop_page' ));
		}
	

		public function hide_add_to_cart_btn_shop_page(){

			$quote_settings['general'] = get_option('wc_quote_general', false);
			if ( isset($quote_settings['general']['add_to_cart_hide_global']) && 1 == $quote_settings['general']['add_to_cart_hide_global'] ) {

				add_filter( 'woocommerce_loop_add_to_cart_link', array( $this, 'hide_block_add_to_cart_btn' ) , 10, 3  );
				remove_action('woocommerce_after_add_to_cart_quantity', array( $this, 'woocommerce_simple_add_to_cart' ));
		
			}
		}


		public function hide_add_to_cart_btn_single_product_page(){

			global $product;
			
			$quote_settings['general'] = get_option('wc_quote_general', false);
			
			if ( isset($quote_settings['general']['add_to_cart_hide_global']) && 1 == $quote_settings['general']['add_to_cart_hide_global'] ) {
				$this->hide_add_cart_single_product_page();
			}
		
			if ( isset($quote_settings['general']['add_to_cart_on_detail_page']) && 1 == $quote_settings['general']['add_to_cart_on_detail_page'] ) {
				$this->hide_add_cart_single_product_page();
			}
		}


		public function hide_add_cart_single_product_page(){
			global $product;
			if ( $product->is_type( 'simple' ) ) {
				remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30); 
				add_action('woocommerce_single_product_summary', array( $this, 'custom_single_add_to_cart_template' ), 30);
			}

			if ( $product->is_type( 'variable' ) ) {
				remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20 );
				add_action('woocommerce_single_variation', array( $this, 'custom_single_add_to_cart_template' ), 20);
			}
		}
		
		public function custom_single_add_to_cart_template() {
			global $product;

			if ( $product->is_type( 'simple' ) ) {
				require_once WATQ_QUOTE_PATH . 'includes/public/views/hide-single-add-to-cart-btn.php';
			}

			if ( $product->is_type( 'variable' ) ) {
				require_once WATQ_QUOTE_PATH . 'includes/public/views/hide-variation.php';
			}
		}
	
		public function hide_block_add_to_cart_btn( $class, $product, $args ){
			return '';
		}
	

		// for adding cart class on quote page
		public function wc_quote_add_class( $classes ) {
			$url     = wp_get_referer();
			$post_id = 0 !== get_queried_object_id() ? get_queried_object_id() : url_to_postid($url);
			$quote_page = isset($this->quote_settings['general']['quote_page']) ? $this->quote_settings['general']['quote_page'] : '';
			$post_content = isset(get_post($post_id)->post_content) ? get_post($post_id)->post_content : '';
			if ($post_id === (int) $quote_page || has_shortcode($post_content, 'wc-quote-request-page') ) :
				$classes[] = 'woocommerce-cart';
				$classes[] = 'woocommerce-page';
				$classes[] = 'woocommerce-quote';
			endif;
			return $classes;
		}

		// for showing variations on quote list
		public function wc_quote_attr() {
			return false;
		}

		public function wc_quote_tier_pricing_bypass( $is_admin ) {
			$url     = wp_get_referer();
			$post_id = 0 !== get_queried_object_id() ? get_queried_object_id() : url_to_postid($url);
			$quote_contents = isset(WC()->session) ? WC()->session->get('wc_quotes') : get_post_meta(get_the_ID(), 'quote_contents', true);
			$quote_page = isset($this->quote_settings['general']['quote_page']) ? $this->quote_settings['general']['quote_page'] : '';
			$post_content = isset(get_post($post_id)->post_content) ? get_post($post_id)->post_content : '';
			$quote_id = isset($_REQUEST['post_id']) ? sanitize_text_field($_REQUEST['post_id']) : get_the_ID();
			if (( !empty($quote_contents) && $post_id === (int) $quote_page ) || ( !empty($quote_contents) && has_shortcode($post_content, 'wc-quote-request-page') ) || ( !empty($quote_contents) && get_post_type($quote_id) === 'wc-quote' ) || ( !empty($quote_contents) && isset($_REQUEST['action']) && 'wcquote_add_to_quote' === sanitize_text_field($_REQUEST['action']) ) || ( 'wcquote_add_to_quote_single' === sanitize_text_field($_REQUEST['action']) || 'wcquote_add_to_quote_variable' === sanitize_text_field($_REQUEST['action']) || 'wcquote_update_quote_items' === sanitize_text_field($_REQUEST['action']) ) ) :
				return false;
			endif;
			return $is_admin;
		}

		public function wc_quote_is_quote_page( $is_cart_or_checkout_page ) {
			$url     = wp_get_referer();
			$post_id = 0 !== get_queried_object_id() ? get_queried_object_id() : url_to_postid($url);
			$quote_contents = isset(WC()->session) ? WC()->session->get('wc_quotes') : get_post_meta(get_the_ID(), 'quote_contents', true);
			$quote_page = isset($this->quote_settings['general']['quote_page']) ? $this->quote_settings['general']['quote_page'] : '';
			$post_content = isset(get_post($post_id)->post_content) ? get_post($post_id)->post_content : '';
			$quote_id = isset($_REQUEST['post_id']) ? sanitize_text_field($_REQUEST['post_id']) : get_the_ID();
			if (( !empty($quote_contents) && $post_id === (int) $quote_page ) || ( !empty($quote_contents) && has_shortcode($post_content, 'wc-quote-request-page') ) || ( !empty($quote_contents) && get_post_type($quote_id) === 'wc-quote' ) || ( !empty($quote_contents) && isset($_REQUEST['action']) &&  'wcquote_add_to_quote' === sanitize_text_field($_REQUEST['action']) )  || ( 'wcquote_add_to_quote_single' === sanitize_text_field($_REQUEST['action']) || 'wcquote_add_to_quote_variable' === sanitize_text_field($_REQUEST['action']) || 'wcquote_update_quote_items' === sanitize_text_field($_REQUEST['action']) ) ) :
				return true;
			endif;
			return $is_cart_or_checkout_page;
		}

		public function wc_quote_override_cart_qty( $qty, $product_id ) {
			$url     = wp_get_referer();
			$post_id = 0 !== get_queried_object_id() ? get_queried_object_id() : url_to_postid($url);
			$quote_contents = isset(WC()->session) ? WC()->session->get('wc_quotes') : get_post_meta(get_the_ID(), 'quote_contents', true);
			$quote_page = isset($this->quote_settings['general']['quote_page']) ? $this->quote_settings['general']['quote_page'] : '';
			$post_content = isset(get_post($post_id)->post_content) ? get_post($post_id)->post_content : '';
			$quote_id = isset($_REQUEST['post_id']) ? sanitize_text_field($_REQUEST['post_id']) : get_the_ID();
			if (( !empty($quote_contents) && $post_id === (int) $quote_page ) || ( !empty($quote_contents) && has_shortcode($post_content, 'wc-quote-request-page') ) || ( !empty($quote_contents) && get_post_type($quote_id) === 'wc-quote' ) || ( !empty($quote_contents) && isset($_REQUEST['action']) && 'wcquote_add_to_quote' === sanitize_text_field($_REQUEST['action']) ) || ( 'wcquote_add_to_quote_single' === sanitize_text_field($_REQUEST['action']) || 'wcquote_add_to_quote_variable' === sanitize_text_field($_REQUEST['action']) || 'wcquote_update_quote_items' === sanitize_text_field($_REQUEST['action']) ) ) :
				foreach ( (array) $quote_contents as $quote_item_key => $quote_item ) {

					if (isset($quote_item['data']) ) {

						$_product = $quote_item['data'];

					} else {

						continue;
					}

					if (! is_object($_product) ) {
						   continue;
					}

					if ($product_id === $_product->get_id() ) {
						$qty = $quote_item['quantity'];
						return $qty;
					}
				}
			endif;
			return $qty;
		}

		public function wc_quote_override_wholesale_qty( $check, $qty, $wholesale_qty, $product ) {
			$url     = wp_get_referer();
			$post_id = 0 !== get_queried_object_id() ? get_queried_object_id() : url_to_postid($url);
			$quote_contents = isset(WC()->session) ? WC()->session->get('wc_quotes') : get_post_meta(get_the_ID(), 'quote_contents', true);
			$quote_page = isset($this->quote_settings['general']['quote_page']) ? $this->quote_settings['general']['quote_page'] : '';
			$post_content = isset(get_post($post_id)->post_content) ? get_post($post_id)->post_content : '';
			$quote_id = isset($_REQUEST['post_id']) ? sanitize_text_field($_REQUEST['post_id']) : get_the_ID();
			if (( !empty($quote_contents) && $post_id === (int) $quote_page ) || ( !empty($quote_contents) && has_shortcode($post_content, 'wc-quote-request-page') ) || ( !empty($quote_contents) && get_post_type($quote_id) === 'wc-quote' ) || ( !empty($quote_contents) && isset($_REQUEST['action']) && 'wcquote_add_to_quote' === sanitize_text_field($_REQUEST['action']) ) || ( 'wcquote_add_to_quote_single' === sanitize_text_field($_REQUEST['action']) || 'wcquote_add_to_quote_variable' === sanitize_text_field($_REQUEST['action']) || 'wcquote_update_quote_items' === sanitize_text_field($_REQUEST['action']) ) ) :
				foreach ( (array) $quote_contents as $quote_item_key => $quote_item ) {

					if (isset($quote_item['data']) ) {

						$_product = $quote_item['data'];

					} else {

						continue;
					}

					if (! is_object($_product) ) {
						   continue;
					}

					if ($product->get_id() === $_product->get_id() ) {

						$qty = $quote_item['quantity'];
						if ('' != $qty && $qty < $wholesale_qty ) {
							return true;
						}
					}
				}
			endif;
		
			return false;
		}
		// Add quote request page in content of page selected in general
		public function wc_quote_add_shop_shortcode_content() {
			$quote_page = isset($this->quote_settings['general']['quote_page']) ? $this->quote_settings['general']['quote_page'] : '';
			if (is_shop() && get_option('woocommerce_shop_page_id', true) == (int) $quote_page ) {
				echo do_shortcode('[wc-quote-request-page]'); 
			}
		}

		// Add quote request page in content of page selected in general
		public function wc_quote_add_shortcode_content( $content ) {
			$quote_page = isset($this->quote_settings['general']['quote_page']) ? $this->quote_settings['general']['quote_page'] : '';
			if (get_the_ID() === (int) $quote_page && false !== strpos($content, '[wc-quote-request-page]') ) {
				return $content;
			} elseif (get_the_ID() === (int) $quote_page ) {
				return $content . '[wc-quote-request-page]';
			}
			return $content;
		}

		public function wc_quote_cart_price_override( $cart_object ) {
			global $woocommerce;
			$quote_contents = WC()->session->get('wc_quotes_temp');
			if (!empty($quote_contents) ) :
			
				foreach ( (array) $quote_contents as $quote_item_key => $quote_item ) {
					foreach ( $cart_object->cart_contents as $key => $value ) {
						if (isset($quote_item['data']) ) {

							$product = $quote_item['data'];

						} else {

							continue;
						}

						if (! is_object($product) ) {
							continue;
						}
						$product_id = $value['product_id'];
						$temp_id = $quote_item['product_id'];
					
						if ($product_id === $temp_id ) {
							$value['data']->price = $quote_item['offered_price'];
							$value['data']->set_price($quote_item['offered_price']);
							if (isset($quote_item['offered_signup']) && !empty($quote_item['offered_signup']) ) :
								$value['data']->update_meta_data('_subscription_sign_up_fee', $quote_item['offered_signup']);
							endif;
						}
					}
				}
			endif;
		}

		public function wc_quote_update_quote_data_after_login( $user_login, $user ) {
			if (isset(WC()->session) ) {
				$saved_quotes   = (array) get_user_meta($user->ID, 'wc_quote', true);
				$session_quotes = (array) WC()->session->get('wc_quotes');
				$final_quotes   = $session_quotes;

				// Merge saved quotes and session quotes.
				foreach ( (array) $saved_quotes as $key => $value ) {

					if (! isset($final_quotes[ $key ]) && ! empty($value) ) {
						$final_quotes[ $key ] = $value;
					}
				}

				// Filter quotes.
				foreach ( $final_quotes as $key => $value ) {
					if (empty($value) ) {
						unset($final_quotes[ $key ]);
					}
				}

				update_user_meta($user->ID, 'wc_quote', $final_quotes);
				WC()->session->set('wc_quotes', $final_quotes);
			}
		}

		public function wc_quote_insert_customer_quote() {

			if (isset($_POST['wc_quote_action']) ) {
			
				check_ajax_referer('save_wcquote', 'wc_quote_nonce');
				$data = (array) sanitize_meta('', wp_unslash($_POST), '');

				$wc_quote = new WC_QUOTE_Process();

				$wc_quote->insert_new_quote(array_merge($data, (array) $_FILES));
			}
		}

		public function wc_quote_convert_to_cart() {
			if (isset($_REQUEST['status']) ) {
				check_ajax_referer('wc_quote_status_nonce', 'accept_nonce');
				$data = $_REQUEST;
				$quote_id = base64_decode($data['quote_id']);
				$quote_id = sanitize_text_field(wp_unslash($quote_id));
				$quote_status = sanitize_text_field(wp_unslash($_REQUEST['status']));

				if (empty(intval($quote_id)) ) {
					return;
				}

				$wc_quote = new WC_QUOTE_Process();

				$wc_quote->convert_quote_to_cart($quote_id, $quote_status);

			} else {
				return;
			}
		}

		public function wc_quote_start_customer_session() {

			if (is_user_logged_in() || is_admin() ) {
				return;
			}

			if (isset(WC()->session) ) {
				if (! WC()->session->has_session() ) {
					WC()->session->set_customer_session_cookie(true);
				}
			}
		}


		public function wc_quote_build_quote_button() {
			
			$quote_page = isset($this->quote_settings['general']['quote_page']) ? $this->quote_settings['general']['quote_page'] : '';
			$build_quote_text = isset($this->quote_settings['general']['build_quote_text']) && !empty(sanitize_text_field($this->quote_settings['general']['build_quote_text'])) ? $this->quote_settings['general']['build_quote_text'] : 'Build Quote';
			echo '<div class="wc-proceed-to-checkout"><a href="javascript:void(0)" class="wp-element-button checkout-button build-quote-button button alt wc-forward" data-url="' . esc_url(get_page_link($quote_page)) . '">' . esc_attr($build_quote_text) . '</a></div>';
		}

		// Add new/extra button
		public function action_woocommerce_after_add_to_cart_button() {
			global $product;
			$allow_guest_user = isset($this->quote_settings['general']['allow_guest']) || !empty($this->quote_settings['general']['allow_guest']) ? $this->quote_settings['general']['allow_guest']: 0;
		
			
			$add_quote_text = __( 'Add to Quote', 'woocommerce' );
				
			if ('variable' === $product->get_type() || 'variable-subscription' === $product->get_type() ) {
				$disable_class = 'disabled wc-variation-selection-needed';
			} else {
				$disable_class = '';
			}
			
			if ( is_user_logged_in() || 1 == $allow_guest_user ) :
				echo '<a href="javascript:void(0)" rel="nofollow" data-product_id="' . intval( $product->get_ID() ) . '" data-product_sku="' . esc_attr( $product->get_sku() ) . '" class="wp-element-button wc_quote_single_page single_add_to_cart_button button alt ' . esc_attr( $disable_class ) . ' product_type_' . esc_attr( $product->get_type() ) . '">' . esc_attr( $add_quote_text ) . '</a>';
			endif;
		}
		
		public function wc_quote_request_page_shortcode_function() {
			ob_start();
			if (file_exists(get_stylesheet_directory() . '/woocommerce/wcquote/front/wc-quote-request-page.php') ) {
				include_once get_stylesheet_directory() . '/woocommerce/wcquote/front/wc-quote-request-page.php';
			} else {
				include_once WATQ_QUOTE_PATH . 'includes/public/views/wc-quote-request-page.php';
			}

			return ob_get_clean();
		}

		public function wc_quote_add_endpoints() {

			add_rewrite_endpoint('my-quotes', EP_ROOT | EP_PAGES);
			flush_rewrite_rules();
		}

		public function wc_quote_add_query_vars( $vars ) {
			$vars[] = 'my-quotes';
			return $vars;
		}

		public function wc_quote_endpoint_title( $title ) {
			global $wp_query;
			$is_endpoint = isset($wp_query->query_vars['my-quotes']);
			if ($is_endpoint && ! is_admin() && is_main_query() && in_the_loop() && is_account_page() ) {
				// New page title.
				$title = esc_html__('Quotes', 'wc-quote-lite');
				remove_filter('the_title', array( $this, 'endpoint_title' ));
			}
			return $title;
		}

		public function wc_quote_new_menu_items( $items ) {
			// Remove the logout menu item.
			$logout = $items['customer-logout'];
			unset($items['customer-logout']);
			// Insert your custom endpoint.
			$items['my-quotes'] = esc_html__('Quotes', 'wc-quote-lite');
			// Insert back the logout item.
			$items['customer-logout'] = $logout;
			return $items;
		}

		public function wc_quote_endpoint_content() {
			global $post;

			$wcquote_id = get_query_var('my-quotes');

			$quote = get_post($wcquote_id);

			if (! empty($wcquote_id) && is_a($quote, 'WP_Post') ) {
				if (file_exists(get_stylesheet_directory() . '/woocommerce/wcquote/front/wc-quote-details-my-account.php') ) {

					include get_stylesheet_directory() . '/woocommerce/wcquote/front/wc-quote-details-my-account.php';

				} else {

					include_once WATQ_QUOTE_PATH . 'includes/public/views/wc-quote-details-my-account.php';
				}
			} else {

				$customer_quotes = get_posts(
					array(
						'numberposts' => -1,
						'author'    => get_current_user_id(),
						'post_type'   => 'wc-quote',
					)
				);
				if (file_exists(get_stylesheet_directory() . '/woocommerce/wcquote/front/wc-quote-list-table.php') ) {

					include get_stylesheet_directory() . '/woocommerce/wcquote/front/wc-quote-list-table.php';

				} else {

					include_once WATQ_QUOTE_PATH . 'includes/public/views/wc-quote-list-table.php';
				}
			}
		}
	}
	new WAQT_Front();
endif;
