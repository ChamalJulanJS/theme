<!DOCTYPE html>
<html <?php language_attributes(); ?> class="scroll-smooth">
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class('bg-gray-50 flex flex-col min-h-screen text-dark font-sans'); ?> x-data="{ mobileMenuOpen: false, cartOpen: false, searchOpen: false, searchQuery: '', searchResults: [], searchLoading: false }" :class="{'overflow-hidden': mobileMenuOpen || cartOpen || searchOpen}">
<?php wp_body_open(); ?>

<header class="sticky top-0 z-[100] bg-white/80 backdrop-blur-2xl border-b border-gray-100 shadow-sm w-full transition-all duration-300 group/header relative">
    <div class="container-premium py-5">
        <div class="flex items-center justify-between">
            
            <div class="site-logo flex-shrink-0 relative z-30">
                <?php 
                if ( has_custom_logo() ) {
                    the_custom_logo();
                } else {
                    echo '<a href="' . esc_url( home_url( '/' ) ) . '" class="text-3xl font-black uppercase tracking-[0.2em] text-dark hover:text-premium-500 transition-colors">' . get_bloginfo( 'name' ) . '</a>';
                }
                ?>
            </div>

            <?php
            // ── Dynamic Mega Menu ──────────────────────────────────────────
            $ff_mega_raw   = wp_get_nav_menu_items( get_nav_menu_locations()['primary-mega'] ?? 0 );
            $ff_top_items  = [];
            if ( $ff_mega_raw ) {
                foreach ( $ff_mega_raw as $mi ) {
                    if ( (int) $mi->menu_item_parent === 0 ) {
                        $ff_top_items[] = $mi;
                    }
                }
            }
            if ( empty( $ff_top_items ) ) {
                $ff_fallback_cats = get_terms( [
                    'taxonomy'   => 'product_cat',
                    'parent'     => 0,
                    'hide_empty' => true,
                    'orderby'    => 'name',
                ] );
            } else {
                $ff_fallback_cats = [];
            }
            ?>

            <nav class="hidden lg:flex flex-1 justify-center z-20 static">
                <ul class="flex items-center gap-10 font-bold text-xs uppercase tracking-[0.15em] text-gray-800 static">

                    <?php
                    $ff_render_items = [];
                    if ( ! empty( $ff_top_items ) ) {
                        foreach ( $ff_top_items as $mi ) {
                            $cat_slug = '';
                            $parsed   = parse_url( $mi->url );
                            if ( ! empty( $parsed['query'] ) ) {
                                parse_str( $parsed['query'], $qvars );
                                $cat_slug = $qvars['product_cat'] ?? '';
                            }
                            $ff_render_items[] = [
                                'title'    => $mi->title,
                                'url'      => $mi->url,
                                'cat_slug' => $cat_slug,
                            ];
                        }
                    } elseif ( ! empty( $ff_fallback_cats ) ) {
                        foreach ( $ff_fallback_cats as $fcat ) {
                            $ff_render_items[] = [
                                'title'    => $fcat->name,
                                'url'      => get_term_link( $fcat ),
                                'cat_slug' => $fcat->slug,
                            ];
                        }
                    }

                    foreach ( $ff_render_items as $ritem ) :
                        $cat_slug  = $ritem['cat_slug'];
                        $item_url  = $ritem['url'];
                        $item_name = $ritem['title'];

                        $cat_obj    = $cat_slug ? get_term_by( 'slug', $cat_slug, 'product_cat' ) : null;
                        $child_cats = [];
                        $cat_desc   = '';
                        $thumb_url  = '';

                        if ( $cat_obj && ! is_wp_error( $cat_obj ) ) {
                            $child_cats = get_terms( [
                                'taxonomy'   => 'product_cat',
                                'parent'     => $cat_obj->term_id,
                                'hide_empty' => false,
                                'orderby'    => 'name',
                            ] );
                            $cat_desc  = $cat_obj->description;
                            $thumb_id  = get_term_meta( $cat_obj->term_id, 'thumbnail_id', true );
                            $thumb_url = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'medium_large' ) : '';
                        }

                        $total_cats = count( $child_cats );
                        if ( $total_cats <= 5 ) {
                            $col1_cats = $child_cats; 
                            $col2_cats = [];
                        } else {
                            $half      = (int) ceil( $total_cats / 2 );
                            $col1_cats = array_slice( $child_cats, 0, $half );
                            $col2_cats = array_slice( $child_cats, $half );
                        }
                    ?>

                    <li class="group/menuitem py-6 static">
                        <a href="<?php echo esc_url( $item_url ); ?>" class="nav-link-premium hover:text-premium-500 transition-colors py-2 flex items-center gap-1">
                            <?php echo esc_html( $item_name ); ?>
                        </a>

                        <div class="absolute top-full left-0 w-full bg-white opacity-0 invisible -translate-y-2 group-hover/menuitem:opacity-100 group-hover/menuitem:visible group-hover/menuitem:translate-y-0 transition-all duration-500 overflow-hidden border-t border-gray-100 shadow-[0_20px_40px_-15px_rgba(0,0,0,0.1)]">
                            <div class="container-premium flex mx-auto py-12 gap-12">

                                <div class="w-1/4 pr-8 border-r border-gray-100 flex flex-col justify-center">
                                    <h3 class="text-3xl font-black text-dark tracking-tight uppercase mb-4">
                                        <?php echo esc_html( $cat_obj ? $cat_obj->name : $item_name ); ?>
                                    </h3>
                                    <?php if ( $cat_desc ) : ?>
                                    <p class="text-sm text-gray-500 font-normal leading-relaxed mb-8 capitalize tracking-normal">
                                        <?php echo esc_html( wp_strip_all_tags( $cat_desc ) ); ?>
                                    </p>
                                    <?php else : ?>
                                    <p class="text-sm text-gray-500 font-normal leading-relaxed mb-8 capitalize tracking-normal">
                                        Shop our curated collection of premium <?php echo esc_html( strtolower( $item_name ) ); ?>.
                                    </p>
                                    <?php endif; ?>
                                    <a href="<?php echo esc_url( $item_url ); ?>" class="inline-flex pb-1 border-b-2 border-dark items-center text-dark hover:text-premium-500 hover:border-premium-500 transition-colors font-bold uppercase tracking-widest text-xs">
                                        Explore All &rarr;
                                    </a>
                                </div>

                                <?php if ( ! empty( $col1_cats ) ) : ?>
                                <div class="w-1/4">
                                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-6">Categories</h4>
                                    <ul class="flex flex-col gap-4 text-sm font-semibold text-gray-800 tracking-normal capitalize">
                                        <?php foreach ( $col1_cats as $child ) : ?>
                                        <li>
                                            <a href="<?php echo esc_url( get_term_link( $child ) ); ?>" class="hover:text-premium-500 transition-colors block">
                                                <?php echo esc_html( $child->name ); ?>
                                            </a>
                                        </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                                <?php else : ?>
                                <div class="w-1/4">
                                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-6">Categories</h4>
                                    <p class="text-sm text-gray-400">Add sub-categories in<br>Products &rarr; Categories</p>
                                </div>
                                <?php endif; ?>

                                <?php if ( ! empty( $col2_cats ) ) : ?>
                                <div class="w-1/4">
                                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-6">More Styles</h4>
                                    <ul class="flex flex-col gap-4 text-sm font-semibold text-gray-800 tracking-normal capitalize">
                                        <?php foreach ( $col2_cats as $child ) : ?>
                                        <li>
                                            <a href="<?php echo esc_url( get_term_link( $child ) ); ?>" class="hover:text-premium-500 transition-colors block">
                                                <?php echo esc_html( $child->name ); ?>
                                            </a>
                                        </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                                <?php else : ?>
                                <div class="w-1/4"></div>
                                <?php endif; ?>

                                <div class="w-1/4 relative group/img overflow-hidden rounded-xl">
                                    <?php if ( $thumb_url ) : ?>
                                    <img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php echo esc_attr( $item_name ); ?>" class="w-full h-full object-cover rounded-xl group-hover/img:scale-105 transition-transform duration-700">
                                    <div class="absolute inset-0 bg-dark/20 group-hover/img:bg-transparent transition-colors duration-500 rounded-xl"></div>
                                    <div class="absolute bottom-6 left-6 text-white text-xl font-bold uppercase tracking-wider drop-shadow-lg">Editor's Pick</div>
                                    <?php else : ?>
                                    <div class="w-full h-48 bg-gray-100 rounded-xl flex flex-col items-center justify-center gap-2">
                                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <span class="text-xs text-gray-400">Set category image</span>
                                    </div>
                                    <?php endif; ?>
                                </div>

                            </div>
                        </div>
                    </li>

                    <?php endforeach; ?>
                </ul>
            </nav>

            <div class="flex items-center gap-6 relative z-30">
                <button type="button" class="text-dark hover:text-premium-500 transition-colors hidden sm:block" @click="searchOpen = true" aria-label="Search">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </button>

                <?php if ( class_exists( 'WooCommerce' ) ) : ?>
                <a href="<?php echo esc_url( get_permalink( get_option('woocommerce_myaccount_page_id') ) ); ?>" class="text-dark hover:text-premium-500 transition-colors hidden sm:block" title="<?php _e( 'My Account', 'fashionfeet' ); ?>">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </a>
                <?php endif; ?>

                <?php if ( class_exists( 'WooCommerce' ) ) : ?>
                <a href="<?php echo esc_url( home_url('/wishlist/') ); ?>" class="text-dark hover:text-premium-500 transition-colors hidden sm:block relative" title="Wishlist">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                    <?php $wl_count = count( ff_get_wishlist() ); if ( $wl_count > 0 ) : ?>
                        <span class="ff-wishlist-badge absolute -top-2 -right-3 bg-premium-500 text-white text-[10px] font-bold h-5 min-w-[20px] px-1 rounded-full flex items-center justify-center shadow-md border-2 border-white"><?php echo $wl_count; ?></span>
                    <?php endif; ?>
                </a>
                <?php endif; ?>

                <?php if ( class_exists( 'WooCommerce' ) ) : ?>
                <div class="header-cart border-l border-gray-200 pl-6 hidden sm:block cursor-pointer">
                    <a class="cart-customlocation relative flex items-center gap-2 group" href="<?php echo esc_url( wc_get_cart_url() ); ?>" @click.prevent="cartOpen = true" title="<?php _e( 'View your shopping cart', 'fashionfeet' ); ?>">
                        <svg class="w-6 h-6 text-dark group-hover:text-premium-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        <?php if ( WC()->cart->get_cart_contents_count() > 0 ) : ?>
                            <span class="absolute -top-2 -right-3 bg-premium-500 text-white text-[10px] font-bold h-5 min-w-[20px] px-1 rounded-full flex items-center justify-center shadow-md border-2 border-white"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
                        <?php endif; ?>
                    </a>
                </div>
                <?php endif; ?>
                
                <button type="button" @click="mobileMenuOpen = true" class="lg:hidden text-dark hover:text-premium-500 focus:outline-none ml-4 relative z-[110]">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
            </div>
            
        </div>
    </div>
