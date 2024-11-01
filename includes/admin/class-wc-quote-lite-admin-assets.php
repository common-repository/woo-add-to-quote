<?php
/**
 * Load assets
 */

if (! defined('ABSPATH') ) {
	exit;
}

if (! class_exists('WC_QUOTE_Admin_Assets', false) ) :

	/**
	 * WC_QUOTE_Admin_Assets Class.
	 */
	class WC_QUOTE_Admin_Assets {
		
		/**
		 * Hook in tabs.
		 */
		public function __construct() {
			add_action('admin_enqueue_scripts', array( $this, 'admin_styles' ), 99);
			add_action('admin_enqueue_scripts', array( $this, 'admin_scripts' ), 99);
			// add_action( 'admin_footer-widgets.php', array( $this, 'widget_scripts' ), 9999 );
		}

		/**
		 * Enqueue styles.
		 */
		public function admin_styles() {
			$screen    = get_current_screen();
			$screen_id = $screen ? $screen->id : '';
			wp_register_style('wc_quote_lite_admin_styles', WATQ_PLUGIN_URL . '/assets/admin/css/wc-quote-admin.css', array(), WATQ_VERSION);
			wp_register_style('wc_quote_bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css', array(), WATQ_VERSION);
			
			wp_enqueue_style('wc_quote_lite_admin_styles');
			/**
			 * Action wc_quote_settings_css
			 * 
			 * @since 1.0
			**/
		
			if (in_array($screen_id, array( 'edit-wc-quote', 'wc-quote', 'quote-for-woo_page_wc-quote-settings', 'aio-quote_page_wc-quote-settings', 'wc-quote-rules', 'edit-wc-quote-rules' )) ) {
				wp_enqueue_style('wc_quote_admin_styles');
				/**
				 * Action wc_quote_admin_css
				 * 
				 * @since 1.0
				**/
				do_action('wc_quote_admin_css');
			}
		}


		/**
		 * Enqueue scripts.
		 */
		public function admin_scripts() {

			$screen       = get_current_screen();
			$screen_id    = $screen ? $screen->id : '';
			 
			$suffix = ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) ? '' : '';
			wp_register_script('wc-quote-admin', WATQ_PLUGIN_URL . '/assets/admin/js/wc-quote-admin' . $suffix . '.js', array( 'jquery' ), WATQ_VERSION);
			// WCQuote admin pages.
			if (in_array($screen_id, array( 'quote-for-woo_page_wc-quote-settings', 'aio-quote_page_wc-quote-settings', 'woo-quote-lite_page_wc-quote-lite-settings' )) ) {
				wp_enqueue_script('iris');
				wp_enqueue_script('selectWoo');
				wp_enqueue_script('wc-enhanced-select');
				wp_enqueue_media();
				$wc_quote_vars = array(
					'ajax_url' => admin_url('admin-ajax.php'),
					'nonce'    => wp_create_nonce('wc-quote-ajax-nonce'),
					'description'   => __( 'Unlock advanced features', 'wc-quote-lite' ),
					'buttonText'    => __( 'Upgrade to Premium', 'wc-quote-lite' ),
					'redirectUrl'   => 'https://wpexperts.io/products/quote-for-woocommerce/?utm_source=popup&utm_medium=quote-for-woo-pro',
				);
				wp_localize_script('wc-quote-admin', 'wc_quote_vars', $wc_quote_vars);
				wp_enqueue_script('wc-quote-admin');
				

				/**
				 * Action wc_quote_settings_js
				 * 
				 * @since 1.0
				**/
				do_action('wc_quote_settings_js');
			}
			if (in_array($screen_id, array( 'widgets' )) ) {
				wp_enqueue_script('iris');
				$colorPicker = 'jQuery(document).ready(function ($){function initColorPicker(){jQuery(".colorpick").iris({change: function (event, ui) {jQuery(this).parent().find(".colorpickpreview").css({backgroundColor: ui.color.toString()});},hide: true,border: true,}).on("click focus", function (event) {event.stopPropagation();jQuery(".iris-picker").hide();jQuery(this).closest("td").find(".iris-picker").show();jQuery(this).data("originalValue", jQuery(this).val());}).on("change", function () {if (jQuery(this).is(".iris-error")) {var original_value = jQuery(this).data("originalValue");if (original_value.match(/^\#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/)) {jQuery(this).val(jQuery(this).data("originalValue")).trigger("change");} else {jQuery(this).val("").trigger("change");}}});jQuery("body").on("click", function () {jQuery(".iris-picker").hide();})}jQuery( document ).on( "widget-added widget-updated", initColorPicker );initColorPicker();});';
				wp_add_inline_script('iris', $colorPicker);
			}
			// Products.

			if (in_array($screen_id, array( 'wc-quote', 'edit-wc-quote', 'wc-quote-rules' )) ) {
				$wc_quote_vars = array(
					'ajax_url' => admin_url('admin-ajax.php'),
					'nonce'     => wp_create_nonce('wc-quote-ajax-nonce'),
					'remove_item_notice' => esc_html__('Are you sure you want to remove the selected items?', 'wc-quote-lite'),
					'description'   => __( 'Unlock advanced features', 'wc-quote-lite' ),
					'buttonText'    => __( 'Upgrade to Premium', 'wc-quote-lite' ),
					'redirectUrl'   => 'https://wpexperts.io/products/quote-for-woocommerce/?utm_source=popup&utm_medium=quote-for-woo-pro',
				);
				wp_localize_script('wc-quote-admin', 'wc_quote_vars', $wc_quote_vars);
				wp_enqueue_script('wc-quote-admin');
				wp_enqueue_script('selectWoo');
				wp_enqueue_script('wc-enhanced-select');
				/**
				 * Action wc_quote_posttype_js
				 * 
				 * @since 1.0
				**/
				do_action('wc_quote_posttype_js');
			}
		}
	}

endif;

new WC_QUOTE_Admin_Assets();
