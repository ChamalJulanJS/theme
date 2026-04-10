<?php
/**
 * The front page template file
 *
 * @package FashionFeet
 */

get_header(); ?>

<main id="primary" class="site-main">

    <!-- HERO SECTION -->
    <section class="relative h-[85vh] min-h-[600px] flex items-center justify-center overflow-hidden">
        <!-- Background Overlay -->
        <div class="absolute inset-0 bg-dark/60 z-10 transition-opacity duration-1000 ease-in-out"></div>
        
        <!-- Background Image -->
        <div class="absolute inset-0 bg-cover bg-center transform scale-105" 
             style="background-image: url('<?php echo get_template_directory_uri(); ?>/images/hero.png');"></div>
             
        <!-- Hero Content -->
        <div class="relative z-20 text-center text-white px-6 w-full max-w-4xl mx-auto flex flex-col items-center">
            <span class="text-premium-400 font-bold tracking-[0.3em] uppercase mb-4 text-sm animate-pulse">New Collection 2026</span>
            <h1 class="text-5xl md:text-7xl lg:text-8xl font-black uppercase tracking-tight mb-8 drop-shadow-2xl leading-tight">Step Into The <span class="text-premium-500">Future</span></h1>
            <p class="text-lg md:text-xl font-medium text-gray-200 mb-10 max-w-2xl mx-auto hidden sm:block">Discover the pinnacle of minimalist luxury and modern design. Elevate your stride today.</p>
            <a href="#featured-products" class="group relative inline-flex items-center justify-center px-8 py-4 text-sm font-bold tracking-widest text-white uppercase bg-premium-500 overflow-hidden rounded-none hover:bg-premium-600 transition-colors duration-300">
                <span class="absolute w-0 h-0 transition-all duration-500 ease-out bg-white rounded-full group-hover:w-56 group-hover:h-56 opacity-10"></span>
                <span class="relative">Shop New Arrivals &rarr;</span>
            </a>
        </div>
    </section>

    <!-- CATEGORIES GRID -->
    <section class="py-24 bg-white">
        <div class="container-premium">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-black text-dark uppercase tracking-widest mb-4">Explore Collections</h2>
                <div class="w-16 h-1 bg-premium-500 mx-auto"></div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Deck Shoes Category -->
                <div class="group relative h-[400px] overflow-hidden rounded-xl shadow-lg cursor-pointer">
                    <div class="absolute inset-0 bg-dark/30 z-10 group-hover:bg-dark/50 transition-colors duration-500"></div>
                    <img src="https://images.unsplash.com/photo-1549298916-b41d501d3772?auto=format&fit=crop&q=80&w=800" alt="Deck Shoes" class="absolute inset-0 w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700 ease-out">
                    <div class="absolute inset-x-0 bottom-0 z-20 p-8 transform translate-y-4 group-hover:translate-y-0 transition-transform duration-500">
                        <h3 class="text-3xl font-black text-white uppercase tracking-widest mb-2">Deck Shoes</h3>
                        <a href="#" class="inline-flex items-center text-premium-400 font-bold uppercase text-sm opacity-0 group-hover:opacity-100 transition-opacity duration-500 delay-100">View Collection <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg></a>
                    </div>
                </div>

                <!-- Heels Category -->
                <div class="group relative h-[400px] overflow-hidden rounded-xl shadow-lg cursor-pointer">
                    <div class="absolute inset-0 bg-dark/30 z-10 group-hover:bg-dark/50 transition-colors duration-500"></div>
                    <img src="https://images.unsplash.com/photo-1543163521-1bf539c55dd2?auto=format&fit=crop&q=80&w=800" alt="Heels" class="absolute inset-0 w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700 ease-out">
                    <div class="absolute inset-x-0 bottom-0 z-20 p-8 transform translate-y-4 group-hover:translate-y-0 transition-transform duration-500">
                        <h3 class="text-3xl font-black text-white uppercase tracking-widest mb-2">Statement Heels</h3>
                        <a href="#" class="inline-flex items-center text-premium-400 font-bold uppercase text-sm opacity-0 group-hover:opacity-100 transition-opacity duration-500 delay-100">View Collection <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg></a>
                    </div>
                </div>
                
                <!-- Accessories Category Placeholder -->
                <div class="group relative h-[400px] overflow-hidden rounded-xl shadow-lg cursor-pointer lg:col-span-1 md:col-span-2 lg:col-auto">
                    <div class="absolute inset-0 bg-dark/40 z-10 group-hover:bg-dark/60 transition-colors duration-500"></div>
                    <!-- Leather wallet/bag background -->
                    <img src="https://images.unsplash.com/photo-1544816155-12df9643f363?q=80&w=800&auto=format&fit=crop" alt="Accessories" class="absolute inset-0 w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700 ease-out">
                    <div class="absolute inset-x-0 bottom-0 z-20 p-8 transform translate-y-4 group-hover:translate-y-0 transition-transform duration-500">
                        <h3 class="text-3xl font-black text-white uppercase tracking-widest mb-2">Accessories</h3>
                        <a href="#" class="inline-flex items-center text-premium-400 font-bold uppercase text-sm opacity-0 group-hover:opacity-100 transition-opacity duration-500 delay-100">View Collection <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FEATURED PRODUCTS LOOP -->
    <section id="featured-products" class="py-24 bg-[#FAFAFA]">
        <div class="container-premium">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-black text-dark uppercase tracking-widest mb-4">Trending Now</h2>
                <p class="text-gray-500 font-medium tracking-wide">Handpicked selections for the modern connoisseur</p>
                <div class="w-16 h-1 bg-premium-500 mx-auto mt-6"></div>
            </div>
            
            <div class="woocommerce">
                <?php
                if ( class_exists('WooCommerce') ) {
                    $args = array(
                        'post_type'      => 'product',
                        'posts_per_page' => 4,
                        'orderby'        => 'date',
                        'order'          => 'DESC',
                    );
                    $loop = new WP_Query( $args );

                    if ( $loop->have_posts() ) {
                        echo '<ul class="products">';
                        while ( $loop->have_posts() ) : $loop->the_post();
                            wc_get_template_part( 'content', 'product' );
                        endwhile;
                        echo '</ul>';
                    } else {
                        echo '<p class="text-center text-gray-500 py-10">No products found.</p>';
                    }
                    wp_reset_postdata();
                } else {
                    echo '<p class="text-center text-red-500 font-bold py-10">Please install and activate WooCommerce to see products here.</p>';
                }
                ?>
            </div>
            
            <?php if ( class_exists('WooCommerce') ) : ?>
            <div class="mt-16 text-center">
                <a href="<?php echo wc_get_page_permalink( 'shop' ); ?>" class="group relative inline-flex items-center justify-center px-10 py-5 text-sm font-bold tracking-[0.2em] text-white uppercase bg-dark overflow-hidden rounded-full shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                    <span class="absolute w-0 h-0 transition-all duration-500 ease-out bg-premium-500 rounded-full group-hover:w-full group-hover:h-full opacity-100 z-0"></span>
                    <span class="relative z-10 flex items-center">View Entire Shop <svg class="ml-3 w-5 h-5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg></span>
                </a>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- BRAND STORY -->
    <section class="py-24 bg-dark text-white relative flex items-center justify-center">
        <div class="container-premium relative z-20">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <div class="relative w-full aspect-square md:aspect-video lg:aspect-square overflow-hidden rounded-2xl border-4 border-gray-800">
                    <img src="<?php echo get_template_directory_uri(); ?>/images/formal.png" alt="Craftsmanship" class="absolute inset-0 w-full h-full object-cover">
                </div>
                <div class="flex flex-col justify-center">
                    <span class="text-premium-500 font-bold tracking-widest uppercase mb-4 text-sm">The Craft</span>
                    <h2 class="text-4xl md:text-5xl lg:text-6xl font-black uppercase tracking-tight mb-8 leading-tight">Uncompromising Quality</h2>
                    <p class="text-lg text-gray-400 mb-10 leading-relaxed">
                        Every pair of Fashion Feet shoes represents a perfect marriage between time-honored craftsmanship and avant-garde design. Our skilled artisans spend countless hours perfecting the intricate details, ensuring that true luxury is felt with every step.
                    </p>
                    <div>
                        <a href="#" class="inline-flex items-center text-white font-bold leading-trim pb-1 border-b-2 border-premium-500 hover:text-premium-400 hover:border-premium-400 transition-colors uppercase tracking-widest text-sm">Learn Our Story &rarr;</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- NEWSLETTER PRE-FOOTER -->
    <section class="py-32 bg-premium-500 text-white relative overflow-hidden">
        <!-- Abstract shape decorations -->
        <div class="absolute -top-[50%] -right-[10%] w-[80%] h-[200%] bg-premium-600 rounded-full blur-[120px] opacity-50 mix-blend-multiply pointer-events-none"></div>
        <div class="absolute -bottom-[50%] -left-[10%] w-[80%] h-[200%] bg-yellow-300 rounded-full blur-[150px] opacity-20 mix-blend-overlay pointer-events-none"></div>
        
        <div class="container-premium relative z-10">
            <div class="max-w-2xl mx-auto text-center bg-white/10 backdrop-blur-md p-10 md:p-16 rounded-3xl shadow-2xl border border-white/20">
                <h2 class="text-3xl md:text-5xl font-black uppercase tracking-tight mb-4 drop-shadow-md">Join the Inner Circle</h2>
                <p class="text-premium-100 text-lg mb-10">Subscribe for exclusive drops, early sale access, and VIP events.</p>
                
                <form class="flex flex-col sm:flex-row gap-4 max-w-lg mx-auto" onsubmit="event.preventDefault();">
                    <input type="email" placeholder="Your Email Address" required 
                           class="flex-1 px-6 py-4 bg-white text-dark rounded-xl focus:outline-none focus:ring-4 focus:ring-white/50 font-medium placeholder-gray-400 transition-shadow">
                    <button type="submit" class="px-8 py-4 bg-dark text-white font-bold uppercase tracking-widest rounded-xl hover:bg-black transition-colors shadow-lg">Subscribe</button>
                </form>
            </div>
        </div>
    </section>

</main>

<?php get_footer(); ?>
