<?php
/**
 * WC Add to Quote Processing
 *
 * The WooCommerce quote class stores quote data and maintain session of quotes.
 * The quote class also has a price calculation function which calls upon other classes to calculate totals.
 */

if (! defined('ABSPATH') ) {
	exit;
}

/**
 * WC_QUOTE_Process class.
 */
class WC_QUOTE_Process {


	/**
	 * Contains an array of quote items.
	 *
	 * @var array
	 */
	public $quote_contents = array();

	/**
	 * Contains an array of quote items.
	 *
	 * @var array
	 */
	public $quote_subtotal = 0;

	/**
	 * Contains an array of quote items.
	 *
	 * @var array
	 */
	public $quote_tax_total = 0;

	/**
	 * Contains an array of quote items.
	 *
	 * @var array
	 */
	public $offered_total = 0;

	/**
	 * Contains an array of quote items.
	 *
	 * @var array
	 */
	public $quote_total = 0;

	/**
	 * Contains an array of quote settings.
	 *
	 * @var array
	 */
	private $quote_settings;

	/**
	 * Contains an array of quote fields.
	 *
	 * @var array
	 */
	private $quote_fields;

	/**
	 * Total defaults used to reset.
	 *
	 * @var array
	 */
	protected $default_totals = array(
		'subtotal'            => 0,
		'subtotal_tax'        => 0,
		'shipping_total'      => 0,
		'shipping_tax'        => 0,
		'shipping_taxes'      => array(),
		'discount_total'      => 0,
		'discount_tax'        => 0,
		'cart_contents_total' => 0,
		'cart_contents_tax'   => 0,
		'cart_contents_taxes' => array(),
		'fee_total'           => 0,
		'fee_tax'             => 0,
		'fee_taxes'           => array(),
		'total'               => 0,
		'total_tax'           => 0,
	);
	/**
	 * Store calculated totals.
	 *
	 * @var array
	 */
	protected $totals = array();

	/**
	 * Constructor for the Add_To_Quote class. Loads quote contents.
	 */
	public function __construct( $quote_contents_arr = array() ) {

		$this->quote_contents = $quote_contents_arr;
		$this->quote_settings['general'] = get_option('wc_quote_general', false);
		$this->quote_settings['notifications'] = get_option('wc_quote_notifications', false);
		
		if (empty($this->quote_contents) && isset(WC()->session) ) {
			$this->quote_contents = (array) WC()->session->get('wc_quotes');
		}

		add_filter('wp_safe_redirect_fallback', array( $this, 'custom_safe_redirect_fallback' ), 10, 2);
		add_action('init', array( $this, 'wc_quote_unread_quotes' ));
	}

	public function wc_quote_unread_quotes() {
		$waqt_unread_quotes = get_option('watq_unread_quotes');
		if ( $waqt_unread_quotes === false || (int) $waqt_unread_quotes < 0 ) {
			update_option('watq_unread_quotes', 0);
		}
	}

	public function custom_safe_redirect_fallback( $fallback_url, $status ) {
		$send_quote_redirect_url = isset($this->quote_settings['notifications']['send_quote_redirect_url']) ? $this->quote_settings['notifications']['send_quote_redirect_url'] : '';
		return $send_quote_redirect_url;
	}

	/**
	 * Get subtotal.
	 *
	 * @since  3.2.0
	 * @return float
	 */
	public function get_subtotal_tax() {
		/**
		 * Filter wc_quote___FUNCTION__
		 * 
		 * @since 1.0
		**/
		return apply_filters('wc_quote_' . __FUNCTION__, $this->get_totals_var('subtotal_tax'));
	}

	/**
	 * Get a total.
	 *
	 * @since  3.2.0
	 * @param  string $key Key of element in $totals array.
	 * @return mixed
	 */
	protected function get_totals_var( $key ) {
		return isset($this->totals[ $key ]) ? $this->totals[ $key ] : $this->default_totals[ $key ];
	}

	/**
	 * Returns 'incl' if tax should be included in cart, otherwise returns 'excl'.
	 *
	 * @return string
	 */
	public function get_tax_price_display_mode() {
		if (WC()->customer && WC()->customer->get_is_vat_exempt() ) {
			return 'excl';
		}

		return get_option('woocommerce_tax_display_cart');
	}

	/**
	 * Return whether or not the cart is displaying prices including tax, rather than excluding tax.
	 *
	 * @since  3.3.0
	 * @return bool
	 */
	public function display_prices_including_tax() {
		/**
		 * Filter wc_quote___FUNCTION__
		 * 
		 * @since 1.0
		**/
		return apply_filters('wc_quote_' . __FUNCTION__, 'incl' === $this->get_tax_price_display_mode());
	}

	/**
	 * Get the product row price per item.
	 *
	 * @param  WC_Product $product Product object.
	 * @return string formatted price
	 */
	public function get_product_price( $product, $args = array(), $view = 'view' ) {

		if ($this->display_prices_including_tax() ) {
			$product_price = wc_get_price_including_tax($product, $args);
		} else {
			$product_price = wc_get_price_excluding_tax($product, $args);
		}

		if ('edit' == $view ) {
			return $product_price;
		}

		$price_suffix = 'incl' === get_option('woocommerce_tax_display_cart') ? wc()->countries->inc_tax_or_vat() : '';

		$price_suffix = '<small>' . $price_suffix . '</small>';
		/**
		 * Filter wc_quote_product_price
		 * 
		 * @since 1.0
		**/
		return apply_filters('wc_quote_product_price', wc_price($product_price) . ' ' . $price_suffix, $product);
	}

