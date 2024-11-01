jQuery(document).ready(function () {
	"use strict";
	var ajaxUrl  = wc_quote_vars.ajax_url;
	var nonce    = wc_quote_vars.nonce;
	var redirect = wc_quote_vars.redirect;
	var hide_cart_all = wc_quote_vars.hide_cart_all;
	var pageurl  = wc_quote_vars.redirect_url;
	var postid =  wc_quote_vars.post_id;
	var quote_page_url  = wc_quote_vars.quote_page_url;
	var global_hide_add_to_cart = wc_quote_vars.global_hide_add_to_cart;
	var quote_list_ids=[];

	jQuery(document).ready(function($) {
		if( global_hide_add_to_cart ){
			$('.wp-block-button__link').hide(); 

			setTimeout(()=>{
				$('.build-quote-button').hide();
			},10)
			
		}
	});
	

	jQuery('.widget_quote_list').each(function(){
		quote_list_ids.push( jQuery(this).attr('id'));
	});

	var quote_icon_ids=[];
	jQuery('.widget_quote_icon').each(function(){
		quote_icon_ids.push( jQuery(this).attr('id'));
	});

	jQuery('div.menu ul').append( '<li>' + jQuery('li.wc-quote-li a:eq(1)').text() + '</li>' );

	jQuery(document).on( 'change', '.variation_id', function (e) {

		if( !jQuery(this).val() ){
			return;
		}

		jQuery( '.wc_quote_single_page' ).addClass( 'disabled wc-variation-is-unavailable' );
		
		var variation_id = parseInt( jQuery(this).val() );
		var current_button = jQuery(this);

		jQuery.ajax({
			url: ajaxUrl,
			type: 'POST',
			data: {
				action       : 'wc_check_availability_of_quote',
				nonce        : nonce,
				variation_id : variation_id,
			},
			success: function ( response ) {
				if( false === response.success ){
					jQuery( '.wc_quote_single_page' ).addClass( 'disabled wc-variation-is-unavailable' );
				} else if ( true === response.success ) {
					jQuery( '.wc_quote_single_page' ).removeClass( 'disabled' );
				}
				
			},
			error: function (response) {
				current_button.removeClass('loading');
				current_button.css('opacity', '1' );
				current_button.css('border', '1px solid red' );
			}
		});

	});


	jQuery('#_email_quote_trigger').click(
		function(e) {
			e.preventDefault();
			jQuery('#_send_quote_email_')[0].click();
		}
	);
	
	jQuery(document).ready(function($) {
		$('.wc-quote-lite .quantity input[type="number"]').attr('disabled', 'disabled');
	});


	jQuery('div.wc_quote_fields input:not([type="submit"]), div.wc_quote_fields textarea, div.wc_quote_fields select').each( function(){

		var current_button = jQuery(this);

		if( !current_button.val() || current_button.val().length < 1 ){

			if( 'required' === current_button.attr('required')  ) {
				current_button.css('border-left', '2px solid #ca1010');
			}
		} else {
			current_button.css('border-left', '2px solid green');
		}
	});

	jQuery( document ).on( 'focusout', 'div.wc_quote_fields input, div.wc_quote_fields textarea, div.wc_quote_fields select', function(ev) {

		var current_button = jQuery(this);

		if( !current_button.val() || current_button.val().length < 1 ){

			if( 'required' === current_button.attr('required')  ) {
				current_button.css('border-left', '2px solid #ca1010');
			}

			return;
		}
		else {
			current_button.css('border-left', '2px solid green');
		}

	});


		jQuery('#wc_quote_empty_quote_btn').on('click', function() {
			jQuery.ajax({
				type: 'POST',
				url: ajaxUrl, // Assuming ajaxurl is defined in your script
				data: {
					action: 'wc_empty_quote',
					nonce    : nonce // Action to be performed in your WordPress backend
				},
				success: function(response) {
					location.reload(); 
				},
				error: function(xhr, status, error) {
					console.error('Error: ' + xhr.responseText);
				}
			});
		});

		jQuery(".send_quote_button").click(function(e) {
			e.preventDefault(); // Prevent default link behavior
			var toSendEmail = jQuery("#_to_send_email").val();

			jQuery.ajax({
				type: "POST",
				url: ajaxUrl,
				data: {
					action: 'wc_send_quote',
					_to_send_email: toSendEmail,
				},
				success: function(response) {
					// Handle success response
					console.log('Email addresses stored successfully.');
				},
				error: function(xhr, status, error) {
					// Handle error
					console.error(xhr.responseText);
				}
			});
		});

	jQuery(window).on('load', function(){
			if( 1 == hide_cart_all ) {
	   jQuery('.wp-block-button.wc-block-grid__product-add-to-cart a.ajax_add_to_cart').hide();
	}

	});
	jQuery(document).on('click', 'button.wc_quote_update_quote_btn', function (e) {
		e.preventDefault();

		if ( jQuery(this).hasClass('loading') ) {
			return;
		}

		jQuery(this).addClass('loading');
		var current_button = jQuery(this);

		jQuery.ajax({
			url: ajaxUrl,
			type: 'POST',
			dataType: 'JSON',
			data: {
				action   : 'wcquote_update_quote_items',
				nonce    : nonce,
				form_data : jQuery('form.wc-quote-form').serialize(),
				quote_id : current_button.data('quote_id'),
				quote_list_ids: quote_list_ids,
				quote_icon_ids: quote_icon_ids,
				post_id: postid
			},
			
			success: function (response) {
				
				current_button.removeClass('loading');
				current_button.addClass('disabled');

				if( response['quote_empty'] ){
					location.reload();
				}

				
				jQuery('table.wc-quote-form__contents').replaceWith( response['quote-table'] );
				jQuery('table.table_quote_totals').replaceWith( response['quote-totals'] );

				jQuery.each(response['quote-widget-list'], function(k,v){
					jQuery('#'+ k + ' table.wc-quote-widget__contents').replaceWith( v );
				})
				
				jQuery.each(response['quote-widget-icon'], function(k,v){
					jQuery('#'+ k + ' div.wc-quote-icon').replaceWith( v );
				})

				jQuery('div.woocommerce-notices-wrapper').html(response['message'] );
				jQuery('body').animate({
					scrollTop: jQuery('div.woocommerce-notices-wrapper').offset().top,
				}, 500
				);
				
			},

			error: function (response) {
				current_button.removeClass('loading');	
				current_button.addClass('disabled');
			}
		});
	});

	jQuery(document).on('click', 'a.wc_quote_add_to_cart', function () {

		var redirect_url = jQuery(this).attr('data-url');
		if( jQuery(this).hasClass('loading') ){
			return;
		}
		jQuery(this).addClass('loading');
		var current_button = jQuery(this);
		jQuery.ajax({
			url: ajaxUrl,
			type: 'POST',
			data: {
				action: 'wcquote_add_to_cart',
				nonce: nonce
			},
			success: function (response) {

				if ( 'success' == jQuery.trim(response) ) {

					window.location.href = redirect_url;

				} else if( 'failed' == jQuery.trim(response) ){

					location.reload();
					
				} else {

					current_button.removeClass('loading');

				}	
			}
		});
	});

	jQuery(document).on('click', '.build-quote-button', function () {

		var redirect_url = jQuery(this).attr('data-url');
		if( jQuery(this).hasClass('loading') ){
			return;
		}
		jQuery(this).addClass('loading');
		var current_button = jQuery(this);
		jQuery.ajax({
			url: ajaxUrl,
			type: 'POST',
			data: {
				action: 'wcquote_build_quote',
				nonce: nonce
			},
			success: function (response) {

				if ( 'success' == jQuery.trim(response) ) {

					window.location.href = redirect_url;

				} else if( 'failed' == jQuery.trim(response) ){

					location.reload();
					
				} else {

					current_button.removeClass('loading');

				}	
			}
		});
	});

	jQuery(document).on('click', '.quote-btn', function (e) {
		e.preventDefault();
	// jQuery(this).attr('disabled', true);

		if( jQuery(this).hasClass('loading') ){
			return false;
		}

		jQuery(this).parent().find('a.added_to_quote').remove();

		if (jQuery(this).is('.product_type_simple') || jQuery(this).is('.product_type_variable') || jQuery(this).is('.product_type_subscription')) {

			var productId = jQuery(this).attr('data-product_id');
			var quantity  = 1;

			jQuery(this).addClass('loading');
			var current_button = jQuery(this);
			jQuery.ajax({
				url: ajaxUrl,
				type: 'POST',
				data: {
					action: 'wcquote_add_to_quote',
					product_id: productId,
					quote_list_ids: quote_list_ids,
					quote_icon_ids: quote_icon_ids,
					post_id: postid,
					quantity: quantity,
					nonce: nonce
				},
				success: function (response) {
					
					if ( 'success' == jQuery.trim(response) ) {
						if ( "1" == redirect ) {

							window.location.href = pageurl;
						} else {

							location.reload();
						}
						
					} else if( 'failed' == jQuery.trim(response) ){

						location.reload();
						
					} else {
						jQuery('.hide_on_quote').remove();
						current_button.removeClass('loading');
						jQuery('table.wc-quote-form__contents').replaceWith( response['quote-table'] );
						jQuery('table.table_quote_totals').replaceWith( response['quote-totals'] );
						
						jQuery.each(response['quote-widget-list'], function(k,v){
							if (jQuery('#'+ k + ' table.wc-quote-widget__contents').length > 0){
								jQuery('#'+ k + ' table.wc-quote-widget__contents').replaceWith( v );
							}
							else{
								jQuery('#'+ k).append( v );
								
							}
							
						});
						
						jQuery.each(response['quote-widget-icon'], function(k,v){
							if (jQuery('#'+ k + ' div.wc-quote-icon').length > 0){
								jQuery('#'+ k + ' div.wc-quote-icon').replaceWith( v );
								
							}
							else{
								jQuery('#'+ k).append( v );
								
							}
							
						});
						
						if ( "1" == redirect ) {
							window.location.href = pageurl;
						}
					}	
					
				}
				
			});

		}
	});

	jQuery(document).on('click', '.wc_quote_single_page', function ($) {

		var current_button = jQuery(this);
		var action_name = '';
		var productId = jQuery(this).attr('data-product_id');
		

		if( current_button.hasClass('loading') ){
			return;
		}
		
		current_button.closest('form').find('a.added_to_quote').remove();
		
		if (current_button.is('.product_type_variable')) {
			if( current_button.hasClass('disabled') ){
				return;
			}
			action_name = 'wcquote_add_to_quote_variable';
		} else {
			action_name = 'wcquote_add_to_quote_single';
		}
		
		current_button.addClass('loading');
		
		jQuery.ajax({
			url: ajaxUrl,
			type: 'POST',
			data: {
				action: action_name,
				form_data : current_button.closest('form').serialize(),
				product_id: productId,
				quote_list_ids: quote_list_ids,
				quote_icon_ids: quote_icon_ids,
				post_id: postid,
				nonce: nonce,

			},
			success: function (response) {
			 			
				var product_name = jQuery('.product_title').text();
				jQuery('#primary').prepend(`<div class="woocommerce-message" role="alert"><a href="${quote_page_url}" class="button wc-forward">View Quote</a> ${product_name} has been added to your quote.</div>`);

				if ( 'success' == jQuery.trim(response) ) {
					if ( "1" == redirect ) {
						
						window.location.href = pageurl;
					} else {

						location.reload();
					}
	

				} else if( 'failed' == jQuery.trim(response) ){

					location.reload();
					
				} else {
					jQuery('.hide_on_quote').remove();
					current_button.removeClass('loading');
					
					jQuery.each(response['quote-widget-list'], function(k,v){
						if (jQuery('#'+ k + ' table.wc-quote-widget__contents').length > 0){
							jQuery('#'+ k + ' table.wc-quote-widget__contents').replaceWith( v );
						}
						else{
							jQuery('#'+ k).append( v );							
						}				
					});
					
					jQuery.each(response['quote-widget-icon'], function(k,v){
						if (jQuery('#'+ k + ' div.wc-quote-icon').length > 0){
							jQuery('#'+ k + ' div.wc-quote-icon').replaceWith( v );
							
						}
						else{
							jQuery('#'+ k).append( v );
							
						}
						
					});

				}

			}

		});
	});

	jQuery(document).on('click', '.wc-quote-remove', function (event) {
		
		"use strict";
		event.preventDefault();
		var quoteKey = jQuery(this).data('cart_item_key');
		if( jQuery(this).closest('li.mini_quote_item').css('opacity') == 0.5 ){
			return;
		}
		
		jQuery(this).closest('li.mini_quote_item').css('opacity', '0.5' );

		jQuery.ajax({
			url: ajaxUrl,
			type: 'POST',
			data: {
				action: 'wcquote_remove_quote_item',
				quote_key: jQuery(this).data('cart_item_key'),
				quote_list_ids: quote_list_ids,
				quote_icon_ids: quote_icon_ids,
				post_id: postid,
				nonce: nonce
			},
			success: function (response) {
				
				if ( response['quote_empty'] ) {
					location.reload();
				}

				jQuery('table.wc-quote-form__contents').replaceWith( response['quote-table'] );		
				jQuery('table.table_quote_totals').replaceWith( response['quote-totals'] );
				jQuery.each(response['quote-widget-list'], function(k,v){
					jQuery('#'+ k + ' table.wc-quote-widget__contents').replaceWith( v );
				})
				
				jQuery.each(response['quote-widget-icon'], function(k,v){
					jQuery('#'+ k + ' div.wc-quote-icon').replaceWith( v );
				})

	
				jQuery('div.woocommerce-notices-wrapper').html(response['message'] );
				
			}
		});
	});


	jQuery(document).on('click', '.wc-quote-remove-item', function (event) {
		event.preventDefault();
		
		var quoteKey = jQuery(this).data('cart_item_key');

		if( jQuery(this).closest('tr').css('opacity') == 0.5 ){
			return;
		}

		jQuery(this).closest('tr').css('opacity', '0.5' );

		jQuery.ajax({
			url: ajaxUrl,
			type: 'POST',
			data: {
				action: 'wcquote_remove_quote_item',
				quote_key: jQuery(this).data('cart_item_key'),
				quote_list_ids: quote_list_ids,
				quote_icon_ids: quote_icon_ids,
				post_id: postid,
				nonce: nonce
			},
			success: function (response) {
				
				if( response['quote_empty'] ){
					location.reload();
				}

				
				jQuery('table.wc-quote-form__contents').replaceWith( response['quote-table'] );
				jQuery('table.table_quote_totals').replaceWith( response['quote-totals'] );
				
				jQuery.each(response['quote-widget-list'], function(k,v){
					jQuery('#'+ k + ' table.wc-quote-widget__contents').replaceWith( v );
				})
				
				jQuery.each(response['quote-widget-icon'], function(k,v){
					jQuery('#'+ k + ' div.wc-quote-icon').replaceWith( v );
				})

				jQuery('div.woocommerce-notices-wrapper').html(response['message'] );

			}
		});
	});


});


