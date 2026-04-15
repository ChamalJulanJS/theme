<?php
/**
 * My Account navigation
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/navigation.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_account_navigation' );
?>

<nav class="woocommerce-MyAccount-navigation mt-6 overflow-x-auto overflow-y-hidden whitespace-nowrap pb-1 no-scrollbar w-full">
	<ul class="flex items-center gap-6 md:gap-10 m-0 p-0 list-none">
		<?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
			<li class="<?php echo wc_get_account_menu_item_classes( $endpoint ); ?> group inline-block">
                <a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>" class="block text-[13px] font-bold text-gray-400 group-[.is-active]:text-dark hover:text-premium-500 uppercase tracking-widest transition-colors pb-2 border-b-2 border-transparent group-[.is-active]:border-dark hover:border-premium-500">
                    <?php echo esc_html( $label ); ?>
                </a>
			</li>
		<?php endforeach; ?>
	</ul>
</nav>

<style>
/* Hide scrollbar for horizonal scroll area */
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>

<?php do_action( 'woocommerce_after_account_navigation' ); ?>
