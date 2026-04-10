<?php get_header(); ?>

<main class="container-premium py-16">
    <header class="mb-12 text-center gsap-fade-up opacity-0 translate-y-10">
        <h1 class="text-4xl font-black text-dark tracking-tight uppercase">
            <?php single_post_title(); ?>
        </h1>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 gsap-stagger-container">
        <?php if ( have_posts() ) : ?>
            <?php while ( have_posts() ) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('bg-white rounded-lg shadow-sm hover:shadow-lg transition-shadow duration-300 overflow-hidden gsap-stagger-item opacity-0 translate-y-10'); ?>>
                    <?php if ( has_post_thumbnail() ) : ?>
                        <div class="aspect-w-16 aspect-h-9 overflow-hidden">
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail('large', array('class' => 'object-cover w-full h-full hover:scale-105 transition-transform duration-500')); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                    <div class="p-6">
                        <h2 class="text-xl font-bold mb-2">
                            <a href="<?php the_permalink(); ?>" class="text-dark hover:text-premium-500 transition-colors">
                                <?php the_title(); ?>
                            </a>
                        </h2>
                        <div class="text-sm text-gray-500 mb-4 line-clamp-3">
                            <?php the_excerpt(); ?>
                        </div>
                        <a href="<?php the_permalink(); ?>" class="inline-flex items-center text-sm font-semibold text-premium-500 hover:text-dark transition-colors">
                            Read More &rarr;
                        </a>
                    </div>
                </article>
            <?php endwhile; ?>
        <?php else : ?>
            <div class="col-span-full text-center py-20">
                <p class="text-xl text-gray-500">No posts found.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <div class="mt-16 flex justify-center">
        <?php 
        the_posts_pagination( array(
            'mid_size'  => 2,
            'prev_text' => '&larr; Previous',
            'next_text' => 'Next &rarr;',
            'class'     => 'pagination-links flex gap-2'
        )); 
        ?>
    </div>
</main>

<?php get_footer(); ?>