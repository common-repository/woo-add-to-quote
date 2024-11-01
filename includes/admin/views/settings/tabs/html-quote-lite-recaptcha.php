<div class="wc-quote-customize-tab">
<table class="form-table">
	<tbody>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<?php echo esc_html__('Enable Captcha', 'wc-quote'); ?>
			</th>
			<td class="forminp forminp-checkbox">
				<fieldset>
					<legend class="screen-reader-text">
						<span><?php echo esc_html__('Enable Captcha feature', 'wc-quote'); ?></span>
					</legend>
					<label for="wc_quote_enable_captcha">
						<input id="wc_quote_enable_captcha" type="checkbox" class="wc-quote-input-toggle" disabled>
					</label>
				</fieldset>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="wc_quote_captcha_site_key">
					<?php echo esc_html__('Site Key', 'wc-quote'); ?>
				</label>
			</th>
			<td class="forminp forminp-email">
				<input id="wc_quote_captcha_site_key" type="text">
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="wc_quote_captcha_secret_key">
					<?php echo esc_html__('Secret Key', 'wc-quote'); ?>
				</label>
			</th>
			<td class="forminp forminp-email">
				<input id="wc_quote_captcha_secret_key" type="text">
			</td>
		</tr>
	</tbody>
</table>
</div>