<?php

$builder_config = json_decode(str_replace('"false"', 'false', wp_unslash(get_option('wc_quote_form_builder'))), true);
$builder_config = array();
if(isset($builder_config['components'])):

foreach ( $builder_config['components'] as $key => $field ) {
	$wc_quote_field_name  = $field['key'];
	$wc_quote_field_type  = $field['type'];
	if ('file' === $wc_quote_field_type  ) :
		echo '<input type="hidden" name="data[' . esc_attr($wc_quote_field_name) . ']">';
	endif;
}
endif;
?>
<div class="quote_fields_start col2-set" id="customer_details">
	<div class="col-12">
		<div class="woocommerce-billing-fields" id="quote-form-fields"></div>
		<?php 
		if ('1' === $enable_captcha ) {
			if (! empty($captcha_site_key) ) {
				?>
		<div class="g-recaptcha" data-sitekey="<?php echo esc_attr($captcha_site_key); ?>"></div>
				<?php 
			}
		}
		?>
	</div>
</div>
