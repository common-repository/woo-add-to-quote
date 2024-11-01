<div class="wc-quote-customize-tab">
<table class="form-table">
	<tbody>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<?php echo esc_html__('Enable PDF', 'wc-quote'); ?>
			</th>
			<td class="forminp forminp-checkbox">
				<fieldset>
					<legend class="screen-reader-text">
						<span><?php echo esc_html__('Enable PDF feature', 'wc-quote'); ?></span>
					</legend>
					<label for="wc_quote_enable_pdf">
						<input id="wc_quote_enable_pdf" type="checkbox" class="wc-quote-input-toggle" disabled>
					</label>
					<p class="description"><?php echo esc_html__('Enable PDF feature', 'wc-quote'); ?></p>
				</fieldset>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<?php echo esc_html__('Enable PDF Download', 'wc-quote'); ?>
			</th>
			<td class="forminp forminp-checkbox">
				<fieldset>
					<legend class="screen-reader-text">
						<span><?php echo esc_html__('Enable this option to allow users to download quote PDF from my account', 'wc-quote'); ?></span>
					</legend>
					<label for="wc_quote_enable_pdf_download">
						<input id="wc_quote_enable_pdf_download" type="checkbox" class="wc-quote-input-toggle" disabled>
					</label>
					<p class="description"><?php echo esc_html__('Enable this option to allow users to download quote PDF from my account', 'wc-quote'); ?></p>
				</fieldset>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<?php echo esc_html__('Attach PDF in Email', 'wc-quote'); ?>
			</th>
			<td class="forminp forminp-checkbox">
				<fieldset>
					<legend class="screen-reader-text">
						<span><?php echo esc_html__('Enable this option to attach PDF in the quote email', 'wc-quote'); ?></span>
					</legend>
					<label for="wc_quote_enable_pdf_attach">
						<input id="wc_quote_enable_pdf_attach" type="checkbox" class="wc-quote-input-toggle" disabled>
					</label>
					<p class="description"><?php echo esc_html__('Enable this option to attach PDF in the quote email', 'wc-quote'); ?></p>
				</fieldset>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="wc_settings_pdf_logo_email">
					<?php echo esc_html__('Upload Logo for PDF', 'wc-quote'); ?>
				</label>
			</th>
			<td class="forminp forminp-pdf_upload">
				<fieldset>
					<legend class="screen-reader-text">
						<span><?php echo esc_html__('Upload logo to add in PDF Head', 'wc-quote'); ?></span>
					</legend>
					<label for="wc_quote_pdf_logo">
						<img src="https://woocommerce.com/wp-content/uploads/2021/04/Quote-for-WooCommerce-2.png" width="60px" height="60px" id="wc_quote_pdf_logo" disabled>
					</label>
					<p class="description"><?php echo esc_html__('Upload logo to add in PDF Head', 'wc-quote'); ?></p>
				</fieldset>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<?php echo esc_html__('Show Link Accept & Checkout', 'wc-quote'); ?>
			</th>
			<td class="forminp forminp-checkbox">
				<fieldset>
					<legend class="screen-reader-text">
						<span><?php echo esc_html__('Enable to display accept and checkout link in pdf', 'wc-quote'); ?></span>
					</legend>
					<label for="wc_quote_pdf_show_link">
						<input id="wc_quote_pdf_show_link" type="checkbox" class="wc-quote-input-toggle" disabled>
					</label>
					<p class="description"><?php echo esc_html__('Enable this option show Accept & Checkout Link in PDF', 'wc-quote'); ?></p>
				</fieldset>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="wc_quote_pdf_template"><?php echo esc_html__('PDF Template', 'wc-quote'); ?></label>
			</th>
			<td class="forminp forminp-radio">
				<fieldset>
					<ul>
						<li>
							<label><input class="wc_quote_default_pdf" type="radio" disabled> 
								<?php echo esc_html__('Default Template', 'wc-quote'); ?>
							</label>
						</li>
						<li>
							<label><input class="wc_quote_custom_pdf" type="radio" disabled>
								<?php echo esc_html__('Custom Template', 'wc-quote'); ?>
							</label>
							<p class="description"><?php echo esc_html__('Select the Default Template or the Custom Template', 'wc-quote'); ?></p>
						</li>
					</ul>
				</fieldset>
			</td>
		</tr>
	</tbody>
</table>
</div>