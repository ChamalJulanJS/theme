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
} elseif ( is_account_page() && ! is_user_logged_in() ) {
    // Login / Register / Lost Password pages should be full width hero
    ?>
    <main class="wc-login-page w-full p-0 m-0 relative">
        <?php woocommerce_content(); ?>
    </main>
    <?php
} elseif ( is_checkout() && ! is_order_received_page() ) {
    // Checkout page — skip ALL default titles, render only the checkout form
    ?>
    <main class="wc-checkout-page mx-auto px-4 py-8 lg:py-12" style="max-width: 1300px;">
        <div class="woocommerce">
            <?php
            // Output notices (errors, messages) without the page title
            wc_print_notices();
            // Render checkout form directly
            echo do_shortcode( '[woocommerce_checkout]' );
            ?>
        </div>
    </main>
    <?php
} else {
    // Cart, My Account (Logged In), Single Product, Thank You, etc. — standard container
    ?>
    <main class="wc-standard-page mx-auto px-4 py-8 lg:py-16" style="max-width: 1300px;">
        <?php woocommerce_content(); ?>
    </main>
    <?php
}

get_footer();