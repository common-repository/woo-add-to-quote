var ajax_url = wc_quote_vars.ajax_url;
var nonce = wc_quote_vars.nonce;
var components = wc_quote_vars.builder_config;
var mycompo = [
{
   key: 'btn-group',
   ignore: true
},
{
   key: 'api',
   ignore: true
},
{
   key: 'layout',
   ignore: true
},
{
   key: 'logic',
   ignore: true
},
{
   key: 'conditional',
   ignore: false,
   components:[
   {
      key: 'customConditionalPanel',
      ignore: true
   }
   ]
},
{
   key: 'display',
   ignore: false,
   components:[
   {
      key: 'showWordCount',
      ignore: true
   },
   {
      key: 'showCharCount',
      ignore: true
   },
   {
      key: 'tabindex',
      ignore: true
   },
   {
      key: 'inputMask',
      ignore: false
   },
   {
      key: 'allowMultipleMasks',
      ignore: true
   },
   {
      key: 'prefix',
      ignore: true
   },
   {
      key: 'suffix',
      ignore: true
   },
   {
      key: 'widget.type',
      ignore: true
   },
   {
      key: 'mask',
      ignore: false
   },
   {
      key: 'autofocus',
      ignore: true
   },
   {
      key: 'modalEdit',
      ignore: true
   },
   {
      key: 'tableView',
      ignore: true
   },
   {
      key: 'refreshOnChange',
      ignore: true
   },
   {
      key: 'displayInTimezone',
      ignore: true
   },
   {
      key: 'useLocaleSettings',
      ignore: true
   },
   {
      key: 'shortcutButtons',
      ignore: true
   },
   {
      key: 'shortcut',
      ignore: true
   },
   {
      key: 'inputType',
      ignore: true
   }
   ]
},
{
   key: 'validation',
   ignore: false,
   components:[
   {
      key: 'validate.pattern',
      ignore: true
   },
   {
      key: 'custom-validation-js',
      ignore: true
   },
   {
      key: 'json-validation-json',
      ignore: true
   },
   {
      key: 'errorLabel',
      ignore: true
   },
   {
      key: 'validate.maxLength',
      ignore: true
   },
   {
      key: 'validate.minLength',
      ignore: true
   },
   {
      key: 'validate.customMessage',
      ignore: true
   },
   {
      key: 'unique',
      ignore: true
   },
   {
      key: 'kickbox',
      ignore: true
   },
   {
      key: 'validate.onlyAvailableItems',
      ignore: true
   }
   ]
},
{
   key: 'data',
   ignore: false,
   components:[
   {
      key: 'persistent',
      ignore: true
   },
   {
      key: 'multiple',
      ignore: true
   },
   {
      key: 'inputFormat',
      ignore: true
   },
   {
      key: 'protected',
      ignore: true
   },
   {
      key: 'dbIndex',
      ignore: true
   },
   {
      key: 'case',
      ignore: true
   },
   {
      key: 'encrypted',
      ignore: true
   },
   {
      key: 'redrawOn',
      ignore: true
   },
   {
      key: 'clearOnHide',
      ignore: true
   },
   {
      key: 'customDefaultValuePanel',
      ignore: true
   },
   {
      key: 'calculateValuePanel',
      ignore: true
   },
   {
      key: 'calculateServer',
      ignore: true
   },
   {
      key: 'allowCalculateOverride',
      ignore: true
   },
   {
      key: 'defaultDate',
      ignore: true
   },
   {
      key: 'customOptions',
      ignore: true
   },
   {
      key: 'dataSrc',
      ignore: true
   },
   {
      key: 'dataType',
      ignore: true
   },
   {
      key: 'idPath',
      ignore: true
   },
   {
      key: 'template',
      ignore: true
   },
   {
      key: 'refreshOn',
      ignore: true
   },
   {
      key: 'refreshOnBlur',
      ignore: true
   },
   {
      key: 'clearOnRefresh',
      ignore: true
   },
   {
      key: 'searchEnabled',
      ignore: true
   },
   {
      key: 'selectThreshold',
      ignore: true
   },
   {
      key: 'useExactSearch',
      ignore: true
   }
   ]
}
];
var filecompo = [
{
   key: 'btn-group',
   ignore: true
},
{
   key: 'api',
   ignore: true
},
{
   key: 'layout',
   ignore: true
},
{
   key: 'logic',
   ignore: true
},
{
   key: 'conditional',
   ignore: false,
   components:[
   {
      key: 'customConditionalPanel',
      ignore: true
   }
   ]
},
{
   key: 'display',
   ignore: false,
   components:[
   {
      key: 'showWordCount',
      ignore: true
   },
   {
      key: 'showCharCount',
      ignore: true
   },
   {
      key: 'tabindex',
      ignore: true
   },
   {
      key: 'inputMask',
      ignore: true
   },
   {
      key: 'allowMultipleMasks',
      ignore: true
   },
   {
      key: 'prefix',
      ignore: true
   },
   {
      key: 'suffix',
      ignore: true
   },
   {
      key: 'widget.type',
      ignore: true
   },
   {
      key: 'mask',
      ignore: true
   },
   {
      key: 'autofocus',
      ignore: true
   },
   {
      key: 'modalEdit',
      ignore: true
   },
   {
      key: 'tableView',
      ignore: true
   },
   {
      key: 'modalEdit',
      ignore: true
   },
   {
      key: 'refreshOnChange',
      ignore: true
   },
   {
      key: 'displayInTimezone',
      ignore: true
   },
   {
      key: 'useLocaleSettings',
      ignore: true
   },
   {
      key: 'shortcutButtons',
      ignore: true
   },
   {
      key: 'shortcut',
      ignore: true
   },
   {
      key: 'inputType',
      ignore: true
   }
   ]
},
{
   key: 'validation',
   ignore: false,
   components:[
   {
      key: 'validate.pattern',
      ignore: true
   },
   {
      key: 'custom-validation-js',
      ignore: true
   },
   {
      key: 'json-validation-json',
      ignore: true
   },
   {
      key: 'errorLabel',
      ignore: true
   },
   {
      key: 'validate.maxLength',
      ignore: true
   },
   {
      key: 'validate.minLength',
      ignore: true
   },
   {
      key: 'validate.customMessage',
      ignore: true
   },
   {
      key: 'unique',
      ignore: true
   },
   {
      key: 'kickbox',
      ignore: true
   },
   {
      key: 'validate.onlyAvailableItems',
      ignore: true
   },
   {
      key: 'errors',
      ignore: true
   }
   ]
},
{
   key: 'file',
   ignore: false,
   components:[
   {
      key: 'dir',
      ignore: true
   },
   {
      key: 'fileNameTemplate',
      ignore: true
   },
   {
      key: 'image',
      ignore: false
   },
   {
      key: 'uploadOnly',
      ignore: true
   },
   {
      key: 'webcam',
      ignore: true
   },
   {
      key: 'fileTypes',
      ignore: true
   },
   {
      key: 'filePattern',
      ignore: false
   },
   {
      key: 'fileMinSize',
      ignore: false
   },
   {
      key: 'fileMaxSize',
      ignore: false
   },
   {
      key: 'storage',
      ignore: true
   }
   ]
},
{
   key: 'data',
   ignore: false,
   components:[
   {
      key: 'multiple',
      ignore: false
   },
   {
      key: 'persistent',
      ignore: true
   },
   {
      key: 'protected',
      ignore: true
   },
   {
      key: 'dbIndex',
      ignore: true
   },
   {
      key: 'encrypted',
      ignore: true
   },
   {
      key: 'encrypted',
      ignore: true
   },
   {
      key: 'redrawOn',
      ignore: true
   },
   {
      key: 'clearOnHide',
      ignore: true
   },
   {
      key: 'customDefaultValuePanel',
      ignore: true
   },
   {
      key: 'calculateValuePanel',
      ignore: true
   },
   {
      key: 'calculateServer',
      ignore: true
   },
   {
      key: 'allowCalculateOverride',
      ignore: true
   }
   ]
}
];
jQuery(window).load( function() {
   var builder = new Formio.FormBuilder(document.getElementById('wc-quote-formbuilder'), components, {
      noDefaultSubmitButton: true,
      noAlerts: true,
      builder: {
         basic: false,
         advanced: false,
         data: false,
         premium: false,
         layout: false,
         customBasic: {
            title: 'All Fields',
            default: true,
            weight: 0,
            components: {
               textfield: true,
               password: true,
               textarea: true,
               email: true,
               url: true,
               phoneNumber: true,
               // recaptcha: true,
               select: true,
               radio: true,
               checkbox: true,
               number: true,
               datetime: true,
               content: true,
               file: {
                  title: 'File',
                  icon: 'file',
                  schema: {
                     type: 'file',
                     storage: 'base64'
                  }
               }
               //columns: true
               }
            },
         },
         editForm: {
            password: mycompo,
            textfield: mycompo,
            textarea: mycompo,
            email: mycompo,
            url: mycompo,
            phoneNumber: mycompo,
            // recaptcha: mycompo,
            select: mycompo,
            radio: mycompo,
            checkbox: mycompo,
            number: mycompo,
            datetime: mycompo,
            content: mycompo,
            file: filecompo,
            //columns: mycompo
         }
      });

   var onBuild = function(build) {

      console.log("Something changed on the form builder");

      var jsonSchema = JSON.stringify( builder.instance.schema, null, 4 );
      console.log(jsonSchema);
      var current_button = jQuery('#wc-quote-save-formbuilder');
      if ( current_button.css('opacity') == 0.2 ) {
         return;
      }

      current_button.css('opacity' ,'0.2' );

      jQuery.ajax({
         url: ajax_url,
         type: 'POST',
         data: {
            action: 'wc_quote_form_builder_save',
            nonce: nonce,
            builder_schema: jsonSchema
         },
         success: function (response) {
            if( response['success'] ) {
               current_button.css('opacity', '1');
               jQuery('.builder-success').fadeIn();
               jQuery('.builder-success p strong').text(response['message']);
               setTimeout(function () {
                  jQuery('.builder-success').fadeOut();
               }, 2000);
            }
         },
         error: function (response) {
            current_button.css('opacity', '1');
            jQuery('.builder-success').fadeOut();
            console.log( response );
         }
      });
   };

   var onReady = function() {
      jQuery('#wc-quote-save-formbuilder').click(function(){
         onBuild();
      });
   };

   builder.instance.ready.then(onReady);
});