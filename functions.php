<?php

// Theme Setup
function fashionfeet_setup() {
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
    ob_start();
    ?>
    <a class="cart-customlocation relative flex items-center gap-2 group" href="<?php echo esc_url( wc_get_cart_url() ); ?>" title="<?php _e( 'View your shopping cart' ); ?>">
        <svg class="w-6 h-6 text-dark group-hover:text-premium-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
        <?php if ( WC()->cart->get_cart_contents_count() > 0 ) : ?>
            <span class="absolute -top-2 -right-2 bg-premium-500 text-white text-[10px] font-bold h-5 w-5 rounded-full flex items-center justify-center"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
        <?php endif; ?>
    </a>
    <?php
    $fragments['a.cart-customlocation'] = ob_get_clean();
    return $fragments;
}

// --------------------------------------------------------------------------------
// [SECURITY] 1. Hide WordPress Version (Prevent Vulnerability Scanning)
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
 * Register all settings fields.
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
