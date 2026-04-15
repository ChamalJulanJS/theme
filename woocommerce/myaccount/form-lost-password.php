<?php
/**
 * Lost password form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-lost-password.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_lost_password_form' );
?>

<div id="customer_lost_password" class="flex flex-col lg:flex-row min-h-[calc(100vh-80px)] w-full font-sans bg-white border-t border-gray-100">
    
    <!-- LEFT SIDE: Hero Image (Hidden on Mobile) -->
    <div class="hidden lg:flex w-1/2 relative bg-dark items-center justify-center overflow-hidden group">
        <img src="<?php echo esc_url( get_template_directory_uri() . '/images/hero.png' ); ?>" alt="Fashion Feet" class="absolute inset-0 w-full h-full object-cover transition-transform duration-[10s] ease-out group-hover:scale-105" />
        <div class="absolute inset-0 bg-gradient-to-t from-dark/90 via-dark/40 to-transparent"></div>
        <div class="relative z-10 w-full h-full flex flex-col justify-between p-20">
            <div></div> <!-- Spacer -->
            <div class="text-white">
                <h2 class="text-[5rem] leading-[0.85] font-black uppercase tracking-tighter mb-6 text-white mix-blend-overlay opacity-90">Recover<br>Your<br>Access.</h2>
                <p class="text-sm font-medium tracking-widest uppercase opacity-70">Secure Password Reset</p>
            </div>
        </div>
    </div>

    <!-- RIGHT SIDE: Form Area -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 sm:p-16 lg:p-24 relative overflow-y-auto">
        <div class="w-full max-w-md relative z-20">
            
            <h3 class="text-4xl font-black tracking-tight text-dark mb-2 uppercase"><?php esc_html_e( 'Reset Password', 'woocommerce' ); ?></h3>
            <p class="text-sm text-gray-500 font-medium mb-10 leading-relaxed"><?php echo apply_filters( 'woocommerce_lost_password_message', esc_html__( 'Lost your password? Please enter your username or email address. You will receive a link to create a new password via email.', 'woocommerce' ) ); ?></p>

            <form method="post" class="woocommerce-ResetPassword lost_reset_password space-y-6">

                <div>
                    <label class="block text-[11px] font-bold text-gray-400 mb-2 uppercase tracking-[0.15em]" for="user_login"><?php esc_html_e( 'Username or email', 'woocommerce' ); ?> <span class="text-red-400">*</span></label>
                    <input class="w-full px-5 py-4 bg-gray-50 border border-gray-200 text-dark focus:bg-white focus:border-dark focus:ring-1 focus:ring-dark transition-all outline-none text-base font-semibold placeholder-gray-400 rounded-xl" type="text" name="user_login" id="user_login" autocomplete="username" placeholder="name@example.com" />
                </div>

                <div class="clear"></div>

                <?php do_action( 'woocommerce_lostpassword_form' ); ?>

                <div class="pt-8">
                    <input type="hidden" name="wc_reset_password" value="true" />
                    <button type="submit" class="w-full bg-dark text-white font-bold uppercase tracking-[0.2em] text-xs py-5 px-6 hover:bg-premium-500 transition-all duration-300 flex justify-center items-center group" value="<?php esc_attr_e( 'Reset password', 'woocommerce' ); ?>">
                        <?php esc_html_e( 'Reset Password', 'woocommerce' ); ?>
                    </button>
                </div>

                <?php wp_nonce_field( 'lost_password', 'woocommerce-lost-password-nonce' ); ?>

            </form>

            <div class="mt-12 pt-8 text-center border-t border-gray-100">
                <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="inline-flex items-center text-[13px] font-bold uppercase tracking-widest text-dark hover:text-premium-500 transition-colors group">
                    <svg class="w-4 h-4 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    Back to Login
                </a>
            </div>

        </div>
    </div>
</div>

<?php
do_action( 'woocommerce_after_lost_password_form' );
