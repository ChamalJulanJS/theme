<?php
/**
 * My Account Dashboard
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/dashboard.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$current_user = wp_get_current_user();
?>

<div class="mb-12">
    <p class="text-gray-500 font-semibold tracking-widest uppercase text-sm leading-relaxed max-w-2xl">
        <?php
        printf(
            /* translators: 1: user display name 2: logout url */
            wp_kses( __( 'Welcome back, <strong class="text-dark font-black">%1$s</strong>. Not %1$s? <a class="text-dark hover:text-premium-500 underline underline-offset-4 decoration-2 decoration-transparent hover:decoration-premium-500 transition-all" href="%2$s">Log out</a>', 'woocommerce' ), array( 'strong' => array( 'class' => array() ), 'a' => array( 'href' => array(), 'class' => array() ) ) ),
            esc_html( $current_user->display_name ),
            esc_url( wc_logout_url() )
        );
        ?>
    </p>
</div>

<!-- Brutalist Quick Links Grid -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    
    <a href="<?php echo esc_url( wc_get_endpoint_url( 'orders' ) ); ?>" class="group block p-10 bg-gray-50 hover:bg-dark transition-all duration-300 border border-gray-100 hover:border-dark relative overflow-hidden">
        <svg class="w-10 h-10 text-dark group-hover:text-white mb-6 transition-colors relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
        <h3 class="text-xl font-black uppercase tracking-tighter text-dark group-hover:text-white mb-3 transition-colors relative z-10 tracking-widest">Orders</h3>
        <p class="text-[13px] text-gray-500 group-hover:text-gray-400 font-medium relative z-10">Review your past purchases and track deliveries.</p>
        
        <svg class="w-6 h-6 text-premium-500 absolute bottom-10 right-10 opacity-0 group-hover:opacity-100 transform group-hover:translate-x-2 transition-all duration-300 z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
    </a>

    <a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address' ) ); ?>" class="group block p-10 bg-gray-50 hover:bg-dark transition-all duration-300 border border-gray-100 hover:border-dark relative overflow-hidden">
        <svg class="w-10 h-10 text-dark group-hover:text-white mb-6 transition-colors relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
        <h3 class="text-xl font-black uppercase tracking-tighter text-dark group-hover:text-white mb-3 transition-colors relative z-10 tracking-widest">Addresses</h3>
        <p class="text-[13px] text-gray-500 group-hover:text-gray-400 font-medium relative z-10">Manage your shipping destinations and billing info.</p>
        
        <svg class="w-6 h-6 text-premium-500 absolute bottom-10 right-10 opacity-0 group-hover:opacity-100 transform group-hover:translate-x-2 transition-all duration-300 z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
    </a>

    <a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-account' ) ); ?>" class="group block p-10 bg-gray-50 hover:bg-dark transition-all duration-300 border border-gray-100 hover:border-dark relative overflow-hidden">
        <svg class="w-10 h-10 text-dark group-hover:text-white mb-6 transition-colors relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
        <h3 class="text-xl font-black uppercase tracking-tighter text-dark group-hover:text-white mb-3 transition-colors relative z-10 tracking-widest">Details</h3>
        <p class="text-[13px] text-gray-500 group-hover:text-gray-400 font-medium relative z-10">Update your personal data and security options.</p>
        
        <svg class="w-6 h-6 text-premium-500 absolute bottom-10 right-10 opacity-0 group-hover:opacity-100 transform group-hover:translate-x-2 transition-all duration-300 z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
    </a>

</div>

<?php
	/**
	 * My Account dashboard.
	 *
	 * @since 2.6.0
	 */
	do_action( 'woocommerce_account_dashboard' );

	/**
	 * Deprecated woocommerce_before_my_account action.
	 *
	 * @deprecated 2.6.0
	 */
	do_action( 'woocommerce_before_my_account' );

	/**
	 * Deprecated woocommerce_after_my_account action.
	 *
	 * @deprecated 2.6.0
	 */
	do_action( 'woocommerce_after_my_account' );
/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
