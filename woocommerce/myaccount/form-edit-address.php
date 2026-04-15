<?php
/**
 * Edit address form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-edit-address.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$page_title = ( 'billing' === $load_address ) ? esc_html__( 'Billing address', 'woocommerce' ) : esc_html__( 'Shipping address', 'woocommerce' );

do_action( 'woocommerce_before_edit_account_address_form' ); ?>

<?php if ( ! $load_address ) : ?>
	<?php wc_get_template( 'myaccount/my-address.php' ); ?>
<?php else : ?>

	<div class="mb-8">
        <a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address' ) ); ?>" class="inline-flex items-center text-xs font-bold uppercase tracking-widest text-gray-400 hover:text-dark transition-colors mb-6 group">
            <svg class="w-4 h-4 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Back to Addresses
        </a>
        <h2 class="text-3xl font-black uppercase tracking-tight text-dark mb-2"><?php echo esc_html( $page_title ); ?></h2>
        <p class="text-sm text-gray-500 font-medium">Please verify and update your details below.</p>
    </div>

	<form method="post" class="woocommerce-form flex flex-col gap-6">

		<div class="grid grid-cols-1 md:grid-cols-2 gap-6 woocommerce-address-fields">
			<?php do_action( "woocommerce_before_edit_address_form_{$load_address}" ); ?>

			<div class="woocommerce-address-fields__field-wrapper w-full col-span-1 md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
				<?php
				foreach ( $address as $key => $field ) {
					woocommerce_form_field( $key, $field, wc_get_post_data_by_key( $key, $field['value'] ) );
				}
				?>
			</div>

			<?php do_action( "woocommerce_after_edit_address_form_{$load_address}" ); ?>

			<div class="col-span-1 md:col-span-2 pt-6 border-t border-gray-100 flex items-center justify-between">
				<button type="submit" class="button bg-dark text-white font-bold uppercase tracking-widest text-xs py-4 px-8 hover:bg-premium-500 transition-colors border-0 rounded-xl" name="save_address" value="<?php esc_attr_e( 'Save address', 'woocommerce' ); ?>"><?php esc_html_e( 'Save address', 'woocommerce' ); ?></button>
				<?php wp_nonce_field( 'woocommerce-edit_address', 'woocommerce-edit-address-nonce' ); ?>
				<input type="hidden" name="action" value="edit_address" />
			</div>
		</div>

	</form>
    
    <style>
        /* Modern overrides for the generated Woo form fields in My Account */
        .woocommerce-address-fields__field-wrapper .form-row { width: 100% !important; float: none !important; margin: 0 !important; }
        .woocommerce-address-fields__field-wrapper .form-row.form-row-wide { grid-column: span 2; }
        .woocommerce-address-fields__field-wrapper .form-row.form-row-first,
        .woocommerce-address-fields__field-wrapper .form-row.form-row-last { grid-column: span 1; }
        
        @media (max-width: 768px) {
            .woocommerce-address-fields__field-wrapper .form-row.form-row-first,
            .woocommerce-address-fields__field-wrapper .form-row.form-row-last { grid-column: span 2; }
        }

        .woocommerce-address-fields__field-wrapper label {
            display: block; font-size: 11px; font-weight: 700; color: #4b5563; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.1em;
        }
        .woocommerce-address-fields__field-wrapper label .required { color: #f87171; text-decoration: none; border: none; }
        
        .woocommerce-address-fields__field-wrapper input[type="text"],
        .woocommerce-address-fields__field-wrapper input[type="email"],
        .woocommerce-address-fields__field-wrapper input[type="tel"],
        .woocommerce-address-fields__field-wrapper select,
        .woocommerce-address-fields__field-wrapper textarea {
            width: 100%; padding: 14px 20px; background: #f9fafb; border: 1px solid #e5e7eb; color: #111; font-size: 14px; font-weight: 500; border-radius: 12px; transition: all 0.3s; box-shadow: none; outline: none; appearance: none;
        }
        .woocommerce-address-fields__field-wrapper input:focus,
        .woocommerce-address-fields__field-wrapper select:focus,
        .woocommerce-address-fields__field-wrapper textarea:focus {
            background: #fff; border-color: #111; box-shadow: 0 0 0 1px #111;
        }
        .select2-container--default .select2-selection--single {
            background: #f9fafb !important; border: 1px solid #e5e7eb !important; border-radius: 12px !important; height: 50px !important; padding: 10px 10px !important; outline: none !important;
        }
        .select2-container--default.select2-container--focus .select2-selection--single {
            background: #fff !important; border-color: #111 !important; box-shadow: 0 0 0 1px #111 !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #111 !important; font-size: 14px !important; font-weight: 500 !important; line-height: 28px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 48px !important; right: 10px !important;
        }
    </style>

<?php endif; ?>

<?php do_action( 'woocommerce_after_edit_account_address_form' ); ?>
