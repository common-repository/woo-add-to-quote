<?php
/**
 * WC Quote Email Controller.
 *
 * The WooCommerce quote class stores quote data and maintain session of quotes.
 * The quote class also has a price calculation function which calls upon other classes to calculate totals.
 */

defined('ABSPATH') || exit;


/**
 * WC_QUOTE_Email_Controller class.
 */
class WC_QUOTE_Email_Controller {


	/**
	 * Contains an array of quote items.
	 *
	 * @var array
	 */
	private static $email_headers;

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
	 * Constructor for the WC_QUOTE_Email_Controller class. Loads email headers.
	 */
	public function __construct() {
		$this->quote_settings['general'] = get_option('wc_quote_general', false);
		$this->quote_settings['email'] = get_option('wc_quote_email', false);
		$this->quote_settings['notifications'] = get_option('wc_quote_notifications', false);
		$this->init();
		add_action('wc_quote_email_header', array( $this, 'get_email_header' ));
		add_action('wc_quote_email_footer', array( $this, 'get_email_footer' ));
		add_action('wc_quote_email_customer_details', array( $this, 'get_customer_info_table' ));
		add_action('wc_quote_email_quote_details', array( $this, 'get_quote_contents_table' ));

		// Action Hooks to send emails.
		add_action('wc_quote_send_quote_email_to_customer', array( $this, 'send_email_to_customer' ));
		add_action('wc_quote_send_quote_email_to_admin', array( $this, 'send_email_to_admins' ));

		add_filter('woocommerce_email_footer_text', array( $this, 'replace_placeholders' ));
	}

	/**
	 * Init function to initialize the necessary actions for emails.
	 */
	public function init() {
		self::$email_headers = $this->get_email_headers();
	}

	/**
	 * Load the template of email header.
	 */
	public function get_email_header( $email_heading ) {

		$this->quote_settings['email'] = get_option('wc_quote_email', false);
		$email_text_before = isset($this->quote_settings['email']['quote_email_before_message']) && ! empty($this->quote_settings['email']['quote_email_before_message']) ? $this->quote_settings['email']['quote_email_before_message'] : '';


		if (file_exists(get_stylesheet_directory() . '/woocommerce/wcquote/emails/wc-quote-email-header.php') ) {

			include get_stylesheet_directory() . '/woocommerce/wcquote//emails/wc-quote-email-header.php';

		} else {

			include WATQ_QUOTE_PATH . 'templates/emails/wc-quote-email-header.php';
		}
	}

	/**
	 * Load the template of email footer.
	 */
	public function get_email_footer() {

		$this->quote_settings['email'] = get_option('wc_quote_email', false);
		$email_text_after = isset($this->quote_settings['email']['quote_email_after_message']) && ! empty($this->quote_settings['email']['quote_email_after_message']) ? $this->quote_settings['email']['quote_email_after_message'] : '';

		if (file_exists(get_stylesheet_directory() . '/woocommerce/wcquote/emails/wc-quote-email-footer.php') ) {

			include get_stylesheet_directory() . '/woocommerce/wcquote/emails/wc-quote-email-footer.php';

		} else {

			include WATQ_QUOTE_PATH . 'templates/emails/wc-quote-email-footer.php';
		}
	}