	/**
	 * Get the quote subtotal.
	 *
	 * @param  WC_Product $product Product object.
	 * @return string formatted price
	 */
	public function calculate_totals() {

		if (is_admin() ) {
			return;
		}

		$this->quote_subtotal  = 0;
		$this->quote_tax_total = 0;
		$this->quote_total     = 0;
		$this->_offered_total  = 0;

		if (empty($this->quote_contents) && isset(WC()->session) ) {
			$this->quote_contents = WC()->session->get('wc_quotes');
		}

		foreach ( (array) $this->quote_contents as $quote_item_key => $quote_item ) {

			if (! isset($quote_item['data']) || ! is_object($quote_item['data']) ) {
				return;
			}
			/**
			 * Filter wc_quote_item_product
			 * 
			 * @since 1.0
			**/
			$product       = apply_filters('wc_quote_item_product', $quote_item['data'], $quote_item, $quote_item_key);
			$quantity      = $quote_item['quantity'];
			$price         = empty($quote_item['addons_price']) ? $product->get_price() : $quote_item['addons_price'];
			$offered_signup = 0;
			$signup_fee = 0;
			if ($product->get_type() === 'subscription' || $product->get_type() === 'variable-subscription' ) :
				$signup_fee = WC_Subscriptions_Product::get_sign_up_fee($product);
				$offered_signup = empty($quote_item['offered_signup']) ? $signup_fee : $quote_item['offered_signup'];
			endif;
			$offered_price = isset($quote_item['offered_price']) ? floatval($quote_item['offered_price']) : $price;

			$this->_offered_total += ( $offered_price + $offered_signup ) * intval($quantity);

			if ($product->is_taxable() ) {

				if (! wc_prices_include_tax() ) {
					$product_subtotal = wc_get_price_including_tax($product, array( 'qty' => $quantity ));
				} else {
					$product_subtotal = wc_get_price_excluding_tax($product, array( 'qty' => $quantity ));

				}

				$difference_price = ( $price * $quantity ) - $product_subtotal;

				if ($difference_price < 0 ) {

					$difference_price = $difference_price * -1;
				}

				$this->quote_subtotal  += ( $product_subtotal + $signup_fee ) * $quantity;
				$this->quote_tax_total += $difference_price;

			} else {
				$product_subtotal       = ( $price + $signup_fee ) * $quantity;
				 
				$this->quote_subtotal  += $product_subtotal;
				$this->quote_tax_total += 0;
			}
		}
		 
		$this->quote_total = $this->quote_subtotal + $this->quote_tax_total;
	}

	/**
	 * Get the quote subtotal.
	 *
	 * @param  WC_Product $product Product object.
	 * @return string formatted price
	 */
	public function get_calculated_totals( $contents = array() ) {

		$quote_totals = array(
			'_subtotal'      => 0,
			'_offered_total' => 0,
			'_tax_total'     => 0,
			'_total'         => 0,
			'calc_subtotal'     => 0,
		);

		if (empty($contents) ) {

			if (isset(WC()->session) ) {
				$contents = WC()->session->get('wc_quotes');
			}

			if (empty($contents) ) {
				return $quote_totals;
			}
		}

		foreach ( $contents as $quote_item_key => $quote_item ) {

			if (! isset($quote_item['data']) || ! is_object($quote_item['data']) ) {
				continue;
			}
			/**
			 * Filter wc_quote_item_product
			 * 
			 * @since 1.0
			**/
			$product       = apply_filters('wc_quote_item_product', $quote_item['data'], $quote_item, $quote_item_key);
			$quantity      = $quote_item['quantity'];
			$price         = empty($quote_item['addons_price']) ? $product->get_price() : $quote_item['addons_price'];
			if (class_exists('Wwp_Wholesale_Pricing') ) {
				 
				$Wholesale_Price_class = new WWP_Easy_Wholesale_Multiuser();
				remove_filter('woocommerce_available_variation', array( $Wholesale_Price_class, 'filter_woocommerce_available_variation' ), 200);
				remove_action('woocommerce_single_variation', array( $Wholesale_Price_class, 'variation_load_tire_priceing_table' ));
				remove_action('woocommerce_before_add_to_cart_form', array( $Wholesale_Price_class, 'simple_load_tire_priceing_table' ));
				$check_wwp = $Wholesale_Price_class->is_wholesale($product->get_id());
				$Wprice = !$check_wwp ? $Wholesale_Price_class->wwp_regular_price_change($price, $product) : $Wholesale_Price_class->wwp_regular_price_change(get_post_meta($product->get_id(), '_regular_price', true), $product);
				$price         = empty($quote_item['addons_price']) ? $Wprice : $quote_item['addons_price'];
			}
			$signup_fee = 0;
			$offered_signup = 0;
			if ($product->get_type() === 'subscription' || $product->get_type() === 'variable-subscription' ) :
				$signup_fee         = WC_Subscriptions_Product::get_sign_up_fee($product);
				$offered_signup = empty($quote_item['offered_signup']) ? $signup_fee : $quote_item['offered_signup'];
			endif;

			$offered_price = isset($quote_item['offered_price']) ? floatval($quote_item['offered_price']) : $price;


			$quote_totals['_offered_total'] += ( $offered_price + $offered_signup ) * intval($quantity);

			if ($product->is_taxable() ) {
				if (! wc_prices_include_tax() ) {
					$product_subtotal = wc_get_price_including_tax($product, array(
						'qty' => $quantity,
						'price' => $price
					));
					$quote_totals['calc_subtotal']  += ( $price + $signup_fee ) * $quantity;
					 
				} else {
					$product_subtotal = wc_get_price_excluding_tax($product, array(
						'qty' => $quantity,
						'price' => $price
					));
					$quote_totals['calc_subtotal']  += ( $product_subtotal + $signup_fee );
				}

				$difference_price = ( $price * $quantity ) - $product_subtotal;

				if ($difference_price < 0 ) {

					$difference_price = $difference_price * -1;
				}
				$quote_totals['_subtotal']  += ( $price + $signup_fee ) * $quantity;
				$quote_totals['_tax_total'] += $difference_price;

			} else {

				$product_subtotal            = ( $price + $signup_fee ) * $quantity;
				$quote_totals['calc_subtotal']  += $product_subtotal;
				$quote_totals['_subtotal']  += $product_subtotal;
				$quote_totals['_tax_total'] += 0;
			}
		}
		 
				 
		$quote_totals['_total'] = $quote_totals['calc_subtotal'] + $quote_totals['_tax_total'];

		return $quote_totals;
	}

