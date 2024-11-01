<?php 
defined('ABSPATH') || exit;
global $post;
$get_user_id = $post->post_author;
$user_string = '';
$user_id     = '';
$user = '';
if ($get_user_id > 0 ) {
	$user_id = absint($get_user_id);
	$user    = get_user_by('id', $user_id);
	 
	$user_string = sprintf(
	/* translators: %1$s: user display name %2$s: user ID %3$s: user email */
		esc_html__('%1$s (#%2$s &ndash; %3$s)', 'wc-quote-lite'),
		$user->display_name,
		absint($user->ID),
		$user->user_email
	);
}
if (!empty($user_id) && is_object($user) ) {
	$id_user   = $user_id;
	$user_name = $user->display_name . '(' . $user->user_email . ')';
}

$extra_fields = json_decode(str_replace('"false"', 'false', wp_unslash(get_option('wc_quote_form_builder'))), true);
foreach ($extra_fields['components'] as $key => $field) {
	$wc_quote_field_name  = $field['key'];
	$wc_quote_field_type  = $field['type'];
		
	if ('file' === $wc_quote_field_type) {
		echo '<input type="hidden" name="data[' . esc_attr($wc_quote_field_name) . ']">';
	}
}
?>
<script type="text/javascript">
	window.onload = function() {
		Formio.createForm(document.getElementById('quote-fields'), components).then(function(form) {
			form.on('change', function() {
					<?php 
					foreach ( $extra_fields['components'] as $key => $field ) {
						$wc_quote_field_name  = $field['key'];
						$wc_quote_field_type  = $field['type'];
						if (isset($field['components']) ) : 
							foreach ($field['components'] as $k => $v) {
								if ('file' === $v['type'] ) :
									?>
									jQuery('input[name="data[<?php echo esc_attr($v['key']); ?>]"]').val(JSON.stringify(form.submission.data.<?php echo esc_attr($v['key']); ?>));
									<?php 
								endif;
							}
						endif;
						if ('file' === $wc_quote_field_type ) : 
							?>
							jQuery('input[name="data[<?php echo esc_attr($wc_quote_field_name); ?>]"]').val(JSON.stringify(form.submission.data.<?php echo esc_attr($wc_quote_field_name); ?>));
							<?php
						endif;
					}
					?>
					
				});
			form.nosubmit = true;
			form.submission =  {data:<?php echo wp_kses_post(str_replace(array( '"[{', '}]"', '"[]"', '""' ), array( '[{', '}]', '[]', '[]' ), wp_unslash(json_encode(get_post_meta($post->ID, 'quote_fields', true))))); ?> } ;
		});
	}
</script>
<table class="form-table">
	<tbody>
		<?php if (! empty($post->ID) ) : ?>
			<tr valign="top">
				<th scope="row" class="titledesc"><?php echo esc_html__('Quote #: ', 'wc-quote-lite'); ?></th>
				<td class="forminp forminp-checkbox">
					<label for="wc_quote_post_id"><?php echo esc_attr($post->ID); ?></label>
				</td>
			</tr>
		<?php endif; ?>
		<tr valign="top">
			<th scope="row" class="titledesc"><?php echo esc_html__('Quote User: ', 'wc-quote-lite'); ?></th>
			<td class="forminp forminp-checkbox">
				<label for="wc_quote_rules_roles_specific">
					<select class="wc-customer-search" id="customer_user" name="post_author_override" data-placeholder="<?php esc_attr_e('Guest', 'wc-quote-lite'); ?>" data-allow_clear="true">
						<option value="<?php echo esc_attr($user_id); ?>" selected="selected"><?php echo wp_kses_post($user_string); // htmlspecialchars to prevent XSS when rendered by selectWoo. ?></option>
					</select>
				</label>
			</td>
		</tr>
	</tbody>
</table>
<div class="forminp forminp-fields" id="quote-fields"></div>
<?php 
	/**
	 * Action wc_quote_after_customer_details
	 * 
	 * @since 1.0
	 **/
do_action('wc_quote_after_customer_details', $post);
wp_nonce_field('wc-quote-save-post', 'wc-quote-save-post');
