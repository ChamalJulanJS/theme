<?php
/**
 * The Template for displaying product archives, including the main shop page
 * Fashion Feet Custom - Nike-Style Filter Sidebar
 * NOTE: get_header() / get_footer() are called by woocommerce.php router
 */

defined( 'ABSPATH' ) || exit;

// ── Admin Settings ──
$ff_show_category   = (bool) ff_get_filter_option( 'show_category', 1 );
$ff_show_price      = (bool) ff_get_filter_option( 'show_price', 1 );
$ff_show_size       = (bool) ff_get_filter_option( 'show_size', 1 );
$ff_show_sale_stock = (bool) ff_get_filter_option( 'show_sale_stock', 1 );
$ff_label_category  = ff_get_filter_option( 'label_category', 'Category' );
$ff_label_price     = ff_get_filter_option( 'label_price', 'Price' );
$ff_label_size      = ff_get_filter_option( 'label_size', 'Size' );
$ff_label_sale      = ff_get_filter_option( 'label_sale_stock', 'Sale & Offers' );
$ff_price_presets   = ff_get_filter_option( 'price_presets', array() );
$ff_default_sort    = ff_get_filter_option( 'default_sort', 'menu_order' );

// Get current filter values from URL
$current_min_price  = isset( $_GET['min_price'] ) ? floatval( $_GET['min_price'] ) : '';
$current_max_price  = isset( $_GET['max_price'] ) ? floatval( $_GET['max_price'] ) : '';
$current_on_sale    = isset( $_GET['on_sale'] ) ? 1 : 0;
$current_in_stock   = isset( $_GET['in_stock'] ) ? 1 : 0;
$current_sizes      = isset( $_GET['filter_size'] ) ? explode( ',', sanitize_text_field( $_GET['filter_size'] ) ) : array();
$current_orderby    = isset( $_GET['orderby'] ) ? sanitize_text_field( $_GET['orderby'] ) : $ff_default_sort;

// Count active filters for badge
$active_filter_count = 0;
if ( $current_min_price || $current_max_price ) $active_filter_count++;
if ( $current_on_sale ) $active_filter_count++;
if ( $current_in_stock ) $active_filter_count++;
if ( ! empty( $current_sizes ) ) $active_filter_count++;
if ( isset( $_GET['product_cat'] ) && $_GET['product_cat'] ) $active_filter_count++;

// Get all product categories
$product_categories = get_terms( array(
    'taxonomy'   => 'product_cat',
    'hide_empty' => true,
    'parent'     => 0,
    'orderby'    => 'name',
) );

// Dynamically find the size attribute taxonomy
// (works no matter what name was given in Dashboard → Products → Attributes)
$size_taxonomy  = '';
$sizes          = array();
$attribute_taxonomies = wc_get_attribute_taxonomies();

foreach ( $attribute_taxonomies as $attr ) {
    $lower = strtolower( $attr->attribute_name );
    // Match common size-related names: size, sz, shoe_size, sizes, etc.
    if ( in_array( $lower, array( 'size', 'sizes', 'sz', 'shoe_size', 'shoesize', 'shoe-size' ), true )
         || strpos( $lower, 'size' ) !== false ) {
        $size_taxonomy = wc_attribute_taxonomy_name( $attr->attribute_name );
        break;
    }
}

// If a size attribute was found, fetch its terms
if ( $size_taxonomy ) {
    $sizes = get_terms( array(
        'taxonomy'   => $size_taxonomy,
        'hide_empty' => true,   // only show sizes linked to at least 1 product
        'orderby'    => 'name',
        'order'      => 'ASC',
    ) );
}


// Build base URL (without filter params) for filter links
function ff_get_base_shop_url() {
    $remove_keys = array( 'min_price', 'max_price', 'on_sale', 'in_stock', 'filter_size', 'product_cat', 'orderby', 'paged' );
    $params = $_GET;
    foreach ( $remove_keys as $key ) {
        unset( $params[ $key ] );
    }
    $base = strtok( $_SERVER['REQUEST_URI'], '?' );
    return $base . ( ! empty( $params ) ? '?' . http_build_query( $params ) : '' );
}

