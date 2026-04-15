<?php 
get_header(); 

// Check WooCommerce page states
$is_my_account = function_exists('is_account_page') && is_account_page();
$is_login      = $is_my_account && ! is_user_logged_in();
$is_wc_wide    = function_exists('is_cart') && ( is_cart() || is_checkout() );
?>

<?php if ( $is_login ) : ?>
    
    <!-- Full Width Container for Login/Register Hero -->
    <main class="w-full p-0 m-0 relative">
        <?php while ( have_posts() ) : the_post(); ?>
            <?php the_content(); ?>
        <?php endwhile; ?>
    </main>

<?php elseif ( function_exists('is_checkout') && is_checkout() && ! is_order_received_page() ) : ?>

    <!-- Checkout — custom layout, no duplicate titles -->
    <main class="wc-checkout-page mx-auto px-4 py-8 lg:py-12" style="max-width: 1300px;">
        <div class="woocommerce">
            <?php
            wc_print_notices();
            echo do_shortcode( '[woocommerce_checkout]' );
            ?>
        </div>
    </main>

<?php elseif ( $is_my_account || $is_wc_wide ) : ?>

    <!-- Wide Container for Logged-In Account / Cart -->
    <main class="mx-auto px-4 py-8 lg:py-16" style="max-width: 1300px;">
        <?php while ( have_posts() ) : the_post(); ?>
            
            <?php if ( ! $is_my_account && ! ( function_exists('is_cart') && is_cart() ) ) : // Hide title for my account and cart ?>
            <header class="mb-12 text-center">
                <h1 class="text-4xl md:text-5xl font-black text-dark tracking-tight uppercase mb-6">
                    <?php the_title(); ?>
                </h1>
            </header>
            <?php endif; ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <div class="px-0 w-full text-gray-600 leading-relaxed font-sans">
                    <?php the_content(); ?>
                </div>
            </article>

        <?php endwhile; ?>
    </main>

<?php else : ?>

    <!-- Standard Narrow Container for Regular Pages -->
    <main class="container-premium py-16 md:py-24 max-w-4xl mx-auto">
        <?php while ( have_posts() ) : the_post(); ?>
            
            <header class="mb-12 text-center">
                <h1 class="text-4xl md:text-5xl font-black text-dark tracking-tight uppercase mb-6">
                    <?php the_title(); ?>
                </h1>
            </header>

            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <div class="prose prose-lg px-0 max-w-none text-gray-600 leading-relaxed font-sans">
                    <?php the_content(); ?>
                </div>
            </article>

        <?php endwhile; ?>
    </main>

<?php endif; ?>

<?php get_footer(); ?>
