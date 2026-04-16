<?php

// ── Hide WooCommerce page title on Checkout ──
add_filter( 'woocommerce_show_page_title', function() {
    if ( is_checkout() ) return false;
    return true;
});

// Theme Setup
function fashionfeet_setup() {
    // Force deactivate conflicting Elementor Header & Footer plugin
    if ( ! function_exists( 'deactivate_plugins' ) ) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    deactivate_plugins( 'header-footer-elementor/header-footer-elementor.php' );

    // WooCommerce Support
    add_theme_support( 'woocommerce' );
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );

    // Standard WP Features
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );
    
    // Custom Logo
    add_theme_support( 'custom-logo', array(
        'height'      => 80,
        'width'       => 250,
        'flex-width'  => true,
        'flex-height' => true,
    ) );

    // Register Menus
    register_nav_menus( array(
        'primary'       => __( 'Primary Menu', 'fashionfeet' ),
        'primary-mega'  => __( 'Primary Mega Menu', 'fashionfeet' ),
        'footer'        => __( 'Footer Menu', 'fashionfeet' ),
    ) );
}
add_action( 'after_setup_theme', 'fashionfeet_setup' );

// Enqueue Styles
function fashionfeet_enqueue_styles() {
    // Tailwind CSS Output
    wp_enqueue_style( 'fashionfeet-tailwind', get_template_directory_uri() . '/dist/output.css', array(), filemtime(get_template_directory() . '/dist/output.css') );
    
    // Original style.css for WP standard compliance
    wp_enqueue_style( 'fashionfeet-style', get_stylesheet_uri(), array('fashionfeet-tailwind'), wp_get_theme()->get( 'Version' ) );
    
    // Google Fonts (Inter)
    wp_enqueue_style( 'fashionfeet-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap', false );

    // Alpine.js (deferred automatically in WP >= 6.3 or use standard)
    wp_enqueue_script( 'alpinejs', 'https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js', array(), null, true );

    // Removed GSAP animations at user request

    // App JS (filter sidebar + animations)
    wp_enqueue_script(
        'fashionfeet-app',
        get_template_directory_uri() . '/js/app.js',
        array(),
        filemtime( get_template_directory() . '/js/app.js' ),
        true
    );

    // Localize AJAX URL for live search
    wp_localize_script( 'fashionfeet-app', 'ffAjax', array(
        'url'   => admin_url( 'admin-ajax.php' ),
        'nonce' => wp_create_nonce( 'ff_search_nonce' ),
    ) );
}
add_action( 'wp_enqueue_scripts', 'fashionfeet_enqueue_styles' );

// ============================================================
// SHOP FILTER — Custom WooCommerce Query Hooks
// ============================================================
add_action( 'woocommerce_product_query', 'fashionfeet_shop_filter_query' );
function fashionfeet_shop_filter_query( $q ) {

    if ( ! $q->is_main_query() ) return;

    $meta_query = $q->get( 'meta_query' ) ?: array();
    $tax_query  = $q->get( 'tax_query' )  ?: array();

    // ---- 1. On Sale filter ----
    if ( ! empty( $_GET['on_sale'] ) ) {
        $sale_ids = wc_get_product_ids_on_sale();
        if ( empty( $sale_ids ) ) $sale_ids = array( 0 );
        $q->set( 'post__in', $sale_ids );
    }

    // ---- 2. In Stock Only filter ----
    if ( ! empty( $_GET['in_stock'] ) ) {
        $meta_query[] = array(
            'key'     => '_stock_status',
            'value'   => 'instock',
            'compare' => '=',
        );
    }

    // ---- 3. Price Range filter ----
    if ( ! empty( $_GET['min_price'] ) || ! empty( $_GET['max_price'] ) ) {
        $price_meta = array( 'key' => '_price', 'type' => 'NUMERIC' );
        if ( ! empty( $_GET['min_price'] ) ) {
            $price_meta['value']   = floatval( $_GET['min_price'] );
            $price_meta['compare'] = '>=';
            if ( ! empty( $_GET['max_price'] ) ) {
                // BETWEEN range
                $price_meta['value']   = array( floatval( $_GET['min_price'] ), floatval( $_GET['max_price'] ) );
                $price_meta['compare'] = 'BETWEEN';
            }
        } elseif ( ! empty( $_GET['max_price'] ) ) {
            $price_meta['value']   = floatval( $_GET['max_price'] );
            $price_meta['compare'] = '<=';
        }
        $meta_query[] = $price_meta;
    }

    // ---- 4. Size Attribute filter (dynamic taxonomy detection) ----
    if ( ! empty( $_GET['filter_size'] ) ) {
        $slugs = array_map( 'sanitize_title', explode( ',', $_GET['filter_size'] ) );

        // Find the actual size taxonomy slug from registered WooCommerce attributes
        $size_tax = 'pa_size'; // fallback default
        $all_attrs = wc_get_attribute_taxonomies();
        foreach ( $all_attrs as $attr ) {
            $lower = strtolower( $attr->attribute_name );
            if ( in_array( $lower, array( 'size', 'sizes', 'sz', 'shoe_size', 'shoesize', 'shoe-size' ), true )
                 || strpos( $lower, 'size' ) !== false ) {
                $size_tax = wc_attribute_taxonomy_name( $attr->attribute_name );
                break;
            }
        }

        $tax_query[] = array(
            'taxonomy' => $size_tax,
            'field'    => 'slug',
            'terms'    => $slugs,
            'operator' => 'IN',
        );
    }


    // ---- 5. Sort / Orderby ----
    if ( ! empty( $_GET['orderby'] ) ) {
        $orderby = sanitize_text_field( $_GET['orderby'] );
        switch ( $orderby ) {
            case 'price':
                $q->set( 'orderby', 'meta_value_num' );
                $q->set( 'meta_key', '_price' );
                $q->set( 'order', 'ASC' );
                break;
            case 'price-desc':
                $q->set( 'orderby', 'meta_value_num' );
                $q->set( 'meta_key', '_price' );
                $q->set( 'order', 'DESC' );
                break;
            case 'date':
                $q->set( 'orderby', 'date' );
                $q->set( 'order', 'DESC' );
                break;
            case 'popularity':
                $q->set( 'orderby', 'meta_value_num' );
                $q->set( 'meta_key', 'total_sales' );
                $q->set( 'order', 'DESC' );
                break;
            default:
                $q->set( 'orderby', 'menu_order title' );
                $q->set( 'order', 'ASC' );
                break;
        }
    }

    if ( ! empty( $meta_query ) ) $q->set( 'meta_query', $meta_query );
    if ( ! empty( $tax_query ) )  $q->set( 'tax_query',  $tax_query );
}