	/**
	 * Get the product row price per item.
	 *
	 * @param  WC_Product $product Product object.
	 * @return string formatted price
	 */
	public function get_quote_subtotal() {

		$quote_subtotal = wc_price($this->quote_subtotal);
		/**
		 * Filter wc_quote_subtotal
		 * 
		 * @since 1.0
		**/
		return apply_filters('wc_quote_subtotal', $quote_subtotal, $this->quote_contents);
	}

	/**
	 * Get the product row price per item.
	 *
	 * @param  WC_Product $product Product object.
	 * @return string formatted price
	 */
	public function get_quote_offered_subtotal() {

		$quote_subtotal = wc_price($this->_offered_total);
		/**
		 * Filter wc_quote__offered_total
		 * 
		 * @since 1.0
		**/
		return apply_filters('wc_quote__offered_total', $quote_subtotal, $this->quote_contents);
	}

	/**
	 * Get the product row price per item.
	 *
	 * @param  WC_Product $product Product object.
	 * @return string formatted price
	 */
	public function get_quote_total() {

		$quote_total = wc_price($this->quote_total);
		/**
		 * Filter wc_quote_total
		 * 
		 * @since 1.0
		**/
		return apply_filters('wc_quote_total', $quote_total, $this->quote_contents);
	}

	/**
	 * Get the product row price per item.
	 *
	 * @param  WC_Product $product Product object.
	 * @return string formatted price
	 */
	public function get_quote_tax_total() {

		$quote_tax_total = wc_price($this->quote_tax_total);
		/**
		 * Filter wc_quote_tax_total
		 * 
		 * @since 1.0
		**/
		return apply_filters('wc_quote_tax_total', $quote_tax_total, $this->quote_contents);
	}

	/**
	 * Get the product row price per item.
	 *
	 * @param  WC_Product $product Product object.
	 * @return string formatted price
	 */
	public function get__offered_total() {

		$offered_total = wc_price($this->_offered_total);
		/**
		 * Filter wc_quote_tax_total
		 * 
		 * @since 1.0
		**/
		return apply_filters('wc_quote_tax_total', $offered_total, $this->quote_contents);
	}

	public function add_tax_product_price( $price, $product ) {

		$return_price = $price;
		if (wc_tax_enabled() ) {

			if ('no' === get_option('woocommerce_prices_include_tax') ) {

				$tax_rates = WC_Tax::get_rates($product->get_tax_class());
				$taxes     = WC_Tax::calc_tax($price, $tax_rates, false);

				if ('yes' === get_option('woocommerce_tax_round_at_subtotal') ) {
					$taxes_total = array_sum($taxes);
				} else {
					$taxes_total = array_sum(array_map('wc_round_tax_total', $taxes));
				}

				$return_price = round($price + $taxes_total, wc_get_price_decimals());

				return $return_price;
			}
		}

		return $return_price;
	}

	/**
	 * Get the product row subtotal.
	 *
	 * Gets the tax etc to avoid rounding issues.
	 *
	 * When on the checkout (review order), this will get the subtotal based on the customer's tax rate rather than the base rate.
	 *
	 * @param  WC_Product $product  Product object.
	 * @param  int        $quantity Quantity being purchased.
	 * @return string formatted price.
	 */
	public function get_product_offered_subtotal( $product, $price, $quantity ) {

		if ($product->is_taxable() ) {

			if ($this->display_prices_including_tax() ) {
				$row_price        = $this->add_tax_product_price($price, $product);
				$product_subtotal = wc_price($row_price);

				if (! wc_prices_include_tax() && $this->quote_tax_total > 0 ) {
					$product_subtotal .= ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>';
				}
			} else {
				$row_price        = wc_get_price_excluding_tax($product, array( 'qty' => $quantity ));
				$product_subtotal = wc_price($row_price);

				if (wc_prices_include_tax() && $this->quote_tax_total > 0 ) {
					$product_subtotal .= ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
				}
			}
		} else {
			$row_price        = $price * $quantity;
			$product_subtotal = wc_price($row_price);
		}
		/**
		 * Filter wc_quote_product_subtotal
		 * 
		 * @since 1.0
		**/
		return apply_filters('wc_quote_product_subtotal', $product_subtotal, $product, $quantity, $this);
	}