	/**
	 * Apply inline styles to dynamic content.
	 *
	 * We only inline CSS for html emails, and to do so we use Emogrifier library (if supported).
	 *
	 * @param  string|null $content Content that will receive inline styles.
	 * @return string
	 */
	public function send_email_to_customer( $quote_id ) {
		 
		// Email to customer.
		$user_name     = get_the_author_meta('display_name', get_post($quote_id)->post_author);
		$user_emails = get_post_meta( $quote_id , 'quote_contents');
		$user_email = $user_emails[0]['sender_email'];
		 
		if (!empty($this->quote_fields) && is_array($this->quote_fields) ) :
			foreach ( $this->quote_fields['components'] as $key => $field ) {
				if ('email' === $field['type'] ) {
					$field_data = get_post_meta($quote_id, 'quote_fields', true);
					$field_key  = $field['key'];
					$user_email = !empty($field_data[ $field_key ]) ? $field_data[ $field_key ] : '' ;
					break;
				}
			}
		endif;
		$quote_status  = get_post($quote_id)->post_status;
		$quote_status  = str_replace('quote_', '', $quote_status);

		// $quote_heading = get_post_status_object(get_post_status($quote_id))->label;
		$quote_heading = __('New', 'wc-quote-lite');
		
		$attachments = array();
		$email_heading = esc_html__($quote_heading . ' Quote Request', 'wc-quote-lite');
		/**
		 * Filter wc_quote_$quote_status_user_email_heading
		 * 
		 * @since 1.0
		**/
		$email_heading = apply_filters('wc_quote_' . $quote_status . '_user_email_heading', $email_heading);
		$email_subject = isset($this->quote_settings['email'][ $quote_status . '_user_email_subject' ]) ? $this->quote_settings['email'][ $quote_status . '_user_email_subject' ] : $email_heading;
		$email_message = isset($this->quote_settings['email'][ $quote_status . '_user_email_msg' ]) ? $this->quote_settings['email'][ $quote_status . '_user_email_msg' ] : '';

	
		$email_valid_msg = isset($this->quote_settings['notifications']['email_valid_msg']) ? $this->quote_settings['notifications']['email_valid_msg'] : 'Please enter a valid email';
		$email_error_msg = isset($this->quote_settings['notifications']['email_error_msg']) ? $this->quote_settings['notifications']['email_error_msg'] : 'Email not sent to customer';

		$email_subject = isset($this->quote_settings['email']['quote_email_subject']) ? $this->quote_settings['email']['quote_email_subject'] : __('New Quote Request', 'wc-quote-lite');

		$email = $user_email;

		$email_subject = str_replace('{quote_id}', $quote_id, $email_subject);
		$email_heading = str_replace('{quote_id}', $quote_id, $email_heading);

		$email_subject = str_replace('{user_name}', $user_name, $email_subject);
		$email_heading = str_replace('{user_name}', $user_name, $email_heading);

		ob_start();

		if (file_exists(get_stylesheet_directory() . '/woocommerce/wcquote/emails/wc-quote-email-to-customer.php') ) {

			include get_stylesheet_directory() . '/woocommerce/wcquote/emails/wc-quote-email-to-customer.php';

		} else {

			include WATQ_QUOTE_PATH . 'templates/emails/wc-quote-email-to-customer.php';
		}

		$template = ob_get_clean();

		$customer_email_html = $this->style_inline($template);

		$send_user_email = wp_mail($user_email, $email_subject, $customer_email_html, self::$email_headers, $attachments);
		if (!$send_user_email && !is_admin() ) {
			wc_add_notice(esc_html__($email_error_msg, 'wc-quote-lite'), 'error');
			return;
		} elseif (!$send_user_email ) {
			printf('<div class="notice notice-error"><p>Cannot send customer email to %s </p></div>', esc_attr($user_email));
			return;
		}
	}

