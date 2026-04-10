<?php
/**
 * WooCommerce Page Router
 * Shop/Archive pages → custom Nike-style filter sidebar layout
 * All other WooCommerce pages → standard layout
 */

get_header();

if ( is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy() ) {
    // Load our custom shop/archive template (no get_header/get_footer inside)
    include( get_template_directory() . '/woocommerce/archive-product.php' );
} else {
    // Cart, Checkout, My Account, Single Product, etc. — standard WooCommerce output
    ?>
    <main class="wc-standard-page" style="max-width: 1200px; margin: 0 auto; padding: 60px 20px 80px;">
        <?php woocommerce_content(); ?>
    </main>
    <?php
}

get_footer();