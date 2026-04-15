<?php
/**
 * The template for displaying product content within loops
 */

defined( 'ABSPATH' ) || exit;

global $product;

// Ensure visibility.
if ( empty( $product ) || ! $product->is_visible() ) {
    return;
}
?>
<li <?php wc_product_class( 'group relative overflow-hidden rounded-xl shadow-sm hover:shadow-2xl transition-all duration-500 bg-white mb-8 border border-gray-100 gsap-stagger-item', $product ); ?>>

    <?php do_action( 'woocommerce_before_shop_loop_item' ); ?>

    <a href="<?php echo esc_url( apply_filters( 'woocommerce_loop_product_link', get_the_permalink(), $product ) ); ?>" class="block relative overflow-hidden bg-gray-100 w-full" style="aspect-ratio: 1 / 1;">
        
        <?php
        // Out of stock badge or Sale badge
        if ( ! $product->is_in_stock() ) {
            echo '<span class="absolute top-4 left-4 z-20 bg-dark text-white text-xs font-bold px-3 py-1 uppercase tracking-widest rounded-full shadow-md">Sold Out</span>';
        } elseif ( $product->is_on_sale() ) {
            echo '<span class="absolute top-4 left-4 z-20 bg-premium-500 text-white text-xs font-bold px-3 py-1 uppercase tracking-widest rounded-full shadow-md">Sale</span>';
        }
        ?>

        <!-- Wishlist Heart Button -->
        <?php $in_wishlist = ff_is_in_wishlist( $product->get_id() ); ?>
        <button type="button" class="ff-heart-btn <?php echo $in_wishlist ? 'ff-hearted' : ''; ?>" 
                onclick="event.preventDefault(); event.stopPropagation(); ffToggleHeart(<?php echo $product->get_id(); ?>, this);"
                aria-label="<?php echo $in_wishlist ? 'Remove from wishlist' : 'Add to wishlist'; ?>">
            <svg class="ff-heart-icon" width="20" height="20" viewBox="0 0 24 24" fill="<?php echo $in_wishlist ? 'currentColor' : 'none'; ?>" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
        </button>

        <!-- Primary Image -->

        <?php
        $thumbnail = $product->get_image( 'woocommerce_thumbnail', array( 'class' => 'absolute inset-0 w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700 ease-in-out z-10' ) );
        echo $thumbnail;
        
        // Secondary Image on hover (if gallery has images)
        $attachment_ids = $product->get_gallery_image_ids();
        if ( $attachment_ids ) {
            $secondary_image_id = $attachment_ids[0];
            echo wp_get_attachment_image( $secondary_image_id, 'woocommerce_thumbnail', false, array( 'class' => 'absolute inset-0 w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700 ease-in-out opacity-0 group-hover:opacity-100 z-15' ) );
        }
        ?>

        <!-- Hover Overlay -->
        <div class="absolute inset-x-0 bottom-0 z-20 p-6 bg-gradient-to-t from-black/60 to-transparent translate-y-4 group-hover:translate-y-0 opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-end">
             <span class="text-white text-sm font-bold uppercase tracking-widest flex items-center">
                 Quick View <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
             </span>
        </div>
    </a>

    <div class="p-6">
        <h2 class="text-lg font-black text-dark tracking-tight uppercase mb-2">
            <a href="<?php echo esc_url( get_the_permalink() ); ?>" class="hover:text-premium-500 transition-colors">
                <?php echo get_the_title(); ?>
            </a>
        </h2>
        
        <!-- Category tags if needed -->
        <div class="text-xs text-gray-400 uppercase tracking-widest mb-4">
            <?php echo wc_get_product_category_list( $product->get_id(), ', ' ); ?>
        </div>
        
        <div class="mb-4">
            <div class="font-bold text-dark text-xl group-hover:text-premium-500 transition-colors">
                <?php echo $product->get_price_html(); ?>
            </div>
        </div>
        
        <div class="mt-4 pt-4 border-t border-gray-100">
            <!-- Injecting inline styles to ensure the WooCommerce button looks premium regardless of compiler state -->
            <style>
                .premium-cart-btn a.button {
                    display: block;
                    width: 100%;
                    text-align: center;
                    background-color: #111111;
                    color: #ffffff;
                    padding: 12px 20px;
                    border-radius: 9999px; /* Pill shape */
                    font-size: 0.75rem;
                    font-weight: 700;
                    text-transform: uppercase;
                    letter-spacing: 0.1em;
                    transition: all 0.3s ease;
                }
                .premium-cart-btn a.button:hover {
                    background-color: #c5a059; /* premium-500 */
                    transform: translateY(-2px);
                    box-shadow: 0 10px 15px -3px rgba(197, 160, 89, 0.3);
                }
            </style>
            <div class="premium-cart-btn">
                <?php do_action( 'woocommerce_after_shop_loop_item' ); ?>
            </div>
        </div>
    </div>
</li>
