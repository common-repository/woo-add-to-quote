<?php
/**
 * File to register post types and menus.
 *
 * @package wc-quote
 */

if (! defined('ABSPATH') ) {
	exit;
}

if (! class_exists('WATQ_Register', false) ) :

	/**
	 *  Class   WC_QUOTE_Register .
	 *  Register plugin post type & menus .
	 */
	class WATQ_Register {
	
	
		private $quote_fields;

		private $quote_settings;

		public function __construct() {

			$this->quote_settings['general'] = get_option('wc_quote_general', false);

			add_action('init', array( $this, 'register_wcquote_post_type' ));
			add_action('pre_get_posts', array( $this, 'wc_quote_status_filter_query' ));
			add_action('init', array( $this, 'register_wcquote_rules_post_type' ));
			add_action('add_meta_boxes', array( $this, 'wc_quote_register_metabox' ));
			//On save post
			add_action('save_post_wc-quote', array( $this, 'wc_quote_save_quote' ), 10, 3);
			add_action('manage_wc-quote_posts_custom_column', array( $this, 'wc_quote_add_column_data' ), 9, 2);
			add_filter('manage_wc-quote_posts_columns', array( $this, 'wc_quote_modify_column_names' ));
			add_filter('post_row_actions', array( $this, 'get_row_actions' ), 100, 2);
			add_filter('post_row_actions', array( $this, 'remove_quick_edit' ), 10, 2);
			add_action( 'admin_enqueue_scripts', array( $this, 'watq_update_unread_quotes' ), 10 );
			add_filter ('bulk_actions-edit-wc-quote', array( $this, 'wc_quote_remove_bulk_actions' ) );
		}

	
		
		public function wc_quote_remove_bulk_actions( $actions ) {
			unset( $actions['edit'] );
			return $actions;
		}       

		public function watq_update_unread_quotes() {
			global $post, $pagenow ;
			if (is_object($post) && isset($pagenow)) {
				if ( $post->post_type == 'wc-quote' && $pagenow == 'post.php' ) {
					if ((int) get_option('watq_unread_quotes') > 0 && $post->post_content == 'unread' ) {
						$unread_quotes = (int) get_option('watq_unread_quotes') -1;
						update_option('watq_unread_quotes', $unread_quotes);
	
						$change_status = array(
							'ID'           => $post->ID,
							'post_content' => 'read',
						);
	
						wp_update_post( $change_status );
					}
				}
			}
		}

		public function remove_quick_edit( $actions, $post ){
			if ($post->post_type =='wc-quote') {
				unset($actions['edit']);
			}
				return $actions;
		}


		public function wc_quote_status_filter_query( $query ) {
			if (is_admin() && 'edit.php' === $query->get('pagenow') && 'wc-quote' === $query->get('post_type') ) {

				$current_status = sanitize_text_field($query->get('post_status'));

				if (! empty($current_status) ) {
					$meta_query = array(
						array(
							'key'   => 'post_status',
							'value' => $current_status,
						),
					);
					$query->set('meta_query', $meta_query);
				}
				$search_query = sanitize_text_field($query->get('s'));

				if (! empty($search_query) ) {
					$query->set('s', $search_query);
				}
			}
		}


		/**
		 * Count quotes of specific status
		 */
		public function get_quote_count( $status ) {
			global $wpdb;
			return absint($wpdb->get_var($wpdb->prepare("SELECT COUNT( * ) FROM {$wpdb->posts} WHERE post_type = 'wc-quote' AND post_status = %s", $status)));
		}


	/**
		 * Register a custom post type called "wc-quote".
		 *
		 * @see get_post_type_labels() for label keys.
		 */
		public function register_wcquote_post_type() {
			$quote_count = $this->get_quote_count('quote_new');
			register_post_type(
				'wc-quote',
				/**
				* Filter wc_quote_register_post_type
				* 
				* @since 1.0
				**/
				apply_filters(
					'wc_quote_register_post_type',
					array(
						'labels'              => array(
							'name'                  => __('Quotes', 'wc-quote-lite'),
							'singular_name'         => _x('Quote', 'wc-quote post type singular name', 'wc-quote-lite'),
							'all_items'             => $quote_count ? __('Quotes', 'wc-quote-lite') . ' <span class="awaiting-mod count-' . esc_attr($quote_count) . '"><span class="new-count">' . number_format_i18n($quote_count) . '</span></span>' : __('Quotes', 'wc-quote'),
							'add_new'               => __('Add quote', 'wc-quote-lite'),
							'add_new_item'          => __('Add new quote', 'wc-quote-lite'),
							'edit'                  => __('Edit', 'wc-quote-lite'),
							'edit_item'             => __('Edit quote', 'wc-quote-lite'),
							'new_item'              => __('New quote', 'wc-quote-lite'),
							'view_item'             => __('View quote', 'wc-quote-lite'),
							'search_items'          => __('Search quotes', 'wc-quote-lite'),
							'not_found'             => __('No quotes found', 'wc-quote-lite'),
							'not_found_in_trash'    => __('No quotes found in trash', 'wc-quote-lite'),
							'parent'                => __('Parent quotes', 'wc-quote-lite'),
							'menu_name'             => _x('WC Quote', 'Admin menu name', 'wc-quote-lite'),
							'filter_items_list'     => __('Filter quotes', 'wc-quote-lite'),
							'items_list_navigation' => __('Quotes navigation', 'wc-quote-lite'),
							'items_list'            => __('Quotes list', 'wc-quote-lite'),
						),
						'description'   => 'Added Quotes',
						'public'        => false,
						'show_in_menu'  => 'wc-quote-lite',
						'show_ui'       => true,
						'capabilities' => array(
							'create_posts' => ( is_multisite() ? 'do_not_allow' : false ),
						),
						'map_meta_cap' => true,
						'supports'      => array( '' ),
						'has_archive'   => true,
					)
				)
			);

			/**
			* Filter wc_quote_register_post_statuses
			* 
			* @since 1.0
			**/
			$quote_statuses = apply_filters(
				'wc_quote_register_post_statuses',
				array(
					'quote_draft'    => array(
						'label'                     => _x('Draft', 'Quote status', 'wc-quote-lite'),
						'public'                    => true,
						'exclude_from_search'       => false,
						'show_in_admin_all_list'    => true,
						'show_in_admin_status_list' => true,
						/* translators: %s: number of quotes */
						'label_count'               => _n_noop('Draft <span class="count">(%s)</span>', 'Draft <span class="count">(%s)</span>', 'wc-quote-lite'),
					),
					'quote_new'    => array(
						'label'                     => _x('unread', 'Quote status', 'wc-quote-lite'),
						'public'                    => true,
						'exclude_from_search'       => false,
						'show_in_admin_all_list'    => true,
						'show_in_admin_status_list' => true,
						/* translators: %s: number of quotes */
						'label_count'               => _n_noop('New <span class="count">(%s)</span>', 'New <span class="count">(%s)</span>', 'wc-quote-lite'),
					),
				
				)
			);

			foreach ( $quote_statuses as $quote_status => $values ) {
				register_post_status($quote_status, $values);
			}
		}

		/**
		 * Register a custom post type called "wc-quote-rules".
		 *
		 * @see get_post_type_labels() for label keys.
		 */
		public function register_wcquote_rules_post_type() {
			register_post_type(
				'wc-quote-rules',
				/**
				* Filter wc_quote_rules_register_post_type
				* 
				* @since 1.0
				**/
				apply_filters(
					'wc_quote_rules_register_post_type',
					array(
						'labels'              => array(
							'name'                  => __(sprintf('Rules <span style="color: #D63638;font-weight: 600;">(Pro)</span>'), 'wc-quote'),
						),
						'show_ui'             => true,
						'show_in_menu'        => 'wc-quote-lite',
					)
				)
			);
		}

		/**
		 * Register metabox for wc-quote.
		 */
		public function wc_quote_register_metabox() {

			add_meta_box( 
				'wc-quote-user-data',
				__('Quote Detail', 'wc-quote-lite'),
				array( $this, 'wc_quote_lite_details_html' ),
				'wc-quote',
				'normal',
				'high'
			);

			add_meta_box( 
				'wc-quote-items',
				__('Quote Products', 'wc-quote-lite'),
				array( $this, 'wc_quote_main_meta_html' ),
				'wc-quote',
				'normal',
				'high'
			);
		}


		/**
		 * Rendering HTMl for main meta box wc-quote-rules
		 */
		public function wc_quote_rules_main_meta_html() {
			if (file_exists(get_stylesheet_directory() . '/woocommerce/wcquote/admin/views/metabox/html-quote-rules-metabox.php') ) {
				include get_stylesheet_directory() . '/woocommerce/wcquote/admin/views/metabox/html-quote-rules-metabox.php';
			} else {
				include_once WATQ_QUOTE_PATH . '/includes/admin/views/metabox/html-quote-rules-metabox.php';
			}
		}

		/**
		 * Rendering HTMl for side meta box wc-quote
		 */
		public function wc_quote_side_meta_html() {
			if (file_exists(get_stylesheet_directory() . '/woocommerce/wcquote/admin/views/metabox/html-quote-meta-actions.php') ) {

				include get_stylesheet_directory() . '/woocommerce/wcquote/admin/views/metabox/html-quote-meta-actions.php';

			} else {

				include_once WATQ_QUOTE_PATH . '/includes/admin/views/metabox/html-quote-meta-actions.php';
			}
		}


		/**
		 * Rendering HTMl for extra meta box wc-quote-lte details
		 */
		public function wc_quote_lite_details_html() {
			if (file_exists(get_stylesheet_directory() . '/woocommerce/wcquote/admin/views/metabox/html-quote-lite-details.php') ) {

				include get_stylesheet_directory() . '/woocommerce/wcquote/admin/views/metabox/html-quote-lite-details.php';

			} else {

				include_once WATQ_QUOTE_PATH . '/includes/admin/views/metabox/html-quote-lite-details.php';
			}
		}


		/**
		 * Rendering HTMl for main meta box wc-quote
		 */
		public function wc_quote_main_meta_html() {
			if (file_exists(get_stylesheet_directory() . '/woocommerce/wcquote/admin/views/metabox/html-quote-meta-list.php') ) {

				include get_stylesheet_directory() . '/woocommerce/wcquote/admin/views/metabox/html-quote-meta-list.php';

			} else {

				include_once WATQ_QUOTE_PATH . '/includes/admin/views/metabox/html-quote-meta-list.php';
			}
		}

		/**
		 * Save post meta foreach quote created by admin.
		 */
		public function wc_quote_save_quote( $post_id, $post ) {

			global $wpdb;
			
			if (! 'wc-quote' === $post->post_type ) {
				return;
			}

			if (!isset($_POST['wc-quote-save-post']) ) {
				return;
			}

			// if our nonce isn't there, or we can't verify it, return
			if (isset($_POST['wc-quote-save-post']) && ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['wc-quote-save-post'])), 'wc-quote-save-post') ) {
				return;
			}
			// Update Quote
			$form_data = sanitize_meta('', wp_unslash($_POST), '');

			if (!empty($form_data) ) {
				$form_data['post_author'] = isset($form_data['post_author_override']) ? $form_data['post_author_override'] : 0;

				$quote_contents = get_post_meta($post_id, 'quote_contents', true);
				$quote_contents = !empty($quote_contents) ? $quote_contents : array( 0 => 0 );

				$quotes = $quote_contents;

				foreach ( $quote_contents as $quote_item_key => $quote_item ) {

					if (isset($form_data['quote_qty'][ $quote_item_key ]) ) {
						$quotes[ $quote_item_key ]['quantity'] = intval($form_data['quote_qty'][ $quote_item_key ]);
					}

					if (isset($form_data['offered_price'][ $quote_item_key ]) ) {
						$quotes[ $quote_item_key ]['offered_price'] = floatval($form_data['offered_price'][ $quote_item_key ]);
					}
				
				}

				// Save Quote Data
				update_post_meta($post_id, 'quote_contents', $quotes);

				// Save Quote Fields
				if (isset($form_data['data']) ) :
					$quote_fields = $this->quote_fields;
					foreach ( $quote_fields['components'] as $key => $field ) {
						$wc_quote_field_name  = $field['key'];
						$wc_quote_field_type  = $field['type'];
						if ('radio' === $wc_quote_field_type  ) :
							$form_data = ! empty($form_data['data'][ $wc_quote_field_name ]) ? $form_data['data'][ $wc_quote_field_name ] : array();
							$form_data['data'][ $wc_quote_field_name ] = reset($form_data);
						endif;
					}
					update_post_meta($post_id, 'quote_fields', $form_data['data']);
				endif;

				$wpdb->update($wpdb->prefix . 'posts', array( 'post_author' => $form_data['post_author'] ), array( 'ID' => $post_id ));
				/**
				 * Action wc_quote_contents_updated
				 * 
				 * @since 1.0
				**/
				do_action('wc_quote_contents_updated', $post_id);

				if (isset($form_data['post_status']) ) {

					$old_status = $post->post_status;
					/**
					 * Action wc_quote_status_updated
					 * 
					 * @since 1.0
					**/
					do_action('wc_quote_status_updated', $post_id, $form_data['post_status'], $old_status);
				}
			
				/**
				 * Action wc_quote_send_quote_email_to_customer
				 * 
				 * @since 1.0
				**/
				do_action('wc_quote_send_quote_email_to_customer', $post_id);
				/**
				 * Action wc_quote_send_quote_email_to_admin
				 * 
				 * @since 1.0
				**/
				do_action('wc_quote_send_quote_email_to_admin', $post_id);

			}
		}


		/**
		 * Add new columns for wc-quote.
		 */
		public function wc_quote_modify_column_names( $columns ) {
			unset($columns['date']);
			$columns['quote_user'] = esc_html__('User', 'wc-quote-lite');
			$columns['quote_status'] = esc_html__('Status', 'wc-quote-lite');
			$columns['date'] = esc_html__('Date', 'wc-quote-lite');
			return $columns;
		}

		/**
		 * Add column data for new added columns in wc-quote.
		 */
		public function wc_quote_add_column_data( $column, $postId ) {
			global $post;

			switch ($column) {

				case 'quote_status':
					echo esc_html( $post->post_content );
					break;
		
				case 'quote_user':
					$post_meta = get_post_meta( $postId, 'quote_contents' );
					if (!empty($post_meta[0]['quote_user_id'])) {
						esc_html_e('Registered', 'wc-quote-lite');
					} else {
						esc_html_e('Guest', 'wc-quote-lite');
					}
					break;
				

			}    
		}


		/**
		 * Remove inline edit for each quote.
		 */
		public function get_row_actions( $actions, $post ) {
			if ('wc-quote' === $post->post_type || 'wc-quote-rules' === $post->post_type ) :
				unset($actions['inline']);
				unset($actions['inline hide-if-no-js']);
			endif;
			return $actions;
		}
	}

	new WATQ_Register();
endif;
