import { registerBlockType } from '@wordpress/blocks';
import { createElement } from '@wordpress/element';
import { getSetting } from '@woocommerce/settings';
import metadata from './block.json';
const { registerCheckoutBlock } = wc.blocksCheckout;


// Retrieve WooCommerce settings
const wcquote_data = getSetting('wc-quote_data', {});
console.log(wcquote_data);
const show_button = wcquote_data.enable_build_quote == 1;

// alert(show_button);
// Define the Block component
const Block = ({ children, checkoutExtensionData }) => {
    return (
        <div className="wc-block-cart__submit wp-block-woocommerce-proceed-to-checkout-block">
            {
                <a
                    href="javascript:void(0)"
                    data-url={wcquote_data.quote_page}
                    className="build-quote-button button components-button wc-block-components-button wp-element-button wc-block-cart__submit-button contained"
                >
                    {wcquote_data.build_quote_text}
                </a>
            }
        </div>
    );
};

registerCheckoutBlock( {
    metadata,
    component: Block
});

// Register your other block type here, if any

// Register your Gutenberg block
// registerBlockType(
//     metadata, {
//     edit: Block // Assuming Edit component is imported from elsewhere
// });
