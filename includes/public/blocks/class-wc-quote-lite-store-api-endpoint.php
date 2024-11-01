<?php
/**

SmartBundle_Woo_Extend_Store_API class */
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Automattic\WooCommerce\StoreApi\StoreApi;
use Automattic\WooCommerce\StoreApi\Schemas\ExtendSchema;
use Automattic\WooCommerce\StoreApi\Schemas\V1\CartItemSchema;


class WCBP_Extend_Store_Endpoint {

	/**
	 * Plugin Identifier, unique to each plugin.
	 *
	 * @var string
	 */
	const IDENTIFIER = 'smart_product_bundle';
	public $bundle_data = '';

	/**
	 * Initialize.
	 */
	public static function init() {
		
		if ( ! function_exists( 'woocommerce_store_api_register_endpoint_data' ) ) {
			return;
		}

		woocommerce_store_api_register_endpoint_data(
			array(
				'endpoint'        => CartItemSchema::IDENTIFIER,
				'namespace'       => self::IDENTIFIER,
				'data_callback'   => array( 'WCBP_Extend_Store_Endpoint', 'extend_checkout_block_data' ),
				'schema_callback' => array( 'WCBP_Extend_Store_Endpoint', 'extend_checkout_block_schema' ),
				'schema_type'     => ARRAY_A,
			)
		);
	}

	/**
	 * Register smartBundle WooCommerce data into cart/checkout endpoint.
	 *
	 * @return array $item_data Registered data or empty array if condition is not satisfied.
	 */
	public static function extend_checkout_block_data( $cart_item ) {
			
		$prod = wc_get_product($cart_item['product_id']);
		$item_data = array();

		if ( isset( $cart_item['wcbp_bundle_product_parent_id'] ) ) {

			$variable_prod_selection = get_post_meta($cart_item['wcbp_bundle_product_parent_id'], 'wcbp_variable_product_selection', true);
			if ( $prod->is_type('variation') && 'yes' == $variable_prod_selection ) {
				$cart_item['product_id'] = $prod->get_parent_id();
			}

			$bundle_data = self::GET_BUNDLE_BY_ID($cart_item['wcbp_bundle_product_parent_id'], $cart_item['product_id']);
		} elseif ( isset( $cart_item['parent_bundle_id'] ) ) {  

			$variable_prod_selection = get_post_meta($cart_item['parent_bundle_id'], 'wcbp_variable_product_selection', true);
			if ( $prod->is_type('variation') && 'yes' == $variable_prod_selection ) {
				$cart_item['product_id'] = $prod->get_parent_id();
			}

			$bundle_data = self::GET_BUNDLE_BY_ID($cart_item['parent_bundle_id'], $cart_item['product_id']);
		}
	
		if ( isset( $cart_item['wcbp_bundle_product_parent_id'] ) ) {

			// add a parent id into the child product 
			$item_data = array( 'parent_bundle_id' => $cart_item['wcbp_bundle_product_parent_id'] );

			// add a item grouping into the cart 
			if ( isset( $bundle_data->bundle_setting['item_grouping'] ) ) {
				$item_data = array_merge( array( 'item_grouping' => $bundle_data->bundle_setting['item_grouping'] ), $item_data );
			}

			// add a product price into the cart 
			if ( isset ($bundle_data->product_setting['indivdual_settings']['price_visible']['cart_checkout']) ) {
				$price = WCBP_Helpers::wcbp_get_price($cart_item['wcbp_bundle_product_parent_id'], $prod->get_id());
				$price_formated = number_format($price, wc_get_price_decimals(), '');
				$item_data = array_merge( array( 'product_price' => $price_formated ), $item_data );
			}

		}

		if ( isset( $cart_item['parent_bundle_id'] ) ) {
			if ( isset( $bundle_data->bundle_setting['edit_in_cart'] ) && 'yes' == $bundle_data->bundle_setting['edit_in_cart'] ) {
				$item_data = array_merge( array( 'edit_in_cart' => $bundle_data->bundle_setting['edit_in_cart'] ), $item_data );
			}
		}

		return $item_data;
	}

	/**
	 * Register myCred WooCommerce schema into cart/checkout endpoint.
	 *
	 * @return array Registered schema.
	 */
	public static function extend_checkout_block_schema() {
		return array(
			'parent_bundle_id' => array(
				'description' => __( 'parent bundle id', 'smart_product_bundle' ),
				'type'        => array( 'string', 'null' ),
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
			),
		);
	}

	public static function GET_BUNDLE_BY_ID( $bundle_id = '', $product_id = '' ) {

		$bundle_data = array(
			'bundle_setting' => array(
				'button_text' => get_post_meta($bundle_id, 'wcbp_single_bundle_page_text', true),
				'pricing_type' => get_post_meta($bundle_id, 'wcbp_bundle_prod_pricing', true),
				'product_layout' => get_post_meta($bundle_id, 'wcbp_bundle_prod_layout', true),
				'item_grouping' => get_post_meta($bundle_id, 'wcbp_bundle_item_grouping', true),
				'min_quantity' => get_post_meta($bundle_id, 'wcbp_bundle_min_quantity', true),
				'max_quantity' => get_post_meta($bundle_id, 'wcbp_bundle_max_quantity', true),
				'disable_quantity' => get_post_meta($bundle_id, 'wcbp_disable_bundle_quantity', true),
				'disable_items_link' => get_post_meta($bundle_id, 'wcbp_disable_bundle_tems_link', true),
				'edit_in_cart' => get_post_meta($bundle_id, 'wcbp_enable_edit_in_cart', true),
				'show_product_price' => get_post_meta($bundle_id, 'wcbp_show_product_addon_price', true),
				'product_selection' => get_post_meta($bundle_id, 'wcbp_product_addons_selection', true),
				'above_text' => get_post_meta($bundle_id, 'wcbp_bundle_above_text', true),
				'under_text' => get_post_meta($bundle_id, 'wcbp_bundle_under_text', true),
				'variable_product_select' => get_post_meta($bundle_id, 'wcbp_variable_product_selection', true),
				'content_type' => get_post_meta($bundle_id, 'wcbp_bundle_content_type', true),
				'individual_product_id' => get_post_meta($bundle_id, 'wcbp_products_addons_values', true),
				'product_category' => get_post_meta($bundle_id, 'wcbp_products_category_values', true),
			),
			'product_setting' => array(
				'indivdual_settings' => get_post_meta($bundle_id, 'wcbp_invidual_product_' . $product_id, true ),
			),
		);

		/**
		**
		*  Hook Filter
		*
		*  @since 1.3
		*/
		return (object) apply_filters( 'bundle_setting_object', $bundle_data, $bundle_id, $product_id );
	}
}