	/**
	 * Get the product row subtotal.
	 *
	 * Gets the tax etc to avoid rounding issues.
	 *
	 * When on the checkout (review order), this will get the subtotal based on the customer's tax rate rather than the base rate.
	 *
	 * @param  WC_Product $product  Product object.
	 * @param  int        $quantity Quantity being purchased.
	 * @return string formatted price
	 */
	public function get_product_subtotal( $product, $quantity, $args = array() ) {
		$price = empty($args['price']) ? $product->get_price() : $args['price'];

		if (class_exists('Wwp_Wholesale_Pricing') ) {
			$Wholesale_Price_class = new WWP_Easy_Wholesale_Multiuser();
			$check_wwp = $Wholesale_Price_class->is_wholesale($product->get_id());
			$Wprice = !$check_wwp ? $Wholesale_Price_class->wwp_regular_price_change($price, $product) : $Wholesale_Price_class->wwp_regular_price_change(get_post_meta($product->get_id(), '_regular_price', true), $product);
			$price = empty($args['price']) ? $Wprice : $args['price'];
		}

		if ($product->is_taxable() ) {

			if ($this->display_prices_including_tax() ) {
				$row_price        = wc_get_price_including_tax($product, $args);
				$product_subtotal = wc_price($row_price);

				if (! wc_prices_include_tax() && $this->quote_tax_total > 0 ) {
					$product_subtotal .= ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>';
				}
			} else {
				$row_price        = wc_get_price_excluding_tax($product, $args);
				$product_subtotal = wc_price($row_price);

				if (wc_prices_include_tax() && $this->quote_tax_total > 0 ) {
					$product_subtotal .= ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
				}
			}
		} else {
			$row_price        = $price * $quantity;
			$product_subtotal = wc_price($row_price);
		}
		/**
		 * Filter wc_quote_product_subtotal
		 * 
		 * @since 1.0
		**/
		return apply_filters('wc_quote_product_subtotal', $product_subtotal, $product, $quantity, $this);
	}

	public function check_quote_availability_for_variation( $variation_id ) {

		if (empty($variation_id) ) {
			return true;
		}

		$variation = wc_get_product($variation_id);
		$show_on_stock = isset($this->quote_settings['general']['show_on_stock']) ? $this->quote_settings['general']['show_on_stock'] : 'hide';
		if (! $variation->is_in_stock() && 'hide' === $show_on_stock ) {
			return false;
		}

		return true;
	}

