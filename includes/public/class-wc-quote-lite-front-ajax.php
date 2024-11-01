<?php
/**
 * WC_QUOTE_Front_Ajax
 *
 * The WooCommerce quote front ajax class stores quote data and maintain session of quotes.
 * The quote class also has a price calculation function which calls upon other classes to calculate totals.
 */

defined('ABSPATH') || exit;


if (! class_exists('WAQT_Front_Ajax', false) ) :
	/**
	 * WC_QUOTE_Front_Ajax class.
	 */
	class WAQT_Front_Ajax {

		/**
		 * Contains an array of quote items.
		 *
		 * @var array
		 */
		public $quote_contents = array();

		/**
		 * Contains an array of quote settings.
		 *
		 * @var array
		 */
		private $quote_settings;
		/**
		 * Contains an array of quote list widget ids.
		 *
		 * @var array
		 */
		public $quote_list_ids;
		/**
		 * Contains an array of quote list icon ids.
		 *
		 * @var array
		 */
		public $quote_icon_ids;
		/**
		 * Contains current post id
		 *
		 * @var int | string
		 */
		public $post_id;
		/**
		 * Contains an array of quote widget list html.
		 *
		 * @var array
		 */
		public $quote_widget_list;
		/**
		 * Contains an array of quote widget icon list html.
		 *
		 * @var array
		 */
		public $quote_widget_icon;


		/**
		 * Constructor for the WC_QUOTE_Front_Ajax class. Loads quote contents.
		 */
		public function __construct() {

			$this->quote_settings['general'] = get_option('wc_quote_general');

			/**
			* Ajax check variation available action.
			*/
			add_action('wp_ajax_wc_check_availability_of_quote', array( $this, 'wc_check_availability_of_quote' ));
			add_action('wp_ajax_nopriv_wc_check_availability_of_quote', array( $this, 'wc_check_availability_of_quote' ));

			/**
			* Ajax add to quote action.
			*/
			add_action('wp_ajax_wcquote_add_to_quote', array( $this, 'wc_quote_add_to_quote' ));
			add_action('wp_ajax_nopriv_wcquote_add_to_quote', array( $this, 'wc_quote_add_to_quote' ));
			/**
			* Ajax Build a quote action.
			*/
			add_action('wp_ajax_wcquote_build_quote', array( $this, 'wc_quote_build_quote' ));
			add_action('wp_ajax_nopriv_wcquote_build_quote', array( $this, 'wc_quote_build_quote' ));
			/**
			* Ajax Build a quote action.
			*/
			add_action('wp_ajax_wcquote_add_to_cart', array( $this, 'wc_quote_add_to_cart' ));
			add_action('wp_ajax_nopriv_wcquote_add_to_cart', array( $this, 'wc_quote_add_to_cart' ));
			/**
			* Ajax add to quote single product action.
			*/
			add_action('wp_ajax_wcquote_add_to_quote_single', array( $this, 'wcquote_add_to_quote_single' ));
			add_action('wp_ajax_nopriv_wcquote_add_to_quote_single', array( $this, 'wcquote_add_to_quote_single' ));
			/**
			* Ajax add to quote variable product action.
			*/
			add_action('wp_ajax_wcquote_add_to_quote_variable', array( $this, 'wcquote_add_to_quote_variable' ));
			add_action('wp_ajax_nopriv_wcquote_add_to_quote_variable', array( $this, 'wcquote_add_to_quote_variable' ));
			/**
			* Ajax update quote list action.
			*/
			add_action('wp_ajax_wcquote_update_quote_items', array( $this, 'wcquote_update_quote_items' ));
			add_action('wp_ajax_nopriv_wcquote_update_quote_items', array( $this, 'wcquote_update_quote_items' ));
			/**
			* Ajax remove quote item action.
			*/
			add_action('wp_ajax_wcquote_remove_quote_item', array( $this, 'wcquote_remove_quote_item' ));
			add_action('wp_ajax_nopriv_wcquote_remove_quote_item', array( $this, 'wcquote_remove_quote_item' ));

			/**
			 * Empty Quote
			*/
			add_action('wp_ajax_wc_empty_quote', array( $this, 'wc_empty_quote' ));
			add_action('wp_ajax_nopriv_wc_empty_quote', array( $this, 'wc_empty_quote' ));
		}

		/**
		 * Empty Quote on quote page
		*/
		public function wc_empty_quote(){
			if (isset($_POST['nonce']) && ! empty($_POST['nonce']) ) {

				$nonce = sanitize_text_field(wp_unslash($_POST['nonce']));
			} else {
				$nonce = 0;
			}

			if (! wp_verify_nonce($nonce, 'wc-quote-ajax-nonce') ) {
				die('Failed ajax security check!');
			}
			
			WC()->session->set('wc_quotes', null);
			wp_die(); 
		}


		/**
		 * Get quote widget files 
		 */
		public function wcquote_get_widgets() {
			if (isset($_POST['nonce']) && ! empty($_POST['nonce']) ) {

				$nonce = sanitize_text_field(wp_unslash($_POST['nonce']));
			} else {
				$nonce = 0;
			}

			if (! wp_verify_nonce($nonce, 'wc-quote-ajax-nonce') ) {

				die('Failed ajax security check!');
			}

			$this->quote_list_ids = isset($_POST['quote_list_ids']) ? sanitize_meta('', wp_unslash($_POST['quote_list_ids']), '') : array();
			$this->quote_icon_ids = isset($_POST['quote_icon_ids']) ? sanitize_meta('', wp_unslash($_POST['quote_icon_ids']), '') : array();
			$this->post_id = isset($_POST['post_id']) ? sanitize_meta('', wp_unslash($_POST['post_id']), '') : '';

			$widget_quote_list = get_option('widget_quote_list', true);

			foreach ( $this->quote_list_ids as $quote_list_id ) {

				$quote_widget_key = str_replace('quote_list-', '', $quote_list_id);
				$instance = $widget_quote_list[ $quote_widget_key ];

				ob_start();
				if (file_exists(get_stylesheet_directory() . '/woocommerce/wcquote/quote/wc-quote-list-widget.php') ) {

					include get_stylesheet_directory() . '/woocommerce/wcquote/quote/wc-quote-list-widget.php';

				} else {

					include WATQ_QUOTE_PATH . 'includes/public/views/wc-quote-list-widget.php';
				}
				$this->quote_widget_list[ $quote_list_id ] = ob_get_clean();
			}


			$widget_quote_list_icon = get_option('widget_quote_list_icon', true);

			foreach ( $this->quote_icon_ids as $quote_icon_id ) {

				$quote_widget_key = str_replace('quote_list_icon-', '', $quote_icon_id);
				$instance = $widget_quote_list_icon[ $quote_widget_key ];

				ob_start();
				if (file_exists(get_stylesheet_directory() . '/woocommerce/wcquote/quote/wc-quote-icon-widget.php') ) {

					include get_stylesheet_directory() . '/woocommerce/wcquote/quote/wc-quote-icon-widget.php';

				} else {

					include WATQ_QUOTE_PATH . 'includes/public/views/wc-quote-icon-widget.php';
				}
				$this->quote_widget_icon[ $quote_icon_id ] = ob_get_clean();
			}
		}

		/**
		 * Check variation availability.
		 */
		public function wc_check_availability_of_quote() {

			$nonce = isset($_POST['nonce']) && '' !== $_POST['nonce'] ? sanitize_text_field(wp_unslash($_POST['nonce'])) : 0;

			if (!empty($_POST['nonce']) && ! wp_verify_nonce($nonce, 'wc-quote-ajax-nonce') ) {
				die('Failed ajax security check!');
			}

			$variation_id = isset($_POST['variation_id']) ? sanitize_text_field(wp_unslash($_POST['variation_id'])) : '';

			if (empty($variation_id) ) {
				wp_send_json_error();
			}

			$variation = wc_get_product($variation_id);
			$show_on_stock = isset($this->quote_settings['general']['show_on_stock']) ? $this->quote_settings['general']['show_on_stock'] : 'hide';
			if (! $variation->is_in_stock() && 'show' !== $show_on_stock ) {
				wp_send_json_error();
			}

			wp_send_json_success();
			die();
		}

		/**
		 * Ajax add to quote controller.
		 */
		public function wc_quote_add_to_quote() {
			if (isset($_POST['nonce']) && ! empty($_POST['nonce']) ) {
				$nonce = sanitize_text_field(wp_unslash($_POST['nonce']));
			} else {
				$nonce = 0;
			}

			if (! wp_verify_nonce($nonce, 'wc-quote-ajax-nonce') ) {
				die('Failed ajax security check!');
			}

			if (isset($_POST) ) {
				$form_data = sanitize_meta('', wp_unslash($_POST), '');
			} else {
				$form_data = '';
			} 
			 
			$product_id = isset($form_data['product_id']) ? intval($form_data['product_id']) : '';
			$quantity   = isset($form_data['quantity']) ? intval($form_data['quantity']) : 1;
			$quote_page = isset($this->quote_settings['general']['quote_page']) ? $this->quote_settings['general']['quote_page'] : '';
			

			$wc_quote = new WC_QUOTE_Process();
			 
			/**
			 * Filter wc_quote_add_to_quote_validation
			 * 
			 * @since 1.0
			**/
			$passed_validation = apply_filters('wc_quote_add_to_quote_validation', true, $product_id, $quantity, $form_data);

			if (!$passed_validation ) {
				echo 'failed';
				die();
			}

			$ajax_item_key = $wc_quote->add_to_quote($form_data, $product_id, $quantity);
			 
			if (is_user_logged_in() ) {
				update_user_meta(get_current_user_id(), 'wc_quote', WC()->session->get('wc_quotes'));
			}

			$quote_contents = wc()->session->get('wc_quotes');
			$product        = '';
			$product_name   = 'Product';

			if (isset($quote_contents[ $ajax_item_key ]) ) {
				$product = $quote_contents[ $ajax_item_key ]['data'];
			}
			 
			if (is_object($product) ) {
				$ajax_product_name = $product->get_name();
				 
			}

			if ('yes' === get_option('woocommerce_enable_ajax_add_to_cart') && false !== $ajax_item_key  ) {
			 
				ob_start();
				if (file_exists(get_stylesheet_directory() . '/woocommerce/wcquote/front/quote-table.php') ) {

					include get_stylesheet_directory() . '/woocommerce/wcquote/front/quote-table.php';

				} else {

					include WATQ_QUOTE_PATH . 'includes/public/views/quote-table.php';
				}
				$quote_table = ob_get_clean();


				ob_start();
				if (file_exists(get_stylesheet_directory() . '/woocommerce/wcquote/front/quote-totals-table.php') ) {

					include get_stylesheet_directory() . '/woocommerce/wcquote/front/quote-totals-table.php';

				} else {

					include WATQ_QUOTE_PATH . 'includes/public/views/quote-totals-table.php';
				}
				$quote_totals = ob_get_clean();

				$this->wcquote_get_widgets();

				if (empty($quote_totals) ) {
					$quote_totals = '';
				}
	 
				wp_send_json(
					array(
						'quote-item-key' => $ajax_item_key,
						'quote-table'  => $quote_table,
						'quote-totals' => $quote_totals,
						'quote-widget-list' => $this->quote_widget_list,
						'quote-widget-icon' => $this->quote_widget_icon,
						 
					)
				);
			} elseif (false === $ajax_item_key ) {

				/* translators: %s: Product name */
				wc_add_notice(sprintf(__('“%s” has not been added to your quote.', 'wc-quote-lite'), $ajax_product_name), 'error');
				echo 'success';
			} else {
				/* translators: %s: Product name */
				wc_add_notice(sprintf(__('<a href="%1$1s" class="wp-element-button button wc-forward">View Quote</a> “%2$2s” has been added to your quote.', 'wc-quote-lite'), esc_url(get_page_link($quote_page)), $ajax_product_name), 'success');
				echo 'success';
				 
			}

			die();
		}

		/**
		 * Ajax Build quote controller.
		 */
		public function wc_quote_build_quote() {
			global $woocommerce;
			if (isset($_POST['nonce']) && ! empty($_POST['nonce']) ) {
				$nonce = sanitize_text_field(wp_unslash($_POST['nonce']));
			} else {
				$nonce = 0;
			}

			if (! wp_verify_nonce($nonce, 'wc-quote-ajax-nonce') ) {
				die('Failed ajax security check!');
			}

			$empty_cart = isset($this->quote_settings['general']['empty_cart']) ? $this->quote_settings['general']['empty_cart'] : '0';


			$posted_data = $_POST;
			$items = $woocommerce->cart->get_cart();

			foreach ( $items as $item => $form_data ) {
				$product_id = isset($form_data['product_id']) ? intval($form_data['product_id']) : '';
				$quantity   = isset($form_data['quantity']) ? intval($form_data['quantity']) : 1;
				$variation_id   = isset($form_data['variation_id']) ? intval($form_data['variation_id']) : 0;
				$variation   = isset($form_data['variation']) ? $form_data['variation'] : array();
				$quote_page = isset($this->quote_settings['general']['quote_page']) ? $this->quote_settings['general']['quote_page'] : '';

				$wc_quote = new WC_QUOTE_Process();
				/**
				 * Filter wc_quote_add_to_quote_validation
				 * 
				 * @since 1.0
				**/
				$passed_validation = apply_filters('wc_quote_add_to_quote_validation', true, $product_id, $quantity, $form_data);

				if (!$passed_validation ) {
					echo 'failed';
					die();
				}
		 
				$quote_id = $wc_quote->generate_quote_id($product_id, $variation_id, $variation);

				// Find the quote item key in the existing quote.
				$quote_item_key = $wc_quote->find_product_in_quote($quote_id);
				if ($quote_item_key ) {
					$wc_quote->remove_quote_item($quote_item_key);
				}
				$quote_item_key = $wc_quote->add_to_quote($posted_data, $product_id, $quantity, $variation_id, $variation);
			 
				if (is_user_logged_in() ) {
					update_user_meta(get_current_user_id(), 'wc_quote', WC()->session->get('wc_quotes'));
				}
				 
				$quote_contents = wc()->session->get('wc_quotes');
				$product        = '';
				$product_name   = 'Product';

				if (isset($quote_contents[ $quote_item_key ]) ) {
					$product = $quote_contents[ $quote_item_key ]['data'];
				}

				if (is_object($product) ) {
					$product_name = $product->get_name();
				}

				if (false === $quote_item_key ) {
					/* translators: %s: Product name */
					wc_add_notice(sprintf(__('“%s” has not been added to your quote.', 'wc-quote-lite'), $product_name), 'error');
				} else {
					/* translators: %s: Product name */
					wc_add_notice(sprintf(__('“%s” has been added to your quote.', 'wc-quote-lite'), $product_name), 'success');
				}

			}
			/**
			 * Action wc_quote_after_build_quote
			 * 
			 * @since 1.0
			**/
			do_action('wc_quote_after_build_quote');
			if ('1' == $empty_cart ) :
				$woocommerce->cart->empty_cart(); 
			endif;

			echo 'success';
			die();
		}

		/**
		 * Ajax Add quote to cart controller.
		 */
		public function wc_quote_add_to_cart() {
			global $woocommerce;
			if (isset($_POST['nonce']) && ! empty($_POST['nonce']) ) {
				$nonce = sanitize_text_field(wp_unslash($_POST['nonce']));
			} else {
				$nonce = 0;
			}

			if (! wp_verify_nonce($nonce, 'wc-quote-ajax-nonce') ) {
				die('Failed ajax security check!');
			}

			$empty_quote = isset($this->quote_settings['general']['empty_quote_to_cart']) ? $this->quote_settings['general']['empty_quote_to_cart'] : '0';

			$items = wc()->session->get('wc_quotes');
			foreach ( $items as $item => $form_data ) {
				$product_id = isset($form_data['product_id']) ? intval($form_data['product_id']) : '';
				$quantity   = isset($form_data['quantity']) ? intval($form_data['quantity']) : 1;
				$variation_id   = isset($form_data['variation_id']) ? intval($form_data['variation_id']) : 0;
				$variation   = isset($form_data['variation']) ? $form_data['variation'] : array();
				/**
				 * Filter wc_quote_add_to_cart_validation
				 * 
				 * @since 1.0
				**/
				$passed_validation = apply_filters('wc_quote_add_to_cart_validation', true, $product_id, $quantity, $form_data);

				if (!$passed_validation ) {
					echo 'failed';
					die();
				}
				$product_cart_id = WC()->cart->generate_cart_id($product_id, $variation_id, $variation);
				$cart_item_key = WC()->cart->find_product_in_cart($product_cart_id);
				if ($cart_item_key ) {
					WC()->cart->remove_cart_item($cart_item_key);
				}
				$cart_item_key = $woocommerce->cart->add_to_cart($product_id, $quantity, $variation_id, $variation);

				$cart_contents = $woocommerce->cart->get_cart();
				$product        = '';
				$product_name   = 'Product';

				if (isset($cart_contents[ $cart_item_key ]) ) {
					$product = $cart_contents[ $cart_item_key ]['data'];
				}

				if (is_object($product) ) {
					$product_name = $product->get_name();
				}

				if (false === $cart_item_key ) {
					/* translators: %s: Product name */
					wc_add_notice(sprintf(__('“%s” has not been added to your cart.', 'wc-quote-lite'), $product_name), 'error');

				} else {
					/* translators: %s: Product name */
					wc_add_notice(sprintf(__('“%s” has been added to your cart.', 'wc-quote-lite'), $product_name), 'success');

					if ( 1 == $empty_quote ) {
						
						WC()->session->set('wc_quotes', null);
					}
				}
				 
			}
			echo 'success';

			die();
		}

		/**
		 * Ajax add to quote controller for single products.
		 */
		public function wcquote_add_to_quote_single() {

			if (isset($_POST['nonce']) && ! empty($_POST['nonce']) ) {

				$nonce = sanitize_text_field(wp_unslash($_POST['nonce']));
			} else {
				$nonce = 0;
			}

			if (! wp_verify_nonce($nonce, 'wc-quote-ajax-nonce') ) {

				die('Failed ajax security check!');
			}

			if (isset($_POST['form_data']) ) {
				parse_str(sanitize_meta('', wp_unslash($_POST['form_data']), ''), $form_data);
				if (isset($_POST['product_id']) ) {
					$form_data['add-to-cart'] = sanitize_text_field(wp_unslash($_POST['product_id']));
				}
			} else {
				$form_data = array();
			}

			$product_id   = isset($form_data['add-to-cart']) ? intval($form_data['add-to-cart']) : '';
			
			$quantity     = isset($form_data['quantity']) ? intval($form_data['quantity']) : 1;
			$variation_id = isset($form_data['variation_id']) ? intval($form_data['variation_id']) : '';
			$variation    = array();
			$quote_page = isset($this->quote_settings['general']['quote_page']) ? $this->quote_settings['general']['quote_page'] : '';
			$wc_quote = new WC_QUOTE_Process();
			/**
			 * Filter wc_add_to_quote_validation
			 * 
			 * @since 1.0
			**/
			$passed_validation = apply_filters('wc_add_to_quote_validation', true, $product_id, $quantity, $form_data);

			if (!$passed_validation ) {
				echo 'failed';
				die();
			}
			 
			$quote_item_key = $wc_quote->add_to_quote($form_data, $product_id, $quantity);

			$quote_contents = wc()->session->get('wc_quotes');

			if (is_user_logged_in() ) {
				update_user_meta(get_current_user_id(), 'wc_quote', WC()->session->get('wc_quotes'));
			}

			$product = '';
			if (isset($quote_contents[ $quote_item_key ]) ) {
				$product = $quote_contents[ $quote_item_key ]['data'];
			} else {
				$product = wc_get_product($product_id);
			}
			
			$product_name = 'Product';
			if (is_object($product) ) {
				$product_name = $product->get_name();
			}
			if ('yes' === get_option('woocommerce_enable_ajax_add_to_cart') && false !== $quote_item_key  ) {
							
				$this->wcquote_get_widgets();
				
				wp_send_json(
					array(
						'quote-widget-list' => $this->quote_widget_list,
						'quote-widget-icon' => $this->quote_widget_icon,
					)
					);
			} elseif (false === $quote_item_key ) {
				/* translators: %s: Product name */
				wc_add_notice(sprintf(__('Quote is not available for “%s”.', 'wc-quote-lite'), $product_name), 'error');
				echo 'success';
			} else {
				/* translators: %s: Product name */
				wc_add_notice(sprintf(__('<a href="%1$1s" class="wp-element-button button wc-forward">View Quote</a> “%2$2s” has been added to your quote.', 'wc-quote-lite'), esc_url(get_page_link($quote_page)), $product_name), 'success');
				echo 'success';
			}   
			die();
		}

		/**
		 * Ajax add to quote controller for variable.
		 */
		public function wcquote_add_to_quote_variable() {

			if (isset($_POST['nonce']) && ! empty($_POST['nonce']) ) {
				$nonce = sanitize_text_field(wp_unslash($_POST['nonce']));
			} else {
				$nonce = 0;
			}

			if (! wp_verify_nonce($nonce, 'wc-quote-ajax-nonce') ) {

				die('Failed ajax security check!');
			}

			if (isset($_POST['form_data']) ) {
				parse_str(sanitize_meta('', wp_unslash($_POST['form_data']), ''), $form_data);
			} else {
				$form_data = '';
			}
		 
		 
			$product_id   = isset($form_data['add-to-cart']) ? intval($form_data['add-to-cart']) : '';
			$quantity     = isset($form_data['quantity']) ? intval($form_data['quantity']) : 1;
			$variation_id = isset($form_data['variation_id']) ? intval($form_data['variation_id']) : '';
			$variation    = array();
			$quote_page = isset($this->quote_settings['general']['quote_page']) ? $this->quote_settings['general']['quote_page'] : '';
			 
			foreach ( $form_data as $key => $value ) {

				if (! in_array($key, array( 'add-to-cart', 'quantity', 'variation_id', 'product_id' ), true) ) {

					$variation[ $key ] = $value;
				}
			}

			$wc_quote = new WC_QUOTE_Process();
			/**
			 * Filter wc_add_to_quote_validation
			 * 
			 * @since 1.0
			**/
			$passed_validation = apply_filters('wc_add_to_quote_validation', true, $product_id, $quantity, $form_data);

			if (!$passed_validation ) {
				echo 'failed';
				die();
			}

			$quote_item_key = $wc_quote->add_to_quote($form_data, $product_id, $quantity, $variation_id, $variation);

			$quote_contents = wc()->session->get('wc_quotes');

			if (is_user_logged_in() ) {
				update_user_meta(get_current_user_id(), 'wc_quote', WC()->session->get('wc_quotes'));
			}

			$product = '';

			if (isset($quote_contents[ $quote_item_key ]) ) {
				$product = $quote_contents[ $quote_item_key ]['data'];
			} else {
				$product = wc_get_product($variation_id);
			}

			$product_name = 'Product';
			if (is_object($product) ) {
				$product_name = $product->get_name();
			}
			if ('yes' === get_option('woocommerce_enable_ajax_add_to_cart') && false !== $quote_item_key  ) {

				$this->wcquote_get_widgets();

				wp_send_json(
					array(
						'quote-widget-list' => $this->quote_widget_list,
						'quote-widget-icon' => $this->quote_widget_icon,
					)
				);
			} elseif (false === $quote_item_key ) {
				/* translators: %s: Product name */
				wc_add_notice(__('Quote is not available for selected variation.', 'wc-quote-lite'), 'error');
				echo 'success';
			}

			die();
		}

		/**
		 * Ajax update quote controller.
		 */
		public function wcquote_update_quote_items() {

			if (isset($_POST['nonce']) && ! empty($_POST['nonce']) ) {
				$nonce = sanitize_text_field(wp_unslash($_POST['nonce']));
			} else {
				$nonce = 0;
			}

			if (! wp_verify_nonce($nonce, 'wc-quote-ajax-nonce') ) {

				die('Failed ajax security check!');
			}

			if (isset($_POST['form_data']) ) {
				parse_str(sanitize_meta('', wp_unslash($_POST['form_data']), ''), $form_data);
			} else {
				$form_data = '';
			}

			$quotes = WC()->session->get('wc_quotes');

			foreach ( WC()->session->get('wc_quotes') as $quote_item_key => $quote_item ) {

				if (isset($form_data['quote_qty'][ $quote_item_key ]) ) {

					if (0 == $form_data['quote_qty'][ $quote_item_key ] ) {

						unset($quotes[ $quote_item_key ]);

					} else {

						$quotes[ $quote_item_key ]['quantity'] = intval($form_data['quote_qty'][ $quote_item_key ]);
					}
				}

				if (isset($form_data['offered_price'][ $quote_item_key ]) ) {
					$quotes[ $quote_item_key ]['offered_price'] = floatval($form_data['offered_price'][ $quote_item_key ]);
				}
				
			}

			WC()->session->set('wc_quotes', $quotes);

			$quotes = WC()->session->get('wc_quotes');

			foreach ( WC()->session->get('wc_quotes') as $quote_item_key => $quote_item ) {

				if (isset($quote_item['quantity']) && empty($quote_item['quantity']) ) {

					unset($quotes[ $quote_item_key ]);
				}

				if (!isset($quote_item['data']) ) {
					unset($quotes[ $quote_item_key ]);
				}
			}

			WC()->session->set('wc_quotes', array_filter($quotes));
			/**
			 * Action wc_quote_session_changed
			 * 
			 * @since 1.0
			**/
			do_action('wc_quote_session_changed');

			if (is_user_logged_in() ) {
				update_user_meta(get_current_user_id(), 'wc_quote', WC()->session->get('wc_quotes'));
			}

			$wc_quote = new WC_QUOTE_Process();


			ob_start();

			if (file_exists(get_stylesheet_directory() . '/woocommerce/wcquote/front/quote-table.php') ) {

				include get_stylesheet_directory() . '/woocommerce/wcquote/front/quote-table.php';

			} else {

				include WATQ_QUOTE_PATH . 'includes/public/views/quote-table.php';
			}
			$quote_table = ob_get_clean();

			$message = wp_kses_post('<div class="woocommerce-message" role="alert">' . esc_html__('Quote updated', 'wc-quote-lite') . '</div>');

			ob_start();

			if (file_exists(get_stylesheet_directory() . '/woocommerce/wcquote/front/quote-totals-table.php') ) {

				include get_stylesheet_directory() . '/woocommerce/wcquote/front/quote-totals-table.php';

			} else {

				include WATQ_QUOTE_PATH . 'includes/public/views/quote-totals-table.php';
			}

			$quote_totals = ob_get_clean();

			$this->wcquote_get_widgets();
		 
			if (empty($quote_totals) ) {
				$quote_totals = '';
			}

			wp_send_json(
				array(
					'quote_empty'  => empty(WC()->session->get('wc_quotes')) ? true : false,
					'quote-table'  => $quote_table,
					'message'      => $message,
					'quote-widget-list' => $this->quote_widget_list,
					'quote-widget-icon' => $this->quote_widget_icon,
					'quote-totals' => $quote_totals,
				)
			);
		}

		/**
		 * Ajax remove item from quote.
		 */
		public function wcquote_remove_quote_item() {

			if (isset($_POST['nonce']) && ! empty($_POST['nonce']) ) {

				$nonce = sanitize_text_field(wp_unslash($_POST['nonce']));
			} else {
				$nonce = 0;
			}

			if (! wp_verify_nonce($nonce, 'wc-quote-ajax-nonce') ) {

				die('Failed ajax security check!');
			}


			$quote_key = isset($_POST['quote_key']) ? sanitize_text_field(wp_unslash($_POST['quote_key'])) : '';

			 
			if (empty($quote_key) ) {
				die('Quote key is empty');
			}

			$quotes = WC()->session->get('wc_quotes');
			 
			$product = $quotes[ $quote_key ]['data'];
			$deleted_product_name = '';
			if (is_object($product) ) {
				$deleted_product_name = $product->get_name();
			}
			unset($quotes[ $quote_key ]);

			WC()->session->set('wc_quotes', $quotes);
			/**
			 * Action wc_quote_session_changed
			 * 
			 * @since 1.0
			**/
			do_action('wc_quote_session_changed');

			if (is_user_logged_in() ) {
				update_user_meta(get_current_user_id(), 'wc_quote', WC()->session->get('wc_quotes'));
			}
			/**
			 * Action wc_quote_item_removed
			 * 
			 * @since 1.0
			**/
			do_action('wc_quote_item_removed', $quote_key, $product);

			ob_start();
			if (file_exists(get_stylesheet_directory() . '/woocommerce/wcquote/front/quote-table.php') ) {

				include get_stylesheet_directory() . '/woocommerce/wcquote/front/quote-table.php';

			} else {

				include WATQ_QUOTE_PATH . 'includes/public/views/quote-table.php';
			}
			$quote_table = ob_get_clean();

			/* translators: %s: Product name */
			$message      = sprintf(__('“%s” has been removed from quote basket.', 'wc-quote-lite'), $deleted_product_name);
			$message_html = '<div class="woocommerce-message" role="alert">' . $message . '</div>';

			ob_start();
			if (file_exists(get_stylesheet_directory() . '/woocommerce/wcquote/front/quote-totals-table.php') ) {

				include get_stylesheet_directory() . '/woocommerce/wcquote/front/quote-totals-table.php';

			} else {

				include WATQ_QUOTE_PATH . 'includes/public/views/quote-totals-table.php';
			}
			$quote_totals = ob_get_clean();
			 
			$this->wcquote_get_widgets();

			if (empty($quote_totals) ) {
				$quote_totals = '';
			}
			wp_send_json(
				array(
					'quote_empty'  => empty(WC()->session->get('wc_quotes')) ? true : false,
					'quote-table'  => $quote_table,
					'message'      => $message_html,
					'quote-widget-list' => $this->quote_widget_list,
					'quote-widget-icon' =>$this->quote_widget_icon,
					'quote-totals' => $quote_totals,
				)
			);

			die();
		}
	}
	new WAQT_Front_Ajax();
endif;