	public function send_email_to_admins( $quote_id ) {

		// Email to administrators, shop managers.
		$user_name     = get_the_author_meta('display_name', get_post($quote_id)->post_author);
		$user_email    = get_the_author_meta('user_email', get_post($quote_id)->post_author);
		$admin_email   = isset($this->quote_settings['email']['admin_email']) && ! empty($this->quote_settings['email']['admin_email']) ? $this->quote_settings['email']['admin_email'] : get_bloginfo('admin_email');
		$quote_status  = get_post($quote_id)->post_status;
		$quote_status  = str_replace('quote_', '', $quote_status);
		// $quote_heading = get_post_status_object(get_post_status($quote_id))->label;
		$quote_heading = __('New', 'wc-quote-lite');

		$email_subject = isset($this->quote_settings['email'][ $quote_status . '_admin_email_subject' ]) ? $this->quote_settings['email'][ $quote_status . '_admin_email_subject' ] : '';
		$email_heading = esc_html__($quote_heading . ' Quote Request', 'wc-quote-lite');
		/**
		 * Filter wc_quote_$quote_status_admin_email_heading
		 * 
		 * @since 1.0
		**/
		$email_heading = apply_filters('wc_quote_' . $quote_status . '_admin_email_heading', $email_heading);
		$email_message = isset($this->quote_settings['email'][ $quote_status . '_admin_email_msg' ]) ? $this->quote_settings['email'][ $quote_status . '_admin_email_msg' ] : '';

		$email_subject = str_replace('{quote_id}', $quote_id, $email_subject);
		$email_heading = str_replace('{quote_id}', $quote_id, $email_heading);

		$email_subject = str_replace('{user_name}', $user_name, $email_subject);
		$email_heading = str_replace('{user_name}', $user_name, $email_heading);

		$email_subject = __('Quote New Request', 'wc-quote-lite');
		/**
		 * Filter wc_quote_admin_email
		 * 
		 * @since 1.0
		**/
		$email = apply_filters('wc_quote_admin_email', $admin_email, $quote_id);
		/**
		 * Filter wc_quote_admin_email
		 * 
		 * @since 1.0
		**/
		$admin_email = apply_filters('wc_quote_admin_email', $admin_email, $quote_id);
		 
		$email_valid_msg = isset($this->quote_settings['notifications']['email_valid_msg']) ? $this->quote_settings['notifications']['email_valid_msg'] : 'Please enter a valid email';
		$email_error_msg = isset($this->quote_settings['notifications']['email_error_msg']) ? $this->quote_settings['notifications']['email_error_msg'] : 'Email not sent to admin';

		 
		if (!is_email($admin_email) && !is_admin() ) { 
			wc_add_notice(esc_html__($email_valid_msg, 'wc-quote-lite'), 'error');
			return;
		} elseif (!is_email($admin_email) ) {
			/* translators: %s: Customer email address. */
			printf('<div class="notice notice-error"><p>%s is not a valid email address please insert a valid email in quote settings</p></div>', esc_attr($admin_email));
			return;
		}

		ob_start();

		if (file_exists(get_stylesheet_directory() . '/woocommerce/wcquote/emails/wc-quote-email-to-admin.php') ) {

			include get_stylesheet_directory() . '/woocommerce/wcquote/emails/wc-quote-email-to-admin.php';

		} else {

			include WATQ_QUOTE_PATH . 'templates/emails/wc-quote-email-to-admin.php';
		}

		$template = ob_get_clean();

		$admin_email_html = $this->style_inline($template);
		 
		$headers   = self::$email_headers;
		$headers[] = 'Reply-to: ' . $user_name . ' <' . $user_email . '>';

		$send_email_admin = wp_mail($admin_email, $email_subject, $admin_email_html, $headers);
		if (!$send_email_admin && !is_admin() ) {
			wc_add_notice(esc_html__($email_error_msg, 'wc-quote-lite'), 'error');
			return;
		} elseif (!$send_email_admin ) {
			printf('<div class="notice notice-error"><p>Cannot send admin email to %s </p></div>', esc_attr($admin_email));
			return;
		}
	}

	/**
	 * Apply inline styles to dynamic content.
	 *
	 * We only inline CSS for html emails, and to do so we use Emogrifier library (if supported).
	 *
	 * @version 4.0.0
	 * @param   string|null $content Content that will receive inline styles.
	 * @return  string
	 */
	public function style_inline( $content ) {

		ob_start();
		wc_get_template('emails/email-styles.php');
		/**
		 * Filter wc_quote_email_styles
		 * 
		 * @since 1.0
		**/
		$css = apply_filters('wc_quote_email_styles', ob_get_clean(), $this);

		$emogrifier_class = 'Pelago\\Emogrifier';

		if (class_exists($emogrifier_class) ) {
			try {
				$emogrifier = new $emogrifier_class($content, $css);
				/**
				* Action wc_quote_emogrifier
				* 
				* @since 1.0
				**/
				do_action('wc_quote_emogrifier', $emogrifier, $this);

				$content    = $emogrifier->emogrify();
				$html_prune = \Pelago\Emogrifier\HtmlProcessor\HtmlPruner::fromHtml($content);
				$html_prune->removeElementsWithDisplayNone();
				$content = $html_prune->render();
			} catch ( Exception $e ) {
				$logger = wc_get_logger();
				$logger->error($e->getMessage(), array( 'source' => 'emogrifier' ));
			}
		} else {
			$content = '<style type="text/css">' . $css . '</style>' . $content;
		}

		return $content;
	}

