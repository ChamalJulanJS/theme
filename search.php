<?php
/**
 * Search Results Template
 *
 * @package FashionFeet
 */

get_header(); ?>

<main id="primary" class="ff-search-page">
    <div class="ff-search-page-header">
        <span class="ff-search-page-eyebrow">Search Results</span>
        <h1 class="ff-search-page-title">
            Results for "<?php echo esc_html( get_search_query() ); ?>"
        </h1>
    </div>

    <div class="ff-search-page-content">
        <?php if ( have_posts() ) : ?>
            <p class="ff-search-page-count">
                <?php
                global $wp_query;
                printf( '%d %s found', $wp_query->found_posts, $wp_query->found_posts === 1 ? 'result' : 'results' );
                ?>
            </p>

            <?php if ( class_exists('WooCommerce') && get_query_var('post_type') === 'product' ) : ?>
                <ul class="products columns-4">
                    <?php while ( have_posts() ) : the_post();
                        wc_get_template_part( 'content', 'product' );
                    endwhile; ?>
                </ul>
            <?php else : ?>
                <div class="ff-search-general-results">
                    <?php while ( have_posts() ) : the_post(); ?>
                        <article class="ff-search-result-card">
                            <?php if ( has_post_thumbnail() ) : ?>
                                <a href="<?php the_permalink(); ?>" class="ff-search-result-card-thumb">
                                    <?php the_post_thumbnail('medium'); ?>
                                </a>
                            <?php endif; ?>
                            <div class="ff-search-result-card-info">
                                <h2>
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h2>
                                <p><?php echo wp_trim_words( get_the_excerpt(), 30 ); ?></p>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>

            <div class="ff-search-pagination">
                <?php the_posts_pagination( array(
                    'mid_size'  => 2,
                    'prev_text' => '&larr; Previous',
                    'next_text' => 'Next &rarr;',
                ) ); ?>
            </div>

        <?php else : ?>
            <div class="ff-search-no-results-page">
                <svg class="w-20 h-20 mx-auto mb-6 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <h2>No Results Found</h2>
                <p>We couldn't find anything matching "<strong><?php echo esc_html( get_search_query() ); ?></strong>"</p>
                <p class="text-sm text-gray-400 mt-2">Try different keywords or browse our shop.</p>
                <?php if ( class_exists('WooCommerce') ) : ?>
                    <a href="<?php echo esc_url( wc_get_page_permalink('shop') ); ?>" class="ff-404-btn ff-404-btn-primary" style="margin-top:24px;">
                        Browse Shop
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
