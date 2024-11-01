<?php

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * DSFW_Blocks
 */
class DSFW_Blocks {

		
	/**
	 * Method __construct
	 * s
	 *
	 * @return void
	 */
	public function __construct() {
		add_action('woocommerce_blocks_loaded', array( $this, 'init' ), 99);
	}

	/**
	 * Method init
	 *
	 * @return void
	 */
	public function init() {
		add_action('woocommerce_blocks_cart_block_registration', array( $this, 'watq_register_delviery_slot_block' ));
	}
	

	/**
	 * Method dsfw_register_delviery_slot_block
	 *
	 * @param Integration_Interface $integration_registry
	 *
	 * @return void
	 */
	public function watq_register_delviery_slot_block( $integration_registry ) {
		include_once WATQ_QUOTE_PATH . 'includes/wc-register-cart-scripts.php';
		$integration_registry->register(RegistercartData::instance());
	}
}

( new DSFW_Blocks() );
