var user_email  = wc_quote_fields_vars.user_email;
var invalid_email_msg = wc_quote_fields_vars.invalid_email_msg;
window.onload = function() {
   if ( window.history.replaceState ) {
      window.history.replaceState( null, null, window.location.href );
   }
   var components = wc_quote_fields_vars.form_fields;
   if ( components == null ) {
      var check_comp = '';
   } else {
      var check_comp = components.components;
   }
   if ( check_comp == '' ) {

      jQuery('a.wc_quote_checkout_place_quote').click( function (e) {
         var current_button = jQuery(this);
         if( current_button.css('opacity') == 0.2 ){
            return;
         }
         current_button.addClass('loading');
         current_button.css('opacity' ,'0.2' );
         jQuery('form.wc-quote-form').submit();
         return false;
      });

   } else {
      
      Formio.createForm(document.getElementById('quote-form-fields'), components, {
         language: 'en',
         i18n: {
            en: {
               invalid_email: invalid_email_msg
            }
         }
      }).then(function(form) {
         form.nosubmit = true;
         const email = form.getComponent('email');
         if ( !email.getValue() ) {
            email.setValue(user_email);
         }
         jQuery('a.wc_quote_checkout_place_quote').click( function (e) {
            e.preventDefault();
            form.emit('submitButton');
         });
         form.on('submit', function(submission) {
            console.log("Something changed on the form builder");
            var current_button = jQuery('a.wc_quote_checkout_place_quote');
            if( current_button.css('opacity') == 0.2 ){
               return;
            }
            current_button.addClass('loading');
            current_button.css('opacity' ,'0.2' );
            jQuery.each(components.components, function (i, item) {
               if (item.components) {
                  jQuery.each(item.components, function (k, v) {
                     if ("file" === v.type) {
                        var dataKey = v.key;
                        jQuery('input[name="data[' + dataKey + ']"]').val(JSON.stringify(form.submission.data[dataKey]));
                     }
                  });
               }
               if ("file" === item.type) {
                  var dataKey = item.key;
                  jQuery('input[name="data[' + dataKey + ']"]').val(JSON.stringify(form.submission.data[dataKey]));
               }
            });
            jQuery('form.wc-quote-form').submit();
            return false;
         });

         jQuery.each(components.components, function (i, item) {
          if (item.components) {
            jQuery.each(item.components, function (k, v) {
              if ("file" === v.type) {
                var dataKey = v.key;
                jQuery('input[name="data[' + dataKey + ']"]').val(
                  JSON.stringify(form.submission.data[dataKey])
                );
              }
            });
          }
          if ("file" === item.type) {
            var dataKey = item.key;
            jQuery('input[name="data[' + dataKey + ']"]').val(
              JSON.stringify(form.submission.data[dataKey])
            );
          }
        });
      });
   }
}