	/**
	 * Add a product to the Quote.
	 *
	 * @throws Exception Plugins can throw an exception to prevent adding to quote.
	 * @param  int   $product_id      contains the id of the product to add to the quote.
	 * @param  int   $quantity        contains the quantity of the item to add.
	 * @param  int   $variation_id    ID of the variation being added to the quote.
	 * @param  array $variation       attribute values.
	 * @param  array $quote_item_data extra quote item data we want to pass into the item.
	 * @return string|bool $quote_item_key
	 */
	public function add_to_quote( $form_data = array(), $product_id = 0, $quantity = 1, $variation_id = 0, $variation = array(), $quote_item_data = array(), $return_contents = false ) {
		try {

			$product_id   = absint($product_id);
			$variation_id = absint($variation_id);

			// Ensure we don't add a variation to the quote directly by variation ID.
			if ('product_variation' === get_post_type($product_id) ) {
				$variation_id = $product_id;
				$product_id   = wp_get_post_parent_id($variation_id);
			}

			$product_data = wc_get_product($variation_id ? $variation_id : $product_id);
			/**
			 * Filter wc_quote_quantity
			 * 
			 * @since 1.0
			**/
			$quantity     = apply_filters('wc_quote_quantity', $quantity, $product_id);

			if ($quantity <= 0 || ! $product_data || 'trash' === $product_data->get_status() ) {
				return false;
			}

			if ($product_data->is_type('variation') ) {

				$missing_attributes = array();
				$parent_data        = wc_get_product($product_data->get_parent_id());

				$variation_attributes = $product_data->get_variation_attributes();
				// Filter out 'any' variations, which are empty, as they need to be explicitly specified while adding to quote.
				$variation_attributes = array_filter($variation_attributes);

				// Gather posted attributes.
				$posted_attributes = array();
				foreach ( $parent_data->get_attributes() as $attribute ) {
					if (! $attribute['is_variation'] ) {
						continue;
					}
					$attribute_key = 'attribute_' . sanitize_title($attribute['name']);

					if (isset($variation[ $attribute_key ]) ) {
						if ($attribute['is_taxonomy'] ) {
							// Don't use wc_clean as it destroys sanitized characters.
							$value = sanitize_title(wp_unslash($variation[ $attribute_key ]));
						} else {
							$value = html_entity_decode(wc_clean(wp_unslash($variation[ $attribute_key ])), ENT_QUOTES, get_bloginfo('charset'));
						}

						// Don't include if it's empty.
						if (! empty($value) || '0' === $value ) {
							$posted_attributes[ $attribute_key ] = $value;
						}
					}
				}

				// Merge variation attributes and posted attributes.
				$posted_and_variation_attributes = array_merge($variation_attributes, $posted_attributes);

				// If no variation ID is set, attempt to get a variation ID from posted attributes.
				if (empty($variation_id) ) {
					$data_store   = WC_Data_Store::load('product');
					$variation_id = $data_store->find_matching_product_variation($parent_data, $posted_attributes);
				}

				// Do we have a variation ID?
				if (empty($variation_id) ) {
					throw new Exception(__('Please choose product options&hellip;', 'wc-quote-lite'));
				}

				// Do we have a variation ID?
				if (! $this->check_quote_availability_for_variation($variation_id) ) {
					throw new Exception(__('Quote is not permitted for selected variation &hellip;', 'wc-quote-lite'));
				}

				// Check the data we have is valid.
				$variation_data = wc_get_product_variation_attributes($variation_id);
				$attributes     = array();

				foreach ( $parent_data->get_attributes() as $attribute ) {
					if (! $attribute['is_variation'] ) {
						continue;
					}

					// Get valid value from variation data.
					$attribute_key = 'attribute_' . sanitize_title($attribute['name']);
					$valid_value   = isset($variation_data[ $attribute_key ]) ? $variation_data[ $attribute_key ] : '';

					/**
					 * If the attribute value was posted, check if it's valid.
					 *
					 * If no attribute was posted, only error if the variation has an 'any' attribute which requires a value.
					 */
					if (isset($posted_and_variation_attributes[ $attribute_key ]) ) {
						$value = $posted_and_variation_attributes[ $attribute_key ];

						// Allow if valid or show error.
						if ($valid_value === $value ) {
							$attributes[ $attribute_key ] = $value;
						} elseif ('' === $valid_value && in_array($value, $attribute->get_slugs(), true) ) {
							// If valid values are empty, this is an 'any' variation so get all possible values.
							$attributes[ $attribute_key ] = $value;
						} else {
							/* translators: %s: Attribute name. */
							throw new Exception(sprintf(__('Invalid value posted for %s', 'wc-quote-lite'), wc_attribute_label($attribute['name'])));
						}
					} elseif ('' === $valid_value ) {
						$missing_attributes[] = wc_attribute_label($attribute['name']);
					}

					$variation = $attributes;
				}
				if (! empty($missing_attributes) && ! is_admin() ) {
					/* translators: %s: Attribute name. */
					throw new Exception(sprintf(_n('%s is a required field', '%s are required fields', count($missing_attributes), 'wc-quote-lite'), wc_format_list_of_items($missing_attributes)));
				}
			}

			// Load quote item data - may be added by other plugins.
			/**
			 * Filter wc_quote_add_item_data
			 * 
			 * @since 1.0
			**/
			$quote_item_data = (array) apply_filters('wc_quote_add_item_data', $quote_item_data, $product_id, $variation_id, $quantity, $form_data);

			// Generate a ID based on product ID, variation ID, variation data, and other quote item data.
			$quote_id = $this->generate_quote_id($product_id, $variation_id, $variation, $quote_item_data);

			// Find the quote item key in the existing quote.
			$quote_item_key = $this->find_product_in_quote($quote_id);

			// Force quantity to 1 if sold individually and check for existing item in quote.
			if ($product_data->is_sold_individually() ) {
				/**
				 * Filter wc_quote_sold_individually_quantity
				 * 
				 * @since 1.0
				**/
				$quantity       = apply_filters('wc_quote_sold_individually_quantity', 1, $quantity, $product_id, $variation_id, $quote_item_data);
				/**
				 * Filter wc_quote_sold_individually_found_in_quote
				 * 
				 * @since 1.0
				**/
				$found_in_quote = apply_filters('wc_quote_sold_individually_found_in_quote', $quote_item_key && $this->quote_contents[ $quote_item_key ]['quantity'] > 0, $product_id, $variation_id, $quote_item_data, $quote_id);

				if ($found_in_quote ) {
					/* translators: %s: product name */
					$message = sprintf(__('You cannot add another "%s" to your quote.', 'wc-quote-lite'), $product_data->get_name());

					/**
					 * Filter message about more than 1 product being added to quote.
					 *
					 * @since 4.5.0
					 * @param string     $message Message.
					 * @param WC_Product $product_data Product data.
					 */
					$message = apply_filters('wc_quote_product_cannot_add_another_message', $message, $product_data);

					throw new Exception(sprintf('<a href="%s" class="button wc-forward">%s</a> %s', '', __('View Quote', 'wc-quote-lite'), $message));
				}
			}

			if (! $product_data->is_purchasable() ) {
				$message = __('Sorry, this product cannot be purchased.', 'wc-quote-lite');
				/**
				 * Filter message about product unable to be purchased.
				 *
				 * @since 3.8.0
				 * @param string     $message Message.
				 * @param WC_Product $product_data Product data.
				*/
				$message = apply_filters('wc_quote_product_cannot_be_purchased_message', $message, $product_data);
				throw new Exception($message);
			}

			// If quote_item_key is set, the item is already in the quote.
			if (!empty($quote_item_key) ) {

				$this->quote_contents[ $quote_item_key ]['quantity'] += intval($quantity);
				 
			} else {
				$quote_item_key = $quote_id;

				$increase_offered_price = isset($this->quote_settings['general']['increase_offered_price']) ? floatval($this->quote_settings['general']['increase_offered_price']) : '';

				$offered_price = $product_data->get_price();
				$args = array(
					'qty'   => 1,
					'price' => $offered_price,
				);
				$offered_price = $this->get_product_price($product_data, $args, 'edit');
				if (class_exists('Wwp_Wholesale_Pricing') ) {
					$Wholesale_Price_class = new WWP_Easy_Wholesale_Multiuser();
					$check_wwp = $Wholesale_Price_class->is_wholesale($product_data->get_id());
					$offered_price = !$check_wwp ? $Wholesale_Price_class->wwp_regular_price_change($offered_price, $product_data) : $Wholesale_Price_class->wwp_regular_price_change(get_post_meta($product_data->get_id(), '_regular_price', true), $product_data);
				}

				if ($product_data->get_type() === 'subscription' || $product_data->get_type() === 'variable-subscription' ) :
					$offered_signup = array( 'offered_signup' => WC_Subscriptions_Product::get_sign_up_fee($product_data) );
					$quote_item_data = array_merge($offered_signup, $quote_item_data);
				endif;

				if (!empty($increase_offered_price) ) {

					$offered_price += ( $increase_offered_price * $offered_price ) / 100;
				}

				// Add item after merging with $quote_item_data - hook to allow plugins to modify quote item.
				/**
				 * Filter wc_quote_add_item
				 * 
				 * @since 1.0
				**/
				$this->quote_contents[ $quote_item_key ] = apply_filters(
					'wc_quote_add_item',
					array_merge(
						$quote_item_data,
						array(
							'key'           => $quote_item_key,
							'product_id'    => $product_id,
							'variation_id'  => $variation_id,
							'variation'     => $variation,
							'quantity'      => $quantity,
							'offered_price' => $offered_price,
							'data'          => $product_data,
							'data_hash'     => wc_get_cart_item_data_hash($product_data),
						)
					),
					$quote_item_key
				);
			}
			/**
			 * Filter wc_quote_contents_changed
			 * 
			 * @since 1.0
			**/
			$this->quote_contents = apply_filters('wc_quote_contents_changed', $this->quote_contents);

			if ($return_contents ) {
				return $this->quote_contents;
			} else {

				if (is_user_logged_in() ) {
					update_user_meta(get_current_user_id(), 'wc_quote', $this->quote_contents);
				}

				wc()->session->set('wc_quotes', $this->quote_contents);
				/**
				 * Action wc_quote_session_changed
				 * 
				 * @since 1.0
				**/
				do_action('wc_quote_session_changed');
			}
			/**
			 * Action wc_quote_add_to_quote
			 * 
			 * @since 1.0
			**/
			do_action('wc_quote_add_to_quote', $quote_item_key, $product_id, $quantity, $variation_id, $variation, $quote_item_data);

			return $quote_item_key;

		} catch ( Exception $e ) {
			if ($e->getMessage() && ! is_admin() ) {
				wc_add_notice($e->getMessage(), 'error');
			}
			return false;
		}
	}

