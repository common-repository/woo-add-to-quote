<?php
/**
 * Admin View: Settings
 */

if (! defined('ABSPATH') ) {
	exit;
}
	/**
	 * Filter wc_quote_tabs_array
	 * 
	 * @since 1.0
	**/
	
	$settings_tabs = apply_filters('wc_quote_tabs_array', $this->tabs);
	
	$tab_exists        = isset($settings_tabs[ $this->current_tab ]) && ( has_action('wc_quote_sections_' . $this->current_tab) || has_action('wc_quote_settings_' . $this->current_tab) );
	$current_tab_label = isset($settings_tabs[ $this->current_tab ]) ? $settings_tabs[ $this->current_tab ] : '';
	
if (! $tab_exists ) {
		wp_safe_redirect(admin_url('admin.php?page=wc-quote-lite-settings'));
	exit;
}
?>
<div class="wrap wc-quote">
	<h2 class="main-heading"><?php echo esc_html__($current_tab_label, 'wc-quote-lite'); ?> <span class="tagline"> <?php echo esc_html__('Quote For WooCommerce', 'wc-quote-lite'); ?> </span> </h2>
	<?php 
	/**
	 * Action wc_quote_before_settings_
	 * 
	 * @since 1.0
	 **/
	do_action('wc_quote_before_settings_' . $this->current_tab);
	/**
	 * Filter wc_quote_settings_form_method_tab_$this->current_tab
	 * 
	 * @since 1.0
	 **/
	$form_method = apply_filters('wc_quote_settings_form_method_tab_' . $this->current_tab, 'post');
	?>
	<form method="<?php echo esc_attr($form_method); ?>" id="mainform" action="options.php" enctype="multipart/form-data">
		<?php settings_fields('wc-quote-lite-settings-' . $this->current_tab); ?>
		<?php do_settings_sections('wc-quote-lite-settings-' . $this->current_tab); ?> 
		<nav class="nav-tab-wrapper woo-nav-tab-wrapper">
			<?php

			foreach ( $settings_tabs as $slug => $label ) {
				// echo  esc_attr($this->current_tab);
				echo '<a href="' . esc_html(admin_url('admin.php?page=wc-quote-lite-settings&tab=' . esc_attr($slug))) . '" class="nav-tab ' . ( $this->current_tab === $slug ? 'nav-tab-active' : '' ) . '">' . esc_html($label) . '</a>';
					
			}
			/**
			* Action wc_quote_settings_tabs
			* 
			* @since 1.0
			**/
			do_action('wc_quote_settings_tabs');

			?>
		</nav>
		<h1 class="screen-reader-text"><?php echo esc_html($current_tab_label); ?></h1>
		<?php
		/**
		 * Action wc_quote_sections_$this->current_tab
		 * 
		 * @since 1.0
		 **/

		do_action('wc_quote_sections_' . $this->current_tab);
		settings_errors();
		/**
		* Action wc_quote_settings_$this->current_tab
		* 
		* @since 1.0
		**/
		do_action('wc_quote_settings_' . $this->current_tab);
		?>

		<div class="quote-sidebar">
		<div class="header">
			<svg style="margin: 20px auto;" xmlns="http://www.w3.org/2000/svg" width="166" height="38" viewBox="0 0 166 38" fill="none">
