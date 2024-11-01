<?php


if (! class_exists('WAQT_FUNCTIONS')) :

	class WAQT_FUNCTIONS {

		private $plugin_page_slug;

		private $current_tab;

		private $quote_settings;

		private $tabs;

		public function __construct(){

			$this->tabs = array(
				'general' => __('General', 'wc-quote-lite'),
				'messages' =>  __('Messages', 'wc-quote-lite'),
				'email' =>  __('Email', 'wc-quote-lite'),
				'customization' =>  __('Customization', 'wc-quote'),
				'notifications' =>  __('Notifications', 'wc-quote'),
				'pdf' =>  __('PDF', 'wc-quote'),
				'recaptcha' =>  __('ReCaptcha', 'wc-quote'),

	
			);
			/**
			* Filter wc_quote_tabs_array
			* 
			* @since 1.0
			**/
			$this->tabs = apply_filters('wc_quote_tabs_array', $this->tabs);
			$this->plugin_page_slug = 'wc-quote-lite-settings';
			$this->current_tab     = empty($_GET['tab']) ? 'general' : sanitize_title(wp_unslash($_GET['tab'])); // WPCS: input var okay, CSRF ok.
			$this->quote_settings = get_option('wc_quote_' . $this->current_tab, false);
			add_action('admin_menu', array( $this, 'create_wcquote_lite_sub_menu' ));
			add_action('admin_init', array( $this, 'wc_quote_save_settings' ));
			add_action('wc_quote_settings_' . $this->current_tab, array( $this, 'wc_quote_settings_tab_html' ));
		}
		



		public function create_wcquote_lite_sub_menu(){

			$plugin_name = apply_filters('wc_quote_plugin_name', __('Woo Quote Lite', 'wc-quote-lite'));
			add_menu_page(
			$plugin_name, 
			$plugin_name,
			'edit_others_shop_orders',
			'wc-quote-lite',
			 function () {
					  return 0; },
			'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" background-color="white"><path d="M15.812 4.819c-.33-.341-.312-.877.028-1.207l3.469-3.365c.17-.164.387-.247.603-.247.219 0 .438.085.604.257l-4.704 4.562zm-5.705 8.572c-.07.069-.107.162-.107.255 0 .194.158.354.354.354.089 0 .178-.033.247-.1l.583-.567-.493-.509-.584.567zm4.924-6.552l-1.994 1.933c-1.072 1.039-1.619 2.046-2.124 3.451l.881.909c1.419-.461 2.442-.976 3.514-2.016l1.994-1.934-2.271-2.343zm5.816-5.958l-5.137 4.982 2.586 2.671 5.138-4.98c.377-.366.566-.851.566-1.337 0-1.624-1.968-2.486-3.153-1.336zm-11.847 12.119h-4v1h4v-1zm9-1.35v1.893c0 4.107-6 2.457-6 2.457s1.518 6-2.638 6h-7.362v-20h12.629l2.062-2h-16.691v24h10.189c3.163 0 9.811-7.223 9.811-9.614v-4.687l-2 1.951z" fill="#fff"></path></svg>'), 
			/**
			* Filter wc_quote_menu_position
			* 
			* @since 1.0
			**/
			apply_filters('wc_quote_menu_position', 54) 
			);
		
			remove_meta_box('submitdiv', 'wc-quote-lite', 'side');

			add_submenu_page( 
				__('wc-quote-lite', 'wc-quote'), 
				__('Form Builder', 'wc-quote'),
				__('Form Builder <span style="color: #D63638;font-weight: 600;">(Pro)</span>', 'wc-quote'),
				__('manage_options', 'wc-quote'),
				__('wc-quote-form-builder', 'wc-quote'),
				array( $this, 'wc_quote_formbuilder_view' ),
				10
			);
		
			$setting_title = apply_filters('wc_quote_settings_title', __('Settings', 'wc-quote-lite'));
			add_submenu_page( 
				'wc-quote-lite',
				$setting_title,
				$setting_title,
				__('manage_options', 'wc-quote-lite'),
				'wc-quote-lite-settings',
				array( $this, 'wc_quote_setting_view' ),
				10
			);
		}

		public function wc_quote_formbuilder_view() {
		}
	
		/**
		 * Main Setting Container
		 */
		public function wc_quote_setting_view() {

			if (file_exists(get_stylesheet_directory() . '/woocommerce/wcquote/admin/views/settings/html-quote-lite-settings.php')) {
				include get_stylesheet_directory() . '/woocommerce/wcquote/admin/views/settings/html-quote-lite-settings.php';
			} else {
				include WATQ_QUOTE_PATH . 'includes/admin/views/settings/html-quote-lite-settings.php';
			}
		}

		public function wc_quote_save_settings() {
			foreach ( $this->tabs as $key => $value ) : 
				register_setting('wc-quote-lite-settings-' . $key, 'wc_quote_' . $key);
			endforeach;
		}

		public function wc_quote_settings_tab_html() {
			/**
			* Filter wc_quote_settings_tab_path
			* 
			* @since 1.0
			**/
			include apply_filters('wc_quote_settings_tab_path', WATQ_QUOTE_PATH . '/includes/admin/views/settings/tabs/html-quote-lite-' . $this->current_tab . '.php');
		}
	}

new WAQT_FUNCTIONS();

endif;
