<?php get_header(); ?>

<main class="container-premium py-16 md:py-24 max-w-4xl mx-auto">
    <?php while ( have_posts() ) : the_post(); ?>
        
        <header class="mb-12 text-center">
            <h1 class="text-4xl md:text-5xl font-black text-dark tracking-tight uppercase mb-6">
                <?php the_title(); ?>
            </h1>
        </header>

        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <div class="prose prose-lg px-0 max-w-none text-gray-600 leading-relaxed font-sans">
                <!-- Prose styles applied natively via Tailwind typography if available, otherwise basic styling -->
                <?php the_content(); ?>
            </div>
        </article>

    <?php endwhile; ?>
</main>

<?php get_footer(); ?>