// Widget Areas
function fashionfeet_widgets_init() {
    register_sidebar( array(
        'name'          => __( 'Footer Column 1', 'fashionfeet' ),
        'id'            => 'footer-1',
        'before_widget' => '<div id="%1$s" class="widget %2$s mb-6">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="text-white text-lg font-bold mb-4">',
        'after_title'   => '</h4>',
    ) );
    register_sidebar( array(
        'name'          => __( 'Footer Column 2', 'fashionfeet' ),
        'id'            => 'footer-2',
        'before_widget' => '<div id="%1$s" class="widget %2$s mb-6">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="text-white text-lg font-bold mb-4">',
        'after_title'   => '</h4>',
    ) );
}
add_action( 'widgets_init', 'fashionfeet_widgets_init' );

// WooCommerce Mini Cart AJAX
add_filter( 'woocommerce_add_to_cart_fragments', 'fashionfeet_woocommerce_cart_fragment' );
function fashionfeet_woocommerce_cart_fragment( $fragments ) {
    // 1) Cart icon badge fragment
    ob_start();
    ?>
    <a class="cart-customlocation relative flex items-center gap-2 group" href="<?php echo esc_url( wc_get_cart_url() ); ?>" title="<?php _e( 'View your shopping cart' ); ?>">
        <svg class="w-6 h-6 text-dark group-hover:text-premium-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
        <?php if ( WC()->cart->get_cart_contents_count() > 0 ) : ?>
            <span class="absolute -top-2 -right-3 bg-premium-500 text-white text-[10px] font-bold h-5 min-w-[20px] px-1 rounded-full flex items-center justify-center shadow-md border-2 border-white"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
        <?php endif; ?>
    </a>
    <?php
    $fragments['a.cart-customlocation'] = ob_get_clean();

    // 2) Mini cart drawer content fragment
    ob_start();
    ?>
    <div class="ff-mini-cart-content" id="ff-mini-cart-content">
        <?php if ( WC()->cart->get_cart_contents_count() > 0 ) : ?>
            <ul class="ff-mini-cart-items">
                <?php foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) :
                    $_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
                    if ( ! $_product || ! $_product->exists() || $cart_item['quantity'] <= 0 ) continue;
                    $product_permalink = $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '';
                ?>
                <li class="ff-mini-cart-item">
                    <div class="ff-mini-cart-thumb">
                        <?php echo $_product->get_image( array(64, 64) ); ?>
                    </div>
                    <div class="ff-mini-cart-item-info">
                        <h4 class="ff-mini-cart-item-name">
                            <?php if ( $product_permalink ) : ?>
                                <a href="<?php echo esc_url( $product_permalink ); ?>"><?php echo esc_html( $_product->get_name() ); ?></a>
                            <?php else : ?>
                                <?php echo esc_html( $_product->get_name() ); ?>
                            <?php endif; ?>
                        </h4>
                        <?php echo wc_get_formatted_cart_item_data( $cart_item ); ?>
                        <span class="ff-mini-cart-item-qty"><?php echo $cart_item['quantity']; ?> &times; <?php echo WC()->cart->get_product_price( $_product ); ?></span>
                    </div>
                    <a href="<?php echo esc_url( wc_get_cart_remove_url( $cart_item_key ) ); ?>" class="ff-mini-cart-remove" aria-label="Remove">&times;</a>
                </li>
                <?php endforeach; ?>
            </ul>
            <div class="ff-mini-cart-footer">
                <div class="ff-mini-cart-subtotal">
                    <span>Subtotal</span>
                    <strong><?php echo WC()->cart->get_cart_subtotal(); ?></strong>
                </div>
                <a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="ff-mini-cart-btn ff-mini-cart-btn-outline">View Cart</a>
                <a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="ff-mini-cart-btn ff-mini-cart-btn-primary">Checkout</a>
            </div>
        <?php else : ?>
            <div class="ff-mini-cart-empty">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                <p>Your cart is currently empty.</p>
                <a href="<?php echo esc_url( wc_get_page_permalink('shop') ); ?>" class="ff-mini-cart-btn ff-mini-cart-btn-primary" style="margin-top:16px;">Continue Shopping</a>
            </div>
        <?php endif; ?>
    </div>
    <?php
    $fragments['#ff-mini-cart-content'] = ob_get_clean();

    return $fragments;
}

// ============================================================
// AJAX Live Search Handler
// ============================================================
add_action( 'wp_ajax_ff_live_search', 'ff_live_search_handler' );
add_action( 'wp_ajax_nopriv_ff_live_search', 'ff_live_search_handler' );
function ff_live_search_handler() {
    check_ajax_referer( 'ff_search_nonce', 'nonce' );

    $query = sanitize_text_field( $_GET['q'] ?? '' );
    if ( strlen( $query ) < 2 ) {
        wp_send_json_success( array( 'results' => array() ) );
    }

    $args = array(
        'post_type'      => 'product',
        'post_status'    => 'publish',
        's'              => $query,
        'posts_per_page' => 6,
    );
    $search = new WP_Query( $args );
    $results = array();

    if ( $search->have_posts() ) {
        while ( $search->have_posts() ) {
            $search->the_post();
            $product = wc_get_product( get_the_ID() );
            $results[] = array(
                'id'        => get_the_ID(),
                'title'     => get_the_title(),
                'url'       => get_the_permalink(),
                'price'     => $product ? $product->get_price_html() : '',
                'image'     => get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' ) ?: '',
            );
        }
    }
    wp_reset_postdata();

    wp_send_json_success( array( 'results' => $results ) );
}

// ============================================================
// Custom Wishlist System (Cookie-based for guests, user meta for logged-in)
// ============================================================

// Get wishlist items
function ff_get_wishlist() {
    if ( is_user_logged_in() ) {
        $wishlist = get_user_meta( get_current_user_id(), 'ff_wishlist', true );
    } else {
        $wishlist = isset( $_COOKIE['ff_wishlist'] ) ? json_decode( stripslashes( $_COOKIE['ff_wishlist'] ), true ) : array();
    }
    return is_array( $wishlist ) ? $wishlist : array();
}

// Save wishlist
function ff_save_wishlist( $wishlist ) {
    if ( is_user_logged_in() ) {
        update_user_meta( get_current_user_id(), 'ff_wishlist', $wishlist );
    } else {
        setcookie( 'ff_wishlist', json_encode( $wishlist ), time() + ( 30 * DAY_IN_SECONDS ), COOKIEPATH, COOKIE_DOMAIN );
    }
}

