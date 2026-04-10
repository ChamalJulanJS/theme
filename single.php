<?php get_header(); ?>

<main class="container-premium py-16 md:py-24 max-w-4xl mx-auto">
    <?php while ( have_posts() ) : the_post(); ?>
        
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            
            <header class="mb-12 text-center">
                <div class="text-sm font-semibold text-premium-500 uppercase tracking-widest mb-4">
                    <?php the_category(', '); ?>
                </div>
                <h1 class="text-4xl md:text-5xl font-black text-dark tracking-tight leading-tight mb-6">
                    <?php the_title(); ?>
                </h1>
                <div class="text-sm text-gray-500 font-medium">
                    By <?php the_author(); ?> on <?php echo get_the_date(); ?>
                </div>
            </header>

            <?php if ( has_post_thumbnail() ) : ?>
                <div class="mb-12 rounded-xl overflow-hidden shadow-xl">
                    <?php the_post_thumbnail('full', array('class' => 'w-full h-auto object-cover max-h-[600px]')); ?>
                </div>
            <?php endif; ?>

            <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed font-sans">
                <?php the_content(); ?>
            </div>

            <!-- Tags -->
            <?php if (has_tag()) : ?>
            <div class="mt-12 pt-8 border-t border-gray-200">
                <div class="flex gap-2 flex-wrap text-sm font-semibold">
                    <?php the_tags('<span class="bg-gray-100 px-3 py-1 rounded text-gray-600 hover:bg-gray-200 transition-colors">', '</span><span class="bg-gray-100 px-3 py-1 rounded text-gray-600 hover:bg-gray-200 transition-colors">', '</span>'); ?>
                </div>
            </div>
            <?php endif; ?>

        </article>

    <?php endwhile; ?>
</main>

<?php get_footer(); ?>