</header>

<div x-cloak x-show="cartOpen" class="relative z-[200]" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
    <div x-show="cartOpen" 
         x-transition:enter="ease-in-out duration-500" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" 
         x-transition:leave="ease-in-out duration-500" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0"
         @click="cartOpen = false"
         class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity backdrop-blur-sm"></div>

    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute inset-y-0 right-0 max-w-full flex pointer-events-none">
            <div x-show="cartOpen"
                 x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700" 
                 x-transition:enter-start="translate-x-full" 
                 x-transition:enter-end="translate-x-0" 
                 x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700" 
                 x-transition:leave-start="translate-x-0" 
                 x-transition:leave-end="translate-x-full"
                 @click.away="cartOpen = false"
                 class="w-screen max-w-md pointer-events-auto">
                <div class="h-full flex flex-col bg-white shadow-xl overflow-y-scroll border-l border-gray-100">
                    <div class="flex-1 py-10 px-4 sm:px-8">
                        <div class="flex items-start justify-between border-b border-gray-100 pb-6">
                            <h2 class="text-2xl font-black text-dark tracking-tight uppercase" id="slide-over-title">Shopping Cart</h2>
                            <div class="ml-3 h-7 flex items-center">
                                <button type="button" @click="cartOpen = false" class="bg-white rounded-md text-gray-400 hover:text-dark focus:outline-none transition-colors">
                                    <span class="sr-only">Close panel</span>
                                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>
                        </div>

                        <div class="mt-8 flex-1">
                            <div class="flow-root">
                                <?php if ( class_exists('WooCommerce') ) : ?>
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
                                            <p class="text-sm text-gray-400">Your cart is currently empty.</p>
                                            <a href="<?php echo esc_url( wc_get_page_permalink('shop') ); ?>" class="ff-mini-cart-btn ff-mini-cart-btn-primary" style="margin-top:16px;">Continue Shopping</a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div x-cloak x-show="mobileMenuOpen" class="relative z-[200] lg:hidden" role="dialog" aria-modal="true">
    <div x-show="mobileMenuOpen" 
         x-transition:enter="ease-in-out duration-300" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" 
         x-transition:leave="ease-in-out duration-300" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0"
         @click="mobileMenuOpen = false"
         class="fixed inset-0 bg-gray-900 bg-opacity-75 backdrop-blur-sm"></div>

    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute inset-y-0 left-0 max-w-full flex pointer-events-none w-full sm:w-80">
            <div x-show="mobileMenuOpen"
                 x-transition:enter="transform transition ease-in-out duration-300" 
                 x-transition:enter-start="-translate-x-full" 
                 x-transition:enter-end="translate-x-0" 
                 x-transition:leave="transform transition ease-in-out duration-300" 
                 x-transition:leave-start="translate-x-0" 
                 x-transition:leave-end="-translate-x-full"
                 @click.away="mobileMenuOpen = false"
                 class="w-full pointer-events-auto h-full flex flex-col bg-white shadow-xl overflow-y-scroll">
                
                <div class="flex items-center justify-between p-6 border-b border-gray-100">
                    <div class="text-xl font-black uppercase tracking-[0.2em] text-dark"><?php bloginfo('name'); ?></div>
                    <button type="button" @click="mobileMenuOpen = false" class="text-gray-400 hover:text-dark transition-colors">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <nav class="flex-1 px-2 py-6 space-y-4">
                    <?php foreach ( $ff_render_items as $ritem ) : ?>
                    <a href="<?php echo esc_url( $ritem['url'] ); ?>" class="block px-4 py-3 text-sm font-bold uppercase tracking-widest text-dark hover:bg-gray-50 hover:text-premium-500 rounded-lg">
                        <?php echo esc_html( $ritem['title'] ); ?>
                    </a>
                    <?php endforeach; ?>

                    <?php if ( class_exists( 'WooCommerce' ) ) : ?>
                    <div class="pt-4 mt-4 border-t border-gray-100">
                        <a href="<?php echo esc_url( get_permalink( get_option('woocommerce_myaccount_page_id') ) ); ?>" class="px-4 py-3 text-sm font-bold uppercase tracking-widest text-dark hover:bg-gray-50 hover:text-premium-500 rounded-lg flex items-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            <?php echo is_user_logged_in() ? 'My Account' : 'Login / Register'; ?>
                        </a>
                    </div>
                    <?php endif; ?>

                </nav>
            </div>
        </div>
    </div>