<g clip-path="url(#clip0_7_579)">
<g clip-path="url(#clip1_7_579)">
<path fill-rule="evenodd" clip-rule="evenodd" d="M26.8318 4.87505C26.845 4.94149 26.8582 5.00793 26.8582 5.06108V5.59259V13.8311V15.1886C26.6046 15.1719 26.3487 15.1635 26.0908 15.1635C19.7337 15.1635 14.5802 20.317 14.5802 26.6741C14.5802 29.622 15.6884 32.3111 17.5108 34.3475H12.4529H5.01283H4.53793C4.45078 34.3475 4.37963 34.3314 4.31566 34.317C4.26344 34.3051 4.216 34.2944 4.16856 34.2944H4.01026C3.94085 34.2711 3.88159 34.258 3.82802 34.2462C3.75942 34.231 3.70016 34.2179 3.6409 34.1881H3.58813H3.53536C3.50898 34.1881 3.4826 34.1748 3.45621 34.1615C3.42983 34.1482 3.40345 34.1349 3.37706 34.1349C3.3243 34.1084 3.28472 34.0951 3.24515 34.0818C3.20557 34.0685 3.166 34.0552 3.11323 34.0286H3.06046C3.0077 34.0021 2.94174 33.9755 2.87578 33.9489C2.80982 33.9223 2.74386 33.8958 2.6911 33.8692H2.63833C2.61195 33.8426 2.58556 33.8293 2.55918 33.816C2.5328 33.8027 2.50641 33.7895 2.48003 33.7629C2.45365 33.7363 2.42726 33.723 2.40088 33.7097C2.3745 33.6964 2.34811 33.6832 2.32173 33.6566C2.32173 33.6034 2.26896 33.6034 2.26896 33.6034C2.22655 33.5749 2.18792 33.5503 2.15206 33.5274C2.05411 33.4648 1.97685 33.4155 1.8996 33.3377H1.84683C1.583 33.1251 1.37193 32.9125 1.05533 32.5936V32.5404C1.00257 32.4872 0.962991 32.4208 0.923417 32.3544C0.883841 32.2879 0.844266 32.2215 0.791499 32.1683L0.738733 32.1152C0.70561 32.0151 0.651696 31.9359 0.603093 31.8646C0.574268 31.8223 0.54731 31.7827 0.527666 31.7431C0.4749 31.7431 0.4749 31.69 0.4749 31.69V31.6368L0.3166 31.3179V31.2648C0.290216 31.185 0.263833 31.1186 0.23745 31.0522C0.211066 30.9857 0.184683 30.9193 0.1583 30.8396V30.7864C0.147287 30.7531 0.136273 30.7222 0.12574 30.6925C0.0858053 30.5803 0.0527666 30.4874 0.0527666 30.3612V30.2549C0.0527666 30.2017 0.039575 30.1353 0.0263833 30.0689C0.0131917 30.0024 0 29.936 0 29.8828V29.8297V29.4045V5.59259C0 4.26381 0.527666 2.93502 1.47747 1.97829C2.42726 1.02156 3.69366 0.49005 5.01283 0.596353H21.9509H22.4786C22.5657 0.596353 22.6369 0.612461 22.7008 0.626942C22.7531 0.638765 22.8005 0.649504 22.8479 0.649504H22.9535C23.0062 0.67608 23.0722 0.689368 23.1382 0.702656C23.2041 0.715944 23.2701 0.729231 23.3228 0.755807H23.3756C23.5339 0.808959 23.6922 0.86211 23.7977 0.915262H23.8505C23.8968 0.938566 23.9431 0.951652 23.9849 0.963481C24.0384 0.978631 24.0847 0.991717 24.1143 1.02156C24.1143 1.02156 24.1671 1.07472 24.2199 1.07472C24.2199 1.12787 24.2726 1.12787 24.2726 1.12787C24.299 1.15444 24.3254 1.16773 24.3518 1.18102C24.3782 1.19431 24.4046 1.20759 24.4309 1.23417C24.4573 1.26075 24.4837 1.27403 24.5101 1.28732C24.5365 1.30061 24.5629 1.3139 24.5892 1.34047C24.5892 1.39363 24.642 1.39363 24.642 1.39363C24.6844 1.42211 24.7231 1.44678 24.7589 1.46967C24.8569 1.53223 24.9341 1.58156 25.0114 1.65938H25.0641C25.2224 1.76569 25.328 1.87199 25.4335 1.97829C25.4863 2.03144 25.539 2.09789 25.5918 2.16433C25.6446 2.23076 25.6973 2.2972 25.7501 2.35035C25.8556 2.45665 25.9612 2.61611 26.0667 2.77556C26.1195 2.77556 26.1195 2.82872 26.1195 2.82872C26.225 2.93502 26.2778 3.04132 26.3305 3.14762C26.3507 3.18823 26.3632 3.22108 26.3738 3.24914C26.391 3.29453 26.4035 3.32738 26.4361 3.36023C26.4625 3.41338 26.4756 3.45324 26.4888 3.49311C26.502 3.53297 26.5152 3.57283 26.5416 3.62599V3.67914C26.568 3.75887 26.5944 3.82531 26.6208 3.89175C26.6471 3.95818 26.6735 4.02462 26.6999 4.10435V4.1575C26.7263 4.21065 26.7395 4.2771 26.7527 4.34354C26.7659 4.40997 26.7791 4.47641 26.8054 4.52956V4.58271V4.68902C26.8054 4.74217 26.8186 4.80861 26.8318 4.87505ZM16.7798 7.29344H6.70136C5.80433 7.29344 5.01283 8.03756 5.01283 8.99429C5.01283 9.95101 5.75156 10.6951 6.70136 10.6951H16.7798C17.7296 10.6951 18.4683 9.95101 18.4683 8.99429C18.4683 8.03756 17.7296 7.29344 16.7798 7.29344ZM13.9832 14.0968H6.70136C5.80433 14.0968 5.01283 14.841 5.01283 15.7977C5.01283 16.7544 5.75156 17.4985 6.70136 17.4985H13.9832C14.933 17.4985 15.6717 16.7544 15.6717 15.7977C15.6717 14.841 14.933 14.0968 13.9832 14.0968ZM6.75413 24.3019H10.395C11.2921 24.3019 12.0308 23.5578 12.0836 22.6011C12.0836 21.6443 11.3448 20.9002 10.395 20.9002H6.75413C5.80433 20.9002 5.0656 21.6443 5.0656 22.6011C5.0656 23.5578 5.80433 24.3019 6.75413 24.3019Z" fill="#383838"/>
<path d="M26.8582 37.4174C32.3677 37.4174 36.8341 32.951 36.8341 27.4415C36.8341 21.932 32.3677 17.4656 26.8582 17.4656C21.3486 17.4656 16.8823 21.932 16.8823 27.4415C16.8823 32.951 21.3486 37.4174 26.8582 37.4174Z" fill="#FDB642"/>
<path d="M28.5883 23.511L28.6024 23.5192L28.6169 23.5267C28.8208 23.6319 29.0598 23.7587 29.3033 23.8879L29.3057 23.8892C29.5394 24.0132 29.7773 24.1394 29.9842 24.2464C30.0262 24.2733 30.0349 24.2979 30.0362 24.3026C30.0372 24.306 30.0378 24.3101 30.0369 24.3166C30.036 24.3228 30.0329 24.3374 30.0199 24.3601C29.3986 25.3536 27.8393 27.8064 26.8905 29.299C26.5638 29.8128 26.3095 30.2129 26.1907 30.4004L26.1783 30.4201L26.1778 30.4211C26.1756 30.4235 26.1728 30.4265 26.1695 30.4301C26.1548 30.4456 26.1374 30.4627 26.1134 30.4853C26.0629 30.5168 26.0057 30.5527 25.9429 30.5921C25.6272 30.7903 25.1706 31.0769 24.7101 31.34L24.6759 31.3596L24.6451 31.3825C24.6423 31.3837 24.633 31.3872 24.6135 31.3879C24.5814 31.3891 24.534 31.3818 24.4816 31.3574C24.4698 31.3519 24.4648 31.3477 24.4634 31.3464C24.4621 31.3452 24.4611 31.3441 24.4598 31.3419C24.4579 31.3386 24.4491 31.3218 24.4469 31.2844C24.4909 30.6712 24.537 29.9879 24.5388 29.6157C24.5417 29.6076 24.5461 29.5958 24.5535 29.5764C24.5549 29.5729 24.5564 29.5691 24.558 29.5649C25.1885 28.6812 27.628 24.8745 28.4081 23.5573C28.4312 23.5279 28.4631 23.5078 28.4965 23.4993C28.5306 23.4907 28.5608 23.4951 28.5883 23.511ZM24.646 31.382C24.646 31.382 24.6458 31.3821 24.6453 31.3824L24.646 31.382Z" fill="#383838" stroke="#383838" stroke-width="1.3155"/>
</g>
<path d="M58.0405 12.3102C58.0405 14.2572 57.409 16.0726 56.3303 17.5986L58.0142 19.0719L56.0409 21.3609L54.1992 19.756C52.5943 20.9662 50.5947 21.6766 48.4373 21.6766C43.2542 21.6766 38.8341 17.3881 38.8341 12.3102C38.8341 7.23239 43.2542 2.97015 48.4373 2.97015C53.673 2.97015 58.0405 7.23239 58.0405 12.3102ZM48.4373 18.1511C49.4897 18.1511 50.4895 17.8353 51.3314 17.2828L49.2266 15.4411L51.1998 13.1522L53.3836 15.0465C53.8308 14.2309 54.0676 13.2837 54.0676 12.3102C54.0676 9.17933 51.4893 6.49571 48.4373 6.49571C45.4116 6.49571 42.8332 9.17933 42.8332 12.3102C42.8332 15.4674 45.4116 18.1511 48.4373 18.1511Z" fill="#383838"/>
<path d="M68.7624 7.75859H72.6037V21.3609H68.7624V19.6507C67.9468 20.8873 66.4208 21.6766 64.6581 21.6766C61.7113 21.6766 59.6065 19.4929 59.6065 16.4146V7.75859H63.3162V15.599C63.3162 17.2828 64.3687 18.4405 65.8946 18.4405C67.4996 18.4405 68.7361 17.2828 68.7361 15.8095L68.7624 15.8884V7.75859Z" fill="#383838"/>
<path d="M81.5296 21.6766C77.4779 21.6766 74.0839 18.4142 74.0839 14.5466C74.0839 10.679 77.4779 7.44287 81.5296 7.44287C85.555 7.44287 88.9227 10.679 88.9227 14.5466C88.9227 18.4142 85.555 21.6766 81.5296 21.6766ZM81.5296 18.4405C83.5555 18.4405 85.2656 16.6514 85.2656 14.5466C85.2656 12.4418 83.5555 10.679 81.5296 10.679C79.4774 10.679 77.7673 12.4418 77.7673 14.5466C77.7673 16.6514 79.4774 18.4405 81.5296 18.4405Z" fill="#383838"/>
<path d="M97.6258 17.9932L98.5467 20.4927C97.7574 21.203 96.5208 21.6766 95.4158 21.6766C93.0216 21.6766 91.3377 19.9665 91.3377 17.5196V10.8632H89.4697V7.75859H91.3377V4.02256H95.0211V7.75859H97.9679V10.8632H95.0211V16.9145C95.0211 17.809 95.5999 18.4405 96.3103 18.4405C96.8365 18.4405 97.3627 18.2563 97.6258 17.9932Z" fill="#383838"/>
<path d="M106.32 18.2826C107.398 18.2826 108.714 17.7827 109.451 17.0724L111.897 19.5718C110.687 20.8347 108.346 21.6766 106.346 21.6766C102.294 21.6766 99.137 18.7036 99.137 14.5203C99.137 10.4159 102.163 7.44287 106.135 7.44287C110.398 7.44287 112.897 10.679 112.897 15.8095H103.084C103.531 17.2828 104.715 18.2826 106.32 18.2826ZM106.293 10.8369C104.846 10.8369 103.636 11.6788 103.136 13.0469H109.214C108.845 11.7051 107.846 10.8369 106.293 10.8369Z" fill="#383838"/>
<path d="M134.763 6.78512H124.739V10.7053H133.816V14.2046H124.739V21.3609H120.845V3.28588H134.763V6.78512Z" fill="#383838"/>
<path d="M143.045 21.6766C138.993 21.6766 135.599 18.4142 135.599 14.5466C135.599 10.679 138.993 7.44287 143.045 7.44287C147.07 7.44287 150.438 10.679 150.438 14.5466C150.438 18.4142 147.07 21.6766 143.045 21.6766ZM143.045 18.4405C145.071 18.4405 146.781 16.6514 146.781 14.5466C146.781 12.4418 145.071 10.679 143.045 10.679C140.993 10.679 139.282 12.4418 139.282 14.5466C139.282 16.6514 140.993 18.4405 143.045 18.4405Z" fill="#383838"/>
<path d="M155.826 10.2844C156.721 8.52158 158.404 7.44287 160.483 7.44287V10.679C157.641 10.679 155.826 12.3102 155.826 14.8623V21.3609H151.985V7.75859H155.826V10.2844Z" fill="#383838"/>
<path d="M65.198 34.872L62.225 25.8345H63.6983L65.9215 32.7277L68.1842 25.8345H69.6576L71.9202 32.7277L74.1434 25.8345H75.6168L72.6438 34.872H71.3151L68.934 27.6762L66.5267 34.872H65.198Z" fill="#383838"/>
<path d="M79.4892 35.0299C77.5423 35.0299 75.8979 33.3986 75.8979 31.4648C75.8979 29.531 77.5423 27.913 79.4892 27.913C81.423 27.913 83.0542 29.531 83.0542 31.4648C83.0542 33.3986 81.423 35.0299 79.4892 35.0299ZM79.4892 33.8985C80.7126 33.8985 81.7519 32.7935 81.7519 31.4648C81.7519 30.1493 80.7126 29.0443 79.4892 29.0443C78.2395 29.0443 77.2002 30.1493 77.2002 31.4648C77.2002 32.7935 78.2395 33.8985 79.4892 33.8985Z" fill="#383838"/>
<path d="M87.801 35.0299C85.8541 35.0299 84.2097 33.3986 84.2097 31.4648C84.2097 29.531 85.8541 27.913 87.801 27.913C89.7348 27.913 91.3661 29.531 91.3661 31.4648C91.3661 33.3986 89.7348 35.0299 87.801 35.0299ZM87.801 33.8985C89.0245 33.8985 90.0637 32.7935 90.0637 31.4648C90.0637 30.1493 89.0245 29.0443 87.801 29.0443C86.5513 29.0443 85.5121 30.1493 85.5121 31.4648C85.5121 32.7935 86.5513 33.8985 87.801 33.8985Z" fill="#383838"/>
<path d="M97.2705 35.0299C94.7316 35.0299 92.5742 32.8856 92.5742 30.3467C92.5742 27.8077 94.7316 25.6766 97.2705 25.6766C98.5597 25.6766 99.7437 26.216 100.599 27.0974L99.6647 27.9919C99.0596 27.3342 98.2045 26.9 97.2705 26.9C95.4683 26.9 93.9423 28.4786 93.9423 30.3467C93.9423 32.2278 95.4683 33.8064 97.2705 33.8064C98.2045 33.8064 99.0596 33.3723 99.6647 32.7146L100.599 33.6091C99.7437 34.4773 98.5597 35.0299 97.2705 35.0299Z" fill="#383838"/>
<path d="M105.118 35.0299C103.171 35.0299 101.527 33.3986 101.527 31.4648C101.527 29.531 103.171 27.913 105.118 27.913C107.052 27.913 108.683 29.531 108.683 31.4648C108.683 33.3986 107.052 35.0299 105.118 35.0299ZM105.118 33.8985C106.342 33.8985 107.381 32.7935 107.381 31.4648C107.381 30.1493 106.342 29.0443 105.118 29.0443C103.869 29.0443 102.829 30.1493 102.829 31.4648C102.829 32.7935 103.869 33.8985 105.118 33.8985Z" fill="#383838"/>
<path d="M117.811 27.913C119.232 27.913 120.258 29.0048 120.258 30.544V34.872H118.982V30.7413C118.982 29.7415 118.39 29.0443 117.535 29.0443C116.561 29.0443 115.877 29.6889 115.877 30.6229V34.872H114.601V30.7413C114.601 29.7415 114.009 29.0443 113.154 29.0443C112.181 29.0443 111.496 29.6889 111.496 30.6229V34.872H110.194V28.0708H111.496V29.018C111.838 28.3471 112.549 27.913 113.417 27.913C114.404 27.913 115.206 28.4392 115.601 29.2811C115.917 28.4392 116.785 27.913 117.811 27.913Z" fill="#383838"/>
<path d="M129.733 27.913C131.153 27.913 132.179 29.0048 132.179 30.544V34.872H130.903V30.7413C130.903 29.7415 130.311 29.0443 129.456 29.0443C128.483 29.0443 127.799 29.6889 127.799 30.6229V34.872H126.523V30.7413C126.523 29.7415 125.931 29.0443 125.076 29.0443C124.102 29.0443 123.418 29.6889 123.418 30.6229V34.872H122.116V28.0708H123.418V29.018C123.76 28.3471 124.471 27.913 125.339 27.913C126.325 27.913 127.128 28.4392 127.523 29.2811C127.838 28.4392 128.707 27.913 129.733 27.913Z" fill="#383838"/>
<path d="M137.169 33.8722C137.879 33.8722 138.708 33.5565 139.115 33.1355L139.931 33.9775C139.326 34.622 138.142 35.0299 137.182 35.0299C135.287 35.0299 133.682 33.5433 133.682 31.4517C133.682 29.4127 135.195 27.913 137.076 27.913C139.155 27.913 140.352 29.4916 140.352 31.9253H134.998C135.169 33.0566 135.998 33.8722 137.169 33.8722ZM137.129 29.0706C136.077 29.0706 135.222 29.781 135.011 30.886H139.089C138.944 29.8336 138.3 29.0706 137.129 29.0706Z" fill="#383838"/>
<path d="M143.23 29.3732C143.664 28.4786 144.585 27.913 145.755 27.913V29.0443C144.348 29.0443 143.348 29.8205 143.23 31.0044V34.872H141.927V28.0708H143.23V29.3732Z" fill="#383838"/>
<path d="M150.236 35.0299C148.302 35.0299 146.658 33.3986 146.658 31.4648C146.658 29.531 148.302 27.913 150.249 27.913C151.222 27.913 152.104 28.3208 152.748 28.9654L151.867 29.781C151.459 29.3206 150.867 29.0443 150.236 29.0443C148.999 29.0443 147.973 30.1493 147.973 31.4648C147.973 32.7935 148.999 33.8985 150.249 33.8985C150.88 33.8985 151.486 33.6091 151.906 33.1355L152.788 33.938C152.13 34.6089 151.249 35.0299 150.236 35.0299Z" fill="#383838"/>
<path d="M157.299 33.8722C158.01 33.8722 158.838 33.5565 159.246 33.1355L160.062 33.9775C159.457 34.622 158.273 35.0299 157.312 35.0299C155.418 35.0299 153.813 33.5433 153.813 31.4517C153.813 29.4127 155.326 27.913 157.207 27.913C159.286 27.913 160.483 29.4916 160.483 31.9253H155.129C155.3 33.0566 156.129 33.8722 157.299 33.8722ZM157.26 29.0706C156.207 29.0706 155.352 29.781 155.142 30.886H159.22C159.075 29.8336 158.431 29.0706 157.26 29.0706Z" fill="#383838"/>
</g>
<defs>
<clipPath id="clip0_7_579">
<rect width="166" height="38" fill="white"/>
</clipPath>
<clipPath id="clip1_7_579">
<rect width="36.8341" height="36.8341" fill="white" transform="translate(0 0.582939)"/>
</clipPath>
</defs>
</svg>
			<div class="sidebar-guarantee">
				<span class="guarantee-item"><svg width="18px" height="18px" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M7.07362 8.125C8.08404 7.45317 8.75 6.30437 8.75 5C8.75 2.92893 7.07108 1.25 5 1.25C4.71358 1.25 4.43467 1.28211 4.16667 1.34293M7.07362 8.125V6.66667M7.07362 8.125H8.54167M2.91667 1.88148C1.91168 2.5542 1.25 3.69982 1.25 5C1.25 7.07108 2.92893 8.75 5 8.75C5.28642 8.75 5.56533 8.71787 5.83333 8.65708M2.91667 1.88148V3.33333M2.91667 1.88148H1.45833" stroke="black" stroke-linecap="round" stroke-linejoin="round"/>
