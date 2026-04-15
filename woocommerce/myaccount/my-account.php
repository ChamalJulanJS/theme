<?php
/**
 * My Account page (wrapper)
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/my-account.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<div class="w-full flex flex-col font-sans">

    <!-- Navigation Area (Top Horizontal Tabs) -->
    <div class="w-full border-b-2 border-dark mb-10 pb-4 flex justify-between items-end">
        <div>
            <h1 class="text-4xl md:text-5xl font-black uppercase tracking-tighter text-dark mb-4 leading-none">Your<br>Account.</h1>
            <?php do_action( 'woocommerce_account_navigation' ); ?>
        </div>
    </div>

    <!-- Content Area -->
    <div class="w-full">
        <?php do_action( 'woocommerce_account_content' ); ?>
    </div>

</div>