// AJAX: Toggle wishlist item
add_action( 'wp_ajax_ff_wishlist_toggle', 'ff_wishlist_toggle' );
add_action( 'wp_ajax_nopriv_ff_wishlist_toggle', 'ff_wishlist_toggle' );
function ff_wishlist_toggle() {
    check_ajax_referer( 'ff_search_nonce', 'nonce' );

    $product_id = absint( $_POST['product_id'] ?? 0 );
    if ( ! $product_id ) wp_send_json_error( 'Invalid product' );

    $wishlist = ff_get_wishlist();
    $key = array_search( $product_id, $wishlist );

    if ( $key !== false ) {
        unset( $wishlist[ $key ] );
        $wishlist = array_values( $wishlist );
        $action = 'removed';
    } else {
        $wishlist[] = $product_id;
        $action = 'added';
    }

    ff_save_wishlist( $wishlist );

    wp_send_json_success( array(
        'action' => $action,
        'count'  => count( $wishlist ),
    ) );
}

// AJAX: Get wishlist count
add_action( 'wp_ajax_ff_wishlist_count', 'ff_wishlist_count' );
add_action( 'wp_ajax_nopriv_ff_wishlist_count', 'ff_wishlist_count' );
function ff_wishlist_count() {
    wp_send_json_success( array( 'count' => count( ff_get_wishlist() ) ) );
}

// Shortcode: [ff_wishlist]
add_shortcode( 'ff_wishlist', 'ff_wishlist_shortcode' );
function ff_wishlist_shortcode() {
    $wishlist = ff_get_wishlist();
    ob_start();
    ?>
    <div class="ff-wishlist-page">
        <div class="ff-wishlist-header">
            <span class="ff-wishlist-eyebrow">My Collection</span>
            <h1 class="ff-wishlist-title">Wishlist</h1>
            <p class="ff-wishlist-count-text"><?php echo count($wishlist); ?> <?php echo count($wishlist) === 1 ? 'item' : 'items'; ?></p>
        </div>

        <?php if ( ! empty( $wishlist ) ) : ?>
            <div class="ff-wishlist-grid">
                <?php foreach ( $wishlist as $pid ) :
                    $product = wc_get_product( $pid );
                    if ( ! $product || ! $product->exists() ) continue;
                ?>
                <div class="ff-wishlist-card" data-product-id="<?php echo $pid; ?>">
                    <button class="ff-wishlist-remove" onclick="ffToggleWishlist(<?php echo $pid; ?>, this)" aria-label="Remove from wishlist">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                    <a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="ff-wishlist-card-img">
                        <?php echo $product->get_image( 'woocommerce_thumbnail' ); ?>
                    </a>
                    <div class="ff-wishlist-card-info">
                        <h3><a href="<?php echo esc_url( $product->get_permalink() ); ?>"><?php echo esc_html( $product->get_name() ); ?></a></h3>
                        <span class="ff-wishlist-card-price"><?php echo $product->get_price_html(); ?></span>
                        <?php if ( $product->is_in_stock() ) : ?>
                            <span class="ff-wishlist-stock ff-in-stock">In Stock</span>
                        <?php else : ?>
                            <span class="ff-wishlist-stock ff-out-stock">Out of Stock</span>
                        <?php endif; ?>
                    </div>
                    <a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="ff-wishlist-card-btn">View Product</a>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <div class="ff-wishlist-empty">
                <svg class="w-20 h-20 mx-auto mb-6 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                <h2>Your wishlist is empty</h2>
                <p>Browse our collection and add items you love.</p>
                <a href="<?php echo esc_url( wc_get_page_permalink('shop') ); ?>" class="ff-404-btn ff-404-btn-primary" style="margin-top:20px;">Browse Shop</a>
            </div>
        <?php endif; ?>
    </div>

    <script>
    function ffToggleWishlist(productId, btn) {
        fetch(ffAjax.url, {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'action=ff_wishlist_toggle&nonce=' + ffAjax.nonce + '&product_id=' + productId
        })
        .then(r => r.json())
        .then(d => {
            if (d.data.action === 'removed') {
                const card = btn.closest('.ff-wishlist-card');
                if (card) { card.style.opacity = '0'; card.style.transform = 'scale(0.9)'; setTimeout(() => card.remove(), 300); }
                // Update count
                const countEl = document.querySelector('.ff-wishlist-count-text');
                if (countEl) countEl.textContent = d.data.count + (d.data.count === 1 ? ' item' : ' items');
                if (d.data.count === 0) setTimeout(() => location.reload(), 400);
            }
            // Update header badge
            const badge = document.querySelector('.ff-wishlist-badge');
            if (badge) { badge.textContent = d.data.count; badge.style.display = d.data.count > 0 ? 'flex' : 'none'; }
        });
    }
    </script>
    <?php
    return ob_get_clean();
}

// Helper: Check if product is in wishlist
function ff_is_in_wishlist( $product_id ) {
    $wishlist = ff_get_wishlist();
    return in_array( (int) $product_id, array_map('intval', $wishlist) );
}

// ============================================================
// Newsletter Subscribe (AJAX - saves to WP option)
// ============================================================
add_action( 'wp_ajax_ff_newsletter_subscribe', 'ff_newsletter_subscribe' );
add_action( 'wp_ajax_nopriv_ff_newsletter_subscribe', 'ff_newsletter_subscribe' );
function ff_newsletter_subscribe() {
    check_ajax_referer( 'ff_search_nonce', 'nonce' );

    $email = sanitize_email( $_POST['email'] ?? '' );
    if ( ! is_email( $email ) ) {
        wp_send_json_error( 'Invalid email' );
    }

    $subscribers = get_option( 'ff_newsletter_subscribers', array() );
    if ( ! in_array( $email, $subscribers ) ) {
        $subscribers[] = $email;
        update_option( 'ff_newsletter_subscribers', $subscribers );
    }

    wp_send_json_success( array( 'message' => 'Subscribed' ) );
}

// ============================================================
// Social Links (Customizer)
// ============================================================
add_action( 'customize_register', 'ff_social_customizer' );
function ff_social_customizer( $wp_customize ) {
    $wp_customize->add_section( 'ff_social_links', array(
        'title'    => 'Social Media Links',
        'priority' => 200,
    ) );

    $socials = array(
        'twitter'   => 'Twitter / X URL',
        'instagram' => 'Instagram URL',
        'facebook'  => 'Facebook URL',
        'tiktok'    => 'TikTok URL',
    );

    foreach ( $socials as $key => $label ) {
        $wp_customize->add_setting( "ff_social_{$key}", array(
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        ) );
        $wp_customize->add_control( "ff_social_{$key}", array(
            'label'   => $label,
            'section' => 'ff_social_links',
            'type'    => 'url',
        ) );
    }
}