</svg><?php esc_html_e('14 Day Money Back Guarantee', 'wc-quote-lite'); ?>
 </span>
				<span class="guarantee-item"><svg width="18px" height="18px" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M3.48989 1.25C3.40076 1.2833 3.3131 1.31963 3.22704 1.35886M8.63237 6.79213C8.67495 6.69996 8.7142 6.60592 8.74999 6.51021M7.70783 8.06863C7.77937 8.00183 7.84854 7.93254 7.91512 7.86083M6.36204 8.90513C6.44291 8.87463 6.52254 8.84167 6.60087 8.80629M5.06499 9.16413C4.96879 9.16746 4.87191 9.16746 4.77566 9.16413M3.24471 8.8085C3.32004 8.84238 3.39659 8.87408 3.47427 8.90346M1.94689 7.88367C2.00384 7.94404 2.06262 8.00267 2.12312 8.0595M1.09695 6.52688C1.12815 6.60925 1.16194 6.69038 1.19821 6.77013M0.835399 5.21054C0.832695 5.12383 0.832703 5.03658 0.835399 4.94975M1.09393 3.64047C1.12458 3.55902 1.15776 3.4788 1.19337 3.39992M1.94 2.28301C2.00028 2.21881 2.0626 2.15655 2.12687 2.09635" stroke="black" stroke-linecap="round" stroke-linejoin="round"/>
