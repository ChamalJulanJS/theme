<?php
/**
 * Edit account form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-edit-account.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_edit_account_form' ); ?>

<div class="mb-8">
    <h2 class="text-3xl font-black uppercase tracking-tight text-dark mb-2">Account Details</h2>
    <p class="text-sm text-gray-500 font-medium">Update your personal information and password.</p>
</div>

<form class="woocommerce-EditAccountForm edit-account" action="" method="post" <?php do_action( 'woocommerce_edit_account_form_tag' ); ?> >

	<?php do_action( 'woocommerce_edit_account_form_start' ); ?>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 ff-custom-form-grid">
        <p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
            <label for="account_first_name"><?php esc_html_e( 'First name', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
            <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_first_name" id="account_first_name" autocomplete="given-name" value="<?php echo esc_attr( $user->first_name ); ?>" />
        </p>
        <p class="woocommerce-form-row woocommerce-form-row--last form-row form-row-last">
            <label for="account_last_name"><?php esc_html_e( 'Last name', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
            <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_last_name" id="account_last_name" autocomplete="family-name" value="<?php echo esc_attr( $user->last_name ); ?>" />
        </p>

        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide col-span-1 md:col-span-2">
            <label for="account_display_name"><?php esc_html_e( 'Display name', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
            <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_display_name" id="account_display_name" value="<?php echo esc_attr( $user->display_name ); ?>" /> <span class="block text-xs font-medium text-gray-400 mt-2"><?php esc_html_e( 'This will be how your name will be displayed in the account section and in reviews', 'woocommerce' ); ?></span>
        </p>

        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide col-span-1 md:col-span-2">
            <label for="account_email"><?php esc_html_e( 'Email address', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
            <input type="email" class="woocommerce-Input woocommerce-Input--email input-text" name="account_email" id="account_email" autocomplete="email" value="<?php echo esc_attr( $user->user_email ); ?>" />
        </p>
    </div>

	<div class="bg-gray-50/50 p-8 rounded-2xl border border-gray-100 mb-8 ff-custom-form-grid">
		<h3 class="text-xl font-black uppercase tracking-tight text-dark mb-6">Password change</h3>

		<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide col-span-1 md:col-span-2">
                <label for="password_current"><?php esc_html_e( 'Current password (leave blank to leave unchanged)', 'woocommerce' ); ?></label>
                <input type="password" class="woocommerce-Input woocommerce-Input--password input-text" name="password_current" id="password_current" autocomplete="off" />
            </p>
            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide col-span-1">
                <label for="password_1"><?php esc_html_e( 'New password (leave blank to leave unchanged)', 'woocommerce' ); ?></label>
                <input type="password" class="woocommerce-Input woocommerce-Input--password input-text" name="password_1" id="password_1" autocomplete="off" />
            </p>
            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide col-span-1">
                <label for="password_2"><?php esc_html_e( 'Confirm new password', 'woocommerce' ); ?></label>
                <input type="password" class="woocommerce-Input woocommerce-Input--password input-text" name="password_2" id="password_2" autocomplete="off" />
            </p>
        </div>
	</div>

	<?php do_action( 'woocommerce_edit_account_form' ); ?>

	<div class="pt-6 border-t border-gray-100">
		<?php wp_nonce_field( 'save_account_details', 'save-account-details-nonce' ); ?>
		<button type="submit" class="woocommerce-Button button bg-dark text-white font-bold uppercase tracking-widest text-xs py-4 px-8 hover:bg-premium-500 transition-colors border-0 rounded-xl" name="save_account_details" value="<?php esc_attr_e( 'Save changes', 'woocommerce' ); ?>"><?php esc_html_e( 'Save changes', 'woocommerce' ); ?></button>
		<input type="hidden" name="action" value="save_account_details" />
	</div>

	<?php do_action( 'woocommerce_edit_account_form_end' ); ?>
</form>

<style>
    .ff-custom-form-grid label {
        display: block; font-size: 11px; font-weight: 700; color: #4b5563; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.1em;
    }
    .ff-custom-form-grid label .required { color: #f87171; text-decoration: none; border: none; }
    
    .ff-custom-form-grid input[type="text"],
    .ff-custom-form-grid input[type="email"],
    .ff-custom-form-grid input[type="password"] {
        width: 100%; padding: 14px 20px; background: #f9fafb; border: 1px solid #e5e7eb; color: #111; font-size: 14px; font-weight: 500; border-radius: 12px; transition: all 0.3s; box-shadow: none; outline: none; appearance: none;
    }
    .ff-custom-form-grid input:focus {
        background: #fff; border-color: #111; box-shadow: 0 0 0 1px #111;
    }
    /* Hide the password strength meter that Woo force-adds, or let it flow nicely */
    .woocommerce-password-strength { font-size: 12px; font-weight: 600; text-transform: uppercase; margin-top: 10px; text-align: right; }
    .woocommerce-password-hint { font-size: 12px; color: #9ca3af; margin-top: 5px; }
</style>

<?php do_action( 'woocommerce_after_edit_account_form' ); ?>
