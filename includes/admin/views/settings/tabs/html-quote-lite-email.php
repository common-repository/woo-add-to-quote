<?php  
$quote_email_subject = isset($this->quote_settings['quote_email_subject']) ? $this->quote_settings['quote_email_subject'] : '';
$quote_email_before_message = isset($this->quote_settings['quote_email_before_message']) ? $this->quote_settings['quote_email_before_message'] : '';
$quote_email_after_message = isset($this->quote_settings['quote_email_after_message']) ? $this->quote_settings['quote_email_after_message'] : '';
?>

<h2><?php echo esc_html__('Notices Option', 'wc-quote-lite'); ?></h2>
<table class="form-table">
	<tbody>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="wc_quote_new_admin_email_subject">
					<?php echo esc_html__('Subject', 'wc-quote-lite'); ?>
				</label>
			</th>
			<td class="forminp forminp-text">
				<input name="wc_quote_email[quote_email_subject]" id="wc_quote_email_subject" type="text" placeholder="Subject" value="<?php echo esc_attr($quote_email_subject); ?>">
				<p class="description"><?php echo esc_html__('Subject of Email', 'wc-quote-lite'); ?></p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="wc_quote_new_admin_email_msg">
					<?php echo esc_html__('Text Before Quote', 'wc-quote-lite'); ?>
				</label>
			</th>
			<td class="forminp forminp-textarea">
				<textarea name="wc_quote_email[quote_email_before_message]" style="width:400px; height: 75px;" id="wc_quote_email_before_message" placeholder="Message"><?php echo wp_kses_post($quote_email_before_message); ?></textarea>
				<p class="description"><?php echo esc_html__('Add Content Before the Message (html allowed)', 'wc-quote-lite'); ?></p>

			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="wc_quote_new_admin_email_msg">
					<?php echo esc_html__('Text After Quote', 'wc-quote-lite'); ?>
				</label>
			</th>
			<td class="forminp forminp-textarea">
				<textarea name="wc_quote_email[quote_email_after_message]" style="width:400px; height: 75px;" id="wc_quote_email_after_message" placeholder="Message"><?php echo wp_kses_post($quote_email_after_message); ?></textarea>
				<p class="description"><?php echo esc_html__('Add Content After the Message (html allowed)', 'wc-quote-lite'); ?></p>

			</td>
		</tr>
	</tbody>
</table>
