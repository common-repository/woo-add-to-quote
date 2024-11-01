<?php  
$admin_email = isset($this->quote_settings['admin_email']) ? $this->quote_settings['admin_email'] : get_bloginfo('admin_email');
$quote_page = isset($this->quote_settings['quote_page']) ? $this->quote_settings['quote_page'] : '';
$enable_convert_cart = isset($this->quote_settings['enable_convert_cart']) ? $this->quote_settings['enable_convert_cart'] : '0';
$add_to_cart_on_detail_page = isset($this->quote_settings['add_to_cart_on_detail_page']) ? $this->quote_settings['add_to_cart_on_detail_page'] : '0';
$add_to_cart_hide_global = isset($this->quote_settings['add_to_cart_hide_global']) ? $this->quote_settings['add_to_cart_hide_global'] : '0';
$build_quote_text = isset($this->quote_settings['build_quote_text']) ? $this->quote_settings['build_quote_text'] : 'Build Quote';
$allow_guest = isset($this->quote_settings['allow_guest']) ? $this->quote_settings['allow_guest'] : '0';
$enable_empty_cart = isset($this->quote_settings['empty_quote_to_cart']) ? $this->quote_settings['empty_quote_to_cart'] : '0';
$empty_quote = isset($this->quote_settings['empty_quote']) ? $this->quote_settings['empty_quote'] : '0';
$empty_cart = isset($this->quote_settings['empty_cart']) ? $this->quote_settings['empty_cart'] : '0';
add_filter('wp_dropdown_pages', 'wc_quote_general_quote_page');

function wc_quote_general_quote_page( $output ) {
	return str_replace('<select ', '<select style="min-width:400px;" data-placeholder="' . esc_html__('Select a page&hellip;', 'wc-quote-lite') . '" ', $output);
}

?>
<h2><?php echo esc_html__('General', 'wc-quote-lite'); ?></h2>
<table class="form-table">
	<tbody>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="wc_quote_admin_email">
					<?php echo esc_html__('Admin Email', 'wc-quote-lite'); ?>
				</label>
			</th>
			<td class="forminp forminp-email">
				<input name="wc_quote_general[admin_email]" id="wc_quote_admin_email" type="email" placeholder="someone@example.com" value="<?php echo esc_attr($admin_email); ?>">
				<p class="description"><?php echo esc_html__('Leave this blank to use default admin email', 'wc-quote-lite'); ?></p>
			</td>
		</tr>
	</tbody>
