<div class="" id="waqt_quote_detail">
	<div class="quote_details">
		<?php
			global $post;
			$quote_id = !empty($post->ID) ? $post->ID : '';
		
		?>
		<h1><?php echo esc_html__('Quote no # ', 'wc-quote-lite') . esc_attr($quote_id); ?> </h1>
			<div class="_general_detail">
				<ul>
					<li><strong><?php esc_html_e('Quote Time: ', 'wc-quote-lite'); ?></strong><?php echo esc_html(get_post_time( 'g:i a', true )); ?></li>
					<li><strong><?php esc_html_e('Quote Date: ', 'wc-quote-lite'); ?></strong><?php echo get_the_date(); ?> </li>
					<li><strong><?php esc_html_e('Status: ', 'wc-quote-lite'); ?> </strong> <?php echo esc_html($post->post_content); ?></li>
				</ul>
			</div>
		<h1><?php esc_html_e('User Details:', 'wc-quote-lite'); ?></h1>
			<div class="_user_detail">
				<?php
					$post_meta = get_post_meta( get_the_ID(), 'quote_contents' );

				if (!empty($post_meta[0]['quote_user_id'])) {
					$user_detail = get_userdata($post_meta[0]['quote_user_id']);
					$display_name = $user_detail->display_name;
					$email = $user_detail->user_email;
				}
				?>
					<ul>
				<?php if (isset($display_name) && isset($email)) { ?>
						<li><strong><?php esc_html_e('User Name: ', 'wc-quote-lite'); ?></strong><?php echo esc_attr($display_name); ?></li>
						<li><strong><?php esc_html_e('User Email: ', 'wc-quote-lite'); ?></strong><?php echo esc_attr($email); ?></li>
						<li><strong><?php esc_html_e('User Status: ', 'wc-quote-lite'); ?></strong><?php esc_html_e('Registered', 'wc-quote-lite'); ?></li>
				<?php } else { ?>                      
						<li><strong><?php esc_html_e('User Status: ', 'wc-quote-lite'); ?></strong><?php esc_html_e('Guest', 'wc-quote-lite'); ?></li>
				<?php } ?>
					</ul>
			</div>
		<h1><?php esc_html_e('Sent To:', 'wc-quote-lite'); ?></h1>
		<div class="_user_detail">
			<?php
				$post_meta = get_post_meta( get_the_ID(), 'quote_contents' );
				$sent_to = isset($post_meta[0]['sender_email']) && ! empty($post_meta[0]['sender_email']) ? $post_meta[0]['sender_email'] : '';
			?>
				<ul>
				<li><?php echo esc_attr($sent_to); ?></li>
				</ul>
		</div>
	</div>
</div>

