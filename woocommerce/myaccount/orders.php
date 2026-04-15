<?php
/**
 * Orders
 *
 * Shows orders on the account page.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/orders.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_account_orders', $has_orders ); ?>

<div class="mb-10">
    <h2 class="text-3xl font-black uppercase tracking-tight text-dark mb-4">Your Orders</h2>
</div>

<?php if ( $has_orders ) : ?>

	<div class="overflow-x-auto">
        <table class="w-full text-left border-collapse my_account_orders">
            <thead>
                <tr>
                    <?php foreach ( wc_get_account_orders_columns() as $column_id => $column_name ) : ?>
                        <th class="woocommerce-orders-table__header woocommerce-orders-table__header-<?php echo esc_attr( $column_id ); ?> border-b-2 border-dark py-4 px-4 text-xs font-bold uppercase tracking-widest text-dark"><span class="nobr"><?php echo esc_html( $column_name ); ?></span></th>
                    <?php endforeach; ?>
                </tr>
            </thead>

            <tbody>
                <?php
                foreach ( $customer_orders->orders as $customer_order ) {
                    $order      = wc_get_order( $customer_order ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
                    $item_count = $order->get_item_count() - $order->get_item_count_refunded();
                    ?>
                    <tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-<?php echo esc_attr( $order->get_status() ); ?> border-b border-gray-100 hover:bg-gray-50/50 transition-colors group">
                        <?php foreach ( wc_get_account_orders_columns() as $column_id => $column_name ) : ?>
                            <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-<?php echo esc_attr( $column_id ); ?> py-5 px-4" data-title="<?php echo esc_attr( $column_name ); ?>">
                                <?php if ( has_action( 'woocommerce_my_account_my_orders_column_' . $column_id ) ) : ?>
                                    <?php do_action( 'woocommerce_my_account_my_orders_column_' . $column_id, $order ); ?>

                                <?php elseif ( 'order-number' === $column_id ) : ?>
                                    <a href="<?php echo esc_url( $order->get_view_order_url() ); ?>" class="font-bold text-dark hover:text-premium-500 transition-colors underline-offset-4 decoration-2 hover:underline">
                                        <?php echo esc_html( _x( '#', 'hash before order number', 'woocommerce' ) . $order->get_order_number() ); ?>
                                    </a>

                                <?php elseif ( 'order-date' === $column_id ) : ?>
                                    <time class="text-sm text-gray-500 font-medium" datetime="<?php echo esc_attr( $order->get_date_created()->date( 'c' ) ); ?>"><?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?></time>

                                <?php elseif ( 'order-status' === $column_id ) : ?>
                                    <?php 
                                    $status = $order->get_status(); 
                                    $status_classes = 'bg-gray-100 text-gray-600';
                                    if ($status === 'completed') $status_classes = 'bg-green-100 text-green-700';
                                    if ($status === 'processing') $status_classes = 'bg-blue-100 text-blue-700';
                                    if ($status === 'on-hold') $status_classes = 'bg-yellow-100 text-yellow-700';
                                    if ($status === 'cancelled' || $status === 'failed') $status_classes = 'bg-red-100 text-red-700';
                                    ?>
                                    <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider <?php echo esc_attr($status_classes); ?>">
                                        <?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?>
                                    </span>

                                <?php elseif ( 'order-total' === $column_id ) : ?>
                                    <span class="text-sm font-semibold text-dark">
                                    <?php
                                    /* translators: 1: formatted order total 2: total order items */
                                    echo wp_kses_post( sprintf( _n( '%1$s for %2$s item', '%1$s for %2$s items', $item_count, 'woocommerce' ), $order->get_formatted_order_total(), $item_count ) );
                                    ?>
                                    </span>

                                <?php elseif ( 'order-actions' === $column_id ) : ?>
                                    <?php
                                    $actions = wc_get_account_orders_actions( $order );

                                    if ( ! empty( $actions ) ) {
                                        echo '<div class="flex items-center gap-3">';
                                        foreach ( $actions as $key => $action ) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
                                            $btn_class = 'bg-dark text-white hover:bg-premium-500';
                                            if ($key !== 'view') {
                                                $btn_class = 'bg-gray-100 text-dark hover:bg-gray-200';
                                            }
                                            echo '<a href="' . esc_url( $action['url'] ) . '" class="inline-block px-4 py-2 text-[10px] font-bold uppercase tracking-widest transition-colors rounded-lg ' . esc_attr( $btn_class ) . '">' . esc_html( $action['name'] ) . '</a>';
                                        }
                                        echo '</div>';
                                    }
                                    ?>
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>

	<?php do_action( 'woocommerce_before_account_orders_pagination' ); ?>

	<?php if ( 1 < $customer_orders->max_num_pages ) : ?>
		<div class="woocommerce-pagination woocommerce-pagination--without-numbers woocommerce-Pagination flex gap-4 mt-8 pt-8 border-t border-gray-100">
			<?php if ( 1 !== $current_page ) : ?>
				<a class="woocommerce-button woocommerce-button--previous woocommerce-Button woocommerce-Button--previous button px-6 py-3 bg-white border border-gray-200 text-xs font-bold uppercase tracking-widest text-dark hover:border-dark hover:bg-dark hover:text-white transition-colors rounded-lg" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page - 1 ) ); ?>"><?php esc_html_e( 'Previous', 'woocommerce' ); ?></a>
			<?php endif; ?>

			<?php if ( intval( $customer_orders->max_num_pages ) !== $current_page ) : ?>
				<a class="woocommerce-button woocommerce-button--next woocommerce-Button woocommerce-Button--next button px-6 py-3 bg-white border border-gray-200 text-xs font-bold uppercase tracking-widest text-dark hover:border-dark hover:bg-dark hover:text-white transition-colors rounded-lg" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page + 1 ) ); ?>"><?php esc_html_e( 'Next', 'woocommerce' ); ?></a>
			<?php endif; ?>
		</div>
	<?php endif; ?>

<?php else : ?>

	<div class="bg-gray-50/50 border border-gray-100 p-12 text-center rounded-2xl woocommerce-message woocommerce-message--info woocommerce-Message woocommerce-Message--info woocommerce-info">
		<svg class="w-16 h-16 mx-auto text-gray-300 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
        <p class="text-gray-500 font-medium mb-8">No order has been made yet.</p>
        <a class="woocommerce-Button button inline-block bg-dark text-white font-bold uppercase tracking-widest text-[11px] py-4 px-8 hover:bg-premium-500 transition-colors border-0 rounded-xl" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>">
			<?php esc_html_e( 'Browse products', 'woocommerce' ); ?>
		</a>
	</div>

<?php endif; ?>

<?php do_action( 'woocommerce_after_account_orders', $has_orders ); ?>