$shop_base_url   = ff_get_base_shop_url();
$current_cat_slug = isset( $_GET['product_cat'] ) ? sanitize_text_field( $_GET['product_cat'] ) : '';
?>

<!-- Breadcrumb Navigation -->
<nav class="ff-breadcrumb" aria-label="Breadcrumb">
    <?php woocommerce_breadcrumb( array(
        'delimiter'   => ' <span class="ff-breadcrumb-sep">/</span> ',
        'wrap_before' => '<div class="ff-breadcrumb-inner">',
        'wrap_after'  => '</div>',
        'before'      => '<span>',
        'after'       => '</span>',
    ) ); ?>
</nav>

<!-- Mobile Filter Button -->
<div class="ff-mobile-filter-bar">
    <button class="ff-mobile-filter-btn" id="ff-filter-open-btn" aria-label="Open Filters">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="4" y1="6" x2="20" y2="6"/><line x1="8" y1="12" x2="20" y2="12"/><line x1="12" y1="18" x2="20" y2="18"/>
        </svg>
        FILTER
        <?php if ( $active_filter_count > 0 ) : ?>
            <span class="ff-filter-badge"><?php echo $active_filter_count; ?></span>
        <?php endif; ?>
    </button>
    <div class="ff-mobile-sort">
        <select id="ff-sort-select-mobile" onchange="ffApplySortFilter(this.value)">
            <option value="menu_order"   <?php selected($current_orderby, 'menu_order'); ?>>Featured</option>
            <option value="popularity"   <?php selected($current_orderby, 'popularity'); ?>>Most Popular</option>
            <option value="date"         <?php selected($current_orderby, 'date'); ?>>Newest</option>
            <option value="price"        <?php selected($current_orderby, 'price'); ?>>Price: Low–High</option>
            <option value="price-desc"   <?php selected($current_orderby, 'price-desc'); ?>>Price: High–Low</option>
        </select>
    </div>
</div>

<!-- Mobile Filter Overlay -->
<div class="ff-filter-overlay" id="ff-filter-overlay"></div>