	/**
	 * Get blog name formatted for emails.
	 *
	 * @return string
	 */
	private function get_blogname() {
		return wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
	}

	/**
	 * Replace placeholder text in strings.
	 *
	 * @since  3.7.0
	 * @param  string $string Email footer text.
	 * @return string         Email footer text with any replacements done.
	 */
	public function replace_placeholders( $string ) {
		$domain = wp_parse_url(home_url(), PHP_URL_HOST);

		return str_replace(
			array(
				'{site_title}',
				'{site_address}',
				'{site_url}',
				'{woocommerce}',
				'{WooCommerce}',
			),
			array(
				$this->get_blogname(),
				$domain,
				$domain,
				'<a href="https://woocommerce.com">WooCommerce</a>',
				'<a href="https://woocommerce.com">WooCommerce</a>',
			),
			$string
		);
	}


	/**
	 * Load the template of email footer.
	 */
	public function get_quote_contents_table( $quote_id ) {

		$quote_contents = get_post_meta($quote_id, 'quote_contents', true);

		if (! isset($wc_quote) ) {
			$wc_quote = new WC_QUOTE_Process($quote_contents);
		}

		$colspan  = 2;
		$colspan  = 'quote_quoted' === get_post($quote_id)->post_status;
		$totals = $wc_quote->get_calculated_totals($quote_contents);
		$quote_subtotal = isset($totals['_subtotal']) ? $totals['_subtotal'] : 0;
		$vat_total      = isset($totals['_tax_total']) ? $totals['_tax_total'] : 0;
		$quote_total    = isset($totals['_total']) ? $totals['_total'] : 0;

		if (empty($quote_contents) ) {
			return;
		}
	
		
		if (file_exists(get_stylesheet_directory() . '/woocommerce/wcquote/emails/wc-quote-contents-table.php') ) {

			include get_stylesheet_directory() . '/woocommerce/wcquote/emails/wc-quote-contents-table.php';

		} else {

			include WATQ_QUOTE_PATH . 'templates/emails/wc-quote-contents-table.php';
		}
	}

	/**
	 * Load the template of email userinfo.
	 */
	public function get_customer_info_table( $quote_id ) {

		$customer_info = $this->get_quote_user_info($quote_id);

		if (empty($customer_info) ) {
			return;
		}

		if (file_exists(get_stylesheet_directory() . '/woocommerce/wcquote/emails/wc-quote-customer-info.php') ) {

			include get_stylesheet_directory() . '/woocommerce/wcquote/emails/wc-quote-customer-info.php';

		} else {

			include WATQ_QUOTE_PATH . 'templates/emails/wc-quote-customer-info.php';
		}
	}

	/**
	 * Load the customer info and extra field data in array.
	 */
	public function get_quote_user_info( $quote_id ) {

		$customer_info = array();
		$quote_date    = date_i18n(get_option('date_format'), get_post_time('U', false, $quote_id, true));
		 
		$customer_info['quote_id']   = array(
			'label' => __('Quote Number', 'wc-quote-lite'),
			'value' => $quote_id,
		);
		$customer_info['quote_date'] = array(
			'label' => __('Quote Date', 'wc-quote-lite'),
			'value' => $quote_date,
		);


		return $customer_info;
	}

	/**
	 * Get WooCommerce settings and return the header of email.
	 *
	 * @return string
	 */
	public function get_email_headers() {

		// Get settings from WooCommerce.
		$from_name  = get_option('woocommerce_email_from_name');
		$from_email = isset($this->quote_settings['email']['admin_email']) ? $this->quote_settings['email']['admin_email'] : get_bloginfo('admin_email');
		$from_email = !empty($from_email) ? $from_email : get_option('woocommerce_email_from_address');

		// More headers.
		$headers[] = 'MIME-Version: 1.0';
		$headers[] = 'Content-Type: text/html; charset=UTF-8';
		$headers[] = 'From: ' . $from_name . ' <' . $from_email . '>';

		return $headers;
	}
}

new WC_QUOTE_Email_Controller();