function ff_get_social_url( $network ) {
    return get_theme_mod( "ff_social_{$network}", '' );
}

// ============================================================
// Disable default WooCommerce breadcrumb (we have custom ones)
// ============================================================
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );

// ============================================================
// Force native lazy loading on all images
// ============================================================
add_filter( 'wp_get_attachment_image_attributes', function( $attr ) {
    if ( ! isset( $attr['loading'] ) ) {
        $attr['loading'] = 'lazy';
    }
    return $attr;
} );

// --------------------------------------------------------------------------------
remove_action( 'wp_head', 'wp_generator' );
add_filter( 'the_generator', '__return_empty_string' );

// --------------------------------------------------------------------------------
// [SECURITY] 2. Disable XML-RPC (Prevent DDoS & Brute-force attacks)
// --------------------------------------------------------------------------------
add_filter( 'xmlrpc_enabled', '__return_false' );

// --------------------------------------------------------------------------------
// [SECURITY] 3. Strict HTTP Security Headers (Clickjacking & XSS Protection)
// --------------------------------------------------------------------------------
function fashionfeet_security_headers() {
    if ( ! is_admin() ) {
        // Prevent site from being embedded in an iframe (Clickjacking defense)
        header( 'X-Frame-Options: SAMEORIGIN' );
        // Prevent MIME type sniffing
        header( 'X-Content-Type-Options: nosniff' );
        // Enable built-in browser XSS filtering
        header( 'X-XSS-Protection: 1; mode=block' );
        // Restrict referrer information when navigating away
        header( 'Referrer-Policy: strict-origin-when-cross-origin' );
    }
}
add_action( 'send_headers', 'fashionfeet_security_headers' );

// ============================================================
// FASHIONFEET — FILTER ADMIN SETTINGS PAGE
// Dashboard → FashionFeet Settings → Filter Settings
// ============================================================

/**
 * Register the top-level admin menu.
 */
function ff_admin_menu() {
    add_menu_page(
        'FashionFeet Settings',          // Page title
        'FashionFeet',                   // Menu label
        'manage_options',                // Capability
        'fashionfeet-settings',          // Menu slug
        'ff_filter_settings_page',       // Callback
        'dashicons-store',               // Icon
        58                               // Position (below WooCommerce)
    );
    add_submenu_page(
        'fashionfeet-settings',
        'Filter Settings',
        'Filter Settings',
        'manage_options',
        'fashionfeet-settings',
        'ff_filter_settings_page'
    );
}
add_action( 'admin_menu', 'ff_admin_menu' );

/**
 * Force enable WooCommerce my account registration
 */
update_option( 'woocommerce_enable_myaccount_registration', 'yes' );

/**
 * Custom Shop Query Logic for Filters
 */
function ff_register_filter_settings() {
    register_setting( 'ff_filter_options_group', 'ff_filter_options', array(
        'sanitize_callback' => 'ff_sanitize_filter_options',
    ) );
}
add_action( 'admin_init', 'ff_register_filter_settings' );

/**
 * Sanitize saved settings.
 */
function ff_sanitize_filter_options( $input ) {
    $clean = array();

    // Section toggles (checkboxes)
    $toggles = array( 'show_category', 'show_price', 'show_size', 'show_sale_stock' );
    foreach ( $toggles as $key ) {
        $clean[ $key ] = ! empty( $input[ $key ] ) ? 1 : 0;
    }

    // Custom labels
    $labels = array( 'label_category', 'label_price', 'label_size', 'label_sale_stock' );
    foreach ( $labels as $key ) {
        $clean[ $key ] = isset( $input[ $key ] ) ? sanitize_text_field( $input[ $key ] ) : '';
    }

    // Default sort
    $allowed_sorts = array( 'menu_order', 'popularity', 'date', 'price', 'price-desc' );
    $clean['default_sort'] = in_array( $input['default_sort'] ?? '', $allowed_sorts, true )
        ? $input['default_sort']
        : 'menu_order';

    // Preset price ranges (stored as JSON-like rows)
    $clean['price_presets'] = array();
    if ( ! empty( $input['preset_label'] ) && is_array( $input['preset_label'] ) ) {
        foreach ( $input['preset_label'] as $i => $lbl ) {
            $lbl = sanitize_text_field( $lbl );
            $min = isset( $input['preset_min'][ $i ] ) ? absint( $input['preset_min'][ $i ] ) : 0;
            $max = isset( $input['preset_max'][ $i ] ) ? absint( $input['preset_max'][ $i ] ) : 0;
            if ( $lbl ) {
                $clean['price_presets'][] = array(
                    'label' => $lbl,
                    'min'   => $min,
                    'max'   => $max,
                );
            }
        }
    }

    return $clean;
}

/**
 * Helper: Get a single filter option (with sensible defaults).
 */
function ff_get_filter_option( $key, $default = null ) {
    $options = get_option( 'ff_filter_options', array() );

    $defaults = array(
        'show_category'    => 1,
        'show_price'       => 1,
        'show_size'        => 1,
        'show_sale_stock'  => 1,
        'label_category'   => 'Category',
        'label_price'      => 'Price',
        'label_size'       => 'Size',
        'label_sale_stock' => 'Sale & Offers',
        'default_sort'     => 'menu_order',
        'price_presets'    => array(),
    );

    $options = wp_parse_args( $options, $defaults );

    return $default !== null && ! isset( $options[ $key ] ) ? $default : $options[ $key ];
}

/**
 * Admin styles for the settings page.
 */
