<?php
/**
 * Login Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-login.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

do_action( 'woocommerce_before_customer_login_form' ); ?>

<div id="customer_login" class="flex flex-col lg:flex-row min-h-[calc(100vh-80px)] w-full font-sans bg-white border-t border-gray-100" x-data="{ isLogin: true }">
    
    <!-- LEFT SIDE: Hero Image (Hidden on Mobile) -->
    <div class="hidden lg:flex w-1/2 relative bg-dark items-center justify-center overflow-hidden group">
        <img src="<?php echo esc_url( get_template_directory_uri() . '/images/hero.png' ); ?>" alt="Fashion Feet" class="absolute inset-0 w-full h-full object-cover transition-transform duration-[10s] ease-out group-hover:scale-105" />
        <div class="absolute inset-0 bg-gradient-to-t from-dark/90 via-dark/40 to-transparent"></div>
        <div class="relative z-10 w-full h-full flex flex-col justify-between p-20">
            <div></div> <!-- Spacer -->
            <div class="text-white">
                <h2 class="text-[5rem] leading-[0.85] font-black uppercase tracking-tighter mb-6 text-white mix-blend-overlay opacity-90">Elevate<br>Your<br>Step.</h2>
                <p class="text-sm font-medium tracking-widest uppercase opacity-70">Exclusive Member Access</p>
            </div>
        </div>
    </div>

    <!-- RIGHT SIDE: Form Area -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 sm:p-16 lg:p-24 relative overflow-y-auto">
        <div class="w-full max-w-md relative z-20">
            
            <!-- Login State -->
            <div x-show="isLogin" x-transition:enter="transition ease-out duration-700" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0">
                <h3 class="text-4xl font-black tracking-tight text-dark mb-2 uppercase"><?php esc_html_e( 'Sign In', 'woocommerce' ); ?></h3>
                <p class="text-sm text-gray-500 font-medium mb-10">Access your exclusive benefits.</p>

                <form class="woocommerce-form woocommerce-form-login login space-y-6" method="post">
                    <?php do_action( 'woocommerce_login_form_start' ); ?>

                    <div>
                        <label class="block text-[11px] font-bold text-gray-400 mb-2 uppercase tracking-[0.15em]" for="username"><?php esc_html_e( 'Email Address', 'woocommerce' ); ?> <span class="text-red-400">*</span></label>
                        <input type="text" class="w-full px-5 py-4 bg-gray-50 border border-gray-200 text-dark focus:bg-white focus:border-dark focus:ring-1 focus:ring-dark transition-all outline-none text-base font-semibold placeholder-gray-400 rounded-xl" name="username" id="username" autocomplete="username" placeholder="name@example.com" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" />
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-gray-400 mb-2 uppercase tracking-[0.15em]" for="password"><?php esc_html_e( 'Password', 'woocommerce' ); ?> <span class="text-red-400">*</span></label>
                        <input class="w-full px-5 py-4 bg-gray-50 border border-gray-200 text-dark focus:bg-white focus:border-dark focus:ring-1 focus:ring-dark transition-all outline-none text-base font-semibold placeholder-gray-400 rounded-xl" type="password" name="password" id="password" autocomplete="current-password" placeholder="••••••••" />
                    </div>

                    <div class="flex items-center justify-between pt-4">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input class="w-4 h-4 text-dark bg-white border-gray-300 focus:ring-dark focus:ring-offset-0 cursor-pointer transition-colors" name="rememberme" type="checkbox" id="rememberme" value="forever" /> 
                            <span class="text-[13px] font-semibold text-gray-400 group-hover:text-dark transition-colors uppercase tracking-widest"><?php esc_html_e( 'Remember Me', 'woocommerce' ); ?></span>
                        </label>
                        <a class="text-[13px] font-bold text-dark hover:text-premium-500 transition-colors uppercase tracking-widest underline underline-offset-4 decoration-2 decoration-transparent hover:decoration-premium-500" href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Forgot?', 'woocommerce' ); ?></a>
                    </div>

                    <?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
                    <div class="pt-8">
                        <button type="submit" class="w-full bg-dark text-white font-bold uppercase tracking-[0.2em] text-xs py-5 px-6 hover:bg-premium-500 transition-all duration-300 flex justify-between items-center group" name="login" value="<?php esc_attr_e( 'Log in', 'woocommerce' ); ?>">
                            <span><?php esc_html_e( 'Sign In', 'woocommerce' ); ?></span>
                            <svg class="w-5 h-5 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </button>
                    </div>
                    <?php do_action( 'woocommerce_login_form_end' ); ?>
                </form>

                <div class="mt-12 pt-8 text-center border-t border-gray-100">
                    <p class="text-[13px] text-gray-500 font-semibold uppercase tracking-widest">
                        New to Fashion Feet? 
                        <button @click="isLogin = false" type="button" class="inline-block font-black text-dark ml-2 hover:text-premium-500 border-b-2 border-dark hover:border-premium-500 pb-0.5 transition-colors focus:outline-none">CREATE ACCOUNT</button>
                    </p>
                </div>
            </div>

            <!-- Register State -->
            <div x-show="!isLogin" x-cloak x-transition:enter="transition ease-out duration-700 delay-100" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0">
                <h3 class="text-4xl font-black tracking-tight text-dark mb-2 uppercase"><?php esc_html_e( 'Create Account', 'woocommerce' ); ?></h3>
                <p class="text-sm text-gray-500 font-medium mb-10">Join our exclusive society.</p>

                <form method="post" class="woocommerce-form woocommerce-form-register register space-y-6" <?php do_action( 'woocommerce_register_form_tag' ); ?> >
                    <?php do_action( 'woocommerce_register_form_start' ); ?>

                    <?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-400 mb-2 uppercase tracking-[0.15em]" for="reg_username"><?php esc_html_e( 'Username', 'woocommerce' ); ?> <span class="text-red-400">*</span></label>
                            <input type="text" class="w-full px-5 py-4 bg-gray-50 border border-gray-200 text-dark focus:bg-white focus:border-dark focus:ring-1 focus:ring-dark transition-all outline-none text-base font-semibold placeholder-gray-400 rounded-xl" name="username" id="reg_username" autocomplete="username" placeholder="johndoe" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" />
                        </div>
                    <?php endif; ?>

                    <div>
                        <label class="block text-[11px] font-bold text-gray-400 mb-2 uppercase tracking-[0.15em]" for="reg_email"><?php esc_html_e( 'Email address', 'woocommerce' ); ?> <span class="text-red-400">*</span></label>
                        <input type="email" class="w-full px-5 py-4 bg-gray-50 border border-gray-200 text-dark focus:bg-white focus:border-dark focus:ring-1 focus:ring-dark transition-all outline-none text-base font-semibold placeholder-gray-400 rounded-xl" name="email" id="reg_email" autocomplete="email" placeholder="name@example.com" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" />
                    </div>

                    <?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-400 mb-2 uppercase tracking-[0.15em]" for="reg_password"><?php esc_html_e( 'Password', 'woocommerce' ); ?> <span class="text-red-400">*</span></label>
                            <input type="password" class="w-full px-5 py-4 bg-gray-50 border border-gray-200 text-dark focus:bg-white focus:border-dark focus:ring-1 focus:ring-dark transition-all outline-none text-base font-semibold placeholder-gray-400 rounded-xl" name="password" id="reg_password" autocomplete="new-password" placeholder="Create a strong password" />
                        </div>
                    <?php else : ?>
                        <div class="bg-gray-50 border border-gray-100 p-5 flex gap-4 items-start">
                            <svg class="w-6 h-6 text-gray-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <p class="text-[13px] text-gray-500 font-medium leading-relaxed"><?php esc_html_e( 'A password creation link will be sent to your email.', 'woocommerce' ); ?></p>
                        </div>
                    <?php endif; ?>

                    <?php do_action( 'woocommerce_register_form' ); ?>

                    <?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
                    <div class="pt-8">
                        <button type="submit" class="w-full bg-dark text-white font-bold uppercase tracking-[0.2em] text-xs py-5 px-6 hover:bg-premium-500 transition-all duration-300 flex justify-between items-center group" name="register" value="<?php esc_attr_e( 'Register', 'woocommerce' ); ?>">
                            <span><?php esc_html_e( 'Create Account', 'woocommerce' ); ?></span>
                            <svg class="w-5 h-5 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </button>
                    </div>

                    <?php do_action( 'woocommerce_register_form_end' ); ?>
                </form>

                <div class="mt-12 pt-8 text-center border-t border-gray-100">
                    <p class="text-[13px] text-gray-500 font-semibold uppercase tracking-widest">
                        Already have an account? 
                        <button @click="isLogin = true" type="button" class="inline-block font-black text-dark ml-2 hover:text-premium-500 border-b-2 border-dark hover:border-premium-500 pb-0.5 transition-colors focus:outline-none">SIGN IN INSTEAD</button>
                    </p>
                </div>
            </div>

        </div>
    </div>
</div>

<?php do_action( 'woocommerce_after_customer_login_form' ); ?>