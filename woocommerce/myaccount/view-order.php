<?php
/**
 * View Order
 *
 * Shows the details of a particular order on the account page.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/view-order.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="mb-10">
    <a href="<?php echo esc_url( wc_get_endpoint_url( 'orders' ) ); ?>" class="inline-flex items-center text-xs font-bold uppercase tracking-widest text-gray-400 hover:text-dark transition-colors mb-6 group">
        <svg class="w-4 h-4 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        Back to Orders
    </a>

    <h2 class="text-3xl font-black uppercase tracking-tight text-dark mb-4">
        Order <span class="text-premium-500">#<?php echo esc_html( $order->get_order_number() ); ?></span>
    </h2>
    <p class="text-gray-500 font-medium bg-gray-50 inline-block px-5 py-3 rounded-lg text-sm">
        <?php
        printf(
            /* translators: 1: order number 2: order date 3: order status */
            esc_html__( 'Order placed on %1$s and is currently %2$s.', 'woocommerce' ),
            '<strong class="text-dark">' . wc_format_datetime( $order->get_date_created() ) . '</strong>', // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            '<strong class="text-dark uppercase text-[10px] tracking-wider">' . wc_get_order_status_name( $order->get_status() ) . '</strong>' // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        );
        ?>
    </p>
</div>

<?php if ( $notes = $order->get_customer_order_notes() ) : ?>
	<h3 class="text-xl font-bold text-dark mt-12 mb-6">Order updates</h3>
	<ol class="woocommerce-OrderUpdates commentlist notes m-0 p-0 list-none space-y-4">
		<?php foreach ( $notes as $note ) : ?>
		<li class="woocommerce-OrderUpdate comment note bg-gray-50 p-6 rounded-xl border border-gray-100">
			<div class="woocommerce-OrderUpdate-inner comment_container">
				<div class="woocommerce-OrderUpdate-text comment-text">
					<p class="woocommerce-OrderUpdate-meta meta text-xs font-bold text-gray-400 uppercase tracking-widest mb-3"><?php echo date_i18n( esc_html__( 'l jS \o\f F Y, h:ia', 'woocommerce' ), strtotime( $note->comment_date ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
					<div class="woocommerce-OrderUpdate-description description text-sm text-gray-600 font-medium">
						<?php echo wpautop( wptexturize( $note->comment_content ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
				</div>
			</div>
		</li>
		<?php endforeach; ?>
	</ol>
<?php endif; ?>

<div class="mt-10">
<?php do_action( 'woocommerce_view_order', $order_id ); ?>
</div>