	/**
	 * Generate a unique ID for the quote item being added.
	 *
	 * @param  int   $product_id      - id of the product the key is being generated for.
	 * @param  int   $variation_id    of the product the key is being generated for.
	 * @param  array $variation       data for the quote item.
	 * @param  array $quote_item_data other quote item data passed which affects this items uniqueness in the quote.
	 * @return string quote item key
	 */
	public function generate_quote_id( $product_id, $variation_id = 0, $variation = array(), $quote_item_data = array() ) {
		$id_parts = array( $product_id );

		if ($variation_id && 0 !== $variation_id ) {
			$id_parts[] = $variation_id;
		}

		if (is_array($variation) && ! empty($variation) ) {
			$variation_key = '';
			foreach ( $variation as $key => $value ) {
				$variation_key .= trim($key) . trim($value);
			}
			$id_parts[] = $variation_key;
		}

		if (is_array($quote_item_data) && ! empty($quote_item_data) ) {
			$quote_item_data_key = '';
			foreach ( $quote_item_data as $key => $value ) {
				if (is_array($value) || is_object($value) ) {
					$value = http_build_query($value);
				}
				$quote_item_data_key .= trim($key) . trim($value);

			}
			$id_parts[] = $quote_item_data_key;
		}
		/**
		 * Filter wc_quote_id
		 * 
		 * @since 1.0
		**/
		return apply_filters('wc_quote_id', md5(implode('_', $id_parts)), $product_id, $variation_id, $variation, $quote_item_data);
	}

