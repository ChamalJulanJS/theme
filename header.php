<!DOCTYPE html>
<html <?php language_attributes(); ?> class="scroll-smooth">
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class('bg-gray-50 flex flex-col min-h-screen text-dark font-sans'); ?> x-data="{ mobileMenuOpen: false, cartOpen: false }" :class="{'overflow-hidden': mobileMenuOpen || cartOpen}">
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
            // Get top-level items from 'Primary Mega Menu' (Appearance → Menus)
            $ff_mega_raw   = wp_get_nav_menu_items( get_nav_menu_locations()['primary-mega'] ?? 0 );
            $ff_top_items  = [];
            if ( $ff_mega_raw ) {
                foreach ( $ff_mega_raw as $mi ) {
                    if ( (int) $mi->menu_item_parent === 0 ) {
                        $ff_top_items[] = $mi;
                    }
                }
            }
            // Fallback: use top-level WooCommerce categories if no menu assigned
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
                    // Build item list: from WP menu OR from WC categories fallback
                    $ff_render_items = [];
                    if ( ! empty( $ff_top_items ) ) {
                        foreach ( $ff_top_items as $mi ) {
                            // Extract product_cat slug from URL query string
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

                        // Load WC category object + children
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

                        // Smart split: single column if ≤5 cats, two columns if >5
                        $total_cats = count( $child_cats );
                        if ( $total_cats <= 5 ) {
                            $col1_cats = $child_cats; // all in one column
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

                                <!-- Col 1: Category intro -->
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

                                <!-- Col 2: Child categories (first half) -->
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

                                <!-- Col 3: Child categories (second half) -->
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

                                <!-- Col 4: Category promo image -->
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
                <button type="button" class="text-dark hover:text-premium-500 transition-colors hidden sm:block">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </button>

                <?php if ( class_exists( 'WooCommerce' ) ) : ?>
                <div class="header-cart border-l border-gray-200 pl-6 hidden sm:block cursor-pointer" @click.prevent="cartOpen = true">
                    <a class="cart-customlocation relative flex items-center gap-2 group" href="<?php echo esc_url( wc_get_cart_url() ); ?>" title="<?php _e( 'View your shopping cart', 'fashionfeet' ); ?>">
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

                        <div class="mt-8">
                            <div class="flow-root">
                                <div class="text-center py-10 text-gray-400 text-sm font-medium">
                                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                    Your cart is currently empty.
                                </div>
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
                </nav>
            </div>
        </div>
    </div>
</div>

<div class="flex-grow">