<path d="M5.625 5C5.625 5.34517 5.34517 5.625 5 5.625C4.65483 5.625 4.375 5.34517 4.375 5C4.375 4.65483 4.65483 4.375 5 4.375M5.625 5C5.625 4.65483 5.34517 4.375 5 4.375M5.625 5H6.66667M5 4.375V2.5" stroke="black" stroke-linecap="round"/>
<path d="M9.16667 4.99999C9.16667 2.69881 7.30117 0.833328 5 0.833328" stroke="black" stroke-linecap="round"/>
</svg>
<?php esc_html_e('Quick and Robust Support', 'wc-quote-lite'); ?>
</span>
			</div>
		</div>
		<ul class="features">
		<li><?php esc_html_e('Add-To-Quote Option On Cart, Shop, And Product Pages', 'wc-quote-lite'); ?></li>
		<li><?php esc_html_e('Convert Quotes Into Orders', 'wc-quote-lite'); ?></li>
		<li><?php esc_html_e('Send Accept And Checkout Link Through PDF', 'wc-quote-lite'); ?></li>
		<li><?php esc_html_e('Send Accept And Checkout Link Via Email', 'wc-quote-lite'); ?></li>
		<li><?php esc_html_e('Customers Can Offer Their Own Prices', 'wc-quote-lite'); ?></li>
		<li><?php esc_html_e('Guest Users Can Request Quotes', 'wc-quote-lite'); ?></li>
		<li><?php esc_html_e('Download Quotations (PDF) From My Account Page', 'wc-quote-lite'); ?></li>

		</ul>
			<p class='more'><?php esc_html_e('& Many More', 'wc-quote-lite'); ?></p>
		<a class="popup-btn" target="_blank" href="https://wpexperts.io/products/quote-for-woocommerce/?utm_source=banner&utm_medium=quote-for-woo-pro">
			<svg xmlns="http://www.w3.org/2000/svg" width="21" height="20" viewBox="0 0 21 20" fill="none">