</table>
<table class="form-table">
	<tbody>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="wc_quote_list_page">
					<?php echo esc_html__('Quote on Cart Page', 'wc-quote-lite'); ?>
				</label>
			</th>

			<td class="forminp forminp-text">    
				<?php
				wp_dropdown_pages(array(
					'name' => 'wc_quote_general[quote_page]',
					'id' => 'wc_quote_list_page',
					'class' => 'wc-enhanced-select-nostd',
					'echo' => true,
					'selected' => absint($quote_page),
					'post_status' => 'publish'
				));
				?>
				<p class="description"><?php echo esc_html__('whether to show built a quote option on cart page', 'wc-quote-lite'); ?></p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<?php echo esc_html__('Convert Quote to Cart', 'wc-quote-lite'); ?>
			</th>
			<td class="forminp forminp-checkbox">
				<fieldset>
					<legend class="screen-reader-text">
						<span><?php echo esc_html__('Enable to show "add to cart" option on quote page', 'wc-quote-lite'); ?></span>
					</legend>
					<label for="wc_quote_build_quote">
						<input name="wc_quote_general[enable_convert_cart]" id="wc_quote_convert_cart" type="checkbox" class="wc-quote-input-toggle" value="1" <?php echo checked(1, $enable_convert_cart, false); ?>>
						<p class="description"><?php echo esc_html__('Enable to show "add to cart" option on quote page', 'wc-quote-lite'); ?></p>
					</label>
				</fieldset>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<?php echo esc_html__('Hide Add to Cart Button on Product Detail Page', 'wc-quote-lite'); ?>
			</th>
			<td class="forminp forminp-checkbox">
				<fieldset>
					<legend class="screen-reader-text">
						<span><?php echo esc_html__('Enable to show build a quote option on cart page', 'wc-quote-lite'); ?></span>
					</legend>
					<label for="wc_quote_build_quote">
						<input name="wc_quote_general[add_to_cart_on_detail_page]" id="wc_quote_add_to_cart_on_detail_page" type="checkbox" class="wc-quote-input-toggle" value="1" <?php echo checked(1, $add_to_cart_on_detail_page, false); ?>>
						<p class="description"><?php echo esc_html__('whether to show/hide "add to cart" button on product detail page', 'wc-quote-lite'); ?></p>
					</label>
				</fieldset>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<?php echo esc_html__('Hide Add to Cart Button everywhere', 'wc-quote-lite'); ?>
			</th>
			<td class="forminp forminp-checkbox">
				<fieldset>
					<legend class="screen-reader-text">
						<span><?php echo esc_html__('whether to show/hide "add to cart" button on site', 'wc-quote-lite'); ?></span>
					</legend>
					<label for="wc_quote_empty_cart">
						<input name="wc_quote_general[add_to_cart_hide_global]" id="add_to_cart_hide_global" type="checkbox" class="wc-quote-input-toggle" value="1" <?php echo checked(1, $add_to_cart_hide_global, false); ?>>
						<p class="description"><?php echo esc_html__('whether to show/hide "add to cart" button on site', 'wc-quote-lite'); ?></p>
					</label>
				</fieldset>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="wc_quote_build_quote_text">
					<?php echo esc_html__('Text for Build a Quote Button', 'wc-quote-lite'); ?>
				</label>
			</th>
			<td class="forminp forminp-text">
				<input name="wc_quote_general[build_quote_text]" id="wc_quote_build_quote_text" type="text" placeholder="Build a quote text" value="<?php echo esc_attr($build_quote_text); ?>">
				<p class="description"><?php echo esc_html__('This Text will show on build a quote button.', 'wc-quote-lite'); ?></p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<?php echo esc_html__( 'Allow Guest User', 'wc-quot-lite' ); ?>
			</th>
			<td class="forminp forminp-checkbox">
				<fieldset>
					<legend class="screen-reader-text">
						<span><?php echo esc_html__( 'Enable to allow guest user to get quote without registration', 'wc-quote-lite' ); ?></span>
					</legend>
					<label for="wc_quote_allow_guest">
						<input name="wc_quote_general[allow_guest]" id="wc_quote_allow_guest" type="checkbox" class="wc-quote-input-toggle" value="1" <?php echo checked( 1, $allow_guest, false ); ?>>
						<p class="description"><?php echo esc_html__( 'Enable to allow guest user to get quote without registration', 'wc-quote-lite' ); ?></p>
					</label>
				</fieldset>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<?php echo esc_html__('Empty Quote', 'wc-quote-lite'); ?>
			</th>
			<td class="forminp forminp-checkbox">
				<fieldset>
					<legend class="screen-reader-text">
						<span><?php echo esc_html__('Enable to show "add to cart" option on quote page', 'wc-quote-lite'); ?></span>
					</legend>
					<label for="wc_quote_build_quote">
						<input name="wc_quote_general[empty_quote_to_cart]" id="wc_quote_empty_quote_to_cart" type="checkbox" class="wc-quote-input-toggle" value="1" <?php echo checked(1, $enable_empty_cart, false); ?>>
						<p class="description"><?php echo esc_html__('if yes, it will empty quote after products moved to cart', 'wc-quote-lite'); ?></p>
					</label>
				</fieldset>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<?php echo esc_html__('Empty Quote After Email', 'wc-quote-lite'); ?>
			</th>
			<td class="forminp forminp-checkbox">
				<fieldset>
					<legend class="screen-reader-text">
						<span><?php echo esc_html__('Empty Quote After Email', 'wc-quote-lite'); ?></span>
					</legend>
					<label for="wc_quote_build_quote">
						<input name="wc_quote_general[empty_quote]" id="wc_quote_empty_quote" type="checkbox" class="wc-quote-input-toggle" value="1" <?php echo checked(1, $empty_quote, false); ?>>
						<p class="description"><?php echo esc_html__('if yes, it will empty quote after email', 'wc-quote-lite'); ?></p>
					</label>
				</fieldset>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<?php echo esc_html__('Empty cart', 'wc-quote-lite'); ?>
			</th>
			<td class="forminp forminp-checkbox">
				<fieldset>
					<legend class="screen-reader-text">
						<span><?php echo esc_html__('Empty Cart After Move to Quote', 'wc-quote-lite'); ?></span>
					</legend>
					<label for="wc_quote_build_quote">
						<input name="wc_quote_general[empty_cart]" id="wc_quote_empty_cart" type="checkbox" class="wc-quote-input-toggle" value="1" <?php echo checked(1, $empty_cart, false); ?>>
						<p class="description"><?php echo esc_html__('if yes, it will empty cart after products moved to quote', 'wc-quote-lite'); ?></p>
					</label>
				</fieldset>
			</td>
		</tr>
	</tbody>
</table>
