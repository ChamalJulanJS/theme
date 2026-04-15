<?php
/**
 * The template for displaying 404 pages (Not Found)
 *
 * @package FashionFeet
 */

get_header(); ?>

<main id="primary" class="ff-404-page">
    <div class="ff-404-container">
        
        <div class="ff-404-content">
            <span class="ff-404-eyebrow">Error 404</span>
            <h1 class="ff-404-title">404</h1>
            <h2 class="ff-404-subtitle">Page Not Found</h2>
            <p class="ff-404-desc">
                The page you're looking for doesn't exist or has been moved.<br>
                Let's get you back on track.
            </p>

            <div class="ff-404-actions">
                <a href="<?php echo esc_url( home_url('/') ); ?>" class="ff-404-btn ff-404-btn-primary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    Go Home
                </a>
                <?php if ( class_exists('WooCommerce') ) : ?>
                <a href="<?php echo esc_url( wc_get_page_permalink('shop') ); ?>" class="ff-404-btn ff-404-btn-outline">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                    Browse Shop
                </a>
                <?php endif; ?>
            </div>

            <!-- Search -->
            <div class="ff-404-search">
                <p class="ff-404-search-label">Or search for what you need:</p>
                <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="ff-404-search-form">
                    <input type="hidden" name="post_type" value="product" />
                    <div class="ff-404-search-wrap">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="search" name="s" placeholder="Search products..." class="ff-404-search-input" required />
                        <button type="submit" class="ff-404-search-btn">Search</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</main>

<?php get_footer(); ?>
