<?php
/**
* Plugin Name: Quote For WooCommerce Lite
* Plugin URI: https://wpexperts.io/products/woo-add-to-quote/
* Description: This Plugin adds woocommerce add to quote functionality and much more...
* Version: 1.5.2
* Tested up to: 6.6
* Author: WPExperts
* Author URI: https://wpexperts.io/
* Text Domain: wc-quote-lite

*/

if ( ! defined( 'ABSPATH' ) ) :
	exit; // Exit if accessed directly
endif;

if ( ! defined('WATQ_QUOTE_PATH') ) :
	define('WATQ_QUOTE_PATH', plugin_dir_path(__FILE__));
endif;

if ( ! defined('WATQ_PLUGIN_URL') ) :
	define('WATQ_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
endif;

if ( ! defined('WATQ_FILE') ) :
	define('WATQ_FILE', __FILE__);
endif;

if ( ! defined('WATQ_VERSION') ) :
	define('WATQ_VERSION', '1.5.2');
endif;

//Freemius Integration Start
if ( ! function_exists( 'wq_fs' ) ) {
    // Create a helper function for easy SDK access.
    function wq_fs() {
        global $wq_fs;

        if ( ! isset( $wq_fs ) ) {
            // Include Freemius SDK.
            require_once dirname(__FILE__) . '/freemius/start.php';

            $wq_fs = fs_dynamic_init( array(
                'id'                  => '8991',
                'slug'                => 'wc-quote',
                'type'                => 'plugin',
                'public_key'          => 'pk_aa699f16558fb26bc0b09206c44c1',
                'is_premium'          => false,
                'has_addons'          => false,
                'has_paid_plans'      => false,
                'menu'                => array(
                    'slug'           => 'wc-quote',
                    'first-path'     => 'admin.php?page=wc-quote-lite-settings',
                    'account'        => false,
                    'support'        => false,
                ),
            ) );
        }

        return $wq_fs;
    }

    // Init Freemius.
    wq_fs();
    // Signal that SDK was initiated.
    do_action( 'wq_fs_loaded' );
}
//Freemius Integration Ends

if (! class_exists('WATQ')) :
	
	/**
	 * class WATQ
	 */
	class WATQ {

		public function __construct(){
			add_action( 'admin_init', array( $this, 'watq_check_if_woo_is_active' ) );
			add_action( 'plugins_loaded', array( $this, 'watq_load_functions' ) );
			add_action( 'plugins_loaded', array( $this, 'modules_include' ));
			add_action( 'wp_loaded' , array( $this, 'wc_quote_backward_compatibility' ) );
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'watq_add_action_links' ) );
		}


		public function watq_add_action_links( $actions ) {
			$temp = $actions['deactivate'];
			unset($actions['deactivate']);

			$mylinks = array(
				'<a href="https://wpexperts.io/products/quote-for-woocommerce/?utm_source=plugin_page&utm_medium=quote-for-woo-pro" target="_blank"><b>Get Premium</b></a>',
				'<a href="' . admin_url( 'admin.php?page=wc-quote-lite-settings' ) . '">Settings</a>',
				$temp
			);
			$actions = array_merge( $actions, $mylinks );
			return $actions;
		}

		public function modules_include() {
			if (class_exists('Automattic\WooCommerce\Blocks\Package') && version_compare(\Automattic\WooCommerce\Blocks\Package::get_version(), '7.2.0') >= 0 ) {
				include_once WATQ_QUOTE_PATH . 'includes/class-blocks.php';
			}
		}

		public function wc_quote_backward_compatibility(){

			$backward_check = get_option('wc_quote_backward_check');

			if (!$backward_check ) {
			
			$quote_settings['general']  = get_option('wc_quote_general', false);
			$quote_settings['messages'] = get_option('wc_quote_messages', false);
			$quote_settings['email']    = get_option('wc_quote_email', false);

			// General Setting Data
			$quote_settings['general']['admin_email']                       = get_option('wc_settings_quote_admin_email' );
			$quote_settings['general']['enable_convert_cart']               = true == get_option('wc_settings_quote_to_cart_select' ) ? 1 : 0;
			$quote_settings['general']['add_to_cart_on_detail_page']        = true == get_option('wc_settings_add_to_cart_on_detail_page') ? 1 : 0;
			$quote_settings['general']['add_to_cart_hide_global']           = true == get_option('wc_settings_add_to_cart_global') ? 1 : 0;
			$quote_settings['general']['build_quote_text']                  = get_option('wc_settings_quote_button_text');
			$quote_settings['general']['allow_guest']                       = get_option('wc_settings_allow_guest_user' );
			$quote_settings['general']['empty_quote_to_cart']               = get_option('wc_settings_empty_quote_to_cart');
			$quote_settings['general']['empty_quote']                       = get_option('wc_settings_empty_quote_after_email');
			$quote_settings['general']['empty_cart']                        = get_option('wc_settings_empty_cart_to_quote');

			//Messages Setting Data
			$quote_settings['messages']['success_email_quote_success']      = get_option('wc_settings_quote_success_email');
			$quote_settings['messages']['error_email_quote_error']          = get_option('wc_settings_quote_error_email');
			$quote_settings['messages']['error_email_user_input']           = get_option('wc_settings_error_email_user_input');

			//Email Setting data
			$quote_settings['email']['quote_email_subject']                 = get_option('wc_settings_quote_email_subject');
			$quote_settings['email']['quote_email_before_message']          = get_option('wc_settings_quote_email_before_message');
			$quote_settings['email']['quote_email_after_message']           = get_option('wc_settings_quote_email_after_message');

			update_option('wc_quote_general', $quote_settings['general']);
			update_option('wc_quote_messages', $quote_settings['messages']);
			update_option('wc_quote_email', $quote_settings['email']);
			
				$old_quotes = get_posts(
					array(
						'post_type' => 'watq-quotes',
						'post_status' => 'publish',
						'posts_per_page' => -1,
					) 
				);
				if (empty($old_quotes) || count($old_quotes) === 0 ) {
						  //backward comp done
						  update_option('wc_quote_backward_check', true);
						  return;
				}
				wc_load_cart();
				foreach ( $old_quotes as $old_quote ) {
			
					WC()->session->set('wc_quotes', null);
					$quote_data = get_post_meta($old_quote->ID, 'quote_post_data', true);
					$user_id = isset($quote_data['user_id']) && !empty($quote_data['user_id']) ? $quote_data['user_id'] : 0;

					if (0 === $user_id ) :
						$customer_email = isset($quote_data['sent_to']) && !empty($quote_data['sent_to']) ? $quote_data['sent_to'] : '';
						$customer_name = isset($quote_data['sent_name']) && ' ' !== $quote_data['sent_name'] ? $quote_data['sent_name'] : 'Guest';
						  else : 
							  $user_info = get_userdata($user_id);
							  $customer_email = isset($quote_data['sent_to']) && !empty($quote_data['sent_to']) ? $quote_data['sent_to'] : $user_info->user_email;
							  $customer_name = isset($quote_data['sent_name']) && ' ' !== $quote_data['sent_name'] ? $quote_data['sent_name'] : $user_info->display_name;
						  endif;
					 
						  $customer_msg = isset($quote_data['sent_message']) ? $quote_data['sent_message'] : '';
						  $post_title = $customer_name . '( ' . $old_quote->ID . ' )';
						  $quote_status = 'quoted' === $old_quote->post_content ? 'quote_quoted' : 'quote_new';

						  set_post_type($old_quote->ID, 'wc-quote');
						  $quote_args    = array(
							  'ID'         => $old_quote->ID,
							  'post_title' => $post_title,
							  'post_author' => $user_id,
							  'post_status' => $quote_status,
						  );
						  wp_update_post($quote_args);
						  $data = array(
							  'email'   => $customer_email,
							  'name'    => $customer_name,
							  'message' => $customer_msg,
						  );
						  update_post_meta($old_quote->ID, 'quote_fields', $data);
						  foreach ( $quote_data['quote_data'] as $product_detail ) :
							  $product_id = isset($product_detail['product_id']) ? intval($product_detail['product_id']) : '';
							  $quantity   = isset($product_detail['product_quantity']) ? floatval($product_detail['product_quantity']) : 1;
							  $variation_id   = $product_detail['product_id'] === $product_detail['variation_id'] ? 0 : intval($product_detail['variation_id']);
							  $variation   = isset($product_detail['variations_attr']) ? $product_detail['variations_attr'] : array();
							  $offered_price   = isset($product_detail['quote_total']) && !empty($product_detail['quote_total']) ? floatval($product_detail['quote_total']) / $quantity : $product_detail['product_price'];
							  $quote_contents = (array) get_post_meta($old_quote->ID, 'quote_contents', true);
							  $wc_quote       = new WC_QUOTE_Process($quote_contents);
							  $quote_contents = $wc_quote->add_to_quote($product_detail, $product_id, $quantity, $variation_id, $variation, array(), true);
			
							  if (is_array($quote_contents) ) { 
								  $quote_totals = $wc_quote->get_calculated_totals($quote_contents);
								  update_post_meta($old_quote->ID, 'quote_totals', wp_json_encode($quote_totals));
								  update_post_meta($old_quote->ID, 'quote_contents', $quote_contents);
							  }
						  endforeach;
				}
				//backward comp done
				update_option('wc_quote_backward_check', true);
			 
			}          
		}
		/**
		 * Check if WooCommerce is active
		 **/
		public function watq_check_if_woo_is_active(){
			if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
				deactivate_plugins( plugin_basename( __FILE__ ) );
				$class = 'error';
				$message = __('Quote plugin requires Woocommerce plugin to be activated.', 'wc-quote-lite');
				echo '<div class=\"' . esc_attr($class) . '\"> <p>' . esc_attr($message) . '</p></div>';
			}
		}
		public function watq_load_functions() {
		
			if ( class_exists('WooCommerce') ) {
				$this->includes();
			}
		}

		public function includes(){
			require_once WATQ_QUOTE_PATH . 'includes/class-wc-quote-functions.php';
			include_once WATQ_QUOTE_PATH . 'includes/class-wc-quote-lite-process.php';
			include_once WATQ_QUOTE_PATH . 'includes/class-wc-quote-lite-email-controller.php';
			require_once WATQ_QUOTE_PATH . 'includes/admin/class-wc-quote-lite-register.php';
			include_once WATQ_QUOTE_PATH . 'includes/admin/class-wc-quote-lite-admin-assets.php';
			include_once WATQ_QUOTE_PATH . 'includes/public/class-wc-quote-lite-front.php';
			include_once WATQ_QUOTE_PATH . 'includes/public/class-wc-quote-lite-front-assets.php';
			include_once WATQ_QUOTE_PATH . 'includes/public/class-wc-quote-lite-front-ajax.php';
			include_once WATQ_QUOTE_PATH . 'includes/public/class-wc-quote-lite-front-assets.php';
		}
	}

	( new WATQ() );

endif;
