<?php  
$success_email_quote_success = isset($this->quote_settings['success_email_quote_success']) ? $this->quote_settings['success_email_quote_success'] : '';
$error_email_quote_error = isset($this->quote_settings['error_email_quote_error']) ? $this->quote_settings['error_email_quote_error'] : '';
$error_email_user_input = isset($this->quote_settings['error_email_user_input']) ? $this->quote_settings['error_email_user_input'] : '';

?>

<h2><?php echo esc_html__('Customize Email Template', 'wc-quote-lite'); ?></h2>
<table class="form-table">
	<tbody>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="wc_quote_new_admin_email_msg">
					<?php echo esc_html__('Message Email Quote on Success', 'wc-quote-lite'); ?>
				</label>
			</th>
			<td class="forminp forminp-textarea">
				<textarea name="wc_quote_messages[success_email_quote_success]" style="width:400px; height: 75px;" id="wc_quote_success_email_quote_success" placeholder="Message"><?php echo wp_kses_post($success_email_quote_success); ?></textarea>
				<p class="description"><?php echo esc_html__('This Text will show as notice when user successfully email quotes.', 'wc-quote-lite'); ?></p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="wc_quote_error_email_quote_error">
					<?php echo esc_html__('Message Email Quote on Error', 'wc-quote-lite'); ?>
				</label>
			</th>
			<td class="forminp forminp-textarea">
				<textarea name="wc_quote_messages[error_email_quote_error]" style="width:400px; height: 75px;" id="wc_quote_error_email_quote_error" placeholder="Message"><?php echo wp_kses_post($error_email_quote_error); ?></textarea>
				<p class="description"><?php echo esc_html__('Message Email Quote on Error', 'wc-quote-lite'); ?></p>
			</td>
		</tr>
	</tbody>
</table>