<path d="M17.1667 10.8333V6.66666H13" stroke="black" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
<path d="M17.1666 6.66666L13 10.8333C12.2645 11.5688 11.8968 11.9365 11.4455 11.9772C11.3708 11.9839 11.2958 11.9839 11.2211 11.9772C10.7698 11.9365 10.4021 11.5688 9.66665 10.8333C8.93115 10.0978 8.56344 9.73016 8.11212 9.68949C8.03751 9.68274 7.96245 9.68274 7.88784 9.68949C7.43652 9.73016 7.06878 10.0978 6.33331 10.8333L3.83331 13.3333" stroke="black" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
</svg><?php esc_html_e('Upgrade to Premium', 'wc-quote-lite'); ?></a>
	</div>

		<p class="submit">
			<?php if (empty($GLOBALS['hide_save_button']) ) : ?>
				<button name="save" class="button-primary wc_quote-save-button" type="submit" value="<?php esc_attr_e('Save changes', 'wc-quote-lite'); ?>"><?php esc_html_e('Save changes', 'wc-quote-lite'); ?></button>
			<?php endif; ?>
		</p>
	</form>
	<?php 
	/**
	 * Action wc_quote_after_settings_$this->current_tab
	 * 
	 * @since 1.0
	 **/
	do_action('wc_quote_after_settings_' . $this->current_tab); 
	?>
</div>
