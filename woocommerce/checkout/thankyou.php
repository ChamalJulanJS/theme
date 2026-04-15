<?php
/**
 * Thankyou page — Premium Redesign
 *
 * @package FashionFeet
 * @version 8.1.0
 *
 * @var WC_Order $order
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="ff-thankyou">

	<?php if ( $order ) :
		do_action( 'woocommerce_before_thankyou', $order->get_id() );
	?>

		<?php if ( $order->has_status( 'failed' ) ) : ?>
			<!-- ═══ FAILED ORDER ═══ -->
			<div class="ff-thankyou-failed">
				<div class="ff-thankyou-icon ff-thankyou-icon-fail">
					<svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
				</div>
				<h2 class="ff-thankyou-title">Payment Failed</h2>
				<p class="ff-thankyou-desc">Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.</p>
				<div class="ff-thankyou-actions">
					<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="ff-thankyou-btn ff-thankyou-btn-primary">Try Again</a>
					<?php if ( is_user_logged_in() ) : ?>
						<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="ff-thankyou-btn ff-thankyou-btn-outline">My Account</a>
					<?php endif; ?>
				</div>
			</div>

		<?php else : ?>
			<!-- ═══ ORDER CONFIRMED ═══ -->
			<div class="ff-thankyou-success">

				<!-- Animated Checkmark -->
				<div class="ff-thankyou-checkmark">
					<svg class="ff-checkmark-svg" viewBox="0 0 52 52">
						<circle class="ff-checkmark-circle" cx="26" cy="26" r="25" fill="none"/>
						<path class="ff-checkmark-check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
					</svg>
				</div>

				<span class="ff-thankyou-eyebrow">Order #<?php echo $order->get_order_number(); ?></span>
				<h1 class="ff-thankyou-title">Thank You!</h1>
				<p class="ff-thankyou-desc">Your order has been confirmed and will be on its way shortly.</p>

				<!-- Order Details Grid -->
				<div class="ff-thankyou-details">
					<div class="ff-thankyou-detail-card">
						<span class="ff-detail-label">Order Number</span>
						<strong class="ff-detail-value">#<?php echo $order->get_order_number(); ?></strong>
					</div>
					<div class="ff-thankyou-detail-card">
						<span class="ff-detail-label">Date</span>
						<strong class="ff-detail-value"><?php echo wc_format_datetime( $order->get_date_created() ); ?></strong>
					</div>
					<?php if ( is_user_logged_in() && $order->get_user_id() === get_current_user_id() && $order->get_billing_email() ) : ?>
					<div class="ff-thankyou-detail-card">
						<span class="ff-detail-label">Email</span>
						<strong class="ff-detail-value"><?php echo $order->get_billing_email(); ?></strong>
					</div>
					<?php endif; ?>
					<div class="ff-thankyou-detail-card">
						<span class="ff-detail-label">Total</span>
						<strong class="ff-detail-value ff-detail-total"><?php echo $order->get_formatted_order_total(); ?></strong>
					</div>
					<?php if ( $order->get_payment_method_title() ) : ?>
					<div class="ff-thankyou-detail-card">
						<span class="ff-detail-label">Payment Method</span>
						<strong class="ff-detail-value"><?php echo wp_kses_post( $order->get_payment_method_title() ); ?></strong>
					</div>
					<?php endif; ?>
				</div>

				<!-- Order Items -->
				<?php $order_items = $order->get_items(); ?>
				<?php if ( ! empty( $order_items ) ) : ?>
				<div class="ff-thankyou-items">
					<h3 class="ff-thankyou-items-title">Order Items</h3>
					<div class="ff-thankyou-items-list">
						<?php foreach ( $order_items as $item_id => $item ) :
							$product = $item->get_product();
							$qty     = $item->get_quantity();
							$total   = $item->get_total();
						?>
						<div class="ff-thankyou-item">
							<div class="ff-thankyou-item-thumb">
								<?php if ( $product ) : ?>
									<?php echo $product->get_image( array(80, 80) ); ?>
								<?php else : ?>
									<div class="ff-thankyou-item-placeholder"></div>
								<?php endif; ?>
								<span class="ff-thankyou-item-qty-badge"><?php echo $qty; ?></span>
							</div>
							<div class="ff-thankyou-item-info">
								<h4><?php echo esc_html( $item->get_name() ); ?></h4>
								<?php
								$meta_data = $item->get_formatted_meta_data( '' );
								if ( ! empty( $meta_data ) ) :
									foreach ( $meta_data as $meta ) :
								?>
									<span class="ff-thankyou-item-meta"><?php echo wp_kses_post( $meta->display_key ); ?>: <?php echo wp_kses_post( $meta->display_value ); ?></span>
								<?php endforeach; endif; ?>
							</div>
							<div class="ff-thankyou-item-price">
								<?php echo wc_price( $total ); ?>
							</div>
						</div>
						<?php endforeach; ?>
					</div>
				</div>
				<?php endif; ?>

				<!-- CTA Buttons -->
				<div class="ff-thankyou-actions">
					<a href="<?php echo esc_url( wc_get_page_permalink('shop') ); ?>" class="ff-thankyou-btn ff-thankyou-btn-primary">
						Continue Shopping
					</a>
					<?php if ( is_user_logged_in() ) : ?>
					<a href="<?php echo esc_url( wc_get_endpoint_url( 'orders', '', wc_get_page_permalink('myaccount') ) ); ?>" class="ff-thankyou-btn ff-thankyou-btn-outline">
						View My Orders
					</a>
					<?php endif; ?>
				</div>

			</div>

		<?php endif; ?>

		<?php do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() ); ?>
		<?php do_action( 'woocommerce_thankyou', $order->get_id() ); ?>

	<?php else : ?>

		<!-- No order data -->
		<div class="ff-thankyou-success">
			<div class="ff-thankyou-checkmark">
				<svg class="ff-checkmark-svg" viewBox="0 0 52 52">
					<circle class="ff-checkmark-circle" cx="26" cy="26" r="25" fill="none"/>
					<path class="ff-checkmark-check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
				</svg>
			</div>
			<h1 class="ff-thankyou-title">Thank You!</h1>
			<p class="ff-thankyou-desc">Your order has been received.</p>
			<div class="ff-thankyou-actions">
				<a href="<?php echo esc_url( wc_get_page_permalink('shop') ); ?>" class="ff-thankyou-btn ff-thankyou-btn-primary">Continue Shopping</a>
			</div>
		</div>

	<?php endif; ?>

</div>
