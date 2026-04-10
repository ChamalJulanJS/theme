<?php
/**
 * The template for displaying product content in the single-product.php template
 */
defined( 'ABSPATH' ) || exit;
global $product;
do_action( 'woocommerce_before_single_product' );
if ( post_password_required() ) {
    echo get_the_password_form(); // WPCS: XSS ok.
    return;
}
?>
<style>
/* Luxury Typography & Layout - Matching Mockup */
body { font-family: 'Inter', sans-serif; background-color: #ffffff; color: #111; }
h1, h2, h3, h4 { letter-spacing: -0.02em; }

.luxury-product-container { display: flex; flex-direction: column; max-width: 1300px; margin: 0 auto; gap: 4rem; padding: 2rem 1rem; }
@media (min-width: 1024px) {
    .luxury-product-container { flex-direction: row; align-items: flex-start; padding: 1rem 2rem 4rem 2rem; gap: 6rem; }
}

/* Left - Standard Gallery Restored */
.luxury-gallery-col { flex: 1; max-width: 580px; margin: 0 auto; width: 100%; }
.luxury-gallery-col .woocommerce-product-gallery { width: 100% !important; float: none !important; margin: 0 !important; }
.luxury-gallery-col .woocommerce-product-gallery figure { margin: 0; }

/* Polish the default gallery */
.woocommerce-product-gallery__wrapper, .woocommerce-product-gallery__image img { border-radius: 12px; overflow: hidden; }
.woocommerce-product-gallery .flex-control-thumbs { margin: 1rem -0.5rem 0 !important; padding: 0 !important; display: flex; gap: 10px; }
.woocommerce-product-gallery .flex-control-thumbs li { width: 25% !important; float: none !important; margin: 0 !important; }
.woocommerce-product-gallery .flex-control-thumbs li img { border-radius: 8px; opacity: 0.5; transition: all 0.3s; cursor: pointer; border: 2px solid transparent; width: 100%; height: auto;}
.woocommerce-product-gallery .flex-control-thumbs li img:hover { opacity: 0.8; }
.woocommerce-product-gallery .flex-control-thumbs li img.flex-active { opacity: 1; border-color: #111; }

/* Right - Sticky Details */
.luxury-details-col { width: 100%; }
@media (min-width: 1024px) {
    /* Adjusting to match the mockup perfectly */
    .luxury-details-col { width: 440px; flex-shrink: 0; }
    .luxury-details-sticky { position: sticky; top: 3rem; padding-bottom: 2rem; }
}

/* Base Typo */
.lux-brand { font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; color: #777; margin-bottom: 1rem; display: block; }
.luxury-title { font-size: 2.25rem; font-weight: 600; text-transform: capitalize; margin-bottom: 0.75rem; line-height: 1.1; color: #111; }
.luxury-price { font-size: 1.5rem; font-weight: 400; color: #555; margin-bottom: 2.5rem; }

/* Variations & Swatches - Base Styling */
.variations_form { margin-top: 2rem; width: 100%; border-top: 1px solid #eaeaea; padding-top: 1.5rem;}
.woocommerce div.product form.cart table.variations { width: 100%; border-spacing: 0; margin-bottom: 2rem; }
.woocommerce div.product form.cart table.variations th { text-align: left; padding-bottom: 0.75rem; vertical-align: middle; width: 100px; }
.woocommerce div.product form.cart table.variations td { padding-bottom: 0.75rem; vertical-align: middle; }
.woocommerce div.product form.cart table.variations label { font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #111; margin: 0; }
.woocommerce div.product form.cart table.variations select { width: 100%; border: 1px solid #d4d4d4; padding: 0.75rem 1rem; border-radius: 4px; font-size: 0.95rem; color: #111; background: #fff; appearance: none; outline: none; margin-bottom: 0.5rem;}
.woocommerce div.product form.cart table.variations select:focus { border-color: #111; }

/* In case of Variation Swatches Plugin overrides */
.woocommerce div.product .variable-items-wrapper,
.woocommerce div.product .tawcvs-swatches { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 1rem; }
.woocommerce div.product .swatch,
.woocommerce div.product .variable-item { 
    min-width: 3.5rem; height: 3.5rem; border: 1px solid #e5e5e5; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; border-radius: 4px; font-size: 0.9rem; transition: all 0.2s ease; background: #fff;
}
.woocommerce div.product .swatch:hover,
.woocommerce div.product .variable-item:hover { border-color: #111; }
.woocommerce div.product .swatch.selected,
.woocommerce div.product .variable-item.selected { border-color: #111; box-shadow: inset 0 0 0 1px #111; font-weight: 600; }
.lux-stock-message { font-size: 0.85rem; margin-top: 1rem; font-weight: 500; min-height: 20px; color: transparent; transition: color 0.3s; }
.lux-stock-message.alert { color: #dc2626; }
.lux-stock-message.safe { color: #16a34a; }

.reset_variations { display: none !important; }

/* Cart Button & Quantity Base */
.woocommerce-variation-add-to-cart { display: flex; flex-direction: column; gap: 1rem; width: 100%; margin-top: 1rem;}
.woocommerce-variation-add-to-cart .quantity { margin-bottom: 0.5rem; }
.woocommerce-variation-add-to-cart .quantity input.qty { height: 3.5rem; border: 1px solid #d4d4d4; border-radius: 4px; outline: none; padding: 0 1rem; font-size: 1rem; width: 100px; text-align: center; }

body.single-product .woocommerce div.product form.cart button.single_add_to_cart_button {
    width: 100%; background-color: #111 !important; color: #fff !important; border: none !important; 
    padding: 1.5rem !important; font-size: 1rem !important; font-weight: 700 !important; 
    text-transform: uppercase; letter-spacing: 0.1em; cursor: pointer; transition: background 0.3s; 
    display: flex; justify-content: center; align-items: center; border-radius: 4px !important; margin: 0 !important; height: auto !important; line-height: 1 !important;
}
body.single-product .woocommerce div.product form.cart button.single_add_to_cart_button:hover { background-color: #333 !important; }
body.single-product .woocommerce div.product form.cart button.single_add_to_cart_button.disabled, 
body.single-product .woocommerce div.product form.cart button.single_add_to_cart_button:disabled { 
    background-color: #e5e5e5 !important; cursor: not-allowed !important; color: #a3a3a3 !important; 
}

/* Trust Badges */
.lux-trust { display: flex; flex-direction: column; gap: 0.75rem; padding: 2rem 0; border-bottom: 1px solid #eaeaea; margin-bottom: 1rem;}
.lux-trust-item { display: flex; align-items: center; gap: 0.75rem; font-size: 0.8rem; color: #555; font-weight: 500;}
.lux-trust-item svg { width: 18px; height: 18px; stroke: #111; fill: none; stroke-width: 1.5;}

/* Accordions for Description & Tabs */
.woocommerce-tabs { display: none !important; }

.lux-accordions { border-top: 1px solid #111; margin-top: 2rem; }
.lux-accordion { border-bottom: 1px solid #eaeaea; }
.lux-accordion-btn { 
    width: 100%; display: flex; justify-content: space-between; align-items: center; padding: 1.5rem 0; 
    font-size: 0.85rem; text-transform: uppercase; font-weight: 600; letter-spacing: 0.05em; background: none; border: none; cursor: pointer; color: #111; outline: none;
}
.lux-accordion-content { max-height: 0; overflow: hidden; transition: max-height 0.4s ease; font-size: 0.95rem; color: #555; line-height: 1.8; }
.lux-accordion.open .lux-accordion-content { max-height: 2000px; }
.lux-accordion-content-inner { padding-bottom: 1.5rem; }
.lux-icon { transition: transform 0.3s ease; stroke: #111; width: 18px; height: 18px;}
.lux-accordion.open .lux-icon { transform: rotate(180deg); }

.lux-accordion-content-inner p:last-child { margin-bottom: 0; }
</style>

<div id="product-<?php the_ID(); ?>" <?php wc_product_class( '', $product ); ?>>

    <div class="luxury-product-container">
        
        <!-- Left Side: Classic Restored Gallery -->
        <div class="luxury-gallery-col">
            <?php
            /**
             * Hook: woocommerce_before_single_product_summary.
             *
             * @hooked woocommerce_show_product_sale_flash - 10
             * @hooked woocommerce_show_product_images - 20
             */
            do_action( 'woocommerce_before_single_product_summary' );
            ?>
        </div>

        <!-- Right Side: Sticky Details Panel -->
        <div class="luxury-details-col">
            <div class="luxury-details-sticky">
                
                <?php $brands = wp_get_post_terms( $product->get_id(), 'pwb-brand' );
                if(!empty($brands) && !is_wp_error($brands)){
                    echo '<span class="lux-brand">'.esc_html($brands[0]->name).'</span>';
                } else {
                    echo '<span class="lux-brand">FASHION FEET EXCLUSIVE</span>';
                }
                ?>
                
                <h1 class="luxury-title"><?php the_title(); ?></h1>
                
                <div class="luxury-price">
                    <?php echo $product->get_price_html(); ?>
                </div>

                <!-- Add to Cart / Variaton Form Output -->
                <?php woocommerce_template_single_add_to_cart(); ?>
                
                <div class="lux-trust">
                    <div class="lux-trust-item">
                        <svg viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                        Free Complimentary Shipping Available
                    </div>
                    <div class="lux-trust-item">
                        <svg viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        Secure Encrypted Checkout
                    </div>
                </div>

                <!-- Luxury Accordions for Description & Info -->
                <div class="lux-accordions">
                    <?php if ( $product->get_description() ) : ?>
                    <div class="lux-accordion open">
                        <button class="lux-accordion-btn">
                            Product Details
                            <svg class="lux-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div class="lux-accordion-content">
                            <div class="lux-accordion-content-inner prose prose-sm max-w-none text-[#555]">
                                <?php echo wpautop( wp_kses_post( $product->get_description() ) ); ?>
                                <?php if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ) : ?>
                                    <p class="mt-6 text-xs tracking-widest uppercase font-medium">SKU: <?php echo ( $sku = $product->get_sku() ) ? $sku : 'N/A'; ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="lux-accordion">
                        <button class="lux-accordion-btn">
                            Delivery & Returns
                            <svg class="lux-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div class="lux-accordion-content">
                            <div class="lux-accordion-content-inner text-sm">
                                <p>We offer express complimentary shipping on all orders. Standard delivery arrives within 3-5 business days. You may return any unworn, structurally intact item within 30 days for a full refund or exchange. Custom or personalized items are final sale.</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<?php do_action( 'woocommerce_after_single_product' ); ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // 1. Accordion Logic
    const accordions = document.querySelectorAll('.lux-accordion');
    accordions.forEach(acc => {
        const btn = acc.querySelector('.lux-accordion-btn');
        btn.addEventListener('click', () => {
            acc.classList.toggle('open');
        });
    });

    // 2. Dynamic Stock Engine (Without overriding plugin swatches)
    const variationForms = document.querySelectorAll('.variations_form');
    
    variationForms.forEach(form => {
        // Find Add-to-cart container to prepend our stock message
        const cartWrapper = form.querySelector('.woocommerce-variation-add-to-cart');
        
        if (cartWrapper) {
            const stockMsg = document.createElement('div');
            stockMsg.className = 'lux-stock-message';
            cartWrapper.prepend(stockMsg);
        }
        
        // WooCommerce Dynamic Event Hooks
        jQuery(form).on('found_variation', function(event, variation) {
            const stockMsg = form.querySelector('.lux-stock-message');
            const addToCartBtn = form.querySelector('.single_add_to_cart_button');
            
            if(stockMsg) {
                stockMsg.classList.remove('alert', 'safe');
                
                if (variation.is_in_stock) {
                    if (variation.max_qty && variation.max_qty <= 5) {
                        stockMsg.textContent = `Hurry, only ${variation.max_qty} pairs left in this size`;
                        stockMsg.classList.add('alert');
                    } else {
                        stockMsg.textContent = `In Stock - Ready to dispatch`;
                        stockMsg.classList.add('safe');
                    }
                } else {
                    stockMsg.textContent = `Currently Out of Stock`;
                    stockMsg.classList.add('alert');
                }
            }
            
            if(variation.is_purchasable && variation.is_in_stock) {
                if(addToCartBtn.tagName.toLowerCase() === 'button') { addToCartBtn.disabled = false; }
            } else {
                if(addToCartBtn.tagName.toLowerCase() === 'button') { addToCartBtn.disabled = true; }
            }
        });
        
        jQuery(form).on('reset_image reset_data', function() {
            const stockMsg = form.querySelector('.lux-stock-message');
            const addToCartBtn = form.querySelector('button.single_add_to_cart_button');
            
            if(stockMsg) { stockMsg.textContent = ''; stockMsg.classList.remove('alert', 'safe'); }
            if(addToCartBtn) { addToCartBtn.disabled = true; }
        });
    });

});
</script>