</div>

<!-- ═══ SEARCH MODAL ═══ -->
<div x-cloak x-show="searchOpen" class="fixed inset-0 z-[300]" role="dialog" aria-modal="true">
    <div x-show="searchOpen"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="searchOpen = false; searchQuery = ''; searchResults = [];"
         class="fixed inset-0 bg-dark/80 backdrop-blur-md"></div>

    <div class="fixed inset-0 flex items-start justify-center pt-[15vh] px-4 pointer-events-none">
        <div x-show="searchOpen"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-8 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 -translate-y-8 scale-95"
             @click.away="searchOpen = false; searchQuery = ''; searchResults = [];"
             @keydown.escape.window="searchOpen = false; searchQuery = ''; searchResults = [];"
             x-init="$watch('searchOpen', val => { if(val) $nextTick(() => $refs.searchInput.focus()) })"
             class="w-full max-w-2xl pointer-events-auto">

            <!-- Search Input -->
            <div class="ff-search-input-wrap">
                <svg class="ff-search-icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input x-ref="searchInput" type="text" x-model="searchQuery" placeholder="Search products..." class="ff-search-input"
                    @input.debounce.350ms="
                        if(searchQuery.length >= 2) {
                            searchLoading = true;
                            fetch(ffAjax.url + '?action=ff_live_search&nonce=' + ffAjax.nonce + '&q=' + encodeURIComponent(searchQuery))
                                .then(r => r.json())
                                .then(d => { searchResults = d.data.results; searchLoading = false; })
                                .catch(() => { searchLoading = false; });
                        } else { searchResults = []; }
                    "
                    @keydown.enter="if(searchQuery.length >= 2) window.location.href = '<?php echo esc_url( home_url('/') ); ?>?s=' + encodeURIComponent(searchQuery) + '&post_type=product';"
                >
                <button @click="searchOpen = false; searchQuery = ''; searchResults = [];" class="ff-search-close">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Loading -->
            <div x-show="searchLoading" class="ff-search-loading">Searching...</div>

            <!-- Results -->
            <div x-show="searchResults.length > 0 && !searchLoading" class="ff-search-results">
                <template x-for="item in searchResults" :key="item.id">
                    <a :href="item.url" class="ff-search-result-item">
                        <div class="ff-search-result-thumb">
                            <img :src="item.image || '<?php echo get_template_directory_uri(); ?>/images/placeholder.png'" :alt="item.title" />
                        </div>
                        <div class="ff-search-result-info">
                            <h4 x-text="item.title"></h4>
                            <span x-html="item.price"></span>
                        </div>
                    </a>
                </template>
                <a :href="'<?php echo esc_url( home_url('/') ); ?>?s=' + encodeURIComponent(searchQuery) + '&post_type=product'" class="ff-search-view-all">View all results &rarr;</a>
            </div>

            <!-- No results -->
            <div x-show="searchQuery.length >= 2 && searchResults.length === 0 && !searchLoading" class="ff-search-no-results">
                <p>No products found for "<span x-text="searchQuery"></span>"</p>
            </div>

            <p class="text-center text-gray-400 text-xs mt-4 tracking-wide">Press ESC or click outside to close</p>
        </div>
    </div>
</div>

<div class="flex-grow">