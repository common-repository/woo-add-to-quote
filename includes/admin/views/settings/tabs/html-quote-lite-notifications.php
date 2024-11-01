<div class="wc-quote-customize-tab">
<table class="form-table">
	<tbody>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="wc_quote_email_success_msg">
					<?php echo esc_html__('Message Email Quote on Success', 'wc-quote'); ?>
				</label>
			</th>
			<td class="forminp forminp-text">
				<input id="wc_quote_email_success_msg" type="text" placeholder="Email Success Message">
				<p class="description"><?php echo esc_html__('This Text will show as notice when user successfully email quotes', 'wc-quote'); ?></p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="wc_quote_email_error_msg">
					<?php echo esc_html__('Message Email Quote on Error', 'wc-quote'); ?>
				</label>
			</th>
			<td class="forminp forminp-text">
				<input id="wc_quote_email_error_msg" type="text" placeholder="Email Error Message">
				<p class="description"><?php echo esc_html__('This Text will show as notice when user get error on email quotes', 'wc-quote'); ?></p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="wc_quote_email_valid_msg">
					<?php echo esc_html__('Message Email Wrong Error', 'wc-quote'); ?>
				</label>
			</th>
			<td class="forminp forminp-text">
				<input id="wc_quote_email_valid_msg" type="text" placeholder="Email Validation Message">
				<p class="description"><?php echo esc_html__('This Text will show as notice when user input wrong email address', 'wc-quote'); ?></p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<?php echo esc_html__('Add To Quote Redirect', 'wc-quote'); ?>
			</th>
			<td class="forminp forminp-checkbox">
				<fieldset>
					<legend class="screen-reader-text">
						<span><?php echo esc_html__('Redirect page on add to quote success', 'wc-quote'); ?></span>
					</legend>
					<label for="wc_quote_add_quote_redirect">
						<input id="wc_quote_add_quote_redirect" type="checkbox" disabled class="wc-quote-input-toggle">
						<p class="description"><?php echo esc_html__('Enable to redirect page on add to quote success', 'wc-quote'); ?></p>
					</label>
				</fieldset>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="wc_quote_add_redirect_url"><?php echo esc_html__('Add To Quote Redirect URL', 'wc-quote'); ?> </label>
			</th>
			<td class="forminp forminp-url">‎
				<input id="wc_quote_add_redirect_url" type="url" placeholder="<?php echo esc_html__('https://www.example.com', 'wc-quote'); ?>">
				<p class="description"><?php echo esc_html__('Enter url to redirect after add to quote success', 'wc-quote'); ?></p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<?php echo esc_html__('Send Quote Redirect', 'wc-quote'); ?>
			</th>
			<td class="forminp forminp-checkbox">
				<fieldset>
					<legend class="screen-reader-text">
						<span><?php echo esc_html__('Redirect page on send quote success', 'wc-quote'); ?></span>
					</legend>
					<label for="wc_quote_send_quote_redirect">
						<input id="wc_quote_send_quote_redirect" type="checkbox" disabled class="wc-quote-input-toggle" >
						<p class="description"><?php echo esc_html__('Enable to redirect page on send quote success', 'wc-quote'); ?></p>
					</label>
				</fieldset>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="wc_quote_send_redirect_url"><?php echo esc_html__('Send Quote Redirect URL', 'wc-quote'); ?> </label>
			</th>
			<td class="forminp forminp-url">‎
				<input id="wc_quote_send_redirect_url" type="url" placeholder="<?php echo esc_html__('https://www.example.com', 'wc-quote'); ?>">
				<p class="description"><?php echo esc_html__('Enter url to redirect after sending quote to admin success', 'wc-quote'); ?></p>
			</td>
		</tr>
	</tbody>
</table>
</div>