	/**
	 * Check if product is in the quote and return quote item key.
	 *
	 * Cart item key will be unique based on the item and its properties, such as variations.
	 *
	 * @param  mixed $quote_id id of product to find in the quote.
	 * @return string quote item key
	 */
	public function find_product_in_quote( $quote_key = false ) {
		if (false !== $quote_key ) {
			if (is_array($this->quote_contents) && isset($this->quote_contents[ $quote_key ]) ) {
				return $quote_key;
			}
		}
		return '';
	}
	/**
	 * Check if product is in the quote and return true.
	 *
	 * Product id will be unique based on the item and its properties, such as variations.
	 *
	 * @param  mixed $product_id id of product to find in the quote.
	 * @return string quote item key
	 */
	public function find_product_id_quote( $product_id ) {
		foreach ($this->quote_contents as $quote_item_key => $quote_item_data ) {
			if (!isset($quote_item_data['data']) || !is_object($quote_item_data['data'])) {
				continue;
			}
			if ($product_id == $quote_item_data['data']->get_id() ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Insert new quote.
	 *
	 * @param array $post_data of quote to insert.
	 */
	public function insert_new_quote( $post_data = array() ) {

		check_ajax_referer('save_wcquote', 'wc_quote_nonce');
		
		if (empty($post_data) || ! is_array($post_data) ) {
			if (! is_admin() ) {
				wc_add_notice(__('Post data should not be empty to create a quote.', 'wc-quote-lite'), 'error');
			}
			return;
		}

		$empty_email = isset($this->quote_settings['general']['empty_quote']) ? $this->quote_settings['general']['empty_quote'] : '0';
		$this->quote_settings['messages'] =  get_option('wc_quote_messages', false);

		try {

			$quotes = WC()->session->get('wc_quotes');

			$validate_email = explode(',', $post_data['_to_send_email']);
			$sanitize_email = array();
			$validation_result = true;
			foreach ( $validate_email as $vali_email ) {
				$validated_email = sanitize_email($vali_email);
				$validated = ( !empty($validated_email) ? true : false );
				if ($validated) {
					$sanitize_email[] = $validated_email;
				} else {
					$validation_result = false;
					break;
				}
			}

			if ( $validation_result ) {

				$quote_emails = implode(',', $sanitize_email);
				$quotes['quote_user_id'] = isset($_POST['quote_user_id'] ) ? sanitize_text_field( $_POST['quote_user_id'] ) : '';
	
				if (isset($quote_emails)) {
					$quotes['sender_email'] = $quote_emails;
				}
				
				foreach ( $quotes as $quote_item_key => $quote_item ) {
					
					if (isset($post_data['quote_qty'][ $quote_item_key ]) ) {
						$quotes[ $quote_item_key ]['quantity'] = intval($post_data['quote_qty'][ $quote_item_key ]);
					}
				}
	
				WC()->session->set('wc_quotes', $quotes);
				/**
				 * Action wc_quote_session_changed
				 * 
				 * @since 1.0
				**/
				do_action('wc_quote_session_changed');
	
				$quote = WC()->session->get('wc_quotes');
	
				$quote_args = array(
					'post_title'   => '',
					'post_content' => 'unread',
					'post_type'    => 'wc-quote',
				);
	
				$quote_id = wp_insert_post($quote_args);
	
				$user = wp_get_current_user();
	
				if ( is_user_logged_in() ) {
					$customer_name = $user->display_name;
				} else {
	
					$customer_name = 'Guest';
				}
	
				$post_title = $customer_name . '( ' . $quote_id . ' )';
				$my_post    = array(
					'ID'         => $quote_id,
					'post_title' => $post_title,
					'post_author' => get_current_user_id(),
					'post_status' => 'quote_new',
				);
	
				wp_update_post($my_post);
	
				// Save Quote Meta

				foreach ( $quotes as $quote_item_key => $quote_item ) {
					if (!isset($quote_item['data']) || !is_object($quote_item['data']) ) {
						continue;
					}
					
					$id = $quote_item['product_id'] ;
					$product = wc_get_product($id);
										
					$wc_quote_counter = ! empty($product->get_meta('quote_counter')) ? $product->get_meta('quote_counter')  : 0 ;
					$wc_quote_counter = (int) $wc_quote_counter + 1 ;
					
					$product->update_meta_data('quote_counter', $wc_quote_counter);
					$product->save();
	
					
				}
					
					
				update_post_meta($quote_id, 'quote_contents', WC()->session->get('wc_quotes'));
				$unread_quotes = (int) get_option('watq_unread_quotes', 0) + 1; // Default to 0 if option does not exist
				update_option('watq_unread_quotes', $unread_quotes);
				if (isset($post_data['data']) ) :
					$quote_fields = $this->quote_fields;
					foreach ( $quote_fields['components'] as $key => $field ) {
						$wc_quote_field_name  = $field['key'];
						$wc_quote_field_type  = $field['type'];
						if ('radio' === $wc_quote_field_type && !empty($post_data['data'][ $wc_quote_field_name ]) ) :
							$post_data['data'][ $wc_quote_field_name ] = reset($post_data['data'][ $wc_quote_field_name ]);
						endif;
					}
					update_post_meta($quote_id, 'quote_fields', $post_data['data']);
				endif;
	
				if (is_user_logged_in() ) {
					update_user_meta(get_current_user_id(), 'wc_quote', '');
				}
					
				if ('1' === $empty_email ) {
					WC()->session->set('wc_quotes', null);
				}
				/**
				 * Action wc_quote_created
				 * 
				 * @since 1.0
				**/
				do_action('wc_quote_created', $quote_id);
				/**
				 * Action wc_quote_send_quote_email_to_customer
				 * 
				 * @since 1.0
				**/
				do_action('wc_quote_send_quote_email_to_customer', $quote_id);
				/**
				 * Action wc_quote_send_quote_email_to_admin
				 * 
				 * @since 1.0
				**/
				do_action('wc_quote_send_quote_email_to_admin', $quote_id);
	
				$email_success_msg = isset($this->quote_settings['messages']['success_email_quote_success']) ? $this->quote_settings['messages']['success_email_quote_success'] : __('Your quote has been submitted successfully.', 'wc-quote-lite');
				$send_quote_redirect = isset($this->quote_settings['notifications']['send_quote_redirect']) ? $this->quote_settings['notifications']['send_quote_redirect'] : '0';
				$send_quote_redirect_url = isset($this->quote_settings['notifications']['send_quote_redirect_url']) ? $this->quote_settings['notifications']['send_quote_redirect_url'] : '';
	
			
				wc_add_notice($email_success_msg, 'success');
					
				if ('1' === $send_quote_redirect && !empty($send_quote_redirect_url) ) {
					wp_safe_redirect(esc_url($send_quote_redirect_url));
					exit;
				}
	
			} else {

				$this->quote_settings['messages'] =  get_option('wc_quote_messages', false);
				$email_error_msg = isset($this->quote_settings['messages']['error_email_quote_error']) ? $this->quote_settings['messages']['error_email_quote_error'] : __('Your Email Address is not Valid.', 'wc-quote-lite');
				wc_add_notice($email_error_msg, 'error');

			}

		} catch ( Exception $e ) {
					echo esc_html($e->getMessage());
		}
	}
		
			

	/**
	 * Convert quote to cart.
	 *
	 * @param mixed $quote_id id of quote to convert.
	 */
	public function convert_quote_to_cart( $quote_id = false, $status = 'quote_accepted' ) {
		global $woocommerce;
		 
		if (false === $quote_id ) {
			wc_add_notice(__('Quote ID is required to convert a quote to order.', 'wc-quote-lite'), 'error');
			return false;
		}

		$quote_status = get_post_status($quote_id);

		if ('quote_converted' === $quote_status ) {
			wc_add_notice(__('Quote is converted to order and cannot be accepted.', 'wc-quote-lite'), 'error');
			return false;
		}

		$quote_contents = get_post_meta($quote_id, 'quote_contents', true);
		 
		if (empty($quote_contents) ) {
			wc_add_notice(__('Quote Contents are empty.', 'wc-quote-lite'), 'error');
			return false;
		}
		WC()->session->set('wc_quotes_temp', null);
		wc_clear_notices();
		WC()->session->set('wc_quotes_temp', $quote_contents);
		foreach ( $quote_contents as $quote_item_key => $quote_item ) {

			if (isset($quote_item['data']) ) {
				$product = $quote_item['data'];
			} else {
				continue;
			}

			if (! is_object($product) ) {
				continue;
			}
			$product_id = isset($quote_item['product_id']) ? intval($quote_item['product_id']) : '';
			$quantity   = isset($quote_item['quantity']) ? intval($quote_item['quantity']) : 1;
			$variation_id   = isset($quote_item['variation_id']) ? intval($quote_item['variation_id']) : 0;
			$variation   = isset($quote_item['variation']) ? intval($quote_item['variation']) : array();
			/**
			 * Filter wc_quote_add_to_cart_validation
			 * 
			 * @since 1.0
			**/
			$passed_validation = apply_filters('wc_quote_add_to_cart_validation', true, $product_id, $quantity, $quote_item);

			if (!$passed_validation ) {
				echo 'failed';
				die();
			}

			$cart_item_key = $woocommerce->cart->add_to_cart($product_id, $quantity, $variation_id, $variation, $quote_item);
		}

		$quote = array(
			'ID'            => $quote_id,
			'post_status'   => $status,
		);
		wp_update_post($quote);
		/**
		 * Action wc_quote_send_quote_email_to_customer
		 * 
		 * @since 1.0
		**/
		do_action('wc_quote_send_quote_email_to_customer', $quote_id);
		/**
		 * Action wc_quote_send_quote_email_to_admin
		 * 
		 * @since 1.0
		**/
		do_action('wc_quote_send_quote_email_to_admin', $quote_id);

		/* translators: %1$s: Customer billing full name */
		/* translators:%2$s: Customer billing full name */
		wc_add_notice(sprintf(__('Your Quote# %1$s has been converted to Cart.', 'wc-quote-lite'), $quote_id), 'success');

		wp_safe_redirect(wc_get_cart_url());
		exit;
	}


	public function remove_quote_item( $quote_item_key ) {
		if (isset($this->quote_contents[ $quote_item_key ]) ) {
			/**
			 * Action wc_quote_remove_quote_item
			 * 
			 * @since 1.0
			**/
			do_action('wc_quote_remove_quote_item', $quote_item_key, $this);
			unset($this->quote_contents[ $quote_item_key ]);
			/**
			 * Action wc_quote_item_removed
			 * 
			 * @since 1.0
			**/
			do_action('wc_quote_item_removed', $quote_item_key, $this);
			return true;
		}
		return false;
	}
}