function ff_admin_settings_styles( $hook ) {
    if ( $hook !== 'toplevel_page_fashionfeet-settings' ) return;
    ?>
    <style>
        #ff-settings-wrap { max-width: 820px; }
        #ff-settings-wrap h1 { font-size: 1.6rem; font-weight: 700; margin-bottom: 4px; }
        .ff-settings-subtitle { color: #666; margin-bottom: 28px; font-size: 14px; }
        .ff-card {
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            padding: 24px 28px;
            margin-bottom: 24px;
            box-shadow: 0 1px 4px rgba(0,0,0,.06);
        }
        .ff-card h2 {
            font-size: 1rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: #111;
            border-bottom: 2px solid #111;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .ff-toggle-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #f2f2f2;
        }
        .ff-toggle-row:last-child { border-bottom: none; }
        .ff-toggle-row label { font-weight: 600; font-size: 14px; }
        .ff-toggle-row input[type="text"] {
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 5px 10px;
            font-size: 13px;
            width: 180px;
        }
        .ff-switch { position: relative; display: inline-block; width: 44px; height: 24px; }
        .ff-switch input { opacity: 0; width: 0; height: 0; }
        .ff-slider {
            position: absolute; cursor: pointer;
            top: 0; left: 0; right: 0; bottom: 0;
            background: #ccc; border-radius: 24px;
            transition: .3s;
        }
        .ff-slider:before {
            content: ""; position: absolute;
            height: 18px; width: 18px; left: 3px; bottom: 3px;
            background: #fff; border-radius: 50%; transition: .3s;
        }
        .ff-switch input:checked + .ff-slider { background: #111; }
        .ff-switch input:checked + .ff-slider:before { transform: translateX(20px); }
        .ff-preset-table { width: 100%; border-collapse: collapse; }
        .ff-preset-table th {
            text-align: left; font-size: 12px; font-weight: 700;
            text-transform: uppercase; color: #888; padding: 4px 6px;
        }
        .ff-preset-table td { padding: 6px; }
        .ff-preset-table input[type="text"],
        .ff-preset-table input[type="number"] {
            border: 1px solid #ddd; border-radius: 6px;
            padding: 6px 10px; font-size: 13px; width: 100%;
        }
        .ff-remove-row { background: none; border: none; cursor: pointer; color: #c00; font-size: 18px; line-height: 1; }
        .ff-add-row-btn {
            margin-top: 12px;
            background: #f4f4f4; border: 1px dashed #bbb;
            border-radius: 6px; padding: 7px 16px;
            font-size: 13px; cursor: pointer; font-weight: 600;
        }
        .ff-add-row-btn:hover { background: #eee; }
        .ff-save-btn {
            background: #111; color: #fff;
            border: none; border-radius: 8px;
            padding: 12px 32px; font-size: 14px;
            font-weight: 700; cursor: pointer;
            letter-spacing: .04em; transition: background .2s;
        }
        .ff-save-btn:hover { background: #333; }
        .ff-sort-select {
            border: 1px solid #ddd; border-radius: 6px;
            padding: 6px 12px; font-size: 13px;
        }
        .ff-badge {
            display: inline-block; background: #111; color: #fff;
            font-size: 11px; font-weight: 700; border-radius: 4px;
            padding: 2px 7px; margin-left: 8px; vertical-align: middle;
        }
    </style>
    <script>
    function ffAddPresetRow() {
        const tbody = document.getElementById('ff-preset-tbody');
        const i = tbody.querySelectorAll('tr').length;
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><input type="text" name="ff_filter_options[preset_label][]" placeholder="e.g. Under LKR 2000" /></td>
            <td><input type="number" name="ff_filter_options[preset_min][]" placeholder="0" min="0" /></td>
            <td><input type="number" name="ff_filter_options[preset_max][]" placeholder="2000" min="0" /></td>
            <td><button type="button" class="ff-remove-row" onclick="this.closest('tr').remove()">×</button></td>
        `;
        tbody.appendChild(tr);
    }
    </script>
    <?php
}
add_action( 'admin_head', 'ff_admin_settings_styles' );

/**
 * Render the settings page HTML.
 */
function ff_filter_settings_page() {
    if ( ! current_user_can( 'manage_options' ) ) return;

    $opts = get_option( 'ff_filter_options', array() );
    $defaults = array(
        'show_category'    => 1,
        'show_price'       => 1,
        'show_size'        => 1,
        'show_sale_stock'  => 1,
        'label_category'   => 'Category',
        'label_price'      => 'Price',
        'label_size'       => 'Size',
        'label_sale_stock' => 'Sale & Offers',
        'default_sort'     => 'menu_order',
        'price_presets'    => array(),
    );
    $o = wp_parse_args( $opts, $defaults );
    ?>
    <div class="wrap" id="ff-settings-wrap">
        <h1>🛍️ FashionFeet Settings</h1>
        <p class="ff-settings-subtitle">Shop Filter Sidebar — Admin Control Panel</p>

        <?php settings_errors(); ?>

        <form method="post" action="options.php">
            <?php settings_fields( 'ff_filter_options_group' ); ?>

            <!-- ── SECTION TOGGLES ── -->
            <div class="ff-card">
                <h2>Filter Sections <span class="ff-badge">Show / Hide</span></h2>

                <?php
                $sections = array(
                    'show_category'   => array( 'label_category',   'Category' ),
                    'show_price'      => array( 'label_price',       'Price' ),
                    'show_size'       => array( 'label_size',        'Size' ),
                    'show_sale_stock' => array( 'label_sale_stock',  'Sale & Offers' ),
                );
                foreach ( $sections as $toggle_key => $info ) :
                    [$label_key, $default_label] = $info;
                ?>
                <div class="ff-toggle-row">
                    <label><?php echo esc_html( $default_label ); ?> Section</label>
                    <div style="display:flex;align-items:center;gap:16px;">
                        <input type="text"
                               name="ff_filter_options[<?php echo esc_attr( $label_key ); ?>]"
                               value="<?php echo esc_attr( $o[ $label_key ] ); ?>"
                               placeholder="Custom label…" />
                        <label class="ff-switch">
                            <input type="checkbox"
                                   name="ff_filter_options[<?php echo esc_attr( $toggle_key ); ?>]"
                                   value="1"
                                   <?php checked( $o[ $toggle_key ], 1 ); ?> />
                            <span class="ff-slider"></span>
                        </label>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- ── PRESET PRICE RANGES ── -->
            <div class="ff-card">
                <h2>Preset Price Ranges <span class="ff-badge">Quick Select Buttons</span></h2>
                <p style="font-size:13px;color:#666;margin-bottom:14px;">
                    Filter sidebar ෙ quick-select price buttons ලෙස show වෙනවා. Max = 0 නම් "Max" limit නැහැ.
                </p>
                <table class="ff-preset-table">
                    <thead>
                        <tr>
                            <th>Label</th>
                            <th>Min Price</th>
                            <th>Max Price</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="ff-preset-tbody">
                        <?php foreach ( $o['price_presets'] as $preset ) : ?>
                        <tr>
                            <td><input type="text" name="ff_filter_options[preset_label][]"
                                       value="<?php echo esc_attr( $preset['label'] ); ?>" /></td>
                            <td><input type="number" name="ff_filter_options[preset_min][]"
                                       value="<?php echo esc_attr( $preset['min'] ); ?>" min="0" /></td>
                            <td><input type="number" name="ff_filter_options[preset_max][]"
                                       value="<?php echo esc_attr( $preset['max'] ); ?>" min="0" /></td>
                            <td><button type="button" class="ff-remove-row" onclick="this.closest('tr').remove()">×</button></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <button type="button" class="ff-add-row-btn" onclick="ffAddPresetRow()">+ Add Price Range</button>
            </div>

            <!-- ── DEFAULT SORT ── -->
            <div class="ff-card">
                <h2>Default Sort Order</h2>
                <div class="ff-toggle-row">
                    <label>Shop Page Default Sort</label>
                    <select name="ff_filter_options[default_sort]" class="ff-sort-select">
                        <option value="menu_order"  <?php selected( $o['default_sort'], 'menu_order' ); ?>>Featured</option>
                        <option value="popularity"  <?php selected( $o['default_sort'], 'popularity' ); ?>>Most Popular</option>
                        <option value="date"        <?php selected( $o['default_sort'], 'date' ); ?>>Newest</option>
                        <option value="price"       <?php selected( $o['default_sort'], 'price' ); ?>>Price: Low–High</option>
                        <option value="price-desc"  <?php selected( $o['default_sort'], 'price-desc' ); ?>>Price: High–Low</option>
                    </select>
                </div>
            </div>

            <!-- ── SAVE ── -->
            <button type="submit" class="ff-save-btn">💾 Save Settings</button>
        </form>
    </div>
    <?php
}

// ============================================================
// AUTO-CREATE POLICY PAGES (One-time on theme activation)
// ============================================================
function ff_create_policy_pages() {
    // Only run once per version
    if ( get_option( 'ff_policy_pages_created' ) === 'v2' ) return;

    $pages = array(

        // ── 1. Privacy Policy ──
        'privacy-policy' => array(
            'title'   => 'Privacy Policy',
            'content' => '
<h2>Privacy Policy</h2>
<p><em>Last Updated: ' . date('F j, Y') . '</em></p>
<p><strong>Fashion Feet</strong> ("we", "us", "our") operates this website. This Privacy Policy explains how we collect, use, disclose, and safeguard your personal information when you visit our site or make a purchase.</p>
<p>By using our site, you consent to the data practices described in this policy.</p>

<h3>1. Information We Collect</h3>
<p><strong>Information You Provide Directly:</strong></p>
<ul>
<li><strong>Account Information:</strong> Name, email address, phone number, password when you register an account.</li>
<li><strong>Order Information:</strong> Billing address, shipping address, payment details (credit/debit card numbers are processed securely by our payment provider and never stored on our servers).</li>
<li><strong>Communication Data:</strong> Any information you provide when contacting our customer support team.</li>
</ul>
<p><strong>Information Collected Automatically:</strong></p>
<ul>
<li><strong>Device &amp; Browser Data:</strong> IP address, browser type, operating system, device type.</li>
<li><strong>Usage Data:</strong> Pages viewed, time spent on pages, links clicked, referring URL.</li>
<li><strong>Cookies &amp; Tracking:</strong> We use cookies and similar technologies to enhance your browsing experience.</li>
</ul>

<h3>2. How We Use Your Information</h3>
<ul>
<li><strong>Process Orders:</strong> To fulfill purchases, process payments, arrange shipping, and send order confirmations.</li>
<li><strong>Account Management:</strong> To create and manage your account, maintain your wishlist, and provide personalized recommendations.</li>
<li><strong>Customer Support:</strong> To respond to your inquiries, resolve disputes, and troubleshoot issues.</li>
<li><strong>Communication:</strong> To send transactional emails (order updates, shipping notifications) and, with your consent, promotional emails.</li>
<li><strong>Improve Our Services:</strong> To analyze usage patterns, improve our website, and enhance your shopping experience.</li>
<li><strong>Legal Compliance:</strong> To comply with applicable laws, regulations, and legal processes.</li>
</ul>

<h3>3. How We Share Your Information</h3>
<p>We do <strong>not</strong> sell, trade, or rent your personal information. We may share data with:</p>
<ul>
<li><strong>Payment Processors:</strong> To securely process your payments.</li>
<li><strong>Shipping Partners:</strong> To deliver your orders.</li>
<li><strong>Analytics Providers:</strong> To understand how our website is used (anonymized data only).</li>
<li><strong>Legal Authorities:</strong> If required by law, court order, or to protect our rights and safety.</li>
</ul>

<h3>4. Data Security</h3>
<p>We implement appropriate security measures:</p>
<ul>
<li>SSL/TLS encryption for all data transmission.</li>
<li>Secure server infrastructure with restricted access.</li>
<li>Regular security audits and updates.</li>
<li>Payment card data is processed by PCI-DSS compliant providers and never stored on our servers.</li>
</ul>

<h3>5. Cookies</h3>
<p>Our site uses cookies for:</p>
<ul>
<li><strong>Essential:</strong> Cart functionality, login sessions, security tokens.</li>
<li><strong>Functional:</strong> Remember your preferences.</li>
<li><strong>Analytics:</strong> Understand site traffic and user behavior.</li>
</ul>
<p>You can control or disable cookies through your browser settings.</p>

<h3>6. Your Rights</h3>
<ul>
<li><strong>Access</strong> your personal data we hold about you.</li>
<li><strong>Correct</strong> inaccurate or incomplete information.</li>
<li><strong>Delete</strong> your personal data (subject to legal obligations).</li>
<li><strong>Opt-out</strong> of marketing communications at any time.</li>
<li><strong>Request a copy</strong> of your data in a portable format.</li>
</ul>

<h3>7. Data Retention</h3>
<p>We retain your information for as long as your account is active, needed to provide services, or as required by law (typically 5-7 years for financial records).</p>

<h3>8. Children\'s Privacy</h3>
<p>Our site is not directed to individuals under 18. We do not knowingly collect personal information from children.</p>

<h3>9. Changes to This Policy</h3>
<p>We may update this Privacy Policy from time to time. Changes will be posted on this page with the updated date.</p>

<h3>10. Contact Us</h3>
<p>If you have questions about this Privacy Policy:</p>
<ul>
<li><strong>Email:</strong> info@fashionfeet.lk</li>
<li><strong>Phone:</strong> +94 XX XXX XXXX</li>
</ul>
',
        ),

        // ── 2. Terms & Conditions ──
        'terms-and-conditions' => array(
            'title'   => 'Terms & Conditions',
            'content' => '
<h2>Terms &amp; Conditions</h2>
<p><em>Last Updated: ' . date('F j, Y') . '</em></p>
<p>Welcome to <strong>Fashion Feet</strong>. These Terms and Conditions govern your use of our website and your purchase of products from us.</p>
<p>By accessing or using our site, you agree to be bound by these Terms.</p>

<h3>1. General</h3>
<p>These Terms constitute the entire agreement between you and Fashion Feet. We reserve the right to modify these Terms at any time. Continued use of the site after changes constitutes acceptance.</p>

<h3>2. Eligibility</h3>
<p>You must be at least 18 years of age to use this site and make purchases. If you are under 18, you may use the site only with the consent of a parent or legal guardian.</p>

<h3>3. Account Registration</h3>
<ul>
<li>You are responsible for maintaining the confidentiality of your account credentials.</li>
<li>You agree to provide accurate, current, and complete information during registration.</li>
<li>You are responsible for all activities that occur under your account.</li>
<li>We reserve the right to suspend or terminate accounts that violate these Terms.</li>
</ul>

<h3>4. Products &amp; Pricing</h3>
<ul>
<li>All products are subject to availability.</li>
<li>We strive to display product images accurately. However, monitor settings may cause color variations.</li>
<li>Prices are listed in <strong>Sri Lankan Rupees (LKR)</strong> unless otherwise stated.</li>
<li>We reserve the right to change prices without prior notice.</li>
<li>In the event of a pricing error, we may cancel orders placed at the incorrect price.</li>
</ul>

<h3>5. Orders &amp; Payment</h3>
<ul>
<li>Placing an order constitutes an offer to purchase. We reserve the right to accept or decline any order.</li>
<li>An order is confirmed only when you receive an Order Confirmation email.</li>
<li>We accept: Cash on Delivery (COD), Credit/Debit Card, Bank Transfer, and online payment gateways.</li>
<li>All payments must be made in full before dispatch (except COD orders).</li>
</ul>

<h3>6. Order Cancellation</h3>
<ul>
<li>You may cancel an order <strong>before it has been dispatched</strong> by contacting us.</li>
<li>Once dispatched, cancellation is not possible. You may follow our Returns Policy instead.</li>
</ul>

<h3>7. Shipping &amp; Delivery</h3>
<p>Please refer to our separate <a href="/shipping-policy/">Shipping Policy</a> for details on methods, costs, and delivery times.</p>

<h3>8. Returns &amp; Refunds</h3>
<p>Please refer to our separate <a href="/refund-returns-policy/">Refunds &amp; Returns Policy</a> for details.</p>

<h3>9. Intellectual Property</h3>
<p>All content on this site, including text, graphics, logos, images, and software, is the property of Fashion Feet and is protected by copyright and intellectual property laws. You may not reproduce, distribute, or modify any content without our written consent.</p>

<h3>10. Prohibited Activities</h3>
<p>You agree not to:</p>
<ul>
<li>Use the site for any unlawful purpose.</li>
<li>Attempt to gain unauthorized access to any part of the site.</li>
<li>Upload viruses or malicious code.</li>
<li>Use automated scripts to collect information (scraping).</li>
<li>Place fraudulent orders or provide false information.</li>
</ul>

<h3>11. Limitation of Liability</h3>
<p>To the fullest extent permitted by law, Fashion Feet shall not be liable for any indirect, incidental, or consequential damages. Our total liability shall not exceed the amount paid for the specific product giving rise to the claim.</p>

<h3>12. Governing Law</h3>
<p>These Terms shall be governed by the laws of <strong>Sri Lanka</strong>. Any disputes shall be subject to the exclusive jurisdiction of the courts of Sri Lanka.</p>

<h3>13. Contact Us</h3>
<ul>
<li><strong>Email:</strong> info@fashionfeet.lk</li>
<li><strong>Phone:</strong> +94 XX XXX XXXX</li>
</ul>
',
        ),

        // ── 3. Shipping Policy ──
        'shipping-policy' => array(
            'title'   => 'Shipping Policy',
            'content' => '
<h2>Shipping Policy</h2>
<p><em>Last Updated: ' . date('F j, Y') . '</em></p>
<p>Thank you for shopping with <strong>Fashion Feet</strong>. This Shipping Policy outlines how we handle the delivery of your orders.</p>

<h3>1. Shipping Areas</h3>
<p>We currently ship <strong>island-wide across Sri Lanka</strong>. International shipping is not available at this time.</p>

<h3>2. Shipping Methods &amp; Costs</h3>
<table>
<thead><tr><th>Method</th><th>Areas</th><th>Cost</th><th>Estimated Delivery</th></tr></thead>
<tbody>
<tr><td><strong>Standard Delivery</strong></td><td>Island-wide</td><td>LKR 350</td><td>3–5 business days</td></tr>
<tr><td><strong>Express Delivery</strong></td><td>Colombo &amp; suburbs</td><td>LKR 600</td><td>1–2 business days</td></tr>
<tr><td><strong>Free Shipping</strong></td><td>Island-wide</td><td>FREE</td><td>3–5 business days</td></tr>
</tbody>
</table>
<p><strong>Free Shipping</strong> is available on all orders over <strong>LKR 5,000</strong>.</p>

<h3>3. Order Processing Time</h3>
<ul>
<li>Orders are processed within <strong>1–2 business days</strong> (Monday to Friday, excluding public holidays).</li>
<li>Orders placed after <strong>3:00 PM</strong> or on weekends/holidays will be processed the next business day.</li>
<li>You will receive an email confirmation once your order has been dispatched.</li>
</ul>

<h3>4. Delivery Times</h3>
<table>
<thead><tr><th>Destination</th><th>Standard Delivery</th><th>Express Delivery</th></tr></thead>
<tbody>
<tr><td><strong>Colombo District</strong></td><td>1–3 business days</td><td>Same day / Next day</td></tr>
<tr><td><strong>Western Province</strong></td><td>2–3 business days</td><td>1–2 business days</td></tr>
<tr><td><strong>Other Provinces</strong></td><td>3–5 business days</td><td>N/A</td></tr>
<tr><td><strong>Remote Areas</strong></td><td>5–7 business days</td><td>N/A</td></tr>
</tbody>
</table>
<p>Delivery times are estimates and may vary due to circumstances beyond our control.</p>

<h3>5. Order Tracking</h3>
<p>Once your order is dispatched, you will receive a tracking number via email and/or SMS. You can track your order through our courier partner\'s website.</p>

<h3>6. Delivery Issues</h3>
<p><strong>Delayed Delivery:</strong> If your order has not arrived within the estimated time, please contact us.</p>
<p><strong>Damaged Packages:</strong> If your package arrives damaged, please do not accept it. If already accepted, contact us within 48 hours with photos. We will arrange a replacement or full refund.</p>
<p><strong>Wrong Items:</strong> Contact us within 48 hours. We will send the correct item and collect the wrong one at our expense.</p>

<h3>7. Failed Delivery Attempts</h3>
<p>Our courier will make up to 2 delivery attempts. If unsuccessful, the package will be returned to us. We will contact you to rearrange delivery or process a refund.</p>

<h3>8. Contact Us</h3>
<ul>
<li><strong>Email:</strong> info@fashionfeet.lk</li>
<li><strong>Phone:</strong> +94 XX XXX XXXX</li>
</ul>
',
        ),

        // ── 4. Refunds & Returns Policy ──
        'refund-returns-policy' => array(
            'title'   => 'Refunds & Returns',
            'content' => '
<h2>Refunds &amp; Returns Policy</h2>
<p><em>Last Updated: ' . date('F j, Y') . '</em></p>
<p>At <strong>Fashion Feet</strong>, we want you to be completely satisfied with your purchase. If you are not happy with your order, we are here to help.</p>

<h3>1. Returns Eligibility</h3>
<p>You may return a product within <strong>14 days</strong> of delivery, provided:</p>
<ul>
<li>The item is <strong>unworn, unused, and in its original condition</strong>.</li>
<li>The item is in its <strong>original packaging</strong> with all tags and labels attached.</li>
<li>You have your <strong>proof of purchase</strong> (order confirmation email or receipt).</li>
</ul>

<h3>2. Non-Returnable Items</h3>
<p>The following items <strong>cannot</strong> be returned or exchanged:</p>
<ul>
<li>Items that have been worn, washed, or altered.</li>
<li>Sale/clearance items marked as "Final Sale".</li>
<li>Socks, insoles, and intimate/hygiene-related products.</li>
<li>Gift cards.</li>
<li>Items returned after the 14-day return window.</li>
<li>Items without original packaging and tags.</li>
</ul>

<h3>3. How to Return an Item</h3>
<ol>
<li><strong>Contact us</strong> at info@fashionfeet.lk with your order number, items to return, reason, and photos (if applicable).</li>
<li>Our team will respond within <strong>24–48 hours</strong> with a Return Authorization and instructions.</li>
<li><strong>Pack the item</strong> securely in its original packaging.</li>
<li><strong>Ship the item</strong> to the address provided in the Return Authorization.</li>
<li>Once received and inspected, we will process your refund or exchange.</li>
</ol>

<h3>4. Return Shipping Costs</h3>
<table>
<thead><tr><th>Scenario</th><th>Who Pays?</th></tr></thead>
<tbody>
<tr><td>Change of mind (wrong size, didn\'t like)</td><td>Customer pays</td></tr>
<tr><td>Defective/damaged item</td><td>Fashion Feet pays (free return)</td></tr>
<tr><td>Wrong item sent</td><td>Fashion Feet pays (free return)</td></tr>
</tbody>
</table>

<h3>5. Refund Methods</h3>
<table>
<thead><tr><th>Original Payment Method</th><th>Refund Method</th></tr></thead>
<tbody>
<tr><td>Credit/Debit Card</td><td>Refund to original card</td></tr>
<tr><td>Bank Transfer</td><td>Refund to your bank account</td></tr>
<tr><td>Cash on Delivery (COD)</td><td>Bank transfer to your account</td></tr>
</tbody>
</table>

<h3>6. Refund Timeline</h3>
<table>
<thead><tr><th>Step</th><th>Timeline</th></tr></thead>
<tbody>
<tr><td>Return received by us</td><td>1–3 business days (after shipping)</td></tr>
<tr><td>Inspection &amp; approval</td><td>1–2 business days</td></tr>
<tr><td>Refund processed</td><td>1–3 business days</td></tr>
<tr><td>Appears in your account</td><td>5–10 business days (depends on bank)</td></tr>
</tbody>
</table>

<h3>7. Exchanges</h3>
<ul>
<li>We offer <strong>size and color exchanges</strong> subject to stock availability.</li>
<li>Follow the same return process and mention your preferred replacement.</li>
<li>If the replacement is a different price, we will charge or refund the difference.</li>
<li>If the desired item is out of stock, we will issue a full refund.</li>
</ul>

<h3>8. Damaged or Defective Items</h3>
<p>If you receive a damaged or defective product:</p>
<ol>
<li>Contact us within <strong>48 hours</strong> of delivery.</li>
<li>Provide photos showing the damage/defect.</li>
<li>Do not discard the item or packaging.</li>
</ol>
<p>We will send a free replacement or issue a full refund including original shipping costs.</p>

<h3>9. Sale Items</h3>
<p>Sale items are eligible for return only if not marked as "Final Sale". Final Sale items are non-returnable and non-refundable.</p>

<h3>10. Contact Us</h3>
<ul>
<li><strong>Email:</strong> info@fashionfeet.lk</li>
<li><strong>Phone:</strong> +94 XX XXX XXXX</li>
</ul>
',
        ),
    );

    foreach ( $pages as $slug => $page_data ) {
        // Check if page already exists (including drafts/trash)
        $existing = get_page_by_path( $slug, OBJECT, 'page' );
        if ( ! $existing ) {
            // Also check trash
            $trashed = get_posts( array(
                'post_type'   => 'page',
                'name'        => $slug,
                'post_status' => array( 'draft', 'trash', 'private' ),
                'numberposts' => 1,
            ) );
            if ( ! empty( $trashed ) ) {
                // Restore and update existing page
                wp_update_post( array(
                    'ID'           => $trashed[0]->ID,
                    'post_status'  => 'publish',
                    'post_content' => $page_data['content'],
                    'post_name'    => $slug,
                ) );
            } else {
                wp_insert_post( array(
                    'post_title'   => $page_data['title'],
                    'post_name'    => $slug,
                    'post_content' => $page_data['content'],
                    'post_status'  => 'publish',
                    'post_type'    => 'page',
                    'post_author'  => 1,
                ) );
            }
        }
    }

    // Set WooCommerce Terms page
    $terms_page = get_page_by_path( 'terms-and-conditions' );
    if ( $terms_page && class_exists( 'WooCommerce' ) ) {
        update_option( 'woocommerce_terms_page_id', $terms_page->ID );
    }

    // Set WP Privacy Policy page
    $privacy_page = get_page_by_path( 'privacy-policy' );
    if ( $privacy_page ) {
        update_option( 'wp_page_for_privacy_policy', $privacy_page->ID );
    }

    // Mark as done so it doesn't run again
    update_option( 'ff_policy_pages_created', 'v2' );
}
add_action( 'init', 'ff_create_policy_pages' );
