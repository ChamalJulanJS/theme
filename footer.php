</div> <!-- End Main Wrapper -->

<footer class="bg-dark text-gray-300 w-full mt-auto py-16">
    <div class="container-premium">
        
        <!-- Footer Widgets -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-12">
            
            <div class="col-span-1 lg:col-span-2 pr-8">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="text-white text-2xl font-black uppercase tracking-widest mb-6 block">Fashion Feet</a>
                <p class="text-sm leading-relaxed mb-6 text-gray-400">
                    Premium minimalist footwear designed to elevate your everyday gait. Discover unparalleled comfort mixed with high-end aesthetic value. 
                </p>
                <div class="flex gap-4">
                    <?php 
                    $social_icons = array(
                        'twitter' => '<svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>',
                        'instagram' => '<svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>',
                        'facebook' => '<svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>',
                        'tiktok' => '<svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>',
                    );
                    foreach ( $social_icons as $network => $icon ) :
                        $url = ff_get_social_url( $network );
                        if ( $url ) : ?>
                            <a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-premium-500 hover:text-white transition-all" aria-label="<?php echo ucfirst($network); ?>"><?php echo $icon; ?></a>
                        <?php else : ?>
                            <span class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center text-gray-500 cursor-default" title="<?php echo ucfirst($network); ?> (Not configured)"><?php echo $icon; ?></span>
                        <?php endif;
                    endforeach; ?>
                </div>
            </div>

            <div class="col-span-1">
                <?php if ( is_active_sidebar( 'footer-1' ) ) : ?>
                    <?php dynamic_sidebar( 'footer-1' ); ?>
                <?php endif; ?>
            </div>

            <div class="col-span-1">
                <?php if ( is_active_sidebar( 'footer-2' ) ) : ?>
                    <?php dynamic_sidebar( 'footer-2' ); ?>
                <?php endif; ?>
            </div>

        </div>

        <div class="border-t border-gray-800 pt-8 mt-8">
            <!-- Policy Links -->
            <div class="flex flex-wrap justify-center gap-x-6 gap-y-2 mb-6">
                <?php
                $policy_links = array(
                    array( 'slug' => 'privacy-policy', 'label' => 'Privacy Policy' ),
                    array( 'slug' => 'terms-and-conditions', 'label' => 'Terms & Conditions' ),
                    array( 'slug' => 'shipping-policy', 'label' => 'Shipping Policy' ),
                    array( 'slug' => 'refund-returns-policy', 'label' => 'Refunds & Returns' ),
                );
                foreach ( $policy_links as $link ) :
                    $page = get_page_by_path( $link['slug'] );
                    if ( $page ) : ?>
                        <a href="<?php echo esc_url( get_permalink( $page->ID ) ); ?>" class="ff-policy-link"><?php echo esc_html( $link['label'] ); ?></a>
                    <?php else : ?>
                        <span class="ff-policy-link ff-policy-disabled" title="Page not created yet"><?php echo esc_html( $link['label'] ); ?></span>
                    <?php endif;
                endforeach; ?>
            </div>
            
            <!-- Copyright -->
            <div class="flex flex-col md:flex-row items-center justify-between text-xs text-gray-500 uppercase tracking-widest font-semibold">
                <p>&copy; <?php echo date('Y'); ?> Fashion Feet. All rights reserved.</p>
                <p class="mt-2 md:mt-0">Designed elegantly</p>
            </div>
        </div>
        
    </div>
</footer>

<!-- Back to Top Button -->
<button id="ff-back-to-top" class="ff-back-to-top" aria-label="Back to top" onclick="window.scrollTo({top:0,behavior:'smooth'})">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 15l-6-6-6 6"/></svg>
</button>

<?php wp_footer(); ?>

<!-- Tailwind scripts for any interactive elements if needed -->

<!-- Wishlist Toggle Script -->
<script>
function ffToggleHeart(productId, btn) {
    fetch(ffAjax.url, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=ff_wishlist_toggle&nonce=' + ffAjax.nonce + '&product_id=' + productId
    })
    .then(r => r.json())
    .then(d => {
        if (!d.success) return;
        const svg = btn.querySelector('svg');
        if (d.data.action === 'added') {
            btn.classList.add('ff-hearted');
            if (svg) svg.setAttribute('fill', 'currentColor');
            btn.style.transform = 'scale(1.3)';
            setTimeout(() => btn.style.transform = '', 300);
        } else {
            btn.classList.remove('ff-hearted');
            if (svg) svg.setAttribute('fill', 'none');
        }
        // Update all header wishlist badges
        document.querySelectorAll('.ff-wishlist-badge').forEach(badge => {
            badge.textContent = d.data.count;
            badge.style.display = d.data.count > 0 ? 'flex' : 'none';
        });
    });
}

// Back to Top Button
(function(){
    const btn = document.getElementById('ff-back-to-top');
    if (!btn) return;
    window.addEventListener('scroll', function() {
        btn.classList.toggle('ff-btt-visible', window.scrollY > 300);
    });
})();

// Page Entrance Animations (IntersectionObserver)
(function(){
    const targets = document.querySelectorAll('.wc-standard-page, .wc-checkout-page, .wc-login-page, .ff-thankyou, .ff-wishlist-page, .ff-404-page, .ff-search-results, main');
    targets.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
    });
    requestAnimationFrame(() => {
        targets.forEach(el => {
            el.style.opacity = '1';
            el.style.transform = 'translateY(0)';
        });
    });
})();
</script>
</body>
</html>