<main class="ff-shop-main">

    <!-- ===================== FILTER SIDEBAR ===================== -->
    <aside class="ff-filter-sidebar" id="ff-filter-sidebar" role="complementary" aria-label="Product Filters">

        <!-- Sidebar Header -->
        <div class="ff-filter-sidebar-header">
            <h2 class="ff-filter-title">Filter</h2>
            <?php if ( $active_filter_count > 0 ) : ?>
                <a href="<?php echo esc_url( wc_get_page_permalink('shop') ); ?>" class="ff-clear-all-btn">Clear All</a>
            <?php endif; ?>
            <button class="ff-filter-close-btn" id="ff-filter-close-btn" aria-label="Close Filters">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>

        <form id="ff-filter-form" method="GET" action="<?php echo esc_url( wc_get_page_permalink('shop') ); ?>">

            <!-- ---- CATEGORY ---- -->
            <?php if ( $ff_show_category && ! empty( $product_categories ) && ! is_wp_error( $product_categories ) ) : ?>
            <div class="ff-filter-section ff-section-open" id="ff-section-category">
                <button type="button" class="ff-filter-section-header" aria-expanded="true" data-target="ff-body-category">
                    <span class="ff-filter-label"><?php echo esc_html( $ff_label_category ); ?></span>
                    <svg class="ff-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <polyline points="6 9 12 15 18 9"/>
                    </svg>
                </button>
                <div class="ff-filter-body" id="ff-body-category">
                    <ul class="ff-filter-list">
                        <?php foreach ( $product_categories as $cat ) :
                            $is_active = ( $current_cat_slug === $cat->slug );
                            $cat_url = add_query_arg( 'product_cat', $cat->slug, $shop_base_url );
                        ?>
                        <li>
                            <a href="<?php echo esc_url( $is_active ? $shop_base_url : $cat_url ); ?>"
                               class="ff-filter-cat-link <?php echo $is_active ? 'ff-cat-active' : ''; ?>">
                                <?php if ( $is_active ) : ?>
                                    <svg class="ff-check-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                        <polyline points="20 6 9 17 4 12"/>
                                    </svg>
                                <?php else : ?>
                                    <span class="ff-cat-circle"></span>
                                <?php endif; ?>
                                <?php echo esc_html( $cat->name ); ?>
                                <span class="ff-cat-count"><?php echo esc_html( $cat->count ); ?></span>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <?php endif; ?>

            <!-- ---- PRICE ---- -->
            <?php if ( $ff_show_price ) : ?>
            <div class="ff-filter-section ff-section-open" id="ff-section-price">
                <button type="button" class="ff-filter-section-header" aria-expanded="true" data-target="ff-body-price">
                    <span class="ff-filter-label"><?php echo esc_html( $ff_label_price ); ?></span>
                    <svg class="ff-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <polyline points="6 9 12 15 18 9"/>
                    </svg>
                </button>
                <div class="ff-filter-body" id="ff-body-price">
                    <div class="ff-price-inputs">
                        <div class="ff-price-input-wrap">
                            <span class="ff-currency-symbol"><?php echo get_woocommerce_currency_symbol(); ?></span>
                            <input type="number" name="min_price" id="ff-min-price" class="ff-price-input"
                                   placeholder="Min" min="0" value="<?php echo esc_attr( $current_min_price ); ?>" />
                        </div>
                        <span class="ff-price-divider">—</span>
                        <div class="ff-price-input-wrap">
                            <span class="ff-currency-symbol"><?php echo get_woocommerce_currency_symbol(); ?></span>
                            <input type="number" name="max_price" id="ff-max-price" class="ff-price-input"
                                   placeholder="Max" min="0" value="<?php echo esc_attr( $current_max_price ); ?>" />
                        </div>
                    </div>
                    <!-- Preset Price Ranges -->
                    <?php if ( ! empty( $ff_price_presets ) ) : ?>
                    <div class="ff-price-presets">
                        <?php foreach ( $ff_price_presets as $preset ) :
                            $p_min = $preset['min'];
                            $p_max = $preset['max'];
                            $p_url = add_query_arg( array(
                                'min_price' => $p_min ?: '',
                                'max_price' => $p_max ?: '',
                            ), $shop_base_url );
                            $is_active_preset = ( (string)$current_min_price === (string)$p_min && (string)$current_max_price === (string)$p_max );
                        ?>
                        <a href="<?php echo esc_url( $is_active_preset ? $shop_base_url : $p_url ); ?>"
                           class="ff-price-preset-btn <?php echo $is_active_preset ? 'ff-preset-active' : ''; ?>">
                            <?php echo esc_html( $preset['label'] ); ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    <button type="submit" class="ff-price-apply-btn">Apply</button>
                </div>
            </div>
            <?php endif; // show_price ?>

            <!-- ---- SIZE ---- -->
            <?php if ( $ff_show_size && ! empty( $sizes ) && ! is_wp_error( $sizes ) ) : ?>
            <div class="ff-filter-section ff-section-open" id="ff-section-size">
                <button type="button" class="ff-filter-section-header" aria-expanded="true" data-target="ff-body-size">
                    <span class="ff-filter-label"><?php echo esc_html( $ff_label_size ); ?></span>
                    <svg class="ff-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <polyline points="6 9 12 15 18 9"/>
                    </svg>
                </button>
                <div class="ff-filter-body" id="ff-body-size">
                    <div class="ff-size-grid">
                        <?php foreach ( $sizes as $size ) :
                            $is_selected = in_array( $size->slug, $current_sizes );
                        ?>
                        <button type="button"
                                class="ff-size-chip <?php echo $is_selected ? 'ff-size-active' : ''; ?>"
                                data-value="<?php echo esc_attr( $size->slug ); ?>"
                                data-label="<?php echo esc_attr( $size->name ); ?>">
                            <?php echo esc_html( $size->name ); ?>
                        </button>
                        <?php endforeach; ?>
                    </div>
                    <input type="hidden" name="filter_size" id="ff-size-hidden" value="<?php echo esc_attr( implode( ',', $current_sizes ) ); ?>" />
                </div>
            </div>
            <?php endif; ?>

            <!-- ---- SALE & STOCK ---- -->
            <?php if ( $ff_show_sale_stock ) : ?>
            <div class="ff-filter-section ff-section-open" id="ff-section-offers">
                <button type="button" class="ff-filter-section-header" aria-expanded="true" data-target="ff-body-offers">
                    <span class="ff-filter-label"><?php echo esc_html( $ff_label_sale ); ?></span>
                    <svg class="ff-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <polyline points="6 9 12 15 18 9"/>
                    </svg>
                </button>
                <div class="ff-filter-body" id="ff-body-offers">
                    <label class="ff-toggle-row">
                        <span class="ff-toggle-label">On Sale</span>
                        <div class="ff-toggle-switch">
                            <input type="checkbox" name="on_sale" id="ff-on-sale" value="1" <?php checked( $current_on_sale, 1 ); ?> onchange="this.form.submit()">
                            <span class="ff-toggle-slider"></span>
                        </div>
                    </label>
                    <label class="ff-toggle-row">
                        <span class="ff-toggle-label">In Stock Only</span>
                        <div class="ff-toggle-switch">
                            <input type="checkbox" name="in_stock" id="ff-in-stock" value="1" <?php checked( $current_in_stock, 1 ); ?> onchange="this.form.submit()">
                            <span class="ff-toggle-slider"></span>
                        </div>
                    </label>
                </div>
            </div>
            <?php endif; // show_sale_stock ?>

            <!-- Hidden: preserve category if set -->
            <?php if ( $current_cat_slug ) : ?>
                <input type="hidden" name="product_cat" value="<?php echo esc_attr( $current_cat_slug ); ?>" />
            <?php endif; ?>

            <!-- Mobile Apply Button -->
            <div class="ff-mobile-apply-wrap">
                <button type="submit" class="ff-mobile-apply-btn">
                    Show Results <?php if ( have_posts() ) { echo '(' . $wp_query->found_posts . ')'; } ?>
                </button>
            </div>

        </form>
    </aside>
    <!-- ================== END FILTER SIDEBAR ==================== -->

    <!-- ===================== PRODUCTS AREA ===================== -->
    <div class="ff-shop-products" id="ff-shop-products">

        <!-- Desktop Top Bar -->
        <div class="ff-shop-topbar">
            <div class="ff-shop-topbar-left">
                <?php if ( have_posts() ) : ?>
                    <span class="ff-results-count">
                        <?php echo $wp_query->found_posts; ?> Results
                    </span>
                <?php endif; ?>

                <!-- Active Filter Tags -->
                <?php if ( $active_filter_count > 0 ) : ?>
                <div class="ff-active-tags">
                    <?php if ( $current_cat_slug ) :
                        $cat_obj = get_term_by( 'slug', $current_cat_slug, 'product_cat' );
                        if ( $cat_obj ) :
                            $rm_url = remove_query_arg( 'product_cat' );
                        ?>
                        <a href="<?php echo esc_url( $rm_url ); ?>" class="ff-active-tag"><?php echo esc_html( $cat_obj->name ); ?> ×</a>
                    <?php endif; endif; ?>
                    <?php if ( $current_on_sale ) : ?>
                        <a href="<?php echo esc_url( remove_query_arg( 'on_sale' ) ); ?>" class="ff-active-tag">Sale ×</a>
                    <?php endif; ?>
                    <?php if ( $current_in_stock ) : ?>
                        <a href="<?php echo esc_url( remove_query_arg( 'in_stock' ) ); ?>" class="ff-active-tag">In Stock ×</a>
                    <?php endif; ?>
                    <?php if ( $current_min_price || $current_max_price ) : ?>
                        <a href="<?php echo esc_url( remove_query_arg( array('min_price','max_price') ) ); ?>" class="ff-active-tag">
                            Price: <?php echo $current_min_price ?: '0'; ?>–<?php echo $current_max_price ?: '∞'; ?> ×
                        </a>
                    <?php endif; ?>
                    <?php if ( ! empty( $current_sizes ) ) : ?>
                        <a href="<?php echo esc_url( remove_query_arg( 'filter_size' ) ); ?>" class="ff-active-tag">
                            Size: <?php echo esc_html( implode( ', ', $current_sizes ) ); ?> ×
                        </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>

            <div class="ff-shop-topbar-right">
                <label class="ff-sort-label" for="ff-sort-select">Sort By</label>
                <select id="ff-sort-select" class="ff-sort-select" onchange="ffApplySortFilter(this.value)">
                    <option value="menu_order"   <?php selected($current_orderby, 'menu_order'); ?>>Featured</option>
                    <option value="popularity"   <?php selected($current_orderby, 'popularity'); ?>>Most Popular</option>
                    <option value="date"         <?php selected($current_orderby, 'date'); ?>>Newest</option>
                    <option value="price"        <?php selected($current_orderby, 'price'); ?>>Price: Low–High</option>
                    <option value="price-desc"   <?php selected($current_orderby, 'price-desc'); ?>>Price: High–Low</option>
                </select>
            </div>
        </div>

        <!-- Product Grid -->
        <?php if ( woocommerce_product_loop() ) : ?>
            <?php woocommerce_product_loop_start(); ?>
            <?php while ( have_posts() ) : the_post(); ?>
                <?php do_action( 'woocommerce_shop_loop' ); ?>
                <?php wc_get_template_part( 'content', 'product' ); ?>
            <?php endwhile; ?>
            <?php woocommerce_product_loop_end(); ?>

            <!-- Pagination -->
            <div class="ff-pagination-wrap">
                <?php do_action( 'woocommerce_after_shop_loop' ); ?>
            </div>
        <?php else : ?>
            <div class="ff-no-products">
    <div class="ff-no-products-icon">
        <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <circle cx="11" cy="11" r="8"/>
            <line x1="21" y1="21" x2="16.65" y2="16.65" stroke-linecap="round"/>
            <line x1="8" y1="11" x2="14" y2="11" stroke-linecap="round"/>
        </svg>
    </div>
    <h3 class="ff-no-products-title">No Products Found</h3>
    <p class="ff-no-products-desc">
        <?php if ( $active_filter_count > 0 ) : ?>
            Sorry, we couldn't find any products matching your current filters. Try adjusting your selection or browse our full collection.
        <?php else : ?>
            This category is empty right now. Check back soon for new arrivals or explore our other collections.
        <?php endif; ?>
    </p>
    <div class="ff-no-products-actions">
        <?php if ( $active_filter_count > 0 ) : ?>
            <a href="<?php echo esc_url( wc_get_page_permalink('shop') ); ?>" class="ff-reset-link ff-btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="1 4 1 10 7 10"/>
                    <path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/>
                </svg>
                Clear All Filters
            </a>
        <?php endif; ?>
        <a href="<?php echo esc_url( wc_get_page_permalink('shop') ); ?>" class="ff-browse-link ff-btn-outline">
            Browse Shop
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="5" y1="12" x2="19" y2="12" stroke-linecap="round"/>
                <polyline points="12 5 19 12 12 19" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </a>
    </div>
    <div class="ff-no-products-suggest">
        <p>Popular searches:</p>
        <div class="ff-suggest-tags">
            <a href="<?php echo esc_url( wc_get_page_permalink('shop') ); ?>?product_cat=men">Men's Sneakers</a>
            <a href="<?php echo esc_url( wc_get_page_permalink('shop') ); ?>?product_cat=women">Women's Heels</a>
            <a href="<?php echo esc_url( wc_get_page_permalink('shop') ); ?>?product_cat=accessories">Accessories</a>
        </div>
    </div>
</div>
        <?php endif; ?>

    </div>
    <!-- ================ END PRODUCTS AREA ================== -->

</main>
