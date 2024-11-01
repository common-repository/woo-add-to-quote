<?php 
use Automattic\WooCommerce\Blocks\Integrations\IntegrationInterface;

class RegistercartData implements IntegrationInterface {

	/**
	 * The single instance of the class.
	 *
	 * @var DSFW_Block_Integration
	 */
	protected static $_instance = null;

	/**
	 * Main DSFW_Block_Integration instance. Ensures only one instance of DSFW_Block_Integration is loaded or can be loaded.
	 *
	 * @static
	 * @return DSFW_Block_Integration
	 */
	public static function instance() {
		if (is_null(self::$_instance) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	/**
	 * The name of the integration.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'wc-quote';
	} 

	public function initialize() {
		$this->register_block_front_scripts();  
	}

	public function get_script_data() {
		$general = get_option('wc_quote_general');
		

		$build_quote_text = isset($general['build_quote_text']) && !empty(sanitize_text_field($general['build_quote_text'])) ? $general['build_quote_text'] : 'Build Quote';
		$quote_page = isset($general['quote_page']) ?  esc_url(get_page_link($general['quote_page'])) : '';
		$enable_build_quote = isset($general['enable_build_quote']) ? $general['enable_build_quote'] : '0';

		return array(
			'quote_page' => $quote_page,
			'enable_build_quote' => $enable_build_quote,
			'build_quote_text' => $build_quote_text
		);
	}

	public function register_block_front_scripts() {
		$script_asset_path = WATQ_QUOTE_PATH . 'build/index.asset.php';
		$script_path       = WATQ_PLUGIN_URL . 'build/index.js';
		$script_asset      = file_exists($script_asset_path) ? include $script_asset_path : array(
			'dependencies' => $script_asset['dependencies'],
			'version'      => $script_asset['version'],
		);

		wp_register_script('wc-quote-fronted', $script_path, $script_asset['dependencies'], $script_asset['version'], true);
	}

	public function get_script_handles() {
		return array( 'wc-quote-fronted' );
	}

	public function get_editor_script_handles() {
		return array( 'wc-quote-fronted' );
	}